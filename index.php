<?php 
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== "user") {
    header("Location: ../login.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h2> Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?> !</h2>
    <p>This is user side</p>
</body>
</html> 