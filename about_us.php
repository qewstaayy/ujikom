<?php
session_start();

require 'includes/header.php';
$title = "Tentang Kami - Bouquet Store";
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <title><?php echo $title; ?></title>
    <style>
        body {
            font-family: poppins;
            margin: 0;
            padding: 0;
            background-color: #FFC0CB;
            ;
            color: #333;
        }

        .container {
            max-width: 800px;
            margin: 50px auto;
            background: white;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        h1 {
            text-align: center;
            color: #56021F;
        }

        p {
            line-height: 1.6;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Tentang Kami</h1>
        <p>Selamat datang di <strong>BloomÉlégance</strong>, toko online terbaik untuk berbagai rangkaian bunga yang indah dan berkualitas tinggi. Kami berdedikasi untuk menghadirkan keindahan dan kebahagiaan melalui setiap rangkaian bunga yang kami buat.</p>
        <p>Dengan tim florist profesional, kami menyediakan berbagai jenis buket untuk berbagai acara, seperti pernikahan, ulang tahun, dan momen spesial lainnya.</p>
        <p>Misi kami adalah memberikan layanan terbaik dengan produk bunga yang segar, berkualitas, dan harga yang bersaing.</p>
        <p>Terima kasih telah mempercayai <strong>BloomÉlégance</strong>. Kami siap memberikan pelayanan terbaik untuk Anda!</p>
    </div>
</body>

</html>