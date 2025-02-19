<?php
session_start();
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $password);

    if ($stmt->execute()) {
        $_SESSION['user_id'] = $stmt->insert_id;

        $redirect = isset($_GET['redirect']) ? '/ujikom/' . ltrim($_GET['redirect'], '/') : '/ujikom/index.php';
        header("Location: " . $redirect);
        exit();
    } else {
        echo "Gagal mendaftar!";
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        /* Global Styles */
        body {
            font-family: 'Poppins';
            background-color: #FFC0CB;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .logo {
            width: 400px;
            height: 115px;
        }

        /* Container Styling */
        .container {
            background: #fff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 350px;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* Heading */
        h2 {
            margin-bottom: 20px;
            color: #B76E79;
            font-size: 32px;
            font-weight: 600;
        }

        /* Form Styling */
        form {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        form label {
            width: 100%;
            text-align: left;
            font-weight: 500;
            margin: 5px 0;
        }

        form input {
            width: calc(100% - 20px);
            padding: 12px;
            margin: 5px 0 15px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }

        /* Button Styling */
        button {
            width: 100%;
            padding: 12px;
            background: #B76E79;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }

        button:hover {
            background: #A1626D;
        }

        /* Link Styling */
        p {
            margin-top: 15px;
            font-size: 14px;
        }

        a {
            color: #B76E79;
            text-decoration: none;
            font-weight: 500;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="container">
        <img class="logo" src="admin/uploads/logo.png" alt="Logo">
        <h2>Registrasi</h2>

        <form method="POST">
            <label for="username">Username: </label>
            <input type="text" name="username" placeholder="username" required>
            <label for="email">Email: </label>
            <input type="email" name="email" placeholder="Email" required>
            <label for="password">Password: </label><input type="password" name="password" placeholder="Password" required>
            <button type="submit">Daftar</button>
        </form>
        <p>Sudah punya akun? <a href="login.php?redirect=checkout.php">Login di sini</a></p>

    </div>
</body>

</html>