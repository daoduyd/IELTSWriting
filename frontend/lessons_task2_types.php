<?php
session_start();
// Kiểm tra người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
// Kết nối đến cơ sở dữ liệu
require 'config.php';

// Lấy danh sách bài học cho các dạng bài Task 2
$categories = [
    'agree_disagree' => 'Agree/Disagree',
    'discussion' => 'Discussion',
    'advantages_disadvantages' => 'Advantages/Disadvantages',
    'problems_solutions' => 'Problems/Solutions',
    'two_part' => 'Two-part Questions',
    'opinion' => 'Opinion Questions'
];

// Tạo mảng để lưu trữ bài học theo từng loại
$lessons_by_type = [];
foreach ($categories as $key => $name) {
    $stmt = $pdo->prepare("SELECT * FROM lectures WHERE category = 'Writing Task 2' AND subtype = ? ORDER BY created_at DESC");
    $stmt->execute([$key]);
    $lessons_by_type[$key] = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <title>Writing Task 2 Lessons - IELTS Writing Practice</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
  <style>
    /* Thiết kế cho thanh navigation */
    .navbar {
      background-color: white;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      padding: 10px 0;
    }

    /* Thiết kế logo */
    .navbar-brand {
      font-weight: bold;
      font-size: 24px;
    }

    .navbar-brand span:first-child {
      color: #e74c3c; /* Màu đỏ cho "IELTS" */
    }

    .navbar-brand span.blue {
      color: #1565c0; /* Màu xanh cho "Writing Pro" */
    }

    /* Thiết kế các mục menu */
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

    /* Thiết kế dropdown menu */
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

    /* Thiết kế dropdown submenu */
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

    /* Điều chỉnh để menu con hiển thị khi nhấp trên mobile */
    @media (max-width: 991px) {
      .dropdown-submenu .dropdown-menu {
        position: static;
        left: 0;
        box-shadow: none;
        border: none;
        padding-left: 20px;
      }
    }

    /* Thiết kế avatar user */
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

    /* Thiết kế cờ ngôn ngữ */
    .vn-flag {
      height: 20px;
      margin-right: 15px;
      cursor: pointer;
    }

    /* Thiết kế dropdown user */
    .user-account .dropdown-menu {
      right: 0;
      left: auto;
      min-width: 200px;
    }

    body {
      background-color: #f8f9fa;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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

    .task2 {
      background-color: #e8f5e9;
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

    .lesson-description {
      color: #666;
      margin-bottom: 15px;
    }

    .lesson-meta {
      font-size: 0.9rem;
      color: #888;
    }

    .chart-icon {
      margin-right: 10px;
      color: #1565c0;
      font-size: 1.2rem;
    }

    .chart-type-card {
      margin-bottom: 20px;
      border-radius: 10px;
      overflow: hidden;
      transition: all 0.3s ease;
    }

    .chart-type-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }

    .chart-type-header {
      padding: 15px;
      background-color: #1565c0;
      color: white;
      font-weight: 600;
    }

    .chart-type-body {
      padding: 20px;
      background-color: white;
    }

    .chart-type-footer {
      padding: 15px;
      background-color: #f8f9fa;
      border-top: 1px solid #eee;
    }

    /* Modal styles for difficulty selection */
    .modal-content {
      border-radius: 10px;
    }

    .modal-header {
      background-color: #1565c0;
      color: white;
      border-top-left-radius: 10px;
      border-top-right-radius: 10px;
    }

    .modal-title {
      font-weight: bold;
    }

    .modal-body {
      padding: 20px;
    }

    .difficulty-btn {
      width: 100%;
      margin-bottom: 10px;
      padding: 10px;
      font-size: 16px;
      transition: all 0.3s ease;
    }

    .difficulty-btn.beginner {
      background-color: #28a745;
      border-color: #28a745;
    }

    .difficulty-btn.beginner:hover {
      background-color: #218838;
      border-color: #1e7e34;
    }

    .difficulty-btn.intermediate {
      background-color: #ffc107;
      border-color: #ffc107;
      color: #212529;
    }

    .difficulty-btn.intermediate:hover {
      background-color: #e0a800;
      border-color: #d39e00;
    }

    .difficulty-btn.advanced {
      background-color: #dc3545;
      border-color: #dc3545;
    }

    .difficulty-btn.advanced:hover {
      background-color: #c82333;
      border-color: #bd2130;
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
                <a class="dropdown-item dropdown-toggle" href="#" id="task1Dropdown" data-toggle="dropdown">Writing Task 1</a>
                <div class="dropdown-menu">
                  <a class="dropdown-item" href="lessons_task1_types.php">Các dạng bài</a>
                  <a class="dropdown-item" href="lessons_task1_topics.php">Chủ đề</a>
                </div>
              </div>
              <div class="dropdown-submenu">
                <a class="dropdown-item dropdown-toggle" href="#" id="task2Dropdown" data-toggle="dropdown">Writing Task 2</a>
                <div class="dropdown-menu">
                  <a class="dropdown-item active" href="lessons_task2_types.php">Các dạng bài</a>
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
        <h1 class="main-title">IELTS Writing Task 2</h1>
        <p class="sub-title">Học cách viết bài luận về các vấn đề xã hội, giáo dục, môi trường, công nghệ và nhiều chủ đề khác. Task 2 yêu cầu bạn viết ít nhất 250 từ trong 40 phút, chiếm 2/3 tổng điểm Writing.</p>
      </div>
    </div>
  </div>

  <div class="container">
    <div class="row">
      <div class="col-md-8">
        <h3 class="section-title">Hướng dẫn cách viết các dạng bài Task 2</h3>
        
        <!-- Agree/Disagree -->
        <div class="chart-type-card">
          <div class="chart-type-header">
            <i class="fas fa-check-circle chart-icon"></i> Agree/Disagree
          </div>
          <div class="chart-type-body">
            <p>Học cách viết bài luận bày tỏ quan điểm đồng ý hoặc không đồng ý với một ý kiến được đưa ra trong đề bài.</p>
            <ul>
              <li>Cách xác định quan điểm cá nhân</li>
              <li>Từ vựng thể hiện sự đồng ý/không đồng ý</li>
              <li>Cấu trúc bài viết rõ ràng</li>
              <li>Cách phát triển luận điểm thuyết phục</li>
            </ul>
          </div>
          <div class="chart-type-footer">
            <button type="button" class="btn btn-view" data-toggle="modal" data-target="#difficultyModalAgreeDisagree">Xem hướng dẫn</button>
          </div>
        </div>
        <!-- Modal for Agree/Disagree -->
        <div class="modal fade" id="difficultyModalAgreeDisagree" tabindex="-1" role="dialog" aria-labelledby="difficultyModalAgreeDisagreeLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="difficultyModalAgreeDisagreeLabel">Chọn cấp độ</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span>
                </button>
              </div>
              <div class="modal-body">
                <a href="task2_beginner.php?type=agree_disagree" class="btn btn-success difficulty-btn beginner">Beginner</a>
                <a href="task2_intermediate.php?type=agree_disagree" class="btn btn-warning difficulty-btn intermediate">Intermediate</a>
                <a href="task2_advanced.php?type=agree_disagree" class="btn btn-danger difficulty-btn advanced">Advanced</a>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Discussion -->
        <div class="chart-type-card">
          <div class="chart-type-header">
            <i class="fas fa-comments chart-icon"></i> Discussion
          </div>
          <div class="chart-type-body">
            <p>Hướng dẫn cách thảo luận cả hai mặt của một vấn đề và đưa ra ý kiến cá nhân ở phần kết luận.</p>
            <ul>
              <li>Cách trình bày quan điểm trung lập</li>
              <li>Từ vựng thảo luận đa chiều</li>
              <li>Phương pháp tổ chức đoạn văn</li>
              <li>Cách kết hợp ý kiến cá nhân</li>
            </ul>
          </div>
          <div class="chart-type-footer">
            <button type="button" class="btn btn-view" data-toggle="modal" data-target="#difficultyModalDiscussion">Xem hướng dẫn</button>
          </div>
        </div>
        <!-- Modal for Discussion -->
        <div class="modal fade" id="difficultyModalDiscussion" tabindex="-1" role="dialog" aria-labelledby="difficultyModalDiscussionLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="difficultyModalDiscussionLabel">Chọn cấp độ</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span>
                </button>
              </div>
              <div class="modal-body">
                <a href="task2_beginner.php?type=discussion" class="btn btn-success difficulty-btn beginner">Beginner</a>
                <a href="task2_intermediate.php?type=discussion" class="btn btn-warning difficulty-btn intermediate">Intermediate</a>
                <a href="task2_advanced.php?type=discussion" class="btn btn-danger difficulty-btn advanced">Advanced</a>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Advantages/Disadvantages -->
        <div class="chart-type-card">
          <div class="chart-type-header">
            <i class="fas fa-balance-scale chart-icon"></i> Advantages/Disadvantages
          </div>
          <div class="chart-type-body">
            <p>Học cách phân tích lợi ích và bất lợi của một vấn đề hoặc tình huống được đưa ra trong đề bài.</p>
            <ul>
              <li>Từ vựng mô tả lợi ích và bất lợi</li>
              <li>Cấu trúc đoạn văn cân bằng</li>
              <li>Cách đưa ví dụ minh họa</li>
              <li>Chiến lược phát triển ý</li>
            </ul>
          </div>
          <div class="chart-type-footer">
            <button type="button" class="btn btn-view" data-toggle="modal" data-target="#difficultyModalAdvantagesDisadvantages">Xem hướng dẫn</button>
          </div>
        </div>
        <!-- Modal for Advantages/Disadvantages -->
        <div class="modal fade" id="difficultyModalAdvantagesDisadvantages" tabindex="-1" role="dialog" aria-labelledby="difficultyModalAdvantagesDisadvantagesLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="difficultyModalAdvantagesDisadvantagesLabel">Chọn cấp độ</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span>
                </button>
              </div>
              <div class="modal-body">
                <a href="task2_beginner.php?type=advantages_disadvantages" class="btn btn-success difficulty-btn beginner">Beginner</a>
                <a href="task2_intermediate.php?type=advantages_disadvantages" class="btn btn-warning difficulty-btn intermediate">Intermediate</a>
                <a href="task2_advanced.php?type=advantages_disadvantages" class="btn btn-danger difficulty-btn advanced">Advanced</a>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Problems/Solutions -->
        <div class="chart-type-card">
          <div class="chart-type-header">
            <i class="fas fa-tools chart-icon"></i> Problems/Solutions
          </div>
          <div class="chart-type-body">
            <p>Hướng dẫn cách xác định vấn đề trong đề bài và đề xuất các giải pháp khả thi để giải quyết chúng.</p>
            <ul>
              <li>Cách phân tích vấn đề cụ thể</li>
              <li>Từ vựng mô tả nguyên nhân và giải pháp</li>
              <li>Cấu trúc bài viết logic</li>
              <li>Cách đánh giá tính khả thi của giải pháp</li>
            </ul>
          </div>
          <div class="chart-type-footer">
            <button type="button" class="btn btn-view" data-toggle="modal" data-target="#difficultyModalProblemsSolutions">Xem hướng dẫn</button>
          </div>
        </div>
        <!-- Modal for Problems/Solutions -->
        <div class="modal fade" id="difficultyModalProblemsSolutions" tabindex="-1" role="dialog" aria-labelledby="difficultyModalProblemsSolutionsLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="difficultyModalProblemsSolutionsLabel">Chọn cấp độ</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span>
                </button>
              </div>
              <div class="modal-body">
                <a href="task2_beginner.php?type=problems_solutions" class="btn btn-success difficulty-btn beginner">Beginner</a>
                <a href="task2_intermediate.php?type=problems_solutions" class="btn btn-warning difficulty-btn intermediate">Intermediate</a>
                <a href="task2_advanced.php?type=problems_solutions" class="btn btn-danger difficulty-btn advanced">Advanced</a>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Two-part Questions -->
        <div class="chart-type-card">
          <div class="chart-type-header">
            <i class="fas fa-question-circle chart-icon"></i> Two-part Questions
          </div>
          <div class="chart-type-body">
            <p>Học cách trả lời hai câu hỏi liên quan trong cùng một bài luận, đảm bảo trả lời đầy đủ cả hai phần.</p>
            <ul>
              <li>Cách phân tích hai câu hỏi</li>
              <li>Từ vựng liên kết hai phần</li>
              <li>Phương pháp phân bổ ý hợp lý</li>
              <li>Cấu trúc bài viết hiệu quả</li>
            </ul>
          </div>
          <div class="chart-type-footer">
            <button type="button" class="btn btn-view" data-toggle="modal" data-target="#difficultyModalTwoPart">Xem hướng dẫn</button>
          </div>
        </div>
        <!-- Modal for Two-part Questions -->
        <div class="modal fade" id="difficultyModalTwoPart" tabindex="-1" role="dialog" aria-labelledby="difficultyModalTwoPartLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="difficultyModalTwoPartLabel">Chọn cấp độ</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span>
                </button>
              </div>
              <div class="modal-body">
                <a href="task2_beginner.php?type=two_part" class="btn btn-success difficulty-btn beginner">Beginner</a>
                <a href="task2_intermediate.php?type=two_part" class="btn btn-warning difficulty-btn intermediate">Intermediate</a>
                <a href="task2_advanced.php?type=two_part" class="btn btn-danger difficulty-btn advanced">Advanced</a>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Opinion Questions -->
        <div class="chart-type-card">
          <div class="chart-type-header">
            <i class="fas fa-comment-alt chart-icon"></i> Opinion Questions
          </div>
          <div class="chart-type-body">
            <p>Hướng dẫn cách trình bày và bảo vệ ý kiến cá nhân về một chủ đề cụ thể trong bài luận.</p>
            <ul>
              <li>Cách xây dựng luận điểm mạnh mẽ</li>
              <li>Từ vựng thể hiện quan điểm</li>
              <li>Cách đưa dẫn chứng thuyết phục</li>
              <li>Chiến lược tổ chức bài viết</li>
            </ul>
          </div>
          <div class="chart-type-footer">
            <button type="button" class="btn btn-view" data-toggle="modal" data-target="#difficultyModalOpinion">Xem hướng dẫn</button>
          </div>
        </div>
        <!-- Modal for Opinion Questions -->
        <div class="modal fade" id="difficultyModalOpinion" tabindex="-1" role="dialog" aria-labelledby="difficultyModalOpinionLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="difficultyModalOpinionLabel">Chọn cấp độ</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span>
                </button>
              </div>
              <div class="modal-body">
                <a href="task2_beginner.php?type=opinion" class="btn btn-success difficulty-btn beginner">Beginner</a>
                <a href="task2_intermediate.php?type=opinion" class="btn btn-warning difficulty-btn intermediate">Intermediate</a>
                <a href="task2_advanced.php?type=opinion" class="btn btn-danger difficulty-btn advanced">Advanced</a>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="col-md-4">
        <div class="card">
          <div class="card-header">
            <h5>Thông tin Task 2</h5>
          </div>
          <div class="card-body">
            <ul class="list-group list-group-flush">
              <li class="list-group-item d-flex justify-content-between align-items-center">
                Thời gian làm bài
                <span class="badge badge-primary badge-pill">40 phút</span>
              </li>
              <li class="list-group-item d-flex justify-content-between align-items-center">
                Số từ tối thiểu
                <span class="badge badge-primary badge-pill">250 từ</span>
              </li>
              <li class="list-group-item d-flex justify-content-between align-items-center">
                Điểm số
                <span class="badge badge-primary badge-pill">2/3 tổng điểm</span>
              </li>
            </ul>
          </div>
        </div>
        
        <div class="card mt-4">
          <div class="card-header">
            <h5>Các dạng bài thường gặp</h5>
          </div>
          <div class="card-body">
            <ul class="list-group list-group-flush">
              <li class="list-group-item">Agree/Disagree</li>
              <li class="list-group-item">Discussion</li>
              <li class="list-group-item">Advantages/Disadvantages</li>
              <li class="list-group-item">Problems/Solutions</li>
              <li class="list-group-item">Two-part Questions</li>
              <li class="list-group-item">Opinion Questions</li>
            </ul>
          </div>
        </div>
        
        <div class="card mt-4">
          <div class="card-header">
            <h5>Mẹo làm bài Task 2</h5>
          </div>
          <div class="card-body">
            <ol>
              <li>Phân tích đề bài kỹ càng</li>
              <li>Lập dàn ý trước khi viết</li>
              <li>Sử dụng từ liên kết phù hợp</li>
              <li>Phát triển ý đủ sâu và rộng</li>
              <li>Kiểm tra lỗi ngữ pháp và từ vựng</li>
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
                <a href="vocabulary_task2.php">Từ vựng Task 2 theo chủ đề</a>
              </li>
              <li class="list-group-item">
                <a href="common_mistakes_task2.php">Lỗi thường gặp trong Task 2</a>
              </li>
              <li class="list-group-item">
                <a href="time_management_task2.php">Quản lý thời gian trong bài thi</a>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <div class="footer">
    <div class="container">
      <div class="row">
        <div class="col-md-12 text-center">
          <p>&copy; <?php echo date('Y'); ?> IELTS Writing Practice. Tất cả quyền được bảo lưu.</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Load JavaScript files -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <script>
    $(document).ready(function(){
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