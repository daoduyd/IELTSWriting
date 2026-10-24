<?php
session_start();
// Kiểm tra người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Kết nối đến cơ sở dữ liệu
require 'config.php';

// Lấy tham số topic từ URL
$topic = isset($_GET['topic']) ? $_GET['topic'] : '';
$valid_topics = [
    'population' => 'Dân số (Population)',
    'economy' => 'Kinh tế (Economy)',
    'energy' => 'Năng lượng (Energy)',
    'urbanization_housing' => 'Đô thị hóa và Nhà ở (Urbanization and Housing)',
    'process_diagrams' => 'Sơ đồ quy trình (Process Diagrams)',
    'maps' => 'Bản đồ (Maps)',
    'education' => 'Giáo dục (Education)',
    'health' => 'Sức khỏe (Health)',
    'employment' => 'Việc làm (Employment)',
    'technology' => 'Công nghệ (Technology)',
    'environment' => 'Môi trường (Environment)',
    'money' => 'Tiền bạc (Money)',
    'food_drinks' => 'Thực phẩm và Đồ uống (Food & Drinks)',
    'sports_leisure' => 'Thể thao và Giải trí (Sports & Leisure)',
    'transport' => 'Giao thông (Transport)',
    'tourism' => 'Du lịch (Tourism)',
    'gender' => 'Giới tính (Gender)',
];

// Kiểm tra xem topic có hợp lệ không
if (!array_key_exists($topic, $valid_topics)) {
    header("Location: lessons_task1_topics.php");
    exit;
}

// Lấy bài giảng từ cơ sở dữ liệu
$lecture = null;
$stmt = $pdo->prepare("SELECT * FROM lectures WHERE category = 'Task 1 Advanced' AND subtype = ?");
$stmt->execute([$topic]);
$lecture = $stmt->fetch(PDO::FETCH_ASSOC);

// Nếu không tìm thấy bài giảng, hiển thị thông báo
if (!$lecture) {
    echo "<div class='container'><p>Không tìm thấy bài giảng cho chủ đề này.</p></div>";
    exit;
}

