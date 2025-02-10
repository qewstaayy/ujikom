<?php

require '../config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $category_id = $_POST['category_id'];
    
    //upload gambar
    $image_name = $_FILES['image']['name'];
    $image_tmp = $_FILES['image']['tmpt_name'];
    $image_path = "uploads/" . $image_name;
    move_uploaded_file($image_tmp, $image_path);

    //Simpan ke dalam database 
    $query = "Insert into product (name, description, price, stock, category_id, image) Values (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssdiss", $name, $description, $price, $stock, $category_id, $image_name);

    if ($stmt->execute()){
        echo"Produk Berhasil ditambahkan!";
    } else{
        echo "ERROR: ". $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
