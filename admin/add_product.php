<?php

require '../config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $category_id = $_POST['category_id'];

    //Memastikan apakah folder uploads ada 
    $upload_dir = "uploads/";
    if(!is_dir($upload_dir)){
        mkdir($upload_dir, 0777, true);
    }

    //Uploads gambar dengan validasi 
    $image_name = basename($_FILES['image']['name']);
    $target_file = $upload_dir . $image_name;

    if(move_uploaded_file($_FILES['image']['tmp_name'], $target_file)){
        //Simpan ke dalam database 
        $query = "INSERT INTO products (name, description, price, stock, category_id, image) VALUES (?, ?, ?, ?, ?, ?)";
        echo "Query: " . $query;
        $stmt = $conn->prepare($query);

        if(!$stmt){
            die("Error pada prepare statement: " .$conn->error);
        }

        $stmt->bind_param("ssdiis", $name, $description, $price, $stock, $category_id, $image_name);

        if ($stmt->execute()){
            echo"Produk Berhasil ditambahkan!";
            header("Location: admin.php");
        } else{
            echo "ERROR: ". $stmt->error;
        }

        $stmt->close();
        $conn->close();
    }
}