// Cập nhật lượt truy cập của người dùng vào bài học
$stmt = $pdo->prepare("SELECT id FROM user_progress WHERE user_id = ? AND lecture_id = ?");
$stmt->execute([$_SESSION['user_id'], $lecture['id']]);
if (!$stmt->fetch()) {
    $stmt = $pdo->prepare("INSERT INTO user_progress (user_id, lecture_id) VALUES (?, ?)");
    $stmt->execute([$_SESSION['user_id'], $lecture['id']]);
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <title>Học <?php echo $valid_topics[$topic]; ?> - IELTS Writing Task 1 Advanced</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
  <style>
    body {
      background-color: #f8f9fa;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .header {
      background-color: #1565c0;
      color: white;
      padding: 15px 0;
      margin-bottom: 30px;
    }
    .-breaker {
      margin: 30px 0;
      border-bottom: 2px solid #1565c0;
    }
    .card {
      margin-bottom: 20px;
      border-radius: 10px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      transition: transform 0.3s;
    }
    .card:hover {
      transform: translateY(-5px);
    }
    .card-header {
      background-color: #f8f9fa;
      border-bottom: 1px solid #e9ecef;
      font-weight: bold;
    }
    .task-badge {
      font-size: 14px;
      padding: 5px 10px;
      border-radius: 20px;
    }
    .task1 {
      background-color: #e3f2fd;
      color: #1565c0;
    }
    .btn-view {
      background-color: #1565c0;
      color: white;
    }
    .btn-view:hover {
      background-color: #0d47a1;
      color: white;
    }
    .section-title {
      margin-bottom: 20px;
      padding-bottom: 10px;
      border-bottom: 2px solid #1565c0;
      color: #1565c0;
    }
    .footer {
      margin-top: 50px;
      background-color: #f1f1f1;
      padding: 20px 0;
      color: #666;
    }
    .navbar {
      background-color: white;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      padding: 10px 0;
    }
    .navbar-brand {
      font-weight: bold;
      font-size: 24px;
    }
    .navbar-brand span:first-child {
      color: #e74c3c;
    }
    .navbar-brand span.blue {
      color: #1565c0;
    }
    .navbar-nav .nav-item {
      margin: 0 5px;
    }
    .navbar-nav .nav-link {
      color: #555;
      font-weight: 500;
      padding: 8px 12px;
      transition: all 0.3s ease;
    }
    .navbar-nav .nav-link:hover,
    .navbar-nav .nav-item.active .nav-link {
      color: #3498db;
    }
    .dropdown-menu {
      border: none;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
      padding: 8px 0;
    }
    .dropdown-item {
      padding: 8px 20px;
      color: #555;
      transition: all 0.2s ease;
    }
    .dropdown-item:hover {
      background-color: #f8f9fa;
      color: #3498db;
    }
    .dropdown-submenu {
      position: relative;
    }
    .dropdown-submenu .dropdown-menu {
      top: 0;
      left: 100%;
      margin-top: -1px;
      display: none;
    }
    .dropdown-submenu:hover .dropdown-menu {
      display: block;
    }
    @media (max-width: 991px) {
      .dropdown-submenu .dropdown-menu {
        position: static;
        left: 0;
        box-shadow: none;
        border: none;
        padding-left: 20px;
      }
    }
    .user-placeholder {
      width: 36px;
      height: 36px;
      background-color: #718096;
      color: white;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: bold;
      cursor: pointer;
    }
    .vn-flag {
      height: 20px;
      margin-right: 15px;
      cursor: pointer;
    }
    .user-account .dropdown-menu {
      right: 0;
      left: auto;
      min-width: 200px;
    }
    .hero-section {
      background-color: #f5f7fa;
      padding: 30px 0;
      border-radius: 15px;
      margin-bottom: 40px;
    }
    .main-title {
      font-size: 2.5rem;
      font-weight: bold;
      color: #263238;
      margin-bottom: 1rem;
      text-align: center;
    }
    .sub-title {
      font-size: 1.2rem;
      color: #546e7a;
      margin-bottom: 2rem;
      text-align: center;
      line-height: 1.6;
    }
    .guide-content {
      background-color: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      margin-bottom: 30px;
    }
    .guide-content h2 {
      color: #1565c0;
      border-bottom: 2px solid #1565c0;
      padding-bottom: 10px;
      margin-bottom: 20px;
    }
    .guide-content h3 {
      color: #333;
      margin-top: 20px;
    }
    .guide-content ul {
      list-style-type: disc;
      margin-left: 20px;
    }
    .guide-content ol {
      margin-left: 20px;
    }
    .guide-content table {
      border-collapse: collapse;
      width: 100%;
      margin-bottom: 20px;
    }
    .guide-content th, .guide-content td {
      border: 1px solid #dee2e6;
      padding: 10px;
      text-align: left;
    }
    .guide-content th {
      background-color: #1565c0;
      color: white;
    }
    .guide-content strong {
      font-weight: bold;
    }
    .guide-content span {
      line-height: 1.6;
    }
    .example-box {
      background-color: #f8f9fa;
      padding: 15px;
      border-left: 4px solid #1565c0;
      margin-bottom: 20px;
    }
    .vocabulary-table th, .vocabulary-table td {
      border: 1px solid #dee2e6;
      padding: 10px;
    }
    .vocabulary-table th {
      background-color: #1565c0;
      color: white;
    }
    .notebooklm-chat-button, .lexibot-chat-button {
      position: fixed;
      bottom: 20px;
      right: 20px;
      width: 60px;
      height: 60px;
      background-color: #1565c0;
      color: white;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
      z-index: 1000;
      transition: background-color 0.3s;
    }
    .lexibot-chat-button {
      bottom: 90px;
      background-color: #28a745;
    }
    .notebooklm-chat-button:hover, .lexibot-chat-button:hover {
      background-color: #0d47a1;
    }
    .lexibot-chat-button:hover {
      background-color: #218838;
    }
    .notebooklm-interaction, .lexibot-interaction {
      position: fixed;
      bottom: 160px;
      right: 20px;
      width: 350px;
      background-color: white;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
      display: none;
      z-index: 1000;
    }
    .lexibot-interaction {
      bottom: 230px;
    }
    .notebooklm-interaction.active, .lexibot-interaction.active {
      display: block;
    }
    .notebooklm-interaction-header, .lexibot-interaction-header {
      background-color: #1565c0;
      color: white;
      padding: 10px 15px;
      border-top-left-radius: 10px;
      border-top-right-radius: 10px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .lexibot-interaction-header {
      background-color: #28a745;
    }
    .notebooklm-interaction-header h2, .lexibot-interaction-header h2 {
      font-size: 1.2rem;
      margin: 0;
    }
    .notebooklm-interaction-header .close-chat, .lexibot-interaction-header .close-chat {
      cursor: pointer;
      font-size: 1.2rem;
    }
    .notebooklm-interaction-body, .lexibot-interaction-body {
      padding: 15px;
    }
    .notebooklm-interaction .form-group, .lexibot-interaction .form-group {
      margin-bottom: 15px;
    }
    .notebooklm-interaction textarea, .lexibot-interaction textarea {
      height: 100px;
      resize: none;
    }
    .notebooklm-interaction .btn-view, .lexibot-interaction .btn-view {
      width: 100%;
    }
    .lexibot-interaction .btn-view {
      background-color: #28a745;
    }
    .lexibot-interaction .btn-view:hover {
      background-color: #218838;
    }
    .notebooklm-instruction, .lexibot-instruction {
      background-color: #f8f9fa;
      padding: 10px;
      border-left: 4px solid #1565c0;
      margin-top: 15px;
    }
    .lexibot-instruction {
      border-left: 4px solid #28a745;
    }
    .notebooklm-instruction p, .lexibot-instruction p {
      margin: 0;
    }
  </style>
</head>
<body>
  <!-- Navigation Bar -->
  <nav class="navbar navbar-expand-lg navbar-light">
    <div class="container">
      <a class="navbar-brand" href="index.php">
        <span>IELTS</span><span class="blue">Writing Pro</span>
      </a>
      
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav mr-auto">
          <li class="nav-item">
            <a class="nav-link" href="index.php">Home</a>
          </li>
          
          <li class="nav-item dropdown active">
            <a class="nav-link dropdown-toggle" href="#" id="lessonsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              Lessons
            </a>
            <div class="dropdown-menu" aria-labelledby="lessonsDropdown">
              <div class="dropdown-submenu">
                <a class="dropdown-item active dropdown-toggle" href="#" id="task1Dropdown" data-toggle="dropdown">Writing Task 1</a>
                <div class="dropdown-menu">
                  <a class="dropdown-item" href="lessons_task1_types.php">Các dạng bài</a>
                  <a class="dropdown-item" href="lessons_task1_topics.php">Chủ đề</a>
                </div>
              </div>
              <div class="dropdown-submenu">
                <a class="dropdown-item dropdown-toggle" href="#" id="task2Dropdown" data-toggle="dropdown">Writing Task 2</a>
                <div class="dropdown-menu">
                  <a class="dropdown-item" href="lessons_task2_types.php">Các dạng bài</a>
                  <a class="dropdown-item" href="lessons_task2_topics.php">Chủ đề</a>
                </div>
              </div>
            </div>
          </li>
          
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="practicesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              Practices
            </a>
            <div class="dropdown-menu" aria-labelledby="practicesDropdown">
              <a class="dropdown-item" href="practice_task1.php">Writing Task 1</a>
              <a class="dropdown-item" href="practice_task2.php">Writing Task 2</a>
            </div>
          </li>
          
          <li class="nav-item">
            <a class="nav-link" href="tests.php">Tests</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="grammar.php">Grammar</a>
          </li>
        </ul>
        
        <div class="d-flex align-items-center">
          <div class="ml-3">
            <img src="images/vietnam-flag.png" alt="Tiếng Việt" class="vn-flag">
          </div>
          
          <div class="user-account ml-3 dropdown">
            <a class="nav-link" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <i class="fas fa-user user-placeholder"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
              <div class="dropdown-item text-center">
                <strong>Xin chào, <?php echo $_SESSION['username']; ?>!</strong>
              </div>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="profile.php"><i class="fas fa-user mr-2"></i> Hồ sơ</a>
              <a class="dropdown-item" href="my_essays.php"><i class="fas fa-file-alt mr-2"></i> Bài viết của tôi</a>
              <a class="dropdown-item" href="settings.php"><i class="fas fa-cog mr-2"></i> Cài đặt</a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt mr-2"></i> Đăng xuất</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </nav>

  <!-- Hero Section -->
  <div class="container hero-section">
    <div class="row">
      <div class="col-md-10 offset-md-1">
        <h1 class="main-title"><?php echo htmlspecialchars($lecture['title']); ?></h1>
        <p class="sub-title"><?php echo htmlspecialchars($lecture['description']); ?></p>
      </div>
    </div>
  </div>

  <!-- Main Content -->
  <div class="container">
    <div class="row">
      <div class="col-md-8">
        <div class="guide-content">
          <h2>Hướng dẫn chi tiết chủ đề <?php echo $valid_topics[$topic]; ?> cho cấp độ nâng cao</h2>
          
          <!-- Hiển thị nội dung bài giảng từ CSDL, bỏ htmlspecialchars để render HTML -->
          <?php echo $lecture['content']; ?>

          <!-- Nút thực hành -->
          <a href="practice_task1.php?topic=<?php echo $topic; ?>" class="btn btn-view mt-3">
            <i class="fas fa-pencil-alt"></i> Thực hành viết chủ đề <?php echo $valid_topics[$topic]; ?>
          </a>
        </div>
      </div>

      <!-- Sidebar -->
      <div class="col-md-4">
        <div class="card">
          <div class="card-header">
            <h5>Thông tin Task 1</h5>
          </div>
          <div class="card-body">
            <ul class="list-group list-group-flush">
              <li class="list-group-item d-flex justify-content-between align-items-center">
                Thời gian làm bài
                <span class="badge badge-primary badge-pill">20 phút</span>
              </li>
              <li class="list-group-item d-flex justify-content-between align-items-center">
                Số từ tối thiểu
                <span class="badge badge-primary badge-pill">150 từ</span>
              </li>
              <li class="list-group-item d-flex justify-content-between align-items-center">
                Điểm số
                <span class="badge badge-primary badge-pill">1/3 tổng điểm</span>
              </li>
            </ul>
          </div>
        </div>

        <div class="card mt-4">
          <div class="card-header">
            <h5>Các chủ đề thường gặp</h5>
          </div>
          <div class="card-body">
            <ul class="list-group list-group-flush">
              <?php foreach ($valid_topics as $key => $title): ?>
                <li class="list-group-item">
                  <a href="advanced_task1.php?topic=<?php echo $key; ?>">
                    <?php echo $title; ?>
                  </a>
                </li>
              <?php endforeach; ?>
            </ul>
          </div>
        </div>

        <div class="card mt-4">
          <div class="card-header">
            <h5>Mẹo cho người nâng cao</h5>
          </div>
          <div class="card-body">
            <ol>
              <li>Phân tích sâu và logic</li>
              <li>Sử dụng từ vựng chuyên sâu</li>
              <li>Kết hợp số liệu phức tạp</li>
              <li>Viết đoạn văn mạch lạc, chặt chẽ</li>
              <li>Tối ưu hóa ngữ pháp và phong cách</li>
              <li>Quản lý thời gian hiệu quả</li>
            </ol>
          </div>
        </div>

        <div class="card mt-4">
          <div class="card-header">
            <h5>Tài liệu hỗ trợ</h5>
          </div>
          <div class="card-body">
            <ul class="list-group list-group-flush">
              <li class="list-group-item">
                <a href="vocabulary_task1.php">Từ vựng Task 1 chuyên sâu</a>
              </li>
              <li class="list-group-item">
                <a href="common_mistakes_task1.php">Lỗi thường gặp</a>
              </li>
              <li class="list-group-item">
                <a href="time_management_task1.php">Quản lý thời gian</a>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- NotebookLM Button -->
  <a href="https://notebooklm.google/" target="_blank" class="notebooklm-chat-button" id="notebooklmChatButton">
    <i class="fas fa-book"></i>
  </a>

  <!-- Lexibot Button -->
  <a href="https://lexibot.me/vi/dich-vu/cham-diem-ielts-writing-mien-phi/" target="_blank" class="lexibot-chat-button" id="lexibotChatButton">
    <i class="fas fa-robot"></i>
  </a>

  <!-- Footer -->
  <div class="footer">
    <div class="container">
      <div class="row">
        <div class="col-md-12 text-center">
          <p>© <?php echo date('Y'); ?> IELTS Writing Practice. Tất cả quyền được bảo lưu.</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Load JavaScript files -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <script>
    $(document).ready(function() {
      $('.dropdown-submenu a.dropdown-toggle').on('click', function(e){
        if (!$(this).next().hasClass('show')) {
          $(this).parents('.dropdown-menu').first().find('.show').removeClass('show');
        }
        var $subMenu = $(this).next('.dropdown-menu');
        $subMenu.toggleClass('show');
        $(this).parents('li.nav-item.dropdown.show').on('hidden.bs.dropdown', function(e) {
          $('.dropdown-submenu .show').removeClass('show');
        });
        return false;
      });
    });
  </script>
</body>
</html>