<?php
// register.php
require 'config.php';  // Kết nối cơ sở dữ liệu

if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Kiểm tra xem email đã tồn tại chưa
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->rowCount() > 0) {
        echo "<p style='color: red; text-align: center;'>Email đã tồn tại!</p>";
    } else {
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        if ($stmt->execute([$username, $email, $password])) {
            header("Location: index.php");
        } else {
            echo "<p style='color: red; text-align: center;'>Đã xảy ra lỗi!</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng ký</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
        }
        .register-container {
            background-color: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 500px; /* Tăng kích thước khung */
            width: 100%;
        }
        h2 {
            color: #444;
            margin-bottom: 25px;
            font-size: 24px; /* Tăng kích thước chữ */
        }
        input[type="text"], input[type="email"], input[type="password"] {
            width: 100%;
            padding: 15px;
            margin: 12px 0;
            border: 1px solid #ddd;
            border-radius: 6px;
            box-sizing: border-box;
            background-color: #f9f9f9;
            font-size: 16px; /* Tăng kích thước chữ */
        }
        input[type="text"]:focus, input[type="email"]:focus, input[type="password"]:focus {
            border-color: #4CAF50;
            outline: none;
            background-color: white;
        }
        button {
            width: 100%;
            padding: 15px;
            background-color: #ff6b6b;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 18px; /* Tăng kích thước chữ */
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #ff5252;
        }
        .additional-options {
            margin-top: 15px;
            font-size: 16px; /* Tăng kích thước chữ */
        }
        .additional-options a {
            color: #007bff;
            text-decoration: none;
        }
        .additional-options a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h2>Đăng ký tài khoản</h2>
        <form action="register.php" method="POST">
            <input type="text" name="username" placeholder="Tên người dùng" required><br>
            <input type="email" name="email" placeholder="Nhập Email" required><br>
            <input type="password" name="password" placeholder="Nhập mật khẩu" required><br>
            <button type="submit" name="register">Đăng ký</button>
        </form>
        <div class="additional-options">
            <a href="login.php">Đã có tài khoản? Đăng nhập</a>
        </div>
    </div>
</body>
</html>