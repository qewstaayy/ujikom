<?php 

require '../config.php';

//Masukan data admin 
$username = 'admin';
$password = password_hash("admin4321", PASSWORD_DEFAULT); 
$role = 'admin';

//Cek apakah admin sudah ada atau belum 
$check = $conn->prepare("SELECT id FROM users WHERE username = ?");
$check->bind_param("s", $username);
$check->execute();
$check->store_result();

if($check->num_rows == 0){
    ///Tambahkan admin jika belum adaa 
    $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES(?,?,?)");
    $stmt->bind_param("sss", $username, $password, $role);

    if($stmt->execute()) {
        echo "Admin berhasil ditambahkan!";
    } else{
        echo "Gagal menambahkan admin!";
    }

    $stmt->close();
}else{
    echo "Admin sudah ada di database!";
}

$check->close();
$conn->close();

?>