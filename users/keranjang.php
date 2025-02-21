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
            background-color: #FFC0CB;
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

        .quantity-container {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
        }

        .btn-qty {
            background: #56021F;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .btn-qty:hover {
            background: #D17D98;
        }

        .quantity-input {
            width: 40px;
            text-align: center;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 5px;
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
            background: #56021F;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            transition: 0.3s;
            cursor: pointer;
        }

        .btn:hover {
            background: #D17D98;
        }

        .btn.disabled {
            background: #ccc !important;
            cursor: not-allowed !important;
            pointer-events: none !important;
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
                                <div class="quantity-container">
                                    <button class="btn-qty update-qty" data-id="<?php echo htmlspecialchars($id); ?>" data-action="kurang">-</button>
                                    <input type="text" class="quantity-input" id="quantity-<?php echo htmlspecialchars($id); ?>" value="<?php echo $quantity; ?>" readonly>
                                    <?php if ($quantity < $stokTersedia) { ?>
                                        <button class="btn-qty update-qty" data-id="<?php echo htmlspecialchars($id); ?>" data-action="tambah">+</button>
                                    <?php } else { ?>
                                        <span style="color: red; font-weight: bold;">M</span>
                                    <?php } ?>
                                </div>
                            </td
                                <td>Rp <span id="subtotal-<?php echo htmlspecialchars($id); ?>"><?php echo number_format($subtotal, 0, ',', '.'); ?></span></td>
                            <td><button class="btn delete-item" data-id="<?php echo htmlspecialchars($id); ?>">Hapus</button></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

            <h2>Total: Rp <span id="total-price"><?php echo number_format($total, 0, ',', '.'); ?></span></h2>


            <a href="checkout.php" class="btn" id="checkout-btn">Checkout</a>
        <?php } else { ?>
            <p>Keranjang belanja kosong.</p>
            <a href="katalog.php" class="btn">Lihat Produk</a>
        <?php } ?>
        <a href="/ujikom/index.php" class="btn">Kembali ke Home</a>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            function cekKeranjang() {
                let totalHarga = document.getElementById("total-price").innerText.replace(/\D/g, "");
                let checkoutBtn = document.getElementById("checkout-btn");

                if (parseInt(totalHarga) === 0 || isNaN(parseInt(totalHarga))) {
                    checkoutBtn.classList.add("disabled");
                } else {
                    checkoutBtn.classList.remove("disabled");
                }
            }

            cekKeranjang();

            document.querySelectorAll(".update-qty, .delete-item").forEach(button => {
                button.addEventListener("click", function() {
                    const productId = this.dataset.id;
                    const action = this.dataset.action || "hapus";

                    fetch("update_keranjang.php", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/x-www-form-urlencoded"
                            },
                            body: `id=${productId}&action=${action}`
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                if (action === "hapus" || data.new_quantity === 0) {
                                    document.getElementById("row-" + productId)?.remove();
                                } else {
                                    document.getElementById("quantity-" + productId).value = data.new_quantity;
                                    document.getElementById("subtotal-" + productId).innerText = data.new_subtotal;
                                }

                                document.getElementById("total-price").innerText = data.new_total;
                                cekKeranjang();
                            }
                        })
                        .catch(error => console.error("Gagal memperbarui keranjang:", error));
                });
            });
        });
    </script>

</body>

</html>