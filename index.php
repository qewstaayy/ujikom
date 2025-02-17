<?php 
require 'config.php';

$sql = "SELECT * FROM products";
$result = $conn->query($sql);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <title>BloomÉlégance</title>
    <style>
        body{
            margin: 0;
            padding: 0;
            text-align: center;
            font-family: poppins;
            background-color: #F4CCE9;          
            color: #56021F;
            display: flex;
            min-height: 100vh;
            flex-direction: column;
        }
        .main-content{
            flex: 1;
        }
        header {
            background-color: #56021F;
            padding: 15px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .nav-links {
            display: flex;
            gap: 20px;
        }

        .nav-links a {
            color: white;
            text-decoration: none; /* Hapus garis bawah */
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .nav-links a:hover {
            color: #D17D98;
        }

        /* Style untuk ikon keranjang */
        .cart-icon {
            font-size: 20px;
            color: white;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .cart-icon:hover {
            color: #D17D98;
        }

        .produk-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            padding: 20px;
            text-decoration: none;
            color: inherit;
        }
        .produk-card {
            border: 1px solid #D17D98;
            padding: 10px;
            border-radius: 10px;
            background-color: white;
            box-shadow: 0 4px 5px rgba(0,0,0,0.1);
            width: 200px; /* Lebar card */
            text-align: center;
            transition: transform 0.3s ease-in-out;
        }
        .produk-card:hover {
            transform: scale(1.05);
        }
        .produk-card img {
            width: 100%;        /* Sesuaikan lebar dengan card */
            height: 150px;      /* Atur tinggi gambar */
            object-fit: cover;  /* Pastikan gambar tetap proporsional dan ter-crop dengan baik */
            border-radius: 5px;
        }
        .produk-card h3{
            font-size: 18px;
            margin: 10px 0;
            text-decoration: none;
        }
        .produk-card p{
            font-size: 14px;
            color: #27ae60;
            text-decoration: none;
        }
        footer{
            background-color: #333;
            color: white;
            padding: 10px;
            text-align: center;
            margin-top: auto;
        }
    </style>
</head>
<body>
    <header> 
        <h1>BloomÉlégance</h1>
        <nav class="nav-links">
            <a href="#">Home</a>
            <a href="/ujikom/users/katalog.php">Katalog</a>
            <a href="#">Tentang kami</a>
            <a href="login.php">Login</a>
        </nav>
        <a href="/ujikom/users/keranjang.php" class="cart-icon">🛒</a>
    </header>

    <section class="hero">
        <h2> Temukan keindahan alma dalam setiap Buket</h2>
        <p>berbagai pilihan buket bungan akan hadir dalam setiap momen</p> 
    </section>

    <section class="katalog">
        <h2> produk populer</h2>
        <div class="produk-container">
            <?php
            if ($result->num_rows > 0){
                while ($row = $result->fetch_assoc()){
                    echo '<a href="/ujikom/users/detail_produk.php?id='. $row["id"].'" class="produk-card">';
                    echo '<img src="admin/uploads/'.$row["image"].'" alt="' .$row["name"].'">';
                    echo '<h3>' . $row["name"].'</h3>';
                    echo '<p>Rp '. number_format($row["price"],0, ',', '.'). '</p>';
                    echo '</a>';
                }
            } else{
                echo '<p> Tidak ada product tersedia </p>';
            }
            ?>
        </div>
    </section>

    <footer>
        <p>© 2025 Bouquet Indah | Instagram: @bouquetindah </p>
    </footer>
</body>
</html>
