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
        }
        header{
            background-color: #56021F;
            padding: 15px;
            color: white;
        }
        h1,h2,h3{
            color: #7D1C4A;
        }
        nav a{
            color: white;
            text-align: none;
            margin : 0 15px;
        }
        .hero{
            background-image: none;
            padding: 50px;
            color: black;
        }
        .btn{
            background-color: #7D1C4A;
            padding: 10px 20px;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .btn:hover{
            background-color: #D17D98;
        }
        .katalog{
            padding: 20px;
        }
        .produk-container{
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }
        .produk-card{
            border: 1px solid #D17D98;
            padding: 10px;
            border-radius: 10px;
            background-color: white;
            box-shadow: 0 4px 5px rgba(0,0,0,0.1);
            width: 200px;
            text-align: center;
        }
        .produk-card img{
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 5px;
        }
        .produk-card h3{
            font-size: 18px;
            margin: 10px 0;
        }
        .produk-card p{
            font-size: 16px;
            font-weight: bold;
            color: #27ae60;
        }
        footer{
            background-color: #333;
            color: white;
            padding: 10px;
        }

    </style>
</head>
<body>
    <header> 
        <h1>BloomÉlégance</h1>
        <nav> 
            <a href=""> Home</a>
            <a href="/ujikom/users/katalog.php"> Katalog</a>
            <a href="#"> Tentang kami</a>
            <a href="login.php">Login</a>
        </nav>
    </header>

    <section class="hero">
        <h2> Temukan keindahan alma dalam setiap Buket</h2>
        <p>berbagai pilihan buket bungan akan hadir dalam setiap momen</p>
        <a href="/ujikom/users/katalog.php" class="btn">Lihat Katalog</a>
        </section>

    <section class="katalog">
        <h2> produk populer</h2>
        <div class="produk-container">
            <?php
            if ($result->num_rows > 0){
                while ($row = $result->fetch_assoc()){
                    echo '<div class="produk-card">';
                    echo '<img src="admin/uploads/'.$row["image"].'" alt="' .$row["name"].'">';
                    echo '<h3>' . $row["name"].'</h3>';
                    echo '<p>Rp '. number_format($row["price"],0, ',', '.'). '</p>';
                    echo '</div>';
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
