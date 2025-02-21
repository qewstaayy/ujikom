<?php
session_start();
require 'config.php';
require 'includes/header.php';

// Pengecekan login hanya untuk fitur tertentu, bukan untuk katalog
$loggedIn = isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] == 'user';


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
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins';
            background-color: #FFC0CB;
        }

        /* Promo Section */
        .promo-container {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: center;
            width: 90%;
            margin: 20px auto;
            gap: 20px;
            flex: 1;
        }

        .promo-image {
            flex: 1;
            max-width: 500px;
        }

        .promo-image img {
            width: 100%;
            height: auto;
            border-radius: 10px;
        }

        .promo-text {
            flex: 1;
            max-width: 400px;
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

        .promo-text p {
            font-size: 16px;
            color: #333;
            margin-top: 10px;
        }

        .promo-text h3 {
            color: #56021F;
            font-size: 15px;
            margin-top: 10px;
        }

        /* Produk Section */
        .katalog {
            text-align: center;
            padding: 20px;
        }

        .judul {
            font-size: 25px;
            font-weight: bold;
            color: #56021F;
            margin-bottom: 20px;
        }

        .produk-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            padding: 20px;
        }

        .produk-card {
            width: 200px;
            background: white;
            padding: 10px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 4px 5px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease-in-out;
            text-decoration: none;
            color: black;
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

        .stock-habis {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .stock-habis p {
            color: red;
            font-weight: bold;
        }

        footer {
            background-color: #56021F;
            color: white;
            padding: 10px;
            text-align: center;
            margin-top: auto;
            width: 100%;
        }

        /* Responsiveness */
        @media screen and (max-width: 768px) {
            .promo-container {
                flex-direction: column;
                text-align: center;
            }

            .promo-text {
                max-width: 100%;
                text-align: center;
            }

            .produk-container {
                flex-direction: column;
                align-items: center;
            }

            .produk-card {
                width: 90%;
                max-width: 250px;
            }
        }
    </style>
</head>

<body>

    <!-- Promo Section -->
    <div class="promo-container">
        <div class="promo-image">
            <img src="admin/uploads/bg3.jpg" alt="Buket Bunga Fresh">
        </div>
        <div class="promo-text">
            <h2>A Small Gift Gives a Deep Meaning</h2>
            <p>Let each petal speak more than words. Our fresh flower bouquet is a symbol of everlasting affection.</p>
            <h3>BloomÉlégance</h3>
        </div>
    </div>

    <!-- Produk Section -->
    <section class="katalog">
        <h2 class="judul">Produk Populer</h2>
        <div class="produk-container">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    // Cek apakah stok masih tersedia
                    if ($row["stock"] > 0) {
                        echo '<a href="/ujikom/users/detail_produk.php?id=' . $row["id"] . '" class="produk-card">';
                    } else {
                        echo '<div class="produk-card stock-habis">';
                    }

                    echo '<img src="admin/uploads/' . $row["image"] . '" alt="' . $row["name"] . '">';
                    echo '<h3>' . $row["name"] . '</h3>';
                    echo '<p>Rp ' . number_format($row["price"], 0, ',', '.') . '</p>';

                    if ($row["stock"] == 0) {
                        echo '<p>Stock Habis</p>';
                    }

                    if ($row["stock"] > 0) {
                        echo '</a>';
                    } else {
                        echo '</div>';
                    }
                }
            } else {
                echo '<p>Tidak ada produk tersedia</p>';
            }
            ?>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <p>© 2025 BloomÉlégance | Instagram: @BloomÉlégance</p>
    </footer>

</body>

</html>