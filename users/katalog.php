<?php 
require '../config.php';

// Mengambil data pencarian dan kategori dari URL
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$category_id = isset($_GET['category']) ? (int)$_GET['category'] : 0; // Pastikan angka

$limit = 6; // Produk per halaman
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Query utama untuk mengambil data produk
$sql = "SELECT * FROM products WHERE 1";

if (!empty($search)) {
    $sql .= " AND name LIKE '%$search%'";
}
if (!empty($category_id)) {
    $sql .= " AND category_id = $category_id"; // Gunakan angka tanpa kutip
}

$sql .= " LIMIT $start, $limit";
$result = $conn->query($sql);

// Debugging: Periksa query yang dieksekusi
// echo $sql; exit;

// Hitung total produk untuk pagination
$total_query = "SELECT COUNT(*) as total FROM products WHERE 1";

if (!empty($search)) {
    $total_query .= " AND name LIKE '%$search%'";
}
if (!empty($category_id)) {
    $total_query .= " AND category_id = $category_id";
}

$total_result = $conn->query($total_query);
if (!$total_result) {
    die("Error pada query pagination: " . $conn->error);
}

$total_row = $total_result->fetch_assoc();
$total_pages = ceil($total_row['total'] / $limit);
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
            font-family: Poppins, sans-serif;
            margin: 0;
            padding: 0;
            text-align: center;
            background-color: #f9f9f9;
        }
        header {
            background-color: #ff6699;
            padding: 15px;
            color: white;
        }
        nav a {
            color: white;
            text-decoration: none;
            margin: 0 15px;
        }
        .container {
            padding: 20px;
        }
        .search-filter {
            margin-bottom: 20px;
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
            box-shadow: 0 4px 5px rgba(0,0,0,0.1);
            width: 270px;
            text-align: center;
            position: relative;
            overflow: hidden;
            transition: transform 0.3s ease-in-out;
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
        .produk-card .rating {
            color: gold;
            font-size: 16px;
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
        .cart-btn, .detail-btn {
            border: none;
            background: white;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 5px;
            font-size: 12px;
            transition: 0.3s;
            text-decoration: none;
        }
        .cart-btn:hover, .detail-btn:hover {
            background: orange;
            color: white;
        }
        .pagination a {
            padding: 8px 12px;
            margin: 5px;
            background: #ff6699;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .pagination a:hover {
            background: #d45582;
        }
    </style>
</head>
<body>

<header>
    <h1>BloomÉlégance</h1>
    <nav>
        <a href="../index.php">Home</a>
        <a href="katalog.php">Katalog</a>
        <a href="#">Tentang Kami</a>
        <a href="login.php">Login</a>
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
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="produk-card">
                    <img src="/ujikom/admin/uploads/<?= htmlspecialchars($row["image"]) ?>" alt="<?= htmlspecialchars($row["name"]) ?>">
                    <h3><?= htmlspecialchars($row["name"]) ?></h3>
                    <p>Rp <?= number_format($row["price"], 0, ',', '.') ?></p>
                    <a href="detail_produk.php?id=<?= $row["id"] ?>" class="detail-btn">🔍 Lihat Detail</a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="color:red; font-weight:bold;">Produk tidak ditemukan.</p>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="katalog.php?<?= http_build_query(['page' => $i, 'search' => $search, 'category' => $category_id]) ?>"><?= $i ?></a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
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
