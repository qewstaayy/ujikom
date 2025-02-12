<?php
include '../config.php';

if(isset($_GET['id'])){
    $id = $_GET['id'];
    $query = "SELECT * FROM products WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    $name = $_POST['name'];
    $stock = $_POST['stock'];
    $price = $_POST['price'];

    $updateQuery = "UPDATE products SET name =?, stock = ?, price = ? WHERE id =?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("sidi", $name, $stock, $price, $id);

    if($stmt->execute()){
        header("Location: admin.php");
        exit();
    }else{
        echo "Error updating product: " . $conn->error;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit product</title>
</head>
<body>
    <h2> Edit product</h2>
    <form method="post">
        <label> Product name :</label>
        <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required><br>
        <label> Product stock :</label>
        <input type="text" name="stock" value="<?= htmlspecialchars($product['stock']) ?>" required><br>
        <label> Product price :</label>
        <input type="text" name="price" value="<?= htmlspecialchars($product['price']) ?>" required><br>

        <button type="submit"> Update </button>
        
    </form>
</body>
</html>