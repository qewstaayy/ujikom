<?php
session_start();
require 'config.php';

// Jika sudah login, redirect ke halaman utama
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE email = ?");

    if ($stmt) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                // Simpan data user ke session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                // Arahkan berdasarkan role
                if ($user['role'] == 'admin') {
                    header("Location: /ujikom/admin/admin.php"); // Ganti dengan halaman admin
                } else {
                    header("Location: /ujikom/index.php"); // Halaman user biasa
                }
                exit();
            } else {
                $error = "⚠️ Password salah!";
            }
        } else {
            $error = "⚠️ Email tidak ditemukan!";
        }
    } else {
        $error = "❌ Terjadi kesalahan pada database.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <title>Login</title>
    <style>
        /* Global Styles */
        body {
            font-family: 'Poppins', sans-serif;
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
            color: #56021F;
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
            background: #56021F;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }

        button:hover {
            background: rgb(131, 34, 68);
            font-weight: bold;
        }

        /* Link Styling */
        p {
            margin-top: 15px;
            font-size: 14px;
        }

        a {
            color: #56021F;
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
        <h2>Login</h2>

        <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>

        <form method="POST">
            <label>Email:</label>
            <input type="email" name="email" placeholder="Email" required>
            <label>Password: </label>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" class="login">Login</button>
        </form>

        <p>Belum punya akun? <a href="register.php?redirect=checkout.php">Daftar di sini</a></p>
    </div>

</body>

</html>