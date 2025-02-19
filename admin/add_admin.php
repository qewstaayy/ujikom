<?php

require '../config.php';

// Data admin
$username = 'admin2';
$email = 'admin@example.com'; // Tambahkan email agar bisa login
$password = password_hash("admin4321", PASSWORD_DEFAULT);
$role = 'admin';

// Cek apakah admin sudah ada
$check = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
$check->bind_param("ss", $username, $email);
$check->execute();
$check->store_result();

if ($check->num_rows == 0) {
    // Tambahkan admin jika belum ada
    $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $email, $password, $role);

    if ($stmt->execute()) {
        echo "✅ Admin berhasil ditambahkan!";
    } else {
        echo "❌ Gagal menambahkan admin: " . $stmt->error;
    }
    $stmt->close();
} else {
    echo "⚠️ Admin sudah ada di database! Jika ingin mengubah password, update manual di database.";
}

$check->close();
$conn->close();
