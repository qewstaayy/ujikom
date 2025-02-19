<?php
session_start();
require '../config.php';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <title>Keranjang Belanja</title>
    <style>
        body {
            font-family: 'Poppins';
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
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
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
            cursor: pointer;
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
    <header>
    </header>
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
                    $total = 0;

                    foreach ($_SESSION['cart'] as $id => $item) {
                        // Ambil stok terbaru dari database
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
                        <tr id="row-<?php echo htmlspecialchars($id); ?>">
                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                            <td><img src="<?php echo $imgSrc; ?>" class="img-product"></td>
                            <td>Rp <?php echo number_format($harga, 0, ',', '.'); ?></td>
                            <td>
                                <button class="btn update-qty" data-id="<?php echo htmlspecialchars($id); ?>" data-action="kurang">-</button>
                                <span id="quantity-<?php echo htmlspecialchars($id); ?>"><?php echo $quantity; ?></span>
                                <?php if ($quantity < $stokTersedia) { ?>
                                    <button class="btn update-qty" data-id="<?php echo htmlspecialchars($id); ?>" data-action="tambah">+</button>
                                <?php } else { ?>
                                    <span style="color: red; font-weight: bold;">M</span>
                                <?php } ?>
                            </td>
                            <td>Rp <span id="subtotal-<?php echo htmlspecialchars($id); ?>"><?php echo number_format($subtotal, 0, ',', '.'); ?></span></td>
                            <td><button class="btn delete-item" data-id="<?php echo htmlspecialchars($id); ?>">Hapus</button></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

            <h2>Total: Rp <span id="total-price"><?php echo number_format($total, 0, ',', '.'); ?></span></h2>

            <a href="checkout.php" class="btn">Checkout</a>
        <?php } else { ?>
            <p>Keranjang belanja kosong.</p>
            <a href="katalog.php" class="btn">Lihat Produk</a>
        <?php } ?>
        <a href="/ujikom/index.php" class="btn">Kembali ke Home</a>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll(".update-qty").forEach(button => {
                button.addEventListener("click", function() {
                    const productId = this.dataset.id;
                    const action = this.dataset.action;

                    console.log("Mengirim request:", {
                        id: productId,
                        action: action
                    });

                    fetch("update_keranjang.php", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/x-www-form-urlencoded"
                            },
                            body: `id=${productId}&action=${action}`
                        })
                        .then(response => response.json())
                        .then(data => {
                            console.log("Respon dari server:", data);

                            if (data.success) {
                                document.getElementById("quantity-" + productId).innerText = data.new_quantity;
                                document.getElementById("subtotal-" + productId).innerText = data.new_subtotal;
                                document.getElementById("total-price").innerText = data.new_total;

                                if (data.new_quantity === 0) {
                                    document.getElementById("row-" + productId).remove();
                                }
                            } else {
                                alert("Error: " + data.error);
                            }
                        })
                        .catch(error => console.error("Gagal memperbarui keranjang:", error));
                });
            });

            document.querySelectorAll(".delete-item").forEach(button => {
                button.addEventListener("click", function() {
                    const productId = this.dataset.id;

                    console.log("Menghapus produk:", productId);

                    fetch("update_keranjang.php", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/x-www-form-urlencoded"
                            },
                            body: `id=${productId}&action=hapus`
                        })
                        .then(response => response.json())
                        .then(data => {
                            console.log("Respon dari server:", data);

                            if (data.success) {
                                document.getElementById("row-" + productId).remove();
                                document.getElementById("total-price").innerText = data.new_total;
                            }
                        })
                        .catch(error => console.error("Gagal menghapus produk:", error));
                });
            });
        });
    </script>

</body>

</html>