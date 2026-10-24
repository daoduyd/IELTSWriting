<?php
session_start();
$logged_in = isset($_SESSION['user_id']);
require 'config.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <title>IELTS Writing Task 1 Topics | IELTS Writing Pro</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
  <style>
    body {
      background-color: #f8f9fa;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
    .section-title {
      margin-bottom: 30px;
      position: relative;
      padding-bottom: 10px;
      color: #1565c0;
      border-bottom: 2px solid #1565c0;
    }
    .topic-box {
      padding: 20px;
      border-radius: 10px;
      background-color: white;
      margin-bottom: 20px;
      border-left: 5px solid #1565c0;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      transition: all 0.3s ease;
    }
    .topic-box:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    .sub-section {
      margin-top: 20px;
      padding-left: 20px;
    }
    .footer {
      margin-top: 50px;
      padding: 20px 0;
      background-color: #f1f1f1;
      color: #666;
    }
    .chart-image {
      max-width: 100%;
      height: auto;
      margin: 10px 0;
      border: 1px solid #ddd;
      border-radius: 5px;
    }
    .vocabulary-table th, .vocabulary-table td {
      border: 1px solid #dee2e6;
      padding: 10px;
    }
    .vocabulary-table th {
      background-color: #1565c0;
      color: white;
    }
    .btn-view {
      background-color: #1565c0;
      color: white;
    }
    .btn-view:hover {
      background-color: #0d47a1;
      color: white;
    }
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
    .chart-icon {
      margin-right: 10px;
      color: #1565c0;
      font-size: 1.2rem;
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
                <a class="dropdown-item dropdown-toggle active" href="#" id="task1Dropdown" data-toggle="dropdown">Writing Task 1</a>
                <div class="dropdown-menu">
                  <a class="dropdown-item" href="lessons_task1_types.php">Các dạng bài</a>
                  <a class="dropdown-item active" href="lessons_task1_topics.php">Chủ đề</a>
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
            <?php if ($logged_in): ?>
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
            <?php else: ?>
            <div class="guest-user">
              <a href="login.php" class="btn btn-outline-primary btn-sm mr-2">Đăng nhập</a>
              <a href="register.php" class="btn btn-primary btn-sm">Đăng ký</a>
            </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </nav>

  <!-- Main Content -->
  <div class="container mt-4">
    <div class="row">
      <div class="col-md-8">
        <h1 class="section-title">Chủ đề trong IELTS Writing Task 1</h1>
        <p>Học cách viết báo cáo mô tả các chủ đề phổ biến trong IELTS Writing Task 1. Chọn cấp độ phù hợp để xem hướng dẫn chi tiết.</p>

        <!-- Topic 1: Population -->
        <div class="topic-box">
          <div class="chart-type-header">
            <i class="fas fa-users chart-icon"></i> Dân số (Population)
          </div>
          <div class="chart-type-body">
            <p>Học cách mô tả dữ liệu về sự thay đổi dân số, phân bố độ tuổi, giới tính hoặc khu vực địa lý. Chủ đề này thường xuất hiện dưới dạng biểu đồ đường, bảng số liệu hoặc biểu đồ cột.</p>
            <ul>
              <li>Phân tích xu hướng dân số</li>
              <li>Từ vựng mô tả tăng trưởng và giảm sút</li>
              <li>Cách so sánh giữa các khu vực hoặc thời kỳ</li>
              <li>Chiến lược tổ chức bài viết</li>
            </ul>
          </div>
          <div class="chart-type-footer">
            <button type="button" class="btn btn-view" data-toggle="modal" data-target="#difficultyModalPopulation">Xem hướng dẫn</button>
          </div>
        </div>
        <!-- Modal for Population -->
        <div class="modal fade" id="difficultyModalPopulation" tabindex="-1" role="dialog" aria-labelledby="difficultyModalPopulationLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="difficultyModalPopulationLabel">Chọn cấp độ</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span>
                </button>
              </div>
              <div class="modal-body">
                <a href="beginner_task1.php?topic=population" class="btn btn-success difficulty-btn beginner">Beginner</a>
                <a href="intermediate_task1.php?topic=population" class="btn btn-warning difficulty-btn intermediate">Intermediate</a>
                <a href="advanced_task1.php?topic=population" class="btn btn-danger difficulty-btn advanced">Advanced</a>
              </div>
            </div>
          </div>
        </div>
        <!-- Topic 2: Economy -->
        <div class="topic-box">
          <div class="chart-type-header">
            <i class="fas fa-chart-line chart-icon"></i> Kinh tế (Economy)
          </div>
          <div class="chart-type-body">
            <p>Học cách mô tả dữ liệu về tăng trưởng kinh tế, xuất nhập khẩu, hoặc tỷ lệ lạm phát. Chủ đề này thường xuất hiện dưới dạng biểu đồ cột, đường hoặc bảng số liệu.</p>
            <ul>
              <li>Phân tích xu hướng kinh tế</li>
              <li>Từ vựng mô tả tỷ lệ và số liệu kinh tế</li>
              <li>Cách so sánh giữa các quốc gia hoặc thời kỳ</li>
              <li>Mẫu câu hữu ích cho bài viết</li>
            </ul>
          </div>
          <div class="chart-type-footer">
            <button type="button" class="btn btn-view" data-toggle="modal" data-target="#difficultyModalEconomy">Xem hướng dẫn</button>
          </div>
        </div>
        <!-- Modal for Economy -->
        <div class="modal fade" id="difficultyModalEconomy" tabindex="-1" role="dialog" aria-labelledby="difficultyModalEconomyLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="difficultyModalEconomyLabel">Chọn cấp độ</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span>
                </button>
              </div>
              <div class="modal-body">
                <a href="beginner_task1.php?topic=economy" class="btn btn-success difficulty-btn beginner">Beginner</a>
                <a href="intermediate_task1.php?topic=economy" class="btn btn-warning difficulty-btn intermediate">Intermediate</a>
                <a href="advanced_task1.php?topic=economy" class="btn btn-danger difficulty-btn advanced">Advanced</a>
              </div>
            </div>
          </div>
        </div>

        <!-- Topic 3: Energy -->
        <div class="topic-box">
          <div class="chart-type-header">
            <i class="fas fa-bolt chart-icon"></i> Năng lượng (Energy)
          </div>
          <div class="chart-type-body">
            <p>Hướng dẫn cách mô tả tiêu thụ năng lượng, tỷ lệ sử dụng năng lượng tái tạo, hoặc sản xuất điện. Chủ đề này thường xuất hiện dưới dạng biểu đồ tròn, cột hoặc bảng số liệu.</p>
            <ul>
              <li>Phân tích tỷ lệ năng lượng tái tạo và hóa thạch</li>
              <li>Từ vựng mô tả tiêu thụ và sản xuất</li>
              <li>Cách so sánh giữa các quốc gia hoặc loại năng lượng</li>
              <li>Cấu trúc bài viết hiệu quả</li>
            </ul>
          </div>
          <div class="chart-type-footer">
            <button type="button" class="btn btn-view" data-toggle="modal" data-target="#difficultyModalEnergy">Xem hướng dẫn</button>
          </div>
        </div>
        <!-- Modal for Energy -->
        <div class="modal fade" id="difficultyModalEnergy" tabindex="-1" role="dialog" aria-labelledby="difficultyModalEnergyLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="difficultyModalEnergyLabel">Chọn cấp độ</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span>
                </button>
              </div>
              <div class="modal-body">
                <a href="beginner_task1.php?topic=energy" class="btn btn-success difficulty-btn beginner">Beginner</a>
                <a href="intermediate_task1.php?topic=energy" class="btn btn-warning difficulty-btn intermediate">Intermediate</a>
                <a href="advanced_task1.php?topic=energy" class="btn btn-danger difficulty-btn advanced">Advanced</a>
              </div>
            </div>
          </div>
        </div>

        <!-- Topic 4: Urbanization and Housing -->
        <div class="topic-box">
          <div class="chart-type-header">
            <i class="fas fa-city chart-icon"></i> Đô thị hóa và Nhà ở (Urbanization and Housing)
          </div>
          <div class="chart-type-body">
            <p>Học cách mô tả dữ liệu về sự phát triển đô thị, giá nhà, hoặc tỷ lệ dân số đô thị và nông thôn. Chủ đề này thường xuất hiện dưới dạng biểu đồ đường, bảng hoặc bản đồ.</p>
            <ul>
              <li>Phân tích xu hướng đô thị hóa</li>
              <li>Từ vựng mô tả nhà ở và dân số</li>
              <li>Cách so sánh giữa các khu vực hoặc thời kỳ</li>
              <li>Mẫu câu hữu ích cho bài viết</li>
            </ul>
          </div>
          <div class="chart-type-footer">
            <button type="button" class="btn btn-view" data-toggle="modal" data-target="#difficultyModalUrbanizationHousing">Xem hướng dẫn</button>
          </div>
        </div>
        <!-- Modal for Urbanization and Housing -->
        <div class="modal fade" id="difficultyModalUrbanizationHousing" tabindex="-1" role="dialog" aria-labelledby="difficultyModalUrbanizationHousingLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="difficultyModalUrbanizationHousingLabel">Chọn cấp độ</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span>
                </button>
              </div>
              <div class="modal-body">
                <a href="beginner_task1.php?topic=urbanization_housing" class="btn btn-success difficulty-btn beginner">Beginner</a>
                <a href="intermediate_task1.php?topic=urbanization_housing" class="btn btn-warning difficulty-btn intermediate">Intermediate</a>
                <a href="advanced_task1.php?topic=urbanization_housing" class="btn btn-danger difficulty-btn advanced">Advanced</a>
              </div>
            </div>
          </div>
        </div>

        <!-- Topic 5: Process Diagrams -->
        <div class="topic-box">
          <div class="chart-type-header">
            <i class="fas fa-cogs chart-icon"></i> Sơ đồ quy trình (Process Diagrams)
          </div>
          <div class="chart-type-body">
            <p>Học cách mô tả các quy trình sản xuất, chu kỳ tự nhiên, hoặc quy trình công nghiệp. Chủ đề này thường xuất hiện dưới dạng sơ đồ quy trình.</p>
            <ul>
              <li>Phân tích các bước trong quy trình</li>
              <li>Từ vựng mô tả chuỗi sự kiện</li>
              <li>Cách tổ chức bài viết theo trình tự</li>
              <li>Mẫu câu hữu ích cho bài viết</li>
            </ul>
          </div>
          <div class="chart-type-footer">
            <button type="button" class="btn btn-view" data-toggle="modal" data-target="#difficultyModalProcessDiagrams">Xem hướng dẫn</button>
          </div>
        </div>
        <!-- Modal for Process Diagrams -->
        <div class="modal fade" id="difficultyModalProcessDiagrams" tabindex="-1" role="dialog" aria-labelledby="difficultyModalProcessDiagramsLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="difficultyModalProcessDiagramsLabel">Chọn cấp độ</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span>
                </button>
              </div>
              <div class="modal-body">
                <a href="beginner_task1.php?topic=process_diagrams" class="btn btn-success difficulty-btn beginner">Beginner</a>
                <a href="intermediate_task1.php?topic=process_diagrams" class="btn btn-warning difficulty-btn intermediate">Intermediate</a>
                <a href="advanced_task1.php?topic=process_diagrams" class="btn btn-danger difficulty-btn advanced">Advanced</a>
              </div>
            </div>
          </div>
        </div>

        <!-- Topic 6: Maps -->
        <div class="topic-box">
          <div class="chart-type-header">
            <i class="fas fa-map chart-icon"></i> Bản đồ (Maps)
          </div>
          <div class="chart-type-body">
            <p>Hướng dẫn cách mô tả sự thay đổi trong bố cục đô thị, cơ sở hạ tầng, hoặc khu vực địa lý. Chủ đề này thường xuất hiện dưới dạng bản đồ so sánh.</p>
            <ul>
              <li>Phân tích thay đổi không gian</li>
              <li>Từ vựng mô tả vị trí và phát triển</li>
              <li>Cách so sánh trước và sau</li>
              <li>Cấu trúc bài viết hiệu quả</li>
            </ul>
          </div>
          <div class="chart-type-footer">
            <button type="button" class="btn btn-view" data-toggle="modal" data-target="#difficultyModalMaps">Xem hướng dẫn</button>
          </div>
        </div>
        <!-- Modal for Maps -->
        <div class="modal fade" id="difficultyModalMaps" tabindex="-1" role="dialog" aria-labelledby="difficultyModalMapsLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="difficultyModalMapsLabel">Chọn cấp độ</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span>
                </button>
              </div>
              <div class="modal-body">
                <a href="beginner_task1.php?topic=maps" class="btn btn-success difficulty-btn beginner">Beginner</a>
                <a href="intermediate_task1.php?topic=maps" class="btn btn-warning difficulty-btn intermediate">Intermediate</a>
                <a href="advanced_task1.php?topic=maps" class="btn btn-danger difficulty-btn advanced">Advanced</a>
              </div>
            </div>
          </div>
        </div>

        <!-- Topic 7: Education -->
        <div class="topic-box">
          <div class="chart-type-header">
            <i class="fas fa-graduation-cap chart-icon"></i> Giáo dục (Education)
          </div>
          <div class="chart-type-body">
            <p>Học cách mô tả số lượng học sinh, tỷ lệ nhập học, chi tiêu giáo dục, hoặc tỷ lệ tốt nghiệp. Chủ đề này thường xuất hiện dưới dạng bảng, biểu đồ cột hoặc biểu đồ đường.</p>
            <ul>
              <li>Phân tích xu hướng giáo dục</li>
              <li>Từ vựng mô tả số lượng và tỷ lệ</li>
              <li>Cách so sánh giữa các quốc gia hoặc thời kỳ</li>
              <li>Mẫu câu hữu ích cho bài viết</li>
            </ul>
          </div>
          <div class="chart-type-footer">
            <button type="button" class="btn btn-view" data-toggle="modal" data-target="#difficultyModalEducation">Xem hướng dẫn</button>
          </div>
        </div>
        <!-- Modal for Education -->
        <div class="modal fade" id="difficultyModalEducation" tabindex="-1" role="dialog" aria-labelledby="difficultyModalEducationLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="difficultyModalEducationLabel">Chọn cấp độ</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span>
                </button>
              </div>
              <div class="modal-body">
                <a href="beginner_task1.php?topic=education" class="btn btn-success difficulty-btn beginner">Beginner</a>
                <a href="intermediate_task1.php?topic=education" class="btn btn-warning difficulty-btn intermediate">Intermediate</a>
                <a href="advanced_task1.php?topic=education" class="btn btn-danger difficulty-btn advanced">Advanced</a>
              </div>
            </div>
          </div>
        </div>

        <!-- Topic 8: Health -->
        <div class="topic-box">
          <div class="chart-type-header">
            <i class="fas fa-heartbeat chart-icon"></i> Sức khỏe (Health)
          </div>
          <div class="chart-type-body">
            <p>Hướng dẫn cách mô tả dữ liệu về tỷ lệ mắc bệnh, chi tiêu y tế, hoặc tuổi thọ trung bình. Chủ đề này thường xuất hiện dưới dạng bảng, biểu đồ tròn hoặc biểu đồ đường.</p>
            <ul>
              <li>Phân tích xu hướng sức khỏe</li>
              <li>Từ vựng mô tả tỷ lệ và số liệu y tế</li>
              <li>Cách so sánh giữa các quốc gia hoặc nhóm tuổi</li>
              <li>Cấu trúc bài viết hiệu quả</li>
            </ul>
          </div>
          <div class="chart-type-footer">
            <button type="button" class="btn btn-view" data-toggle="modal" data-target="#difficultyModalHealth">Xem hướng dẫn</button>
          </div>
        </div>
        <!-- Modal for Health -->
        <div class="modal fade" id="difficultyModalHealth" tabindex="-1" role="dialog" aria-labelledby="difficultyModalHealthLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="difficultyModalHealthLabel">Chọn cấp độ</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span>
                </button>
              </div>
              <div class="modal-body">
                <a href="beginner_task1.php?topic=health" class="btn btn-success difficulty-btn beginner">Beginner</a>
                <a href="intermediate_task1.php?topic=health" class="btn btn-warning difficulty-btn intermediate">Intermediate</a>
                <a href="advanced_task1.php?topic=health" class="btn btn-danger difficulty-btn advanced">Advanced</a>
              </div>
            </div>
          </div>
        </div>

        <!-- Topic 9: Employment -->
        <div class="topic-box">
          <div class="chart-type-header">
            <i class="fas fa-briefcase chart-icon"></i> Việc làm (Employment)
          </div>
          <div class="chart-type-body">
            <p>Học cách mô tả dữ liệu về tỷ lệ việc làm, thất nghiệp, hoặc phân bố lao động theo ngành nghề. Chủ đề này thường xuất hiện dưới dạng biểu đồ cột, bảng hoặc biểu đồ đường.</p>
            <ul>
              <li>Phân tích xu hướng việc làm</li>
              <li>Từ vựng mô tả tỷ lệ và ngành nghề</li>
              <li>Cách so sánh giữa các nhóm lao động hoặc thời kỳ</li>
              <li>Mẫu câu hữu ích cho bài viết</li>
            </ul>
          </div>
          <div class="chart-type-footer">
            <button type="button" class="btn btn-view" data-toggle="modal" data-target="#difficultyModalEmployment">Xem hướng dẫn</button>
          </div>
        </div>
        <!-- Modal for Employment -->
        <div class="modal fade" id="difficultyModalEmployment" tabindex="-1" role="dialog" aria-labelledby="difficultyModalEmploymentLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="difficultyModalEmploymentLabel">Chọn cấp độ</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span>
                </button>
              </div>
              <div class="modal-body">
                <a href="beginner_task1.php?topic=employment" class="btn btn-success difficulty-btn beginner">Beginner</a>
                <a href="intermediate_task1.php?topic=employment" class="btn btn-warning difficulty-btn intermediate">Intermediate</a>
                <a href="advanced_task1.php?topic=employment" class="btn btn-danger difficulty-btn advanced">Advanced</a>
              </div>
            </div>
          </div>
        </div>

        <!-- Topic 10: Technology -->
        <div class="topic-box">
          <div class="chart-type-header">
            <i class="fas fa-laptop chart-icon"></i> Công nghệ (Technology)
          </div>
          <div class="chart-type-body">
            <p>Hướng dẫn cách mô tả dữ liệu về sử dụng công nghệ, sản xuất thiết bị, hoặc đầu tư nghiên cứu công nghệ. Chủ đề này thường xuất hiện dưới dạng biểu đồ đường, cột hoặc bảng.</p>
            <ul>
              <li>Phân tích xu hướng công nghệ</li>
              <li>Từ vựng mô tả phát triển và sử dụng</li>
              <li>Cách so sánh giữa các lĩnh vực hoặc quốc gia</li>
              <li>Cấu trúc bài viết hiệu quả</li>
            </ul>
          </div>
          <div class="chart-type-footer">
            <button type="button" class="btn btn-view" data-toggle="modal" data-target="#difficultyModalTechnology">Xem hướng dẫn</button>
          </div>
        </div>
        <!-- Modal for Technology -->
        <div class="modal fade" id="difficultyModalTechnology" tabindex="-1" role="dialog" aria-labelledby="difficultyModalTechnologyLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="difficultyModalTechnologyLabel">Chọn cấp độ</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span>
                </button>
              </div>
              <div class="modal-body">
                <a href="beginner_task1.php?topic=technology" class="btn btn-success difficulty-btn beginner">Beginner</a>
                <a href="intermediate_task1.php?topic=technology" class="btn btn-warning difficulty-btn intermediate">Intermediate</a>
                <a href="advanced_task1.php?topic=technology" class="btn btn-danger difficulty-btn advanced">Advanced</a>
              </div>
            </div>
          </div>
        </div>

        <!-- Topic 11: Environment -->
        <div class="topic-box">
          <div class="chart-type-header">
            <i class="fas fa-leaf chart-icon"></i> Môi trường (Environment)
          </div>
          <div class="chart-type-body">
            <p>Học cách mô tả dữ liệu về khí thải, mức ô nhiễm, hoặc sử dụng tài nguyên thiên nhiên. Chủ đề này thường xuất hiện dưới dạng biểu đồ cột, tròn hoặc bảng.</p>
            <ul>
              <li>Phân tích dữ liệu môi trường</li>
              <li>Từ vựng mô tả ô nhiễm và tài nguyên</li>
              <li>Cách so sánh giữa các quốc gia hoặc thời kỳ</li>
              <li>Mẫu câu hữu ích cho bài viết</li>
            </ul>
          </div>
          <div class="chart-type-footer">
            <button type="button" class="btn btn-view" data-toggle="modal" data-target="#difficultyModalEnvironment">Xem hướng dẫn</button>
          </div>
        </div>
        <!-- Modal for Environment -->
        <div class="modal fade" id="difficultyModalEnvironment" tabindex="-1" role="dialog" aria-labelledby="difficultyModalEnvironmentLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="difficultyModalEnvironmentLabel">Chọn cấp độ</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span>
                </button>
              </div>
              <div class="modal-body">
                <a href="beginner_task1.php?topic=environment" class="btn btn-success difficulty-btn beginner">Beginner</a>
                <a href="intermediate_task1.php?topic=environment" class="btn btn-warning difficulty-btn intermediate">Intermediate</a>
                <a href="advanced_task1.php?topic=environment" class="btn btn-danger difficulty-btn advanced">Advanced</a>
              </div>
            </div>
          </div>
        </div>

        <!-- Topic 12: Money -->
        <div class="topic-box">
          <div class="chart-type-header">
            <i class="fas fa-money-bill-wave chart-icon"></i> Tiền bạc (Money)
          </div>
          <div class="chart-type-body">
            <p>Hướng dẫn cách mô tả dữ liệu về ngân sách chính phủ, chi tiêu hộ gia đình, hoặc tăng trưởng tài chính. Chủ đề này thường xuất hiện dưới dạng bảng, biểu đồ tròn hoặc cột.</p>
            <ul>
              <li>Phân tích xu hướng tài chính</li>
              <li>Từ vựng mô tả chi tiêu và ngân sách</li>
              <li>Cách so sánh giữa các hạng mục hoặc thời kỳ</li>
              <li>Cấu trúc bài viết hiệu quả</li>
            </ul>
          </div>
          <div class="chart-type-footer">
            <button type="button" class="btn btn-view" data-toggle="modal" data-target="#difficultyModalMoney">Xem hướng dẫn</button>
          </div>
        </div>
        <!-- Modal for Money -->
        <div class="modal fade" id="difficultyModalMoney" tabindex="-1" role="dialog" aria-labelledby="difficultyModalMoneyLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="difficultyModalMoneyLabel">Chọn cấp độ</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span>
                </button>
              </div>
              <div class="modal-body">
                <a href="beginner_task1.php?topic=money" class="btn btn-success difficulty-btn beginner">Beginner</a>
                <a href="intermediate_task1.php?topic=money" class="btn btn-warning difficulty-btn intermediate">Intermediate</a>
                <a href="advanced_task1.php?topic=money" class="btn btn-danger difficulty-btn advanced">Advanced</a>
              </div>
            </div>
          </div>
        </div>

        <!-- Topic 13: Food & Drinks -->
        <div class="topic-box">
          <div class="chart-type-header">
            <i class="fas fa-utensils chart-icon"></i> Thực phẩm & Đồ uống (Food & Drinks)
          </div>
          <div class="chart-type-body">
            <p>Học cách mô tả dữ liệu về tiêu thụ thực phẩm, sản xuất nông nghiệp, hoặc xu hướng ăn uống. Chủ đề này thường xuất hiện dưới dạng biểu đồ cột, tròn hoặc bảng.</p>
            <ul>
              <li>Phân tích xu hướng tiêu thụ thực phẩm</li>
              <li>Từ vựng mô tả sản xuất và tiêu dùng</li>
              <li>Cách so sánh giữa các quốc gia hoặc loại thực phẩm</li>
              <li>Mẫu câu hữu ích cho bài viết</li>
            </ul>
          </div>
          <div class="chart-type-footer">
            <button type="button" class="btn btn-view" data-toggle="modal" data-target="#difficultyModalFoodDrinks">Xem hướng dẫn</button>
          </div>
        </div>
        <!-- Modal for Food & Drinks -->
        <div class="modal fade" id="difficultyModalFoodDrinks" tabindex="-1" role="dialog" aria-labelledby="difficultyModalFoodDrinksLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="difficultyModalFoodDrinksLabel">Chọn cấp độ</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span>
                </button>
              </div>
              <div class="modal-body">
                <a href="beginner_task1.php?topic=food_drinks" class="btn btn-success difficulty-btn beginner">Beginner</a>
                <a href="intermediate_task1.php?topic=food_drinks" class="btn btn-warning difficulty-btn intermediate">Intermediate</a>
                <a href="advanced_task1.php?topic=food_drinks" class="btn btn-danger difficulty-btn advanced">Advanced</a>
              </div>
            </div>
          </div>
        </div>

        <!-- Topic 14: Sports & Leisure -->
        <div class="topic-box">
          <div class="chart-type-header">
            <i class="fas fa-futbol chart-icon"></i> Thể thao & Giải trí (Sports & Leisure)
          </div>
          <div class="chart-type-body">
            <p>Hướng dẫn cách mô tả dữ liệu về tỷ lệ tham gia thể thao, chi tiêu cho giải trí, hoặc thời gian dành cho hoạt động giải trí. Chủ đề này thường xuất hiện dưới dạng bảng, biểu đồ tròn hoặc cột.</p>
            <ul>
              <li>Phân tích xu hướng thể thao và giải trí</li>
              <li>Từ vựng mô tả hoạt động và chi tiêu</li>
              <li>Cách so sánh giữa các nhóm hoặc thời kỳ</li>
              <li>Cấu trúc bài viết hiệu quả</li>
            </ul>
          </div>
          <div class="chart-type-footer">
            <button type="button" class="btn btn-view" data-toggle="modal" data-target="#difficultyModalSportsLeisure">Xem hướng dẫn</button>
          </div>
        </div>
        <!-- Modal for Sports & Leisure -->
        <div class="modal fade" id="difficultyModalSportsLeisure" tabindex="-1" role="dialog" aria-labelledby="difficultyModalSportsLeisureLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="difficultyModalSportsLeisureLabel">Chọn cấp độ</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span>
                </button>
              </div>
              <div class="modal-body">
                <a href="beginner_task1.php?topic=sports_leisure" class="btn btn-success difficulty-btn beginner">Beginner</a>
                <a href="intermediate_task1.php?topic=sports_leisure" class="btn btn-warning difficulty-btn intermediate">Intermediate</a>
                <a href="advanced_task1.php?topic=sports_leisure" class="btn btn-danger difficulty-btn advanced">Advanced</a>
              </div>
            </div>
          </div>
        </div>

        <!-- Topic 15: Transport -->
        <div class="topic-box">
          <div class="chart-type-header">
            <i class="fas fa-bus chart-icon"></i> Giao thông (Transport)
          </div>
          <div class="chart-type-body">
            <p>Học cách mô tả dữ liệu về sử dụng phương tiện, giao thông công cộng, hoặc cơ sở hạ tầng giao thông. Chủ đề này thường xuất hiện dưới dạng biểu đồ đường, bảng hoặc bản đồ.</p>
            <ul>
              <li>Phân tích xu hướng giao thông</li>
              <li>Từ vựng mô tả phương tiện và cơ sở hạ tầng</li>
              <li>Cách so sánh giữa các phương tiện hoặc thời kỳ</li>
              <li>Mẫu câu hữu ích cho bài viết</li>
            </ul>
          </div>
          <div class="chart-type-footer">
            <button type="button" class="btn btn-view" data-toggle="modal" data-target="#difficultyModalTransport">Xem hướng dẫn</button>
          </div>
        </div>
        <!-- Modal for Transport -->
        <div class="modal fade" id="difficultyModalTransport" tabindex="-1" role="dialog" aria-labelledby="difficultyModalTransportLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="difficultyModalTransportLabel">Chọn cấp độ</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span>
                </button>
              </div>
              <div class="modal-body">
                <a href="beginner_task1.php?topic=transport" class="btn btn-success difficulty-btn beginner">Beginner</a>
                <a href="intermediate_task1.php?topic=transport" class="btn btn-warning difficulty-btn intermediate">Intermediate</a>
                <a href="advanced_task1.php?topic=transport" class="btn btn-danger difficulty-btn advanced">Advanced</a>
              </div>
            </div>
          </div>
        </div>

        <!-- Topic 16: Tourism -->
        <div class="topic-box">
          <div class="chart-type-header">
            <i class="fas fa-plane chart-icon"></i> Du lịch (Tourism)
          </div>
          <div class="chart-type-body">
            <p>Học cách mô tả dữ liệu về số lượng khách du lịch, điểm đến, hoặc doanh thu từ du lịch. Chủ đề này thường xuất hiện dưới dạng biểu đồ cột, đường hoặc bảng số liệu.</p>
            <ul>
              <li>Phân tích xu hướng du lịch</li>
              <li>Từ vựng mô tả số lượng và doanh thu</li>
              <li>Cách so sánh giữa các quốc gia hoặc thời kỳ</li>
              <li>Cấu trúc bài viết hiệu quả</li>
            </ul>
          </div>
          <div class="chart-type-footer">
            <button type="button" class="btn btn-view" data-toggle="modal" data-target="#difficultyModalTourism">Xem hướng dẫn</button>
          </div>
        </div>
        <!-- Modal for Tourism -->
        <div class="modal fade" id="difficultyModalTourism" tabindex="-1" role="dialog" aria-labelledby="difficultyModalTourismLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="difficultyModalTourismLabel">Chọn cấp độ</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span>
                </button>
              </div>
              <div class="modal-body">
                <a href="beginner_task1.php?topic=tourism" class="btn btn-success difficulty-btn beginner">Beginner</a>
                <a href="intermediate_task1.php?topic=tourism" class="btn btn-warning difficulty-btn intermediate">Intermediate</a>
                <a href="advanced_task1.php?topic=tourism" class="btn btn-danger difficulty-btn advanced">Advanced</a>
              </div>
            </div>
          </div>
        </div>

        <!-- Topic 17: Gender -->
        <div class="topic-box">
          <div class="chart-type-header">
            <i class="fas fa-venus-mars chart-icon"></i> Giới tính (Gender)
          </div>
          <div class="chart-type-body">
            <p>Hướng dẫn cách mô tả dữ liệu về tỷ lệ việc làm, giáo dục, hoặc thu nhập theo giới tính. Chủ đề này thường xuất hiện dưới dạng bảng, biểu đồ cột hoặc biểu đồ tròn.</p>
            <ul>
              <li>Phân tích xu hướng giới tính</li>
              <li>Từ vựng mô tả tỷ lệ và bình đẳng</li>
              <li>Cách so sánh giữa các giới tính hoặc thời kỳ</li>
              <li>Mẫu câu hữu ích cho bài viết</li>
            </ul>
          </div>
          <div class="chart-type-footer">
            <button type="button" class="btn btn-view" data-toggle="modal" data-target="#difficultyModalGender">Xem hướng dẫn</button>
          </div>
        </div>
        <!-- Modal for Gender -->
        <div class="modal fade" id="difficultyModalGender" tabindex="-1" role="dialog" aria-labelledby="difficultyModalGenderLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="difficultyModalGenderLabel">Chọn cấp độ</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span>
                </button>
              </div>
              <div class="modal-body">
                <a href="beginner_task1.php?topic=gender" class="btn btn-success difficulty-btn beginner">Beginner</a>
                <a href="intermediate_task1.php?topic=gender" class="btn btn-warning difficulty-btn intermediate">Intermediate</a>
                <a href="advanced_task1.php?topic=gender" class="btn btn-danger difficulty-btn advanced">Advanced</a>
              </div>
            </div>
          </div>
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
              <li class="list-group-item"><a href="#difficultyModalPopulationDemographics">Dân số và Nhân khẩu học</a></li>
              <li class="list-group-item"><a href="#difficultyModalEconomy">Kinh tế</a></li>
              <li class="list-group-item"><a href="#difficultyModalEnergy">Năng lượng</a></li>
              <li class="list-group-item"><a href="#difficultyModalUrbanizationHousing">Đô thị hóa và Nhà ở</a></li>
              <li class="list-group-item"><a href="#difficultyModalProcessDiagrams">Sơ đồ quy trình</a></li>
              <li class="list-group-item"><a href="#difficultyModalMaps">Bản đồ</a></li>
              <li class="list-group-item"><a href="#difficultyModalEducation">Giáo dục</a></li>
              <li class="list-group-item"><a href="#difficultyModalHealth">Sức khỏe</a></li>
              <li class="list-group-item"><a href="#difficultyModalEmployment">Việc làm</a></li>
              <li class="list-group-item"><a href="#difficultyModalTechnology">Công nghệ</a></li>
              <li class="list-group-item"><a href="#difficultyModalEnvironment">Môi trường</a></li>
              <li class="list-group-item"><a href="#difficultyModalMoney">Tiền bạc</a></li>
              <li class="list-group-item"><a href="#difficultyModalFoodDrinks">Thực phẩm & Đồ uống</a></li>
              <li class="list-group-item"><a href="#difficultyModalSportsLeisure">Thể thao & Giải trí</a></li>
              <li class="list-group-item"><a href="#difficultyModalTransport">Giao thông</a></li>
              <li class="list-group-item"><a href="#difficultyModalTourism">Du lịch</a></li>
              <li class="list-group-item"><a href="#difficultyModalGender">Giới tính</a></li>
            </ul>
          </div>
        </div>

        <div class="card mt-4">
          <div class="card-header">
            <h5>Mẹo cho người mới</h5>
          </div>
          <div class="card-body">
            <ol>
              <li>Xác định loại biểu đồ</li>
              <li>Nhận diện xu hướng chính</li>
              <li>Sử dụng từ vựng so sánh</li>
              <li>Tổ chức bài viết rõ ràng</li>
              <li>Kiểm tra số liệu chính xác</li>
              <li>Quản lý thời gian</li>
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
                <a href="vocabulary_task1.php">Từ vựng Task 1 cơ bản</a>
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