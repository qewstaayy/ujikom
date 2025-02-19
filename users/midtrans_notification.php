<?php
require_once 'midtrans_config.php';

use Midtrans\Transaction;

$rawBody = file_get_contents("php://input");
$json = json_decode($rawBody, true);

if (!$json) {
    http_response_code(400);
    exit();
}

$order_id = $json['order_id'];
$transaction_status = $json['transaction_status'];

// Update status pesanan di database
$status = 'pending';

if ($transaction_status == "settlement") {
    $status = "Lunas";
} elseif ($transaction_status == "cancel" || $transaction_status == "expire") {
    $status = "Dibatalkan";
} elseif ($transaction_status == "deny") {
    $status = "Ditolak";
}

$query = "UPDATE orders SET status = ? WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("si", $status, $order_id);
$stmt->execute();

http_response_code(200);
