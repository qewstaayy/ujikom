<?php 
include '../config.php';

session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== "admin") {
    header("Location: login.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Page</title>
</head>
<body>
    <h2> Welcome, Admin <?php echo htmlspecialchars($_SESSION['username']); ?></h2>
    <p> This is your page</p>

    <h3> Manage ur Products</h3>
    <a href="add_product.php"> Add New product</a>
</body>
</html>