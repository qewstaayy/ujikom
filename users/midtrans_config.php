<?php
require_once '../vendor/autoload.php'; // Jika menggunakan Composer

// Konfigurasi Midtrans
\Midtrans\Config::$serverKey = 'SB-Mid-server--9nOsGKNOxnM3eLT6_axqtCa';
\Midtrans\Config::$isProduction = false; // false untuk sandbox, true untuk production
\Midtrans\Config::$isSanitized = true;
\Midtrans\Config::$is3ds = true;
