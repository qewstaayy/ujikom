<?php
session_start();
require '../config.php';

// Pastikan parameter 'id' tersedia dan valid
if (!isset($_GET['id'])) {
    header("Location: keranjang.php?error=invalid_request");
    exit();
}

$id = intval($_GET['id']);
if ($id <= 0) {
    header("Location: keranjang.php?error=invalid_id");
    exit();
}

// Query untuk mendapatkan informasi produk
$sql = "SELECT * FROM products WHERE id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Error dalam query: " . $conn->error);
}

$stmt->bind_param("i", $id);
if (!$stmt->execute()) {
    die("Gagal menjalankan query: " . $stmt->error);
}

$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $product = $result->fetch_assoc();

    // Pastikan stok mencukupi sebelum menambahkan ke keranjang
    if (!isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id] = [
            'name' => $product['name'],
            'price' => $product['price'],
            'image' => $product['image'],
            'quantity' => 1
        ];
    } else {
        if ($_SESSION['cart'][$id]['quantity'] < $product['stock']) { // Pastikan stok mencukupi
            $_SESSION['cart'][$id]['quantity'] += 1;
        } else {
            header("Location: keranjang.php?error=out_of_stock");
            exit();
        }
    }

    // Debugging: Cek isi keranjang setelah penambahan
    echo "<pre>";
    print_r($_SESSION['cart']);
    echo "</pre>";

    // Redirect ke halaman keranjang
    header("Location: keranjang.php");
    exit();
} else {
    header("Location: keranjang.php?error=product_not_found");
    exit();
}
