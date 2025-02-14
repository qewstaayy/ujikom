<?php 
session_start();
require 'config.php';

if($_SERVER["REQUEST_METHOD"] == 'POST'){
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Pastikan tidak ada variabel kosong 
    if(!empty($username) && !empty($password)){
        $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
        
        if (!$stmt) {
            die("Query gagal: " . $conn->error); // Debugging jika prepare gagal
        }

        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        // Pastikan username ditemukan
        if($stmt->num_rows > 0){
            $stmt->bind_result($id, $username, $hashed_password, $role);
            $stmt->fetch();
    
            if(password_verify($password, $hashed_password)) {
                $_SESSION['username'] = $username;
                $_SESSION['role'] = $role;
                $_SESSION['id'] = $id;
    
                $stmt->close(); // Tutup statement sebelum redirect

                if ($role == "admin"){
                    header("Location: /ujikom/admin/admin.php");
                } else{
                    header("Location: /ujikom/users/user.php");
                }
                exit();
            } else {
                echo "<script>alert('Password salah!');</script>";
            }
        } else {
            echo "<script>alert('Username tidak ditemukan!');</script>";
        }

        $stmt->close(); // Pastikan statement selalu ditutup
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <title>Login</title>
</head>
<body>
    <h2>Login</h2>
    <form method="post">
        <label>Username:</label>
        <input type="text" name="username" required><br>
        <label>Password:</label>
        <input type="password" name="password" required><br>
        <button type="submit">Login</button>
    </form>
    <p>Belum punya akun? <a href="register.php">Daftar di sini</a></p>
</body>
</html>
