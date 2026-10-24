<?php
session_start();
$logged_in = isset($_SESSION['user_id']);
require 'config.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <title>IELTS Writing Task 2 Topics | IELTS Writing Pro</title>
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
    .task2 {
      background-color: #ffebee;
      color: #c62828;
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
                <a class="dropdown-item active dropdown-toggle" href="#" id="task2Dropdown" data-toggle="dropdown">Writing Task 2</a>
                <div class="dropdown-menu">
                  <a class="dropdown-item" href="lessons_task2_types.php">Các dạng bài</a>
                  <a class="dropdown-item active" href="lessons_task2_topics.php">Chủ đề</a>
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
        <h1 class="section-title">Chủ đề trong IELTS Writing Task 2</h1>
        <p>Học cách viết bài luận nghị luận về các chủ đề phổ biến trong IELTS Writing Task 2. Chọn cấp độ phù hợp để xem hướng dẫn chi tiết.</p>

        <!-- Topic 1: Education -->
        <div class="topic-box">
          <div class="chart-type-header">
            <i class="fas fa-graduation-cap chart-icon"></i> Giáo dục (Education)
          </div>
          <div class="chart-type-body">
            <p>Học cách viết bài luận về giáo dục, bao gồm vai trò của giáo dục, phương pháp giảng dạy, học phí, hoặc bình đẳng trong giáo dục. Chủ đề này thường xuất hiện dưới dạng Opinion, Discussion, hoặc Problem-Solution essays.</p>
            <ul>
              <li>Xác định dạng bài và từ khóa</li>
              <li>Từ vựng mô tả giáo dục và bình đẳng</li>
              <li>Cấu trúc bài luận logic</li>
              <li>Chiến lược đưa ra lập luận và ví dụ</li>
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
                <a href="beginner_task2.php?topic=education" class="btn btn-success difficulty-btn beginner">Beginner</a>
                <a href="intermediate_task2.php?topic=education" class="btn btn-warning difficulty-btn intermediate">Intermediate</a>
                <a href="advanced_task2.php?topic=education" class="btn btn-danger difficulty-btn advanced">Advanced</a>
              </div>
            </div>
          </div>
        </div>

        <!-- Topic 2: Crime -->
        <div class="topic-box">
          <div class="chart-type-header">
            <i class="fas fa-gavel chart-icon"></i> Tội phạm (Crime)
          </div>
          <div class="chart-type-body">
            <p>Hướng dẫn cách viết bài luận về các vấn đề liên quan đến tội phạm, như nguyên nhân, hậu quả, hoặc các biện pháp giảm thiểu tội phạm. Chủ đề này thường xuất hiện dưới dạng Causes/Solutions hoặc Discussion essays.</p>
            <ul>
              <li>Phân tích nguyên nhân và giải pháp</li>
              <li>Từ vựng mô tả tội phạm và luật pháp</li>
              <li>Cách tổ chức lập luận logic</li>
              <li>Mẫu câu hữu ích cho bài viết</li>
            </ul>
          </div>
          <div class="chart-type-footer">
            <button type="button" class="btn btn-view" data-toggle="modal" data-target="#difficultyModalCrime">Xem hướng dẫn</button>
          </div>
        </div>
        <!-- Modal for Crime -->
        <div class="modal fade" id="difficultyModalCrime" tabindex="-1" role="dialog" aria-labelledby="difficultyModalCrimeLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="difficultyModalCrimeLabel">Chọn cấp độ</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span>
                </button>
              </div>
              <div class="modal-body">
                <a href="beginner_task2.php?topic=crime" class="btn btn-success difficulty-btn beginner">Beginner</a>
                <a href="intermediate_task2.php?topic=crime" class="btn btn-warning difficulty-btn intermediate">Intermediate</a>
                <a href="advanced_task2.php?topic=crime" class="btn btn-danger difficulty-btn advanced">Advanced</a>
              </div>
            </div>
          </div>
        </div>

        <!-- Topic 3: Health -->
        <div class="topic-box">
          <div class="chart-type-header">
            <i class="fas fa-heartbeat chart-icon"></i> Sức khỏe (Health)
          </div>
          <div class="chart-type-body">
            <p>Hướng dẫn cách viết bài luận về lối sống, hệ thống y tế, hoặc các vấn đề như béo phì và sức khỏe tinh thần. Chủ đề này thường xuất hiện dưới dạng Causes/Solutions hoặc Opinion essays.</p>
            <ul>
              <li>Phân tích nguyên nhân và giải pháp sức khỏe</li>
              <li>Từ vựng mô tả dinh dưỡng và lối sống</li>
              <li>Cách đưa ra ví dụ thực tế</li>
              <li>Chiến lược tổ chức bài viết</li>
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
                <a href="beginner_task2.php?topic=health" class="btn btn-success difficulty-btn beginner">Beginner</a>
                <a href="intermediate_task2.php?topic=health" class="btn btn-warning difficulty-btn intermediate">Intermediate</a>
                <a href="advanced_task2.php?topic=health" class="btn btn-danger difficulty-btn advanced">Advanced</a>
              </div>
            </div>
          </div>
        </div>

        <!-- Topic 4: Work -->
        <div class="topic-box">
          <div class="chart-type-header">
            <i class="fas fa-briefcase chart-icon"></i> Công việc (Work)
          </div>
          <div class="chart-type-body">
            <p>Học cách viết bài luận về các vấn đề liên quan đến công việc, như cân bằng công việc và cuộc sống, việc làm từ xa, hoặc bình đẳng giới trong công việc. Chủ đề này thường xuất hiện dưới dạng Opinion hoặc Advantages/Disadvantages essays.</p>
            <ul>
              <li>Phân tích lợi ích và thách thức trong công việc</li>
              <li>Từ vựng mô tả môi trường làm việc</li>
              <li>Cách tổ chức lập luận cân bằng</li>
              <li>Mẫu câu hữu ích cho bài viết</li>
            </ul>
          </div>
          <div class="chart-type-footer">
            <button type="button" class="btn btn-view" data-toggle="modal" data-target="#difficultyModalWork">Xem hướng dẫn</button>
          </div>
        </div>
        <!-- Modal for Work -->
        <div class="modal fade" id="difficultyModalWork" tabindex="-1" role="dialog" aria-labelledby="difficultyModalWorkLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="difficultyModalWorkLabel">Chọn cấp độ</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span>
                </button>
              </div>
              <div class="modal-body">
                <a href="beginner_task2.php?topic=work" class="btn btn-success difficulty-btn beginner">Beginner</a>
                <a href="intermediate_task2.php?topic=work" class="btn btn-warning difficulty-btn intermediate">Intermediate</a>
                <a href="advanced_task2.php?topic=work" class="btn btn-danger difficulty-btn advanced">Advanced</a>
              </div>
            </div>
          </div>
        </div>

        <!-- Topic 5: Technology -->
        <div class="topic-box">
          <div class="chart-type-header">
            <i class="fas fa-laptop chart-icon"></i> Công nghệ (Technology)
          </div>
          <div class="chart-type-body">
            <p>Hướng dẫn cách viết bài luận về tác động của công nghệ lên công việc, giao tiếp, hoặc xã hội. Chủ đề này thường xuất hiện dưới dạng Advantages/Disadvantages hoặc Discussion essays.</p>
            <ul>
              <li>Phân tích lợi ích và bất lợi của công nghệ</li>
              <li>Từ vựng mô tả tiến bộ và tác động công nghệ</li>
              <li>Cách tổ chức lập luận cân bằng</li>
              <li>Mẫu câu hữu ích cho bài viết</li>
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
                <a href="beginner_task2.php?topic=technology" class="btn btn-success difficulty-btn beginner">Beginner</a>
                <a href="intermediate_task2.php?topic=technology" class="btn btn-warning difficulty-btn intermediate">Intermediate</a>
                <a href="advanced_task2.php?topic=technology" class="btn btn-danger difficulty-btn advanced">Advanced</a>
              </div>
            </div>
          </div>
        </div>

        <!-- Topic 6: Science -->
        <div class="topic-box">
          <div class="chart-type-header">
            <i class="fas fa-flask chart-icon"></i> Khoa học (Science)
          </div>
          <div class="chart-type-body">
            <p>Học cách viết bài luận về vai trò của khoa học trong xã hội, như nghiên cứu y học, công nghệ sinh học, hoặc tác động của khoa học đến môi trường. Chủ đề này thường xuất hiện dưới dạng Discussion hoặc Opinion essays.</p>
            <ul>
              <li>Phân tích lợi ích và rủi ro của khoa học</li>
              <li>Từ vựng mô tả nghiên cứu và đổi mới</li>
              <li>Cách đưa ra lập luận logic</li>
              <li>Mẫu câu hữu ích cho bài viết</li>
            </ul>
          </div>
          <div class="chart-type-footer">
            <button type="button" class="btn btn-view" data-toggle="modal" data-target="#difficultyModalScience">Xem hướng dẫn</button>
          </div>
        </div>
        <!-- Modal for Science -->
        <div class="modal fade" id="difficultyModalScience" tabindex="-1" role="dialog" aria-labelledby="difficultyModalScienceLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="difficultyModalScienceLabel">Chọn cấp độ</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span>
                </button>
              </div>
              <div class="modal-body">
                <a href="beginner_task2.php?topic=science" class="btn btn-success difficulty-btn beginner">Beginner</a>
                <a href="intermediate_task2.php?topic=science" class="btn btn-warning difficulty-btn intermediate">Intermediate</a>
                <a href="advanced_task2.php?topic=science" class="btn btn-danger difficulty-btn advanced">Advanced</a>
              </div>
            </div>
          </div>
        </div>

        <!-- Topic 7: Advertising -->
        <div class="topic-box">
          <div class="chart-type-header">
            <i class="fas fa-ad chart-icon"></i> Quảng cáo (Advertising)
          </div>
          <div class="chart-type-body">
            <p>Hướng dẫn cách viết bài luận về ảnh hưởng của quảng cáo đến hành vi tiêu dùng, xã hội, hoặc trẻ em. Chủ đề này thường xuất hiện dưới dạng Advantages/Disadvantages hoặc Opinion essays.</p>
            <ul>
              <li>Phân tích tác động của quảng cáo</li>
              <li>Từ vựng mô tả marketing và truyền thông</li>
              <li>Cách tổ chức lập luận cân bằng</li>
              <li>Mẫu câu hữu ích cho bài viết</li>
            </ul>
          </div>
          <div class="chart-type-footer">
            <button type="button" class="btn btn-view" data-toggle="modal" data-target="#difficultyModalAdvertising">Xem hướng dẫn</button>
          </div>
        </div>
        <!-- Modal for Advertising -->
        <div class="modal fade" id="difficultyModalAdvertising" tabindex="-1" role="dialog" aria-labelledby="difficultyModalAdvertisingLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="difficultyModalAdvertisingLabel">Chọn cấp độ</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span>
                </button>
              </div>
              <div class="modal-body">
                <a href="beginner_task2.php?topic=advertising" class="btn btn-success difficulty-btn beginner">Beginner</a>
                <a href="intermediate_task2.php?topic=advertising" class="btn btn-warning difficulty-btn intermediate">Intermediate</a>
                <a href="advanced_task2.php?topic=advertising" class="btn btn-danger difficulty-btn advanced">Advanced</a>
              </div>
            </div>
          </div>
        </div>

        <!-- Topic 8: Environment -->
        <div class="topic-box">
          <div class="chart-type-header">
            <i class="fas fa-leaf chart-icon"></i> Môi trường (Environment)
          </div>
          <div class="chart-type-body">
            <p>Học cách viết bài luận về ô nhiễm, biến đổi khí hậu, hoặc các giải pháp bảo vệ môi trường. Chủ đề này thường xuất hiện dưới dạng Problem-Solution hoặc Causes/Effects essays.</p>
            <ul>
              <li>Phân tích nguyên nhân và giải pháp môi trường</li>
              <li>Từ vựng mô tả ô nhiễm và tính bền vững</li>
              <li>Cách đưa ra giải pháp khả thi</li>
              <li>Cấu trúc bài viết hiệu quả</li>
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
                <a href="beginner_task2.php?topic=environment" class="btn btn-success difficulty-btn beginner">Beginner</a>
                <a href="intermediate_task2.php?topic=environment" class="btn btn-warning difficulty-btn intermediate">Intermediate</a>
                <a href="advanced_task2.php?topic=environment" class="btn btn-danger difficulty-btn advanced">Advanced</a>
              </div>
            </div>
          </div>
        </div>

        <!-- Topic 9: Money -->
        <div class="topic-box">
          <div class="chart-type-header">
            <i class="fas fa-money-bill-wave chart-icon"></i> Tiền bạc (Money)
          </div>
          <div class="chart-type-body">
            <p>Hướng dẫn cách viết bài luận về vai trò của tiền bạc trong xã hội, quản lý tài chính cá nhân, hoặc tác động của sự giàu có. Chủ đề này thường xuất hiện dưới dạng Opinion hoặc Discussion essays.</p>
            <ul>
              <li>Phân tích tác động của tiền bạc</li>
              <li>Từ vựng mô tả tài chính và kinh tế</li>
              <li>Cách đưa ra lập luận logic</li>
              <li>Mẫu câu hữu ích cho bài viết</li>
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
                <a href="beginner_task2.php?topic=money" class="btn btn-success difficulty-btn beginner">Beginner</a>
                <a href="intermediate_task2.php?topic=money" class="btn btn-warning difficulty-btn intermediate">Intermediate</a>
                <a href="advanced_task2.php?topic=money" class="btn btn-danger difficulty-btn advanced">Advanced</a>
              </div>
            </div>
          </div>
        </div>

        <!-- Topic 10: Food & Drinks -->
        <div class="topic-box">
          <div class="chart-type-header">
            <i class="fas fa-utensils chart-icon"></i> Thực phẩm & Đồ uống (Food & Drinks)
          </div>
          <div class="chart-type-body">
            <p>Học cách viết bài luận về thói quen ăn uống, an toàn thực phẩm, hoặc tác động của thực phẩm đến sức khỏe. Chủ đề này thường xuất hiện dưới dạng Causes/Solutions hoặc Opinion essays.</p>
            <ul>
              <li>Phân tích thói quen ăn uống và sức khỏe</li>
              <li>Từ vựng mô tả dinh dưỡng và thực phẩm</li>
              <li>Cách đưa ra ví dụ thực tế</li>
              <li>Chiến lược tổ chức bài viết</li>
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
                <a href="beginner_task2.php?topic=food_drinks" class="btn btn-success difficulty-btn beginner">Beginner</a>
                <a href="intermediate_task2.php?topic=food_drinks" class="btn btn-warning difficulty-btn intermediate">Intermediate</a>
                <a href="advanced_task2.php?topic=food_drinks" class="btn btn-danger difficulty-btn advanced">Advanced</a>
              </div>
            </div>
          </div>
        </div>

        <!-- Topic 11: Sports & Leisure -->
        <div class="topic-box">
          <div class="chart-type-header">
            <i class="fas fa-futbol chart-icon"></i> Thể thao & Giải trí (Sports & Leisure)
          </div>
          <div class="chart-type-body">
            <p>Hướng dẫn cách viết bài luận về vai trò của thể thao và giải trí trong cuộc sống, sức khỏe, hoặc xã hội. Chủ đề này thường xuất hiện dưới dạng Opinion hoặc Advantages/Disadvantages essays.</p>
            <ul>
              <li>Phân tích lợi ích của thể thao và giải trí</li>
              <li>Từ vựng mô tả hoạt động thể chất và giải trí</li>
              <li>Cách tổ chức lập luận cân bằng</li>
              <li>Mẫu câu hữu ích cho bài viết</li>
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
                <a href="beginner_task2.php?topic=sports_leisure" class="btn btn-success difficulty-btn beginner">Beginner</a>
                <a href="intermediate_task2.php?topic=sports_leisure" class="btn btn-warning difficulty-btn intermediate">Intermediate</a>
                <a href="advanced_task2.php?topic=sports_leisure" class="btn btn-danger difficulty-btn advanced">Advanced</a>
              </div>
            </div>
          </div>
        </div>

        <!-- Topic 12: Art & Culture -->
        <div class="topic-box">
          <div class="chart-type-header">
            <i class="fas fa-paint-brush chart-icon"></i> Nghệ thuật & Văn hóa (Art & Culture)
          </div>
          <div class="chart-type-body">
            <p>Học cách viết bài luận về vai trò của nghệ thuật và văn hóa trong xã hội, bảo tồn di sản, hoặc tác động của văn hóa đại chúng. Chủ đề này thường xuất hiện dưới dạng Opinion hoặc Discussion essays.</p>
            <ul>
              <li>Phân tích vai trò của nghệ thuật và văn hóa</li>
              <li>Từ vựng mô tả di sản và sáng tạo</li>
              <li>Cách đưa ra lập luận logic</li>
              <li>Mẫu câu hữu ích cho bài viết</li>
            </ul>
          </div>
          <div class="chart-type-footer">
            <button type="button" class="btn btn-view" data-toggle="modal" data-target="#difficultyModalArtCulture">Xem hướng dẫn</button>
          </div>
        </div>
        <!-- Modal for Art & Culture -->
        <div class="modal fade" id="difficultyModalArtCulture" tabindex="-1" role="dialog" aria-labelledby="difficultyModalArtCultureLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="difficultyModalArtCultureLabel">Chọn cấp độ</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span>
                </button>
              </div>
              <div class="modal-body">
                <a href="beginner_task2.php?topic=art_culture" class="btn btn-success difficulty-btn beginner">Beginner</a>
                <a href="intermediate_task2.php?topic=art_culture" class="btn btn-warning difficulty-btn intermediate">Intermediate</a>
                <a href="advanced_task2.php?topic=art_culture" class="btn btn-danger difficulty-btn advanced">Advanced</a>
              </div>
            </div>
          </div>
        </div>

        <!-- Topic 13: Society -->
        <div class="topic-box">
          <div class="chart-type-header">
            <i class="fas fa-users chart-icon"></i> Xã hội (Society)
          </div>
          <div class="chart-type-body">
            <p>Học cách viết bài luận về bất bình đẳng, trách nhiệm cá nhân, hoặc thay đổi cộng đồng. Chủ đề này thường xuất hiện dưới dạng Opinion hoặc Discussion essays.</p>
            <ul>
              <li>Phân tích vai trò cá nhân và xã hội</li>
              <li>Từ vựng mô tả bình đẳng và cộng đồng</li>
              <li>Cách đưa ra lập luận cân bằng</li>
              <li>Mẫu câu hữu ích cho bài viết</li>
            </ul>
          </div>
          <div class="chart-type-footer">
            <button type="button" class="btn btn-view" data-toggle="modal" data-target="#difficultyModalSociety">Xem hướng dẫn</button>
          </div>
        </div>
        <!-- Modal for Society -->
        <div class="modal fade" id="difficultyModalSociety" tabindex="-1" role="dialog" aria-labelledby="difficultyModalSocietyLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="difficultyModalSocietyLabel">Chọn cấp độ</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span>
                </button>
              </div>
              <div class="modal-body">
                <a href="beginner_task2.php?topic=society" class="btn btn-success difficulty-btn beginner">Beginner</a>
                <a href="intermediate_task2.php?topic=society" class="btn btn-warning difficulty-btn intermediate">Intermediate</a>
                <a href="advanced_task2.php?topic=society" class="btn btn-danger difficulty-btn advanced">Advanced</a>
              </div>
            </div>
          </div>
        </div>

        <!-- Topic 14: Transport -->
        <div class="topic-box">
          <div class="chart-type-header">
            <i class="fas fa-bus chart-icon"></i> Giao thông (Transport)
          </div>
          <div class="chart-type-body">
            <p>Hướng dẫn cách viết bài luận về hệ thống giao thông, giao thông công cộng, hoặc tác động của giao thông đến môi trường. Chủ đề này thường xuất hiện dưới dạng Problem-Solution hoặc Advantages/Disadvantages essays.</p>
            <ul>
              <li>Phân tích lợi ích và vấn đề của giao thông</li>
              <li>Từ vựng mô tả phương tiện và cơ sở hạ tầng</li>
              <li>Cách đưa ra giải pháp khả thi</li>
              <li>Cấu trúc bài viết hiệu quả</li>
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
                <a href="beginner_task2.php?topic=transport" class="btn btn-success difficulty-btn beginner">Beginner</a>
                <a href="intermediate_task2.php?topic=transport" class="btn btn-warning difficulty-btn intermediate">Intermediate</a>
                <a href="advanced_task2.php?topic=transport" class="btn btn-danger difficulty-btn advanced">Advanced</a>
              </div>
            </div>
          </div>
        </div>

        <!-- Topic 15: Personality -->
        <div class="topic-box">
          <div class="chart-type-header">
            <i class="fas fa-user-friends chart-icon"></i> Tính cách (Personality)
          </div>
          <div class="chart-type-body">
            <p>Học cách viết bài luận về ảnh hưởng của tính cách đến thành công, mối quan hệ, hoặc sự phát triển cá nhân. Chủ đề này thường xuất hiện dưới dạng Opinion hoặc Discussion essays.</p>
            <ul>
              <li>Phân tích vai trò của tính cách</li>
              <li>Từ vựng mô tả đặc điểm cá nhân</li>
              <li>Cách đưa ra lập luận logic</li>
              <li>Mẫu câu hữu ích cho bài viết</li>
            </ul>
          </div>
          <div class="chart-type-footer">
            <button type="button" class="btn btn-view" data-toggle="modal" data-target="#difficultyModalPersonality">Xem hướng dẫn</button>
          </div>
        </div>
        <!-- Modal for Personality -->
        <div class="modal fade" id="difficultyModalPersonality" tabindex="-1" role="dialog" aria-labelledby="difficultyModalPersonalityLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="difficultyModalPersonalityLabel">Chọn cấp độ</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span>
                </button>
              </div>
              <div class="modal-body">
                <a href="beginner_task2.php?topic=personality" class="btn btn-success difficulty-btn beginner">Beginner</a>
                <a href="intermediate_task2.php?topic=personality" class="btn btn-warning difficulty-btn intermediate">Intermediate</a>
                <a href="advanced_task2.php?topic=personality" class="btn btn-danger difficulty-btn advanced">Advanced</a>
              </div>
            </div>
          </div>
        </div>

        <!-- Topic 16: Language -->
        <div class="topic-box">
          <div class="chart-type-header">
            <i class="fas fa-language chart-icon"></i> Ngôn ngữ (Language)
          </div>
          <div class="chart-type-body">
            <p>Hướng dẫn cách viết bài luận về vai trò của ngôn ngữ, học ngoại ngữ, hoặc bảo tồn ngôn ngữ địa phương. Chủ đề này thường xuất hiện dưới dạng Opinion hoặc Discussion essays.</p>
            <ul>
              <li>Phân tích lợi ích của việc học ngôn ngữ</li>
              <li>Từ vựng mô tả ngôn ngữ và giao tiếp</li>
              <li>Cách tổ chức lập luận cân bằng</li>
              <li>Mẫu câu hữu ích cho bài viết</li>
            </ul>
          </div>
          <div class="chart-type-footer">
            <button type="button" class="btn btn-view" data-toggle="modal" data-target="#difficultyModalLanguage">Xem hướng dẫn</button>
          </div>
        </div>
        <!-- Modal for Language -->
        <div class="modal fade" id="difficultyModalLanguage" tabindex="-1" role="dialog" aria-labelledby="difficultyModalLanguageLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="difficultyModalLanguageLabel">Chọn cấp độ</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span>
                </button>
              </div>
              <div class="modal-body">
                <a href="beginner_task2.php?topic=language" class="btn btn-success difficulty-btn beginner">Beginner</a>
                <a href="intermediate_task2.php?topic=language" class="btn btn-warning difficulty-btn intermediate">Intermediate</a>
                <a href="advanced_task2.php?topic=language" class="btn btn-danger difficulty-btn advanced">Advanced</a>
              </div>
            </div>
          </div>
        </div>

        <!-- Topic 17: Tourism -->
        <div class="topic-box">
          <div class="chart-type-header">
            <i class="fas fa-plane chart-icon"></i> Du lịch (Tourism)
          </div>
          <div class="chart-type-body">
            <p>Học cách viết bài luận về tác động của du lịch đến văn hóa, kinh tế, hoặc môi trường. Chủ đề này thường xuất hiện dưới dạng Advantages/Disadvantages hoặc Problem-Solution essays.</p>
            <ul>
              <li>Phân tích lợi ích và thách thức của du lịch</li>
              <li>Từ vựng mô tả du lịch và văn hóa</li>
              <li>Cách đưa ra giải pháp khả thi</li>
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
                <a href="beginner_task2.php?topic=tourism" class="btn btn-success difficulty-btn beginner">Beginner</a>
                <a href="intermediate_task2.php?topic=tourism" class="btn btn-warning difficulty-btn intermediate">Intermediate</a>
                <a href="advanced_task2.php?topic=tourism" class="btn btn-danger difficulty-btn advanced">Advanced</a>
              </div>
            </div>
          </div>
        </div>

        <!-- Topic 18: Gender -->
        <div class="topic-box">
          <div class="chart-type-header">
            <i class="fas fa-venus-mars chart-icon"></i> Giới tính (Gender)
          </div>
          <div class="chart-type-body">
            <p>Hướng dẫn cách viết bài luận về bình đẳng giới, vai trò giới trong xã hội, hoặc các vấn đề liên quan đến giới tính. Chủ đề này thường xuất hiện dưới dạng Opinion hoặc Discussion essays.</p>
            <ul>
              <li>Phân tích vai trò và bình đẳng giới</li>
              <li>Từ vựng mô tả giới tính và bình đẳng</li>
              <li>Cách đưa ra lập luận cân bằng</li>
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
                <a href="beginner_task2.php?topic=gender" class="btn btn-success difficulty-btn beginner">Beginner</a>
                <a href="intermediate_task2.php?topic=gender" class="btn btn-warning difficulty-btn intermediate">Intermediate</a>
                <a href="advanced_task2.php?topic=gender" class="btn btn-danger difficulty-btn advanced">Advanced</a>
              </div>
            </div>
          </div>
        </div>

      </div>

      <!-- Sidebar -->
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
            <h5>Các chủ đề thường gặp</h5>
          </div>
          <div class="card-body">
            <ul class="list-group list-group-flush">
              <li class="list-group-item"><a href="#difficultyModalEducation">Giáo dục (Education)</a></li>
              <li class="list-group-item"><a href="#difficultyModalCrime">Tội phạm (Crime)</a></li>
              <li class="list-group-item"><a href="#difficultyModalHealth">Sức khỏe (Health)</a></li>
              <li class="list-group-item"><a href="#difficultyModalWork">Công việc (Work)</a></li>
              <li class="list-group-item"><a href="#difficultyModalTechnology">Công nghệ (Technology)</a></li>
              <li class="list-group-item"><a href="#difficultyModalScience">Khoa học (Science)</a></li>
              <li class="list-group-item"><a href="#difficultyModalAdvertising">Quảng cáo (Advertising)</a></li>
              <li class="list-group-item"><a href="#difficultyModalEnvironment">Môi trường (Environment)</a></li>
              <li class="list-group-item"><a href="#difficultyModalMoney">Tiền bạc (Money)</a></li>
              <li class="list-group-item"><a href="#difficultyModalFoodDrinks">Thực phẩm & Đồ uống (Food & Drinks)</a></li>
              <li class="list-group-item"><a href="#difficultyModalSportsLeisure">Thể thao & Giải trí (Sports & Leisure)</a></li>
              <li class="list-group-item"><a href="#difficultyModalArtCulture">Nghệ thuật & Văn hóa (Art & Culture)</a></li>
              <li class="list-group-item"><a href="#difficultyModalSociety">Xã hội (Society)</a></li>
              <li class="list-group-item"><a href="#difficultyModalTransport">Giao thông (Transport)</a></li>
              <li class="list-group-item"><a href="#difficultyModalPersonality">Tính cách (Personality)</a></li>
              <li class="list-group-item"><a href="#difficultyModalLanguage">Ngôn ngữ (Language)</a></li>
              <li class="list-group-item"><a href="#difficultyModalTourism">Du lịch (Tourism)</a></li>
              <li class="list-group-item"><a href="#difficultyModalGender">Giới tính (Gender)</a></li>
            </ul>
          </div>
        </div>

        <div class="card mt-4">
          <div class="card-header">
            <h5>Mẹo cho người mới</h5>
          </div>
          <div class="card-body">
            <ol>
              <li>Đọc kỹ đề bài</li>
              <li>Xác định dạng bài luận</li>
              <li>Lập dàn ý trước khi viết</li>
              <li>Sử dụng từ nối cơ bản</li>
              <li>Kiểm tra lỗi ngữ pháp</li>
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
                <a href="vocabulary_task2.php">Từ vựng Task 2 cơ bản</a>
              </li>
              <li class="list-group-item">
                <a href="common_mistakes_task2.php">Lỗi thường gặp</a>
              </li>
              <li class="list-group-item">
                <a href="time_management_task2.php">Quản lý thời gian</a>
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