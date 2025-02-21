<?php

require '../config.php';

// Cek koneksi database
if (!$conn) {
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}

// Data admin
$username = 'elin';
$email = 'admin@example.com'; // Email wajib agar bisa login
$password = password_hash("elin1234", PASSWORD_DEFAULT);
$role = 'admin';

// Cek apakah tabel users memiliki kolom role
$check_columns = $conn->query("SHOW COLUMNS FROM users LIKE 'role'");
if ($check_columns->num_rows == 0) {
    echo "❌ Kolom 'role' belum ada! Tambahkan dengan query: <br>";
    echo "<code>ALTER TABLE users ADD COLUMN role VARCHAR(20) NOT NULL DEFAULT 'user';</code>";
    exit();
}

// Cek apakah admin sudah ada berdasarkan username atau email
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
