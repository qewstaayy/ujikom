<?php
session_start();
require 'config.php';

// Ambil daftar produk dari database
$sql = "SELECT * FROM products";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <title>BloomÉlégance</title>
    <style>
        header {
            background-color: #FFC0CB;
            padding: 10px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            width: 400px;
            height: 115px;
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
            color: #56021F;
            transition: color 0.3s ease;
            font-weight: bold;
            font-size: 20px;
        }

        .nav-links a:hover {
            color: rgb(236, 162, 173);
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
            background-color: #FFC0CB;
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
            color: #56021F;
            padding: 10px;
        }

        .dropdown-content a:hover {
            background-color: #FFC0CB;
            display: block;
        }

        /* Tampilkan dropdown saat hover */
        .dropdown:hover .dropdown-content {
            display: block;
        }

        body {
            margin: 0;
            padding: 0;
            text-align: center;
            font-family: poppins;
            background-color: #FFC0CB;
            display: flex;
            min-height: 100vh;
            flex-direction: column;
        }

        .promo-section {
            display: flex;
            flex-direction: column;
            align-items: left;
            text-align: left;
        }

        .promo-container {
            display: flex;
            align-items: center;
            /* Posisikan vertikal sejajar */
            justify-content: space-between;
            /* Jarak antara gambar dan teks */
            width: 90%;
            /* Lebar kontainer */
            /* Maksimal lebar */
            margin: 0 auto;
            /* Tengah di dalam halaman */
        }

        .promo-image img {
            margin-top: 30px;
            width: 100%;
            /* Biar tidak terlalu besar */
            height: 400px;
            object-fit: cover;
        }


        /* Bagian teks setelah gambar */
        .promo-text {
            width: 40%;
            height: 200px;
            /* Pastikan teks berada di kanan */
            background: white;
            padding: 20px;
            border-radius: 10px;
            text-align: left;
        }

        .promo-text h2 {
            color: #56021F;
            font-size: 26px;
            font-weight: bold;
        }

        .promo-text h3 {
            color: #56021F;
            font-size: 15px;
        }

        .promo-text p {
            font-size: 16px;
            color: #333;
        }

        .produk-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            padding: 20px;
        }

        .judul {
            position: relative;
            text-align: center;
            font-size: 25px;
            font-weight: bold;
            margin-top: 50px;
            color: #56021F;
            padding-top: 30px;
        }

        .judul::before {
            content: "";
            position: absolute;
            top: 0;
            left: 50%;
            width: 90%;
            height: 2px;
            background-color: #8b5d67;
            transform: translateX(-50%);
        }

        .produk-card {
            margin-top: 20px;
            border: 1px solid #D17D98;
            padding: 10px;
            border-radius: 10px;
            background-color: white;
            box-shadow: 0 4px 5px rgba(0, 0, 0, 0.1);
            width: 200px;
            text-align: center;
            transition: transform 0.3s ease-in-out;
            text-decoration: none;
        }

        .produk-card:hover {
            transform: scale(1.05);
        }

        .produk-card img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 5px;
        }

        .produk-card h3 {
            font-size: 18px;
            margin: 10px 0;
        }

        .produk-card p {
            font-size: 14px;
            color: #27ae60;
        }

        footer {
            background-color: #B76E79;
            color: white;
            padding: 10px;
            text-align: center;
            margin-top: auto;
        }
    </style>
</head>

<body>
    <header>
        <img class="logo" src="admin/uploads/logo.png" alt="Logo">
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
                    <li><a href="login.php">Login</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>


    <div class="promo-container">
        <div class="promo-image">
            <img src="admin/uploads/bg3.jpg" alt="Buket Bunga Fresh">
        </div>
        <div class="promo-text">
            <h2>A Small gift gives a Deep Meaning</h2>
            <p>Let each petal speak more than words. Our fresh flower bouquet is a symbol of everlasting affection. </p>
            <h3>BloomÉlégance</h3>
        </div>
    </div>


    <section class="katalog">
        <h2 class="judul">Produk Populer</h2>
        <div class=" produk-container">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<a href="/ujikom/users/detail_produk.php?id=' . $row["id"] . '" class="produk-card">';
                    echo '<img src="admin/uploads/' . $row["image"] . '" alt="' . $row["name"] . '">';
                    echo '<h3>' . $row["name"] . '</h3>';
                    echo '<p>Rp ' . number_format($row["price"], 0, ',', '.') . '</p>';
                    echo '</a>';
                }
            } else {
                echo '<p> Tidak ada produk tersedia </p>';
            }
            ?>
        </div>
    </section>

    <footer>
        <p>© 2025 Bouquet Indah | Instagram: @bouquetindah </p>
    </footer>
</body>

</html>