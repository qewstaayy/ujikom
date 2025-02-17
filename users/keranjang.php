<?php
session_start();
require '../config.php';

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

    echo json_encode([
        "success" => true,
        "new_quantity" => $_SESSION['cart'][$id]['quantity'] ?? 0,
        "new_subtotal" => number_format($_SESSION['cart'][$id]['quantity'] * $_SESSION['cart'][$id]['price'], 0, ',', '.'),
        "new_total" => number_format($total, 0, ',', '.')
    ]);
    exit();
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #F9F9F9;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 50px auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }
        .btn {
            display: inline-block;
            text-decoration: none;
            background: #7D1C4A;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            transition: 0.3s;
        }
        .btn:hover {
            background: #D17D98;
        }
        .img-product {
            width: 50px;
            height: 50px;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Keranjang Belanja</h1>

        <?php if (!empty($_SESSION['cart'])) { ?>
            <table>
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Gambar</th>
                        <th>Harga</th>
                        <th>Jumlah</th>
                        <th>Total</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    require '../config.php';
                    $total = 0;
                    
                    foreach ($_SESSION['cart'] as $id => $item) {
                        // Ambil data stok terbaru dari database
                        $stmt = $conn->prepare("SELECT stock FROM products WHERE id = ?");
                        $stmt->bind_param("i", $id);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $productData = $result->fetch_assoc();
                        $stokTersedia = $productData['stock'];

                        $harga = intval($item['price']);
                        $quantity = intval($item['quantity']);
                        $subtotal = $harga * $quantity;
                        $total += $subtotal;
                        $imgSrc = !empty($item['image']) ? "../admin/uploads/" . htmlspecialchars($item['image']) : "../images/default.png";
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                            <td><img src="<?php echo $imgSrc; ?>" class="img-product"></td>
                            <td>Rp <?php echo number_format($harga, 0, ',', '.'); ?></td>
                            <td>
                                <button class="btn update-qty" data-id="<?php echo htmlspecialchars($id); ?>" data-action="kurang">-</button>
                                <span id="quantity-<?php echo htmlspecialchars($id); ?>"><?php echo $quantity; ?></span>
                                <?php if ($quantity < $stokTersedia){?>
                                    <button class="btn update-qty" data-id="<?php echo htmlspecialchars($id); ?>" data-action="tambah">+</button>
                                <?php }else{ ?>
                                    <span style="color: red; font-weight: bold;">MAX</span>
                                <?php } ?>
                                <td>Rp <span id="subtotal-<?php echo htmlspecialchars($id); ?>"><?php echo number_format($subtotal, 0, ',', '.'); ?></span></td>
                            </td>
                            <td> <span id="subtotal-<?php echo htmlspecialchars($id); ?>"> HAPUS</span></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

            <h2>Total: Rp <?php echo number_format($total, 0, ',', '.'); ?></h2>

            <a href="checkout.php" class="btn">Checkout</a>
        <?php } else { ?>
            <p>Keranjang belanja kosong.</p>
            <a href="katalog.php" class="btn">Lihat Produk</a>
        <?php } ?>
        <a href="katalog.php" class="btn">Kembali ke Katalog</a>
    </div>
    <script>
document.addEventListener("DOMContentLoaded", function () {
    const updateButtons = document.querySelectorAll(".update-qty");
    const deleteButtons = document.querySelectorAll(".delete-item");

    function updateCart(productId, action) {
        fetch("keranjang.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: "id=" + productId + "&action=" + action
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById("quantity-" + productId).innerText = data.new_quantity;
                document.getElementById("subtotal-" + productId).innerText = data.new_subtotal;
                document.getElementById("total-price").innerText = data.new_total;

                if (data.new_quantity === 0) {
                    document.getElementById("row-" + productId).remove();
                }
            } else {
                alert(data.error);
            }
        })
        .catch(error => console.error("Error:", error));
    }

    updateButtons.forEach(button => {
        button.addEventListener("click", function () {
            const productId = this.getAttribute("data-id");
            const action = this.getAttribute("data-action");
            updateCart(productId, action);
        });
    });

    deleteButtons.forEach(button => {
        button.addEventListener("click", function () {
            const productId = this.getAttribute("data-id");
            if (confirm("Apakah Anda yakin ingin menghapus produk ini?")) {
                updateCart(productId, "hapus");
            }
        });
    });
});
</script>

</body>
</html>
