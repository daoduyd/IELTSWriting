<?php
// Khởi tạo session nếu chưa được khởi tạo
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Kết nối đến cơ sở dữ liệu
require 'config.php';

// Nhận dữ liệu JSON từ request
$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

// Kiểm tra dữ liệu hợp lệ
if (!$data || !isset($data['guest_id']) || !isset($data['rating'])) {
    echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
    exit;
}

// Lấy dữ liệu từ request
$guest_id = $data['guest_id'];
$rating = intval($data['rating']);
$comment = isset($data['comment']) ? $data['comment'] : '';

// Kiểm tra và tạo bảng guest_feedback nếu chưa tồn tại
try {
    $stmt = $pdo->prepare("SHOW TABLES LIKE 'guest_feedback'");
    $stmt->execute();
    
    if ($stmt->rowCount() == 0) {
        // Tạo bảng nếu chưa tồn tại
        $sql = "CREATE TABLE guest_feedback (
            id INT AUTO_INCREMENT PRIMARY KEY,
            guest_id VARCHAR(100) NOT NULL,
            rating INT NOT NULL,
            comment TEXT,
            feedback_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        $pdo->exec($sql);
    }
    
    // Kiểm tra xem người dùng này đã gửi đánh giá trước đó chưa
    $stmt = $pdo->prepare("SELECT id FROM guest_feedback WHERE guest_id = ?");
    $stmt->execute([$guest_id]);
    
    if ($stmt->rowCount() > 0) {
        // Nếu đã gửi đánh giá trước đó, cập nhật đánh giá mới
        $feedback_id = $stmt->fetch(PDO::FETCH_ASSOC)['id'];
        $stmt = $pdo->prepare("UPDATE guest_feedback SET rating = ?, comment = ?, feedback_time = CURRENT_TIMESTAMP WHERE id = ?");
        $result = $stmt->execute([$rating, $comment, $feedback_id]);
    } else {
        // Nếu chưa gửi đánh giá, thêm đánh giá mới
        $stmt = $pdo->prepare("INSERT INTO guest_feedback (guest_id, rating, comment) VALUES (?, ?, ?)");
        $result = $stmt->execute([$guest_id, $rating, $comment]);
    }
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Đánh giá đã được lưu thành công']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Không thể lưu đánh giá']);
    }
} catch (PDOException $e) {
    // Ghi log lỗi và trả về thông báo lỗi chung
    error_log('PDO Error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Đã xảy ra lỗi khi xử lý đánh giá']);
}
?>