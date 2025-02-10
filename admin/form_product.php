<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah product</title>
</head>
<body>
    <?php include "../config.php"; ?>
    <form action="add_product.php" method="post" enctype="multipart/form-data">
        <label>Name Bouquet: </label>
        <input type="text" name="name" required> <br>

        <label>Deskripsi : </label>
        <textarea name="description"required></textarea> <br>
        
        <label>Harga: </label>
        <input type="number" name="price" step="0.01" required> <br>
        
        <label>Stok: </label>
        <input type="number" name="stock" required> <br>

        <label>Kategori: </label>
        <select name="category_id"required>
            <option value="">Pilih Kategori</option>
            <?php
                $conn = new mysqli("localhost", "root", "", "ecommerce");
                if ($conn->connect_error) {
                    die("Koneksi gagal: " . $conn->connect_error);
                }

                $query = "SELECT id, name FROM categories";
                $result = $conn->query($query);

                if (!$result) {
                    die("Query Error: " . $conn->error);
                }

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>";
                    }
                } else {
                    echo "<option value=''>Kategori tidak ditemukan</option>";
                }
                $conn->close();
            ?>
        </select>

        <label>Gambar Bouquet: </label>
        <input type="file" name="image" accept="image/*" required> <br>

        <button type="submit" name="submit">Tambah Bouquet</button>
    </form>
</body>
</html>