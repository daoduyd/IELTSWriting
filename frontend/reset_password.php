<?php
// reset_password.php
require 'config.php';  // Kết nối cơ sở dữ liệu

if (isset($_POST['reset'])) {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password === $confirm_password) {
        // Mã hóa mật khẩu mới
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $_SESSION['reset_email'] = $email;  // Giả sử người dùng đã nhập email cần khôi phục
        // Lưu mật khẩu mới vào cơ sở dữ liệu (ví dụ: qua email hoặc ID người dùng đã lưu trong session)
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
        if ($stmt->execute([$hashed_password, $email])) {
            $success_message = "Thiết lập mật khẩu thành công!";
        } else {
            $error_message = "Đã xảy ra lỗi khi thiết lập mật khẩu.";
        }
    } else {
        $error_message = "Vui lòng nhập lại mật khẩu!";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Khôi phục mật khẩu</title>
    <style>
    body {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
        font-family: Arial, sans-serif;
        background-color: #f0f0f0;
    }
    .reset-container {
        background-color: white;
        padding: 40px;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.1);
        text-align: center;
        max-width: 500px; /* Tăng kích thước khung để đồng bộ với login/register */
        width: 100%;
    }
    h2 {
        color: #333;
        margin-bottom: 25px;
        font-size: 24px; /* Tăng kích thước chữ */
    }
    input[type="password"], button {
        width: 100%;
        padding: 15px;
        margin: 12px 0;
        border: 1px solid #ddd;
        border-radius: 6px;
        box-sizing: border-box;
        font-size: 16px; /* Tăng kích thước chữ */
    }
    button {
        background-color: #4CAF50;
        color: white;
        border: none;
        cursor: pointer;
    }
    button:hover {
        background-color: #45a049;
    }
    .message {
        color: green;
        margin-top: 15px;
        font-size: 16px; /* Tăng kích thước chữ */
    }
    .error {
        color: red;
        margin-top: 15px;
        font-size: 16px; /* Tăng kích thước chữ */
    }
  </style>
</head>
<body>
    <div class="reset-container">
        <h2>Khôi phục mật khẩu</h2>
        <form action="reset_password.php" method="POST">
            <input type="password" name="new_password" placeholder="Nhập mật khẩu mới" required><br>
            <input type="password" name="confirm_password" placeholder="Nhập lại mật khẩu" required><br>
            <button type="submit" name="reset">Xác nhận</button>
        </form>
        <?php
        if (isset($success_message)) {
            echo '<div class="message">'.$success_message.'</div>';
            echo '<a href="login.php"><button>Quay lại trang đăng nhập</button></a>';
        }
        if (isset($error_message)) {
            echo '<div class="error">'.$error_message.'</div>';
        }
        ?>
    </div>
</body>
</html>