<?php

session_start();
require '../config.php';
require_once 'midtrans_config.php';

use Midtrans\Snap;

// Cek koneksi database
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: /ujikom/login.php?redirect=/users/checkout.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Ambil data user untuk prefill form checkout
$query = "SELECT username, alamat, no_hp FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    die("User tidak ditemukan!");
}

// Cek apakah ada produk dalam keranjang
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    die("Keranjang Anda kosong, silakan tambah produk terlebih dahulu.");
}

$snapToken = null; // Inisialisasi Snap Token

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $alamat = $_POST['alamat'];
    $no_hp = $_POST['no_hp'];
    $total_price = 0;

    $query = "INSERT INTO orders (user_id, total_price, alamat) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iis", $user_id, $total_price, $alamat);
    $stmt->execute();
    $order_id = $stmt->insert_id;

    // Persiapkan query
    $stmt_select_product = $conn->prepare("SELECT price, stock FROM products WHERE id = ?");
    $stmt_insert_order_item = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price, subtotal) VALUES (?, ?, ?, ?, ?)");
    $stmt_update_stock = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");

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
    }

    $query = "UPDATE orders SET total_price = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $total_price, $order_id);
    $stmt->execute();

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
        'item_details' => [],
    ];

    foreach ($_SESSION['cart'] as $id => $item) {
        $transaction['item_details'][] = [
            'id' => $id,
            'price' => $product['price'],
            'quantity' => $item['quantity'],
            'name' => $item['name'],
        ];
    }

    try {
        $snapToken = Snap::getSnapToken($transaction);

        $query = "UPDATE orders SET snap_token = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $snapToken, $order_id);
        $stmt->execute();

        unset($_SESSION['cart']); // Kosongkan keranjang setelah checkout
    } catch (Exception $e) {
        die("Error membuat Snap Token: " . $e->getMessage());
    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <style>
        body {
            font-family: 'Poppins';
            background-color: #f9f9f9;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        input,
        textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .btn {
            background: #7D1C4A;
            color: white;
            padding: 10px 15px;
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
            <label>Nama:</label>
            <input type="text" name="nama" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
            <label>Alamat:</label>
            <textarea name="alamat" required><?php echo htmlspecialchars($user['alamat']); ?></textarea>
            <label>No. Telepon:</label>
            <input type="text" name="no_hp" value="<?php echo htmlspecialchars($user['no_hp']); ?>" required>
            <button type="submit" class="btn">Buat Pesanan</button>
        </form>

        <?php if ($snapToken) : ?>
            <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="SB-Mid-server--9nOsGKNOxnM3eLT6_axqtCa"></script>
            <button id="pay-button" class="btn">Bayar Sekarang</button>
            <script>
                document.getElementById('pay-button').onclick = function() {
                    window.snap.pay("<?php echo $snapToken; ?>");
                };
            </script>
        <?php endif; ?>

        <br>
        <a href="/ujikom/users/keranjang.php" class="btn">Kembali ke Keranjang</a>
    </div>
</body>

</html>