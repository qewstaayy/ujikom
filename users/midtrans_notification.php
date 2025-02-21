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

http_response_code(200);
