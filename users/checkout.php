<?php
session_start();
require '../config.php';

// Cek koneksi
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
if (!$stmt) {
    die("Query Error (Ambil User): " . $conn->error);
}

// Mengikat parameter user_id
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Cek apakah user ditemukan
if (!$user) {
    die("User  tidak ditemukan!");
}

// Cek apakah ada produk dalam keranjang
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    die("Keranjang Anda kosong, silakan tambah produk terlebih dahulu.");
}

// Jika form checkout dikirim
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $alamat = $_POST['alamat'];
    $no_hp = $_POST['no_hp'];
    $total_price = 0;

    // Insert ke tabel orders
    $query = "INSERT INTO orders (user_id, total_price, status, alamat, no_hp) VALUES (?, ?, 'pending', ?, ?)";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("Query Error (Insert Orders): " . $conn->error);
    }

    // Mengikat parameter
    $stmt->bind_param("iiss", $user_id, $total_price, $alamat, $no_hp);
    if (!$stmt->execute()) {
        die("Eksekusi gagal: " . $stmt->error);
    }
    $order_id = $stmt->insert_id;

    // Insert ke tabel order_items dan hitung total harga
    foreach ($_SESSION['cart'] as $id => $item) {
        // Ambil harga terbaru dari database untuk mencegah manipulasi harga
        $query = "SELECT price, stock FROM products WHERE id = ?";
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            die("Query Error (Ambil Produk): " . $conn->error);
        }
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();

        if (!$product) {
            die("Produk tidak ditemukan!");
        }

        // Cek stok cukup atau tidak
        if ($product['stock'] < $item['quantity']) {
            die("Stok tidak mencukupi untuk produk {$item['name']}!");
        }

        $subtotal = $product['price'] * $item['quantity'];
        $total_price += $subtotal;

        // Insert ke order_items
        $query = "INSERT INTO order_items (order_id, product_id, quantity, price, subtotal) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            die("Query Error (Insert Order Items): " . $conn->error);
        }
        $stmt->bind_param("iiiii", $order_id, $id, $item['quantity'], $product['price'], $subtotal);
        $stmt->execute();

        // Kurangi stok produk
        $query = "UPDATE products SET stock = stock - ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            die("Query Error (Update Stok): " . $conn->error);
        }
        $stmt->bind_param("ii", $item['quantity'], $id);
        $stmt->execute();
    }
    // Update total_price di tabel orders
    $query = "UPDATE orders SET total_price = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $total_price, $order_id);
    $stmt->execute();

    // Kosongkan keranjang setelah checkout
    unset($_SESSION['cart']);

    echo "Checkout berhasil! Pesanan Anda telah dibuat.";
    exit();
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

        <br>
        <a href="/ujikom/users/keranjang.php" class="btn">Kembali ke Keranjang</a>
    </div>
</body>

</html>