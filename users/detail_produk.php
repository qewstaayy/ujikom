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
        header {
            background-color: #56021F;
            padding: 10px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* Pastikan dropdown bisa ditampilkan */
        .nav-links {
            list-style: none;
            display: flex;
            gap: 20px;
            align-items: center;
            padding: 0;
        }

        .nav-links a {
            text-decoration: none;
            color: white;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .nav-links a:hover {
            color: #D17D98;
        }

        /* Atur posisi dropdown agar turunannya muncul */
        .dropdown {
            position: relative;
            display: inline-block;
        }

        /* Sembunyikan dropdown-content saat default */
        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #56021F;
            min-width: 100px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
            list-style: none;
            padding: 0;
            top: 100%;
            left: 0;
            z-index: 1000;
        }

        /* Pastikan setiap item dalam dropdown terlihat rapi */
        .dropdown-content li {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

        .dropdown-content li:last-child {
            border-bottom: none;
        }

        /* Warna hover untuk menu dropdown */
        .dropdown-content a {
            display: block;
            text-decoration: none;
            color: white;
            padding: 10px;
        }

        .dropdown-content a:hover {
            background-color: #56021F;
        }

        /* Tampilkan dropdown saat hover */
        .dropdown:hover .dropdown-content {
            display: block;
        }


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
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
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
    <header>

        <h1>BloomÉlégance</h1>
        <nav>
            <ul class="nav-links">
                <li><a href="#">Home</a></li>
                <li><a href="/ujikom/users/katalog.php">Katalog</a></li>
                <li><a href="#">Tentang Kami</a></li>
                <li><a href="/ujikom/users/keranjang.php">Keranjang</a></li>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="dropdown">
                        <a href="#" class="dropbtn"><?php echo htmlspecialchars($_SESSION['username']); ?> ▼</a>
                        <ul class="dropdown-content">
                            <li><a href="logout.php">Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li><a href="/ujikom/login.php">Login</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

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