<?php 
session_start();
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = "user"; // Peran otomatis menjadi user

    $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES ( ?, ?, ?)");
    $stmt->bind_param("sss", $username, $password, $role);

    if($stmt->execute()){
        $_SESSION['username'] = $username;
        $_SESSION['role'] = $role;

        header("Location: /ujikom/users/user.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi</title>
</head>
<body>
    <h2>Registrasi Form</h2>

    <form method="post" action="">
        <label>Username: </label>
        <input type="text" name="username" required><br>
        <label>Password: </label>
        <input type="text" name="password" required><br>
        <button type="submit">Register</button>
    </form>

    <p> Sudah punya akun? <a href="login.php"> Login disini </a></p>
    
</body>
</html>