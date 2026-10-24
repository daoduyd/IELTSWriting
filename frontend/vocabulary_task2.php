<?php
session_start();
// Kiểm tra người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <title>Từ vựng IELTS Writing Task 2 - IELTS Writing Practice</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
  <style>
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
      background-color: #1565c0;
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
    .vn-flag {
      height: 20px;
      margin-right: 15px;
      cursor: pointer;
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
    .vocabulary-table th, .vocabulary-table td {
      border: 1px solid #dee2e6;
      padding: 10px;
    }
    .vocabulary-table th {
      background-color: #1565c0;
      color: white;
    }
    /* Thiết kế cho NotebookLM */
    .notebooklm-chat-button {
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
    .notebooklm-chat-button:hover {
      background-color: #1565c0;
    }
    /* Thiết kế cho Lexibot */
    .lexibot-chat-button {
      position: fixed;
      bottom: 90px;
      right: 20px;
      width: 60px;
      height: 60px;
      background-color: #28a745;
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
    .lexibot-chat-button:hover {
      background-color: #28a745;
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
        <h1 class="main-title">Từ vựng IELTS Writing Task 2</h1>
        <p class="sub-title">Nâng cao vốn từ vựng với các từ và cụm từ chuyên dụng cho IELTS Writing Task 2, được phân loại theo loại bài luận và chủ đề phổ biến.</p>
      </div>
    </div>
  </div>

  <!-- Main Content -->
  <div class="container">
    <div class="row">
      <div class="col-md-8">
        <div class="guide-content">
          <h2>Danh sách từ vựng Writing Task 2</h2>

          <h3>1. Từ vựng cho bài luận quan điểm (Opinion Essay)</h3>
          <table class="table vocabulary-table">
            <tr>
              <th>Từ vựng</th>
              <th>Nghĩa</th>
              <th>Ví dụ</th>
            </tr>
            <tr>
              <td>Advocate</td>
              <td>Ủng hộ</td>
              <td>I advocate stricter laws to protect the environment.</td>
            </tr>
            <tr>
              <td>Contend</td>
              <td>Khẳng định</td>
              <td>Some contend that technology isolates individuals.</td>
            </tr>
            <tr>
              <td>Undermine</td>
              <td>Làm suy yếu</td>
              <td>Excessive screen time can undermine social skills.</td>
            </tr>
            <tr>
              <td>Substantiate</td>
              <td>Chứng minh</td>
              <td>Research substantiates the benefits of early education.</td>
            </tr>
            <tr>
              <td>Assert</td>
              <td>Quả quyết</td>
              <td>Experts assert that recycling reduces waste significantly.</td>
            </tr>
            <tr>
              <td>Refute</td>
              <td>Bác bỏ</td>
              <td>I refute the claim that social media is entirely harmful.</td>
            </tr>
            <tr>
              <td>Endorse</td>
              <td>Tán thành</td>
              <td>I endorse policies promoting renewable energy.</td>
            </tr>
            <tr>
              <td>Justify</td>
              <td>Bào chữa</td>
              <td>High taxes are justified to fund public services.</td>
            </tr>
            <tr>
              <td>Persuade</td>
              <td>Thuyết phục</td>
              <td>The campaign persuaded citizens to vote for change.</td>
            </tr>
            <tr>
              <td>Dispute</td>
              <td>Tranh cãi</td>
              <td>Some dispute the effectiveness of online learning.</td>
            </tr>
            <tr>
              <td>Validate</td>
              <td>Xác nhận</td>
              <td>Studies validate the link between diet and health.</td>
            </tr>
            <tr>
              <td>Oppose</td>
              <td>Phản đối</td>
              <td>Many oppose the legalization of recreational cannabis.</td>
            </tr>
          </table>

          <h3>2. Từ vựng cho bài luận thảo luận (Discussion Essay)</h3>
          <table class="table vocabulary-table">
            <tr>
              <th>Từ vựng</th>
              <th>Nghĩa</th>
              <th>Ví dụ</th>
            </tr>
            <tr>
              <td>Proponent</td>
              <td>Người ủng hộ</td>
              <td>Proponents of globalization argue it boosts economies.</td>
            </tr>
            <tr>
              <td>Counterargument</td>
              <td>Lập luận phản bác</td>
              <td>A counterargument is that globalization harms local cultures.</td>
            </tr>
            <tr>
              <td>Reconcile</td>
              <td>Hòa giải</td>
              <td>It is challenging to reconcile growth with environmental protection.</td>
            </tr>
            <tr>
              <td>Concede</td>
              <td>Thừa nhận</td>
              <td>I concede that technology has some drawbacks.</td>
            </tr>
            <tr>
              <td>Advocate</td>
              <td>Ủng hộ</td>
              <td>Some advocate for free education to reduce inequality.</td>
            </tr>
            <tr>
              <td>Controversy</td>
              <td>Tranh cãi</td>
              <td>The policy sparked controversy among citizens.</td>
            </tr>
            <tr>
              <td>Balance</td>
              <td>Cân bằng</td>
              <td>We must balance economic progress with social welfare.</td>
            </tr>
            <tr>
              <td>Critic</td>
              <td>Nhà phê bình</td>
              <td>Critics argue that automation reduces job opportunities.</td>
            </tr>
            <tr>
              <td>Perspective</td>
              <td>Quan điểm</td>
              <td>Both perspectives on urban development are valid.</td>
            </tr>
            <tr>
              <td>Compromise</td>
              <td>Thỏa hiệp</td>
              <td>A compromise is needed to satisfy both parties.</td>
            </tr>
            <tr>
              <td>Debate</td>
              <td>Tranh luận</td>
              <td>The debate over privacy rights continues.</td>
            </tr>
            <tr>
              <td>Consensus</td>
              <td>Sự đồng thuận</td>
              <td>A consensus was reached on climate action.</td>
            </tr>
          </table>

          <h3>3. Từ vựng cho bài luận nguyên nhân - giải pháp (Cause-Solution Essay)</h3>
          <table class="table vocabulary-table">
            <tr>
              <th>Từ vựng</th>
              <th>Nghĩa</th>
              <th>Ví dụ</th>
            </tr>
            <tr>
              <td>Exacerbate</td>
              <td>Làm trầm trọng</td>
              <td>Pollution exacerbates health issues in urban areas.</td>
            </tr>
            <tr>
              <td>Mitigate</td>
              <td>Giảm thiểu</td>
              <td>Renewable energy can mitigate climate change impacts.</td>
            </tr>
            <tr>
              <td>Perpetuate</td>
              <td>Duy trì, kéo dài</td>
              <td>Poverty perpetuates educational inequality.</td>
            </tr>
            <tr>
              <td>Remedy</td>
              <td>Khắc phục</td>
              <td>Subsidies are a remedy for unemployment.</td>
            </tr>
            <tr>
              <td>Trigger</td>
              <td>Kích hoạt</td>
              <td>Economic instability triggered widespread protests.</td>
            </tr>
            <tr>
              <td>Alleviate</td>
              <td>Làm dịu</td>
              <td>Charity programs alleviate poverty in rural areas.</td>
            </tr>
            <tr>
              <td>Contribute</td>
              <td>Đóng góp</td>
              <td>Deforestation contributes to global warming.</td>
            </tr>
            <tr>
              <td>Address</td>
              <td>Giải quyết</td>
              <td>Policies must address the root causes of crime.</td>
            </tr>
            <tr>
              <td>Stem from</td>
              <td>Bắt nguồn từ</td>
              <td>Many social issues stem from economic disparities.</td>
            </tr>
            <tr>
              <td>Resolve</td>
              <td>Giải quyết</td>
              <td>International cooperation can resolve water shortages.</td>
            </tr>
            <tr>
              <td>Aggravate</td>
              <td>Làm nghiêm trọng</td>
              <td>Inefficient policies aggravate traffic congestion.</td>
            </tr>
            <tr>
              <td>Implement</td>
              <td>Thực hiện</td>
              <td>Governments must implement sustainable solutions.</td>
            </tr>
          </table>

          <h3>4. Từ vựng cho bài luận lợi ích - bất lợi (Advantage-Disadvantage Essay)</h3>
          <table class="table vocabulary-table">
            <tr>
              <th>Từ vựng</th>
              <th>Nghĩa</th>
              <th>Ví dụ</th>
            </tr>
            <tr>
              <td>Outweigh</td>
              <td>Vượt trội hơn</td>
              <td>The benefits of technology outweigh its drawbacks.</td>
            </tr>
            <tr>
              <td>Detrimental</td>
              <td>Có hại</td>
              <td>Screen time is detrimental to mental health.</td>
            </tr>
            <tr>
              <td>Facilitate</td>
              <td>Thúc đẩy</td>
              <td>Online learning facilitates access to education.</td>
            </tr>
            <tr>
              <td>Hinder</td>
              <td>Cản trở</td>
              <td>Lack of funding hinders scientific research.</td>
            </tr>
            <tr>
              <td>Enhance</td>
              <td>Nâng cao</td>
              <td>Technology enhances productivity in workplaces.</td>
            </tr>
            <tr>
              <td>Compromise</td>
              <td>Làm tổn hại</td>
              <td>Overuse of smartphones compromises sleep quality.</td>
            </tr>
            <tr>
              <td>Promote</td>
              <td>Khuyến khích</td>
              <td>Urbanization promotes economic growth.</td>
            </tr>
            <tr>
              <td>Impede</td>
              <td>Cản trở</td>
              <td>Poor infrastructure impedes development.</td>
            </tr>
            <tr>
              <td>Benefit</td>
              <td>Lợi ích</td>
              <td>Telecommuting offers benefits like flexibility.</td>
            </tr>
            <tr>
              <td>Drawback</td>
              <td>Nhược điểm</td>
              <td>A major drawback of automation is job loss.</td>
            </tr>
            <tr>
              <td>Enable</td>
              <td>Cho phép</td>
              <td>Internet access enables global communication.</td>
            </tr>
            <tr>
              <td>Undermine</td>
              <td>Làm suy yếu</td>
              <td>Social media can undermine face-to-face interaction.</td>
            </tr>
          </table>

          <h3>5. Từ vựng theo chủ đề phổ biến (Environment)</h3>
          <table class="table vocabulary-table">
            <tr>
              <th>Từ vựng</th>
              <th>Nghĩa</th>
              <th>Ví dụ</th>
            </tr>
            <tr>
              <td>Sustainable</td>
              <td>Bền vững</td>
              <td>Sustainable practices are crucial for future generations.</td>
            </tr>
            <tr>
              <td>Degradation</td>
              <td>Suy thoái</td>
              <td>Deforestation causes environmental degradation.</td>
            </tr>
            <tr>
              <td>Conservation</td>
              <td>Bảo tồn</td>
              <td>Conservation efforts protect endangered species.</td>
            </tr>
            <tr>
              <td>Carbon footprint</td>
              <td>Dấu chân carbon</td>
              <td>Reducing our carbon footprint combats climate change.</td>
            </tr>
            <tr>
              <td>Pollution</td>
              <td>Ô nhiễm</td>
              <td>Air pollution poses a threat to public health.</td>
            </tr>
            <tr>
              <td>Renewable</td>
              <td>Tái tạo</td>
              <td>Renewable energy sources reduce emissions.</td>
            </tr>
            <tr>
              <td>Biodiversity</td>
              <td>Đa dạng sinh học</td>
              <td>Protecting biodiversity is essential for ecosystems.</td>
            </tr>
            <tr>
              <td>Deforestation</td>
              <td>Phá rừng</td>
              <td>Deforestation threatens wildlife habitats.</td>
            </tr>
            <tr>
              <td>Emission</td>
              <td>Khí thải</td>
              <td>Industrial emissions contribute to global warming.</td>
            </tr>
            <tr>
              <td>Preservation</td>
              <td>Bảo vệ</td>
              <td>Preservation of natural resources is critical.</td>
            </tr>
            <tr>
              <td>Ecosystem</td>
              <td>Hệ sinh thái</td>
              <td>Marine ecosystems are harmed by plastic waste.</td>
            </tr>
            <tr>
              <td>Recycle</td>
              <td>Tái chế</td>
              <td>Recycling reduces the demand for raw materials.</td>
            </tr>
          </table>
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