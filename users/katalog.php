<?php
require '../config.php';

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
        body {

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

        .main-content {
            flex: 1;
        }

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

        .container {
            padding: 20px;
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

        footer {
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
        <nav>
            <ul class="nav-links">
                <li><a href="/ujikom/index.php">Home</a></li>
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
        <h2>Katalog Produk Kami</h2>

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
                    echo '<a href="/ujikom/users/detail_produk.php?id=' . $row["id"] . '" class="produk-card">';
                    echo '<img src="/ujikom/admin/uploads/' . $row["image"] . '" alt="' . $row["name"] . '">';
                    echo '<h3 style="text-decoration: none;">' . $row["name"] . '</h3>';
                    echo '<p>Rp ' . number_format($row["price"], 0, ',', '.') . '</p>';
                    echo '</a>';
                }
            } else {
                echo '<p> Tidak ada product tersedia </p>';
            }
            ?>
        </div>
    </div>

    <footer>
        <p>© 2025 Bouquet Indah | Instagram: @bouquetindah </p>
    </footer>

    <script>
        function addToFavorites(productId) {
            alert("Produk " + productId + " telah ditambahkan ke favorit!");
        }
    </script>

</body>

</html>