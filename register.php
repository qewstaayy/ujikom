<?php
session_start();
require 'config.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validasi input
    if (empty($username) || empty($email) || empty($password)) {
        $error = "Semua kolom harus diisi!";
    } elseif (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) {
        $error = "Username hanya boleh huruf, angka, dan underscore (3-20 karakter)!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Email tidak valid!";
    } elseif (strlen($password) < 6) {
        $error = "Password minimal 6 karakter!";
    } else {
        // Cek apakah username atau email sudah digunakan
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
        $stmt->bind_param("ss", $email, $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Username atau email sudah terdaftar!";
        } else {
            // Hash password dan simpan data ke database
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $role = "user"; // Default user, admin harus ditambahkan manual

            $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $username, $email, $hashed_password, $role);

            if ($stmt->execute()) {
                $_SESSION['user_id'] = $stmt->insert_id;
                $_SESSION['username'] = $username;
                $_SESSION['role'] = $role;
                header("Location: login.php?success=1");
                exit();
            } else {
                $error = "Gagal mendaftarkan akun. Silakan coba lagi!";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins';
            background-color: #FFC0CB;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

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

        .logo {
            width: 400px;
            height: auto;
        }

        h2 {
            color: #56021F;
            font-size: 32px;
            font-weight: 600;
        }

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
        <h2>Registrasi</h2>

        <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
        <form method="POST">
            <label for="username">Username:</label>
            <input type="username" name="username" placeholder="Masukkan username" required>

            <label for="email">Email:</label>
            <input type="email" name="email" placeholder="Masukkan email" required>

            <label for="password">Password:</label>
            <input type="password" name="password" placeholder="Masukkan password" required>

            <button type="submit">Daftar</button>
        </form>

        <p>Sudah punya akun? <a href="login.php">Login di sini</a></p>
    </div>
</body>

</html>