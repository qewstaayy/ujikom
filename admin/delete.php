<?php
include '../config.php';

if(isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = "DELETE FROM products WHERE id =?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);

    if($stmt->execute()){
        header("Location: admin.php");
        exit();
    }else {
        echo "Error deleting product:" . $conn->error;
    }
}