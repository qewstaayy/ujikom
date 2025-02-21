<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins';
            background-color: #FFC0CB;
            text-align: center;
            padding: 50px;
        }

        .success-box {
            background: rgb(254, 254, 254);
            color: #56021F;
            padding: 20px;
            border-radius: 5px;
        }
    </style>
</head>

<body>
    <div class="success-box">
        <h2>✅ Pembayaran Berhasil!</h2>
        <p>Pesanan Anda telah diproses. Terima kasih telah berbelanja!</p>
        <a href="/ujikom/index.php">Kembali ke Beranda</a>
    </div>
</body>

</html>