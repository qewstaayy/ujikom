<?php 
session_start();
require 'config.php';

if($_SERVER["REQUEST_METHOD"] == 'POST'){
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    //Pastikan tidak ada Variabel yang kosong 
    if(!empty($username) && !empty($password)){
        $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
        
        if ($stmt) {
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $stmt->store_result();

            //PAstikan untuk menemukan user cok 
            if($stmt->num_rows >0){
                $stmt->bind_result($id, $username, $hashed_password, $role);
                $stmt->fetch();
        
                if(password_verify($password, $hashed_password)) {
                    $_SESSION['username'] = $username;
                    $_SESSION['role'] = $role;
                    $_SESSION['id'] = $id;
        
                    if ($role == "admin"){
                        header("Location: /ujikom/admin/admin.php");
                    } else{
                        header("Location: /ujikom/users/user.php");
                    }
                    exit();
                } else{
                    echo "Password Salah!";
                }
            } else {
                echo "Username tidak ditemukan!";
            }
        }
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        .date{
            border: 1pc solid transparent;
        }
    </style>
</head>
<body>
    <h2> Login </h2>
    <form method="post">
        <label>Username:</label>
        <input type="text" name="username" required><br>
        <label>Password:</label>
        <input type="password" name="password" required><br>
        <button type="submit">Login</button>
    </form>
    <p> Belum punya akun? <a href="register.php">Daftar disini</a>
    
</body>
</html>