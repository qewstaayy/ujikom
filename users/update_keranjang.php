<?php
session_start();
require '../config.php';

header('Content-Type: application/json');

// Tangani permintaan AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $action = $_POST['action'];

    if (!isset($_SESSION['cart'][$id])) {
        echo json_encode(["success" => false, "error" => "Produk tidak ditemukan"]);
        exit();
    }

    // Ambil stok produk dari database
    $stmt = $conn->prepare("SELECT stock FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $productData = $result->fetch_assoc();
    $stokTersedia = $productData['stock'];

    if ($action === 'tambah') {
        if ($_SESSION['cart'][$id]['quantity'] < $stokTersedia) {
            $_SESSION['cart'][$id]['quantity'] += 1;
        }
    } elseif ($action === 'kurang') {
        if ($_SESSION['cart'][$id]['quantity'] > 1) {
            $_SESSION['cart'][$id]['quantity'] -= 1;
        } else {
            unset($_SESSION['cart'][$id]); // Jika jumlah 1 dan dikurangi, hapus dari keranjang
        }
    } elseif ($action === 'hapus') {
        unset($_SESSION['cart'][$id]); // Hapus produk dari keranjang
    }

    // Hitung ulang total harga
    $total = 0;
    foreach ($_SESSION['cart'] as $cartItem) {
        $total += $cartItem['price'] * $cartItem['quantity'];
    }

    // Pastikan tidak mengakses produk yang sudah dihapus
    echo json_encode([
        "success" => true,
        "new_quantity" => isset($_SESSION['cart'][$id]) ? $_SESSION['cart'][$id]['quantity'] : 0,
        "new_subtotal" => isset($_SESSION['cart'][$id]) ? number_format($_SESSION['cart'][$id]['quantity'] * $_SESSION['cart'][$id]['price'], 0, ',', '.') : '0',
        "new_total" => number_format($total, 0, ',', '.')
    ]);
    exit();
}
