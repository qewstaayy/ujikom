<?php 

session_start();

if(isset($_GET['id'])){
    $id = $_GET['id'];

    //hapus produk dari keranjang 
    if (isset($_SESSION['cart'][$id])){
        unset($_SESSION['id'][$id]);
    }
}

header("Location: cart.php");
exit();

?>