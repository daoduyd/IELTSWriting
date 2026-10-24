<?php
// config.php
$host = 'localhost';  // Địa chỉ máy chủ cơ sở dữ liệu
$db = 'ielts_writing';  // Tên cơ sở dữ liệu
$user = 'root';  // Tên đăng nhập MySQL
$pass = '';  // Mật khẩu MySQL

// Thiết lập kết nối PDO
$dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Kết nối cơ sở dữ liệu thất bại: " . $e->getMessage());
}

