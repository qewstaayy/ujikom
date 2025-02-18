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

<form method="POST">
    <input type="text" name="username" placeholder="username" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Daftar</button>
</form>
<p>Sudah punya akun? <a href="login.php?redirect=checkout.php">Login di sini</a></p>