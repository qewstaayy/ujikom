<?php

require '../config.php';

// Mengecek apakah ada ID produk di url 
if (isset($_GET['id'])){
    $id = $_GET['id'];
    $sql = "SELECT * FROM products WHERE id = ? ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();


    //Mengecek apakah produk sudah ditemukan atau belum 
    if($result->num_rows > 0){
        $product = $result->fetch_assoc();
    }else{
        echo "Produk tidak ditemukan";
        exit();
    }
}else{
    echo "ID Produk tidak valid";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <title>Detail produk</title>
    <style>
        body{
            font-family: poppins;
            text-align: center;
            margin: 0;
            padding: 0;
        }
        .container{
            width: 60%;
            margin: auto;
            padding: 20px;
            text-align: left;
        }
        .produk-img{
            width: 100%;
            max-width: 400%;
            height: auto;
            border-radius: 5px;
        }
        .btn{
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #ff6699;
            border-radius: 5px;
        }
        .btn :hover{
            background-color: #ff3366;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><?php echo $product['name']; ?></h1>
        <img src="../admin/uploads/<?php echo $product['image'];?>" alt="<?php echo $product['name']; ?>"class="produk-img">
        <p><strong>Harga: </strong> Rp<?php echo number_format($product['price'],0, ',', '.');?></p>
        <p><?php echo $product['description']; ?></p>

        <a href="katalog.php" class="btn">Kembali ke katalog</a>
        <a href="keranjang.php?add=<?php echo $product['id']; ?>" class="btn">Tambah ke keranjang</a>
    </div>
</body>
</html>

