<?php 

//Koneksikan ke database 

$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'ecommerce';
$conn = new mysqli($host, $user, $pass, $dbname);

if($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

?>