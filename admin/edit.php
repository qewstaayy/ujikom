<?php
include '../config.php'; // Pastikan koneksi database benar

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Ambil data produk berdasarkan ID
    $query = "SELECT * FROM products WHERE id = ?";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        die("Error dalam query: " . $conn->error);
    }

    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    if (!$product) {
        die("Produk tidak ditemukan!");
    }
} else {
    die("ID produk tidak valid!");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $stock = $_POST['stock'];
    $price = $_POST['price'];

    // Ambil gambar lama
    $query = "SELECT image FROM products WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($oldImage);
    $stmt->fetch();
    $stmt->close();

    // Cek apakah ada gambar baru
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "uploads/";
        $imageName = basename($_FILES['image']['name']);
        $targetFilePath = $targetDir . $imageName;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
            $newImage = $imageName;
        } else {
            echo "Error uploading image.";
            exit();
        }
    } else {
        $newImage = $oldImage; // Pakai gambar lama
    }

    // Update data di database
    $updateQuery = "UPDATE products SET name = ?, stock = ?, price = ?, image = ? WHERE id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("sidsi", $name, $stock, $price, $newImage, $id);

    if ($stmt->execute()) {
        header("Location: admin.php");
        exit();
    } else {
        echo "Error updating product: " . $stmt->error;
    }
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            text-align: center;
        }
        .container {
            width: 50%;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
        }
        label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
            text-align: left;
        }
        input[type="text"], input[type="number"], input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        img {
            margin-top: 10px;
            width: 150px;
            border-radius: 5px;
        }
        button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Edit Produk</h2>
        <form action="edit.php?id=<?= $product['id']; ?>" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= $product['id']; ?>">

            <label>Nama Produk:</label>
            <input type="text" name="name" value="<?= htmlspecialchars($product['name']); ?>" required>

            <label>Stok:</label>
            <input type="number" name="stock" value="<?= $product['stock']; ?>" required>

            <label>Harga:</label>
            <input type="number" name="price" value="<?= $product['price']; ?>" required>

            <label>Gambar Produk:</label>
            <input type="file" name="image">
            
            <!-- Menampilkan gambar lama -->
            <?php if (!empty($product['image'])): ?>
                <img src="uploads/<?= $product['image']; ?>" alt="Gambar Produk">
            <?php endif; ?>

            <button type="submit">Update</button>
        </form>
    </div>
</body>
</html>
