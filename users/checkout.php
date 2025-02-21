<?php
session_start();
require '../config.php';
require_once 'midtrans_config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit();
}

use Midtrans\Snap;

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

if (!isset($_SESSION['user_id'])) {
    header("Location: /ujikom/login.php?redirect=/users/checkout.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$query = "SELECT username, alamat, no_hp FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    die("User tidak ditemukan!");
}

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    die("Keranjang Anda kosong, silakan tambah produk terlebih dahulu.");
}

$snapToken = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $alamat = $_POST['alamat'];
    $no_hp = $_POST['no_hp'];
    $total_price = 0;

    $query = "INSERT INTO orders (user_id, total_price, alamat) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iis", $user_id, $total_price, $alamat);
    $stmt->execute();
    $order_id = $stmt->insert_id;

    $stmt_select_product = $conn->prepare("SELECT price, stock FROM products WHERE id = ?");
    $stmt_insert_order_item = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price, subtotal) VALUES (?, ?, ?, ?, ?)");
    $stmt_update_stock = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");

    $item_details = [];

    foreach ($_SESSION['cart'] as $id => $item) {
        $stmt_select_product->bind_param("i", $id);
        $stmt_select_product->execute();
        $result = $stmt_select_product->get_result();
        $product = $result->fetch_assoc();

        if (!$product) {
            die("Produk tidak ditemukan!");
        }

        if ($product['stock'] < $item['quantity']) {
            die("Stok tidak mencukupi untuk produk {$item['name']}!");
        }

        $subtotal = $product['price'] * $item['quantity'];
        $total_price += $subtotal;

        $stmt_insert_order_item->bind_param("iiiii", $order_id, $id, $item['quantity'], $product['price'], $subtotal);
        $stmt_insert_order_item->execute();

        $stmt_update_stock->bind_param("ii", $item['quantity'], $id);
        $stmt_update_stock->execute();

        $item_details[] = [
            'id' => $id,
            'price' => $product['price'],
            'quantity' => $item['quantity'],
            'name' => $item['name'],
        ];
    }

    $query = "UPDATE orders SET total_price = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $total_price, $order_id);
    $stmt->execute();

    unset($_SESSION['cart']);

    $transaction = [
        'transaction_details' => [
            'order_id' => $order_id,
            'gross_amount' => $total_price,
        ],
        'customer_details' => [
            'first_name' => $user['username'],
            'email' => 'user@example.com',
            'phone' => $no_hp,
        ],
        'item_details' => $item_details,
    ];

    try {
        $snapToken = Snap::getSnapToken($transaction);
        $query = "UPDATE orders SET snap_token = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $snapToken, $order_id);
        $stmt->execute();
    } catch (Exception $e) {
        die("Error Snap Token: " . $e->getMessage());
    }
}
?>


<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <title>Checkout</title>
    <style>
        body {
            font-family: 'Poppins';
            background-color: #FFC0CB;
            padding: 20px;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        .form-group {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        label {
            width: 120px;
            /* Lebar tetap untuk label */
            font-weight: 500;
        }

        input,
        textarea {
            flex: 1;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }


        .container {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        textarea {
            width: 100%;
            height: 100px;
            /* Atur tinggi tetap */
            resize: none;
            /* Mencegah pengguna mengubah ukuran textarea */
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }


        .btn {
            background: #56021F;
            color: white;
            padding: 10px 15px;
            margin-top: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn:hover {
            background: #D17D98;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Checkout</h2>

        <form action="" method="POST">
            <div class="form-group">
                <label for="nama">Nama:</label><br>
                <input type="text" id="nama" name="nama" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
            </div>

            <div class="form-group">
                <label for="alamat">Alamat:</label>
                <textarea id="alamat" name="alamat" required><?php echo htmlspecialchars($user['alamat']); ?></textarea>
            </div>

            <div class="form-group">
                <label for="no_hp">No. Telepon:</label>
                <input type="text" id="no_hp" name="no_hp" value="<?php echo htmlspecialchars($user['no_hp']); ?>" required>
            </div>

            <button type="submit" class="btn">Buat Pesanan</button>
        </form>

        <?php if ($snapToken) : ?>
            <button id="pay-button" class="btn" style="margin-top: 10px;">Bayar Sekarang</button>
        <?php endif; ?>
    </div>

    <script src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="SB-Mid-server--9nOsGKNOxnM3eLT6_axqtCa">
    </script>

    <script>
        document.getElementById('pay-button')?.addEventListener('click', function() {
            console.log("✅ Tombol 'Bayar Sekarang' diklik!");

            if (!window.snap) {
                console.error("❌ Snap tidak tersedia!");
                alert("Error: Snap tidak termuat. Coba refresh halaman.");
                return;
            }

            window.snap.pay("<?php echo $snapToken; ?>", {
                onSuccess: function(result) {
                    console.log("🎉 Pembayaran berhasil!", result);
                    alert("Pembayaran berhasil! Anda akan diarahkan ke halaman sukses.");
                    window.location.href = "/ujikom/users/checkout_success.php"; // Redirect ke halaman sukses
                },
                onPending: function(result) {
                    alert("Pembayaran masih diproses.");
                },
                onError: function(result) {
                    alert("Pembayaran gagal! Silakan coba lagi.");
                },
                onClose: function() {
                    alert("Transaksi belum selesai. Selesaikan pembayaran untuk melanjutkan.");
                }
            });

        });
    </script>
</body>

</html>