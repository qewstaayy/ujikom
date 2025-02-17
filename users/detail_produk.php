<?php
require '../config.php';

// Pastikan koneksi database berjalan
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Mengecek apakah ada ID produk di URL dan mengamankannya
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    echo "ID Produk tidak valid";
    exit();
}

$sql = "SELECT * FROM products WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

// Mengecek apakah produk ditemukan
if ($result->num_rows > 0) {
    $product = $result->fetch_assoc();
    $stock = intval($product['stock']);
} else {
    echo "Produk tidak ditemukan";
    exit();
}

// Menentukan gambar produk
$imagePath = !empty($product['image']) ? "../admin/uploads/" . htmlspecialchars($product['image']) : "../images/default.png";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <title>Detail Produk</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #F9F9F9;
            color: #333;
        }
        .container {
            display: flex;
            max-width: 900px;
            margin: 50px auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .product-img {
            max-width: 500px;
            height: auto;
            border-radius: 10px;
            margin-right: 20px;
        }
        .product-info {
            flex: 1;
        }
        h1 {
            font-size: 24px;
            color: #222;
        }
        .price {
            font-size: 22px;
            font-weight: bold;
            color: #27ae60;
            margin: 10px 0;
        }
        .description {
            font-size: 16px;
            color: #555;
            margin-bottom: 15px;
        }
        .stock-info {
            font-size: 16px;
            color: #e74c3c;
            font-weight: bold;
            margin-bottom: 10px;
        }
        label {
            font-size: 16px;
            font-weight: 500;
            display: block;
            margin-top: 10px;
        }
        select {
            width: 60px;
            padding: 5px;
            font-size: 16px;
        }
        .btn {
            display: inline-block;
            text-decoration: none;
            background: #7D1C4A;
            color: white;
            padding: 12px 20px;
            border-radius: 5px;
            transition: 0.3s;
            margin-top: 20px;
            font-size: 16px;
        }
        .btn:hover {
            background: #D17D98;
        }
        .out-of-stock {
            font-size: 18px;
            color: red;
            font-weight: bold;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <img src="<?php echo $imagePath; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-img">

        <div class="product-info">
            <h1><?php echo htmlspecialchars($product['name']); ?></h1>
            <p class="price">Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></p>
            <p class="description"><?php echo htmlspecialchars($product['description']); ?></p>
            <p class="stock-info">Stock: <?php echo $stock; ?> pcs</p>

            <?php if ($stock > 0) { ?>
                <label for="quantity">Quantity</label>
                <select id="quantity" name="quantity">
                    <?php 
                        $maxQuantity = min($stock, 10);
                        for ($i = 1; $i <= $maxQuantity; $i++) { 
                            echo "<option value='$i'>$i</option>";
                        } 
                    ?>
                </select>
                <br>
                <a href="cart.php?id=<?php echo $product['id']; ?>" class="btn">Add to Cart</a>
            <?php } else { ?>
                <p class="out-of-stock">Out of Stock</p>
            <?php } ?>

            <br>
            <a href="katalog.php" class="btn">Kembali ke Katalog</a>
        </div>
    </div>
</body>
</html>
