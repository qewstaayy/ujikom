<?php
session_start();
require '../config.php';
require '../includes/header.php';

// Mengambil data pencarian dan kategori dari URL
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$category_id = isset($_GET['category']) ? (int)$_GET['category'] : 0; // Pastikan angka


// Query utama untuk mengambil data produk
$sql = "SELECT * FROM products WHERE 1";

if (!empty($search)) {
    $sql .= " AND name LIKE '%$search%'";
}
if (!empty($category_id)) {
    $sql .= " AND category_id = $category_id"; // Gunakan angka tanpa kutip
}

$result = $conn->query($sql);


?>


<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <title>Katalog Produk - BloomÉlégance</title>
    <style>
        .main-content {
            flex: 1;
        }

        .container {
            padding: 20px;
            color: #56021F;
            flex: 1;
        }

        .search-filter {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
        }

        .search-filter input,
        .search-filter select {
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #333;
        }

        .search-filter button {
            background-color: #56021F;
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            border: 1px solid black;
        }

        .search-filter button:hover {
            background-color: #F4CCE9;
            color: #56021F;
            font-weight: bold;
        }


        .produk-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }

        .produk-card {
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 10px;
            background-color: #fff;
            box-shadow: 0 4px 5px rgba(0, 0, 0, 0.1);
            width: 270px;
            text-align: center;
            position: relative;
            overflow: hidden;
            transition: transform 0.3s ease-in-out;
            text-decoration: none;
        }

        .produk-card:hover {
            transform: scale(1.05);
        }

        .produk-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 5px;
        }

        .produk-card h3 {
            font-size: 18px;
            margin: 10px 0;
        }

        .produk-card p {
            font-size: 16px;
            font-weight: bold;
            color: #27ae60;
        }

        .hover-actions {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            justify-content: space-around;
            padding: 6px;
            opacity: 0;
            transition: opacity 0.3s ease-in-out;
        }

        .produk-card:hover .hover-actions {
            opacity: 1;
        }

        .stock-habis {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .stock-habis p {
            color: red;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="container">

        <!-- Search and filter -->
        <div class="search-filter">
            <form method="GET" action="katalog.php">
                <input type="text" name="search" placeholder="Cari produk.." value="<?= htmlspecialchars($search) ?>">
                <select name="category">
                    <option value="">Semua Kategori</option>
                    <option value="1" <?= $category_id == 1 ? 'selected' : '' ?>>Bouquet Wisuda</option>
                    <option value="2" <?= $category_id == 2 ? 'selected' : '' ?>>Bouquet Valentine</option>
                    <option value="3" <?= $category_id == 3 ? 'selected' : '' ?>>Bouquet Uang</option>
                    <option value="4" <?= $category_id == 4 ? 'selected' : '' ?>>Bouquet Makanan</option>
                </select>
                <button type="submit">Cari</button>
            </form>
        </div>
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

                    echo '<img src="/ujikom/admin/uploads/' . $row["image"] . '" alt="' . $row["name"] . '">';
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
    </div>

</body>

</html>