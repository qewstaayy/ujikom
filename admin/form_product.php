<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <title>Tambah product</title>
    <style> 
        body{
            font-family: poppins;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .form-container{
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow:0 0 10px rgba(0,0,0,0.1);
            width: 500px;
            text-align: center;
        }
        .form-container h2{
            margin-bottom: 15px;
        }
        label{
            display: block;
            text-align: left;
            margin: 10px 0 5px;
        }
        input, select{
            width: 90%;
            padding: 8px;
            border : 1px solid #ccc;
            border-radius: 5px;
        }
        textarea{
            width: 90%;
            height: 100px;
            resize: none;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button{
            background-color: #28a745;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }
        button:hover{
            background-color: #218838;
        }

    </style>
</head>
<body>
    <?php include "../config.php"; ?>
    <div class="form-container">
        <h2> Tambah Product</h2>
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