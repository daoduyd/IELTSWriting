<?php
session_start();

// Kiểm tra người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Kết nối đến cơ sở dữ liệu
require 'config.php';

// Lấy danh sách hướng dẫn theo dạng bài Task 1
$stmt = $pdo->prepare("SELECT * FROM lectures WHERE category = 'Writing Task 1' ORDER BY created_at DESC");
$stmt->execute();
$lectures = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <title>Writing Task 1 Lessons - IELTS Writing Practice</title>
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

    .feature-box {
      padding: 30px 20px;
      border-radius: 10px;
      margin-bottom: 30px;
      background-color: #f8f9fa;
      transition: all 0.3s ease;
      border-left: 5px solid #3d5a80;
    }

    .feature-box:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }

    .feature-icon {
      width: 60px;
      height: 60px;
      margin-bottom: 20px;
      color: #3d5a80;
    }

    .section-title {
      margin-bottom: 30px;
      position: relative;
      padding-bottom: 10px;
    }

    .section-title:after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      width: 50px;
      height: 3px;
      background-color: #3d5a80;
    }

    .hero-section {
      background-color: #f5f7fa;
      padding: 30px 0;
      border-radius: 15px;
      margin-bottom: 40px;
    }

    .task-badge {
      padding: 5px 10px;
      border-radius: 20px;
      font-size: 12px;
      color: white;
    }

    .task1 {
      background-color: #3d5a80;
    }

    .task2 {
      background-color: #ee6c4d;
    }

    .btn-practice {
      background-color: #3d5a80;
      color: white;
      border: none;
    }

    .btn-practice:hover {
      background-color: #293241;
      color: white;
    }

    .footer {
      margin-top: 50px;
      padding: 20px 0;
      background-color: #293241;
      color: white;
    }

    .roadmap-step {
      position: relative;
      padding-left: 70px;
      margin-bottom: 20px;
    }

    .roadmap-number {
      position: absolute;
      left: 0;
      top: 0;
      width: 50px;
      height: 50px;
      background-color: #3d5a80;
      color: white;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 20px;
      font-weight: bold;
    }

    .user-placeholder {
      font-size: 24px;
      color: white;
      background-color: #718096;
      border-radius: 50%;
      width: 36px;
      height: 36px;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
    }

    /* Styles from lessons_task1_types.php */
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

    .btn-view {
      background-color: #1565c0;
      color: white;
    }

    .btn-view:hover {
      background-color: #0d47a1;
      color: white;
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
        <h1 class="main-title">IELTS Writing Task 1</h1>
        <p class="sub-title">Học cách viết báo cáo mô tả biểu đồ, bảng, sơ đồ hoặc quy trình. Task 1 yêu cầu bạn viết ít nhất 150 từ trong 20 phút, chiếm 1/3 tổng điểm Writing.</p>
      </div>
    </div>
  </div>

  <div class="container">
    <div class="row">
      <div class="col-md-8">
        <h3 class="section-title">Hướng dẫn cách viết các dạng bài Task 1</h3>
        
        <!-- Line Graph -->
        <div class="chart-type-card">
          <div class="chart-type-header">
            <i class="fas fa-chart-line chart-icon"></i> Biểu đồ đường (Line Graph)
          </div>
          <div class="chart-type-body">
            <p>Học cách mô tả biểu đồ đường, xác định xu hướng tăng/giảm và so sánh các đường khác nhau trên biểu đồ. Dạng bài này thường mô tả sự thay đổi theo thời gian và xu hướng phát triển.</p>
            <ul>
              <li>Cách nhận diện các xu hướng chính</li>
              <li>Từ vựng mô tả xu hướng tăng, giảm, dao động</li>
              <li>Cấu trúc câu so sánh dữ liệu</li>
              <li>Cách tổ chức đoạn văn hiệu quả</li>
            </ul>
          </div>
          <div class="chart-type-footer">
            <button type="button" class="btn btn-view" data-toggle="modal" data-target="#difficultyModalLineGraph">Xem hướng dẫn</button>
          </div>
        </div>
        <!-- Modal for Line Graph -->
        <div class="modal fade" id="difficultyModalLineGraph" tabindex="-1" role="dialog" aria-labelledby="difficultyModalLineGraphLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="difficultyModalLineGraphLabel">Chọn cấp độ</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <a href="task1_beginner.php?type=line_graph" class="btn btn-success difficulty-btn beginner">Beginner</a>
                <a href="task1_intermediate.php?type=line_graph" class="btn btn-warning difficulty-btn intermediate">Intermediate</a>
                <a href="task1_advanced.php?type=line_graph" class="btn btn-danger difficulty-btn advanced">Advanced</a>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Bar Chart -->
        <div class="chart-type-card">
          <div class="chart-type-header">
            <i class="fas fa-chart-bar chart-icon"></i> Biểu đồ cột (Bar Chart)
          </div>
          <div class="chart-type-body">
            <p>Hướng dẫn cách mô tả biểu đồ cột đơn và biểu đồ cột nhóm, phân tích sự khác biệt và tương đồng giữa các nhóm dữ liệu.</p>
            <ul>
              <li>Phương pháp phân tích dữ liệu số lượng</li>
              <li>Từ vựng mô tả sự khác biệt và so sánh</li>
              <li>Cách xác định thông tin nổi bật nhất</li>
              <li>Mẫu câu hữu ích cho biểu đồ cột</li>
            </ul>
          </div>
          <div class="chart-type-footer">
            <button type="button" class="btn btn-view" data-toggle="modal" data-target="#difficultyModalBarChart">Xem hướng dẫn</button>
          </div>
        </div>
        <!-- Modal for Bar Chart -->
        <div class="modal fade" id="difficultyModalBarChart" tabindex="-1" role="dialog" aria-labelledby="difficultyModalBarChartLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="difficultyModalBarChartLabel">Chọn cấp độ</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <a href="task1_beginner.php?type=bar_chart" class="btn btn-success difficulty-btn beginner">Beginner</a>
                <a href="task1_intermediate.php?type=bar_chart" class="btn btn-warning difficulty-btn intermediate">Intermediate</a>
                <a href="task1_advanced.php?type=bar_chart" class="btn btn-danger difficulty-btn advanced">Advanced</a>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Pie Chart -->
        <div class="chart-type-card">
          <div class="chart-type-header">
            <i class="fas fa-chart-pie chart-icon"></i> Biểu đồ tròn (Pie Chart)
          </div>
          <div class="chart-type-body">
            <p>Học cách mô tả biểu đồ tròn, biểu diễn tỷ lệ phần trăm và phân bổ. Dạng bài này thường yêu cầu so sánh tỷ lệ giữa các thành phần hoặc sự thay đổi tỷ lệ theo thời gian.</p>
            <ul>
              <li>Từ vựng mô tả tỷ lệ và phân bổ</li>
              <li>Cấu trúc câu cho biểu đồ tròn</li>
              <li>Cách so sánh hai hoặc nhiều biểu đồ tròn</li>
              <li>Chiến lược tổ chức bài viết</li>
            </ul>
          </div>
          <div class="chart-type-footer">
            <button type="button" class="btn btn-view" data-toggle="modal" data-target="#difficultyModalPieChart">Xem hướng dẫn</button>
          </div>
        </div>
        <!-- Modal for Pie Chart -->
        <div class="modal fade" id="difficultyModalPieChart" tabindex="-1" role="dialog" aria-labelledby="difficultyModalPieChartLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="difficultyModalPieChartLabel">Chọn cấp độ</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <a href="task1_beginner.php?type=pie_chart" class="btn btn-success difficulty-btn beginner">Beginner</a>
                <a href="task1_intermediate.php?type=pie_chart" class="btn btn-warning difficulty-btn intermediate">Intermediate</a>
                <a href="task1_advanced.php?type=pie_chart" class="btn btn-danger difficulty-btn advanced">Advanced</a>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Combined Charts -->
        <div class="chart-type-card">
          <div class="chart-type-header">
            <i class="fas fa-chart-area chart-icon"></i> Biểu đồ kết hợp (Combined Charts)
          </div>
          <div class="chart-type-body">
            <p>Hướng dẫn cách xử lý dạng bài kết hợp nhiều loại biểu đồ, như đường và cột, cột và tròn. Dạng bài này đòi hỏi khả năng tích hợp thông tin từ nhiều nguồn khác nhau.</p>
            <ul>
              <li>Cách tìm mối liên hệ giữa các biểu đồ</li>
              <li>Phương pháp tổ chức thông tin</li>
              <li>Từ vựng liên kết giữa các phần</li>
              <li>Bài mẫu và phân tích</li>
            </ul>
          </div>
          <div class="chart-type-footer">
            <button type="button" class="btn btn-view" data-toggle="modal" data-target="#difficultyModalCombinedCharts">Xem hướng dẫn</button>
          </div>
        </div>
        <!-- Modal for Combined Charts -->
        <div class="modal fade" id="difficultyModalCombinedCharts" tabindex="-1" role="dialog" aria-labelledby="difficultyModalCombinedChartsLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="difficultyModalCombinedChartsLabel">Chọn cấp độ</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <a href="task1_beginner.php?type=combined_charts" class="btn btn-success difficulty-btn beginner">Beginner</a>
                <a href="task1_intermediate.php?type=combined_charts" class="btn btn-warning difficulty-btn intermediate">Intermediate</a>
                <a href="task1_advanced.php?type=combined_charts" class="btn btn-danger difficulty-btn advanced">Advanced</a>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Table -->
        <div class="chart-type-card">
          <div class="chart-type-header">
            <i class="fas fa-table chart-icon"></i> Bảng số liệu (Table)
          </div>
          <div class="chart-type-body">
            <p>Học cách phân tích và mô tả bảng số liệu, xác định xu hướng và điểm nổi bật từ dữ liệu thô. Dạng bài này yêu cầu khả năng tổng hợp và nhóm thông tin.</p>
            <ul>
              <li>Cách đọc và hiểu bảng số liệu</li>
              <li>Phương pháp nhóm thông tin liên quan</li>
              <li>Từ vựng mô tả dữ liệu số</li>
              <li>Cấu trúc bài viết cho bảng dữ liệu</li>
            </ul>
          </div>
          <div class="chart-type-footer">
            <button type="button" class="btn btn-view" data-toggle="modal" data-target="#difficultyModalTable">Xem hướng dẫn</button>
          </div>
        </div>
        <!-- Modal for Table -->
        <div class="modal fade" id="difficultyModalTable" tabindex="-1" role="dialog" aria-labelledby="difficultyModalTableLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="difficultyModalTableLabel">Chọn cấp độ</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <a href="task1_beginner.php?type=table_data" class="btn btn-success difficulty-btn beginner">Beginner</a>
                <a href="task1_intermediate.php?type=table_data" class="btn btn-warning difficulty-btn intermediate">Intermediate</a>
                <a href="task1_advanced.php?type=table_data" class="btn btn-danger difficulty-btn advanced">Advanced</a>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Process -->
        <div class="chart-type-card">
          <div class="chart-type-header">
            <i class="fas fa-project-diagram chart-icon"></i> Sơ đồ quy trình (Process)
          </div>
          <div class="chart-type-body">
            <p>Hướng dẫn cách mô tả sơ đồ quy trình, chu trình hoặc vòng đời. Dạng bài này yêu cầu trình bày các bước theo trình tự thời gian hoặc logic.</p>
            <ul>
              <li>Cách xác định cấu trúc của quy trình</li>
              <li>Từ vựng cho quy trình tự nhiên và nhân tạo</li>
              <li>Liên từ và từ nối thể hiện trình tự</li>
              <li>Cách sử dụng thì và thể bị động</li>
            </ul>
          </div>
          <div class="chart-type-footer">
            <button type="button" class="btn btn-view" data-toggle="modal" data-target="#difficultyModalProcess">Xem hướng dẫn</button>
          </div>
        </div>
        <!-- Modal for Process -->
        <div class="modal fade" id="difficultyModalProcess" tabindex="-1" role="dialog" aria-labelledby="difficultyModalProcessLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="difficultyModalProcessLabel">Chọn cấp độ</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <a href="task1_beginner.php?type=process_diagram" class="btn btn-success difficulty-btn beginner">Beginner</a>
                <a href="task1_intermediate.php?type=process_diagram" class="btn btn-warning difficulty-btn intermediate">Intermediate</a>
                <a href="task1_advanced.php?type=process_diagram" class="btn btn-danger difficulty-btn advanced">Advanced</a>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Map -->
        <div class="chart-type-card">
          <div class="chart-type-header">
            <i class="fas fa-map chart-icon"></i> Bản đồ (Map)
          </div>
          <div class="chart-type-body">
            <p>Học cách mô tả bản đồ thể hiện sự thay đổi địa lý, đô thị hoặc môi trường. Dạng bài này thường yêu cầu so sánh hai bản đồ ở các thời điểm khác nhau.</p>
            <ul>
              <li>Từ vựng mô tả vị trí và hướng</li>
              <li>Cách mô tả sự thay đổi theo thời gian</li>
              <li>Cấu trúc câu so sánh đối chiếu</li>
              <li>Phương pháp tổ chức bài viết</li>
            </ul>
          </div>
          <div class="chart-type-footer">
            <button type="button" class="btn btn-view" data-toggle="modal" data-target="#difficultyModalMap">Xem hướng dẫn</button>
          </div>
        </div>
        <!-- Modal for Map -->
        <div class="modal fade" id="difficultyModalMap" tabindex="-1" role="dialog" aria-labelledby="difficultyModalMapLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="difficultyModalMapLabel">Chọn cấp độ</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <a href="task1_beginner.php?type=map" class="btn btn-success difficulty-btn beginner">Beginner</a>
                <a href="task1_intermediate.php?type=map" class="btn btn-warning difficulty-btn intermediate">Intermediate</a>
                <a href="task1_advanced.php?type=map" class="btn btn-danger difficulty-btn advanced">Advanced</a>
              </div>
            </div>
          </div>
        </div>
      </div>
      
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
            <h5>Các dạng bài thường gặp</h5>
          </div>
          <div class="card-body">
            <ul class="list-group list-group-flush">
              <li class="list-group-item">Biểu đồ đường (Line graph)</li>
              <li class="list-group-item">Biểu đồ cột (Bar chart)</li>
              <li class="list-group-item">Biểu đồ tròn (Pie chart)</li>
              <li class="list-group-item">Bảng số liệu (Table)</li>
              <li class="list-group-item">Sơ đồ quy trình (Process)</li>
              <li class="list-group-item">Bản đồ (Map)</li>
            </ul>
          </div>
        </div>
        
        <div class="card mt-4">
          <div class="card-header">
            <h5>Mẹo làm bài Task 1</h5>
          </div>
          <div class="card-body">
            <ol>
              <li>Phân tích biểu đồ/bảng kỹ trước khi viết</li>
              <li>Xác định xu hướng chính và số liệu nổi bật</li>
              <li>Sử dụng ngôn ngữ đa dạng để mô tả số liệu</li>
              <li>Chia bài viết thành 3-4 đoạn rõ ràng</li>
              <li>Không đưa ý kiến cá nhân vào bài viết</li>
              <li>Kiểm tra lỗi ngữ pháp và chính tả</li>
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
                <a href="vocabulary_task1.php">Từ vựng Task 1 theo chủ đề</a>
              </li>
              <li class="list-group-item">
                <a href="common_mistakes_task1.php">Lỗi thường gặp trong Task 1</a>
              </li>
              <li class="list-group-item">
                <a href="time_management_task1.php">Quản lý thời gian trong bài thi</a>
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
      // Xử lý dropdown submenu khi nhấp
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