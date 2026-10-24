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
  <title>Từ vựng IELTS Writing Task 1 - IELTS Writing Practice</title>
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
      color: #主
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
      background-color: #0d47a1;
    }
    /* Thiết kế cho Lexibot */
    .lexibot-chat-button {
      position: fixed;
      bottom: 90px;
      right: 20px;
      width: 60px;
      height: 60px;
      background-color: #6B46C1;
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
      background-color: #553C9A;
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
        <h1 class="main-title">Từ vựng IELTS Writing Task 1</h1>
        <p class="sub-title">Nâng cao vốn từ vựng với các từ và cụm từ chuyên dụng cho IELTS Writing Task 1, được phân loại theo loại biểu đồ và chủ đề phổ biến.</p>
      </div>
    </div>
  </div>

  <!-- Main Content -->
  <div class="container">
    <div class="row">
      <div class="col-md-8">
        <div class="guide-content">
          <h2>Danh sách từ vựng Writing Task 1</h2>

          <h3>1. Từ vựng mô tả xu hướng (Line Graph)</h3>
          <table class="table vocabulary-table">
            <tr>
              <th>Từ vựng</th>
              <th>Nghĩa</th>
              <th>Ví dụ</th>
            </tr>
            <tr>
              <td>Surge</td>
              <td>Tăng mạnh</td>
              <td>Internet usage surged from 2010 to 2015.</td>
            </tr>
            <tr>
              <td>Plummet</td>
              <td>Giảm mạnh</td>
              <td>Sales plummeted during the economic crisis.</td>
            </tr>
            <tr>
              <td>Fluctuate</td>
              <td>Biến động</td>
              <td>Temperatures fluctuated throughout the year.</td>
            </tr>
            <tr>
              <td>Plateau</td>
              <td>Ổn định, không đổi</td>
              <td>Population growth plateaued after 2018.</td>
            </tr>
            <tr>
              <td>Soar</td>
              <td>Tăng vọt</td>
              <td>Air travel costs soared in the summer months.</td>
            </tr>
            <tr>
              <td>Decline</td>
              <td>Giảm dần</td>
              <td>Coal consumption declined steadily over the decade.</td>
            </tr>
            <tr>
              <td>Stabilize</td>
              <td>Ổn định</td>
              <td>Prices stabilized after the initial spike.</td>
            </tr>
            <tr>
              <td>Peak</td>
              <td>Đạt đỉnh</td>
              <td>Unemployment peaked in 2009 during the recession.</td>
            </tr>
            <tr>
              <td>Dip</td>
              <td>Giảm nhẹ</td>
              <td>Production dipped slightly in the third quarter.</td>
            </tr>
            <tr>
              <td>Recover</td>
              <td>Phục hồi</td>
              <td>The economy recovered rapidly after 2020.</td>
            </tr>
            <tr>
              <td>Escalate</td>
              <td>Tăng nhanh</td>
              <td>Healthcare costs escalated over the past five years.</td>
            </tr>
            <tr>
              <td>Level off</td>
              <td>Ổn định ở mức</td>
              <td>Inflation levels leveled off by mid-2023.</td>
            </tr>
          </table>

          <h3>2. Từ vựng so sánh (Bar Chart)</h3>
          <table class="table vocabulary-table">
            <tr>
              <th>Từ vựng</th>
              <th>Nghĩa</th>
              <th>Ví dụ</th>
            </tr>
            <tr>
              <td>Exceed</td>
              <td>Vượt qua</td>
              <td>Malaysia’s spending exceeded Thailand’s in 2020.</td>
            </tr>
            <tr>
              <td>Outnumber</td>
              <td>Nhiều hơn</td>
              <td>Women outnumbered men in the arts program.</td>
            </tr>
            <tr>
              <td>Comparable</td>
              <td>Tương đương</td>
              <td>The figures for 2019 and 2020 were comparable.</td>
            </tr>
            <tr>
              <td>Disparity</td>
              <td>Sự chênh lệch</td>
              <td>There was a notable disparity between urban and rural incomes.</td>
            </tr>
            <tr>
              <td>Surpass</td>
              <td>Vượt xa</td>
              <td>China’s exports surpassed those of Japan in 2022.</td>
            </tr>
            <tr>
              <td>Contrast</td>
              <td>Tương phản</td>
              <td>Urban areas contrast sharply with rural regions in income levels.</td>
            </tr>
            <tr>
              <td>Equal</td>
              <td>Bằng nhau</td>
              <td>Investment in both sectors equaled $5 million.</td>
            </tr>
            <tr>
              <td>Outstrip</td>
              <td>Vượt trội</td>
              <td>Online sales outstripped in-store purchases in 2021.</td>
            </tr>
            <tr>
              <td>Similarity</td>
              <td>Sự tương đồng</td>
              <td>There was a similarity in spending patterns across age groups.</td>
            </tr>
            <tr>
              <td>Gap</td>
              <td>Khoảng cách</td>
              <td>The gap between rich and poor widened in 2020.</td>
            </tr>
            <tr>
              <td>Rank</td>
              <td>Xếp hạng</td>
              <td>Germany ranked highest in renewable energy adoption.</td>
            </tr>
            <tr>
              <td>Proportion</td>
              <td>Tỷ lệ</td>
              <td>A large proportion of funds went to healthcare.</td>
            </tr>
          </table>

          <h3>3. Từ vựng mô tả tỷ lệ (Pie Chart)</h3>
          <table class="table vocabulary-table">
            <tr>
              <th>Từ vựng</th>
              <th>Nghĩa</th>
              <th>Ví dụ</th>
            </tr>
            <tr>
              <td>Constitute</td>
              <td>Chiếm, tạo thành</td>
              <td>Food constituted 35% of household spending.</td>
            </tr>
            <tr>
              <td>Account for</td>
              <td>Chiếm tỷ lệ</td>
              <td>Transport accounted for 20% of emissions.</td>
            </tr>
            <tr>
              <td>Predominate</td>
              <td>Chiếm ưu thế</td>
              <td>Renewable energy predominated in the energy mix.</td>
            </tr>
            <tr>
              <td>Segment</td>
              <td>Phân khúc</td>
              <td>Entertainment was the smallest segment at 5%.</td>
            </tr>
            <tr>
              <td>Comprise</td>
              <td>Bao gồm</td>
              <td>Education comprised 25% of the budget.</td>
            </tr>
            <tr>
              <td>Represent</td>
              <td>Đại diện</td>
              <td>Housing represented the largest share of expenditure.</td>
            </tr>
            <tr>
              <td>Contribute</td>
              <td>Đóng góp</td>
              <td>Industry contributed 40% to GDP.</td>
            </tr>
            <tr>
              <td>Portion</td>
              <td>Phần</td>
              <td>A significant portion of income went to taxes.</td>
            </tr>
            <tr>
              <td>Dominate</td>
              <td>Thống trị</td>
              <td>Oil exports dominated the country’s economy.</td>
            </tr>
            <tr>
              <td>Share</td>
              <td>Phần chia</td>
              <td>Healthcare had a 15% share of total spending.</td>
            </tr>
            <tr>
              <td>Slice</td>
              <td>Phần nhỏ</td>
              <td>Leisure activities formed a small slice of the budget.</td>
            </tr>
            <tr>
              <td>Make up</td>
              <td>Tạo thành</td>
              <td>Imports made up 30% of total trade.</td>
            </tr>
          </table>

          <h3>4. Từ vựng mô tả quy trình (Process Diagram)</h3>
          <table class="table vocabulary-table">
            <tr>
              <th>Từ vựng</th>
              <th>Nghĩa</th>
              <th>Ví dụ</th>
            </tr>
            <tr>
              <td>Extract</td>
              <td>Khai thác</td>
              <td>Limestone is extracted from quarries.</td>
            </tr>
            <tr>
              <td>Transform</td>
              <td>Chuyển đổi</td>
              <td>Raw materials are transformed into cement.</td>
            </tr>
            <tr>
              <td>Undergo</td>
              <td>Trải qua</td>
              <td>The mixture undergoes heating in a kiln.</td>
            </tr>
            <tr>
              <td>Yield</td>
              <td>Tạo ra</td>
              <td>The process yields high-quality products.</td>
            </tr>
            <tr>
              <td>Process</td>
              <td>Xử lý</td>
              <td>Grain is processed into flour.</td>
            </tr>
            <tr>
              <td>Refine</td>
              <td>Tinh chế</td>
              <td>Crude oil is refined into gasoline.</td>
            </tr>
            <tr>
              <td>Convert</td>
              <td>Chuyển đổi</td>
              <td>Biomass is converted into energy.</td>
            </tr>
            <tr>
              <td>Assemble</td>
              <td>Lắp ráp</td>
              <td>Components are assembled into a final product.</td>
            </tr>
            <tr>
              <td>Distribute</td>
              <td>Phân phối</td>
              <td>Goods are distributed to retailers.</td>
            </tr>
            <tr>
              <td>Harvest</td>
              <td>Thu hoạch</td>
              <td>Crops are harvested before processing.</td>
            </tr>
            <tr>
              <td>Package</td>
              <td>Đóng gói</td>
              <td>Products are packaged for sale.</td>
            </tr>
            <tr>
              <td>Recycle</td>
              <td>Tái chế</td>
              <td>Plastic is recycled into new materials.</td>
            </tr>
          </table>

          <h3>5. Từ vựng mô tả thay đổi không gian (Map)</h3>
          <table class="table vocabulary-table">
            <tr>
              <th>Từ vựng</th>
              <th>Nghĩa</th>
              <th>Ví dụ</th>
            </tr>
            <tr>
              <td>Relocate</td>
              <td>Di dời</td>
              <td>The factory was relocated to the outskirts.</td>
            </tr>
            <tr>
              <td>Expand</td>
              <td>Mở rộng</td>
              <td>The town expanded significantly by 2020.</td>
            </tr>
            <tr>
              <td>Demolish</td>
              <td>Phá dỡ</td>
              <td>Old buildings were demolished to build a mall.</td>
            </tr>
            <tr>
              <td>Construct</td>
              <td>Xây dựng</td>
              <td>A new school was constructed in the south.</td>
            </tr>
            <tr>
              <td>Develop</td>
              <td>Phát triển</td>
              <td>The area was developed into a commercial hub.</td>
            </tr>
            <tr>
              <td>Renovate</td>
              <td>Tu sửa</td>
              <td>The old library was renovated in 2021.</td>
            </tr>
            <tr>
              <td>Replace</td>
              <td>Thay thế</td>
              <td>A park replaced the abandoned factory site.</td>
            </tr>
            <tr>
              <td>Extend</td>
              <td>Kéo dài</td>
              <td>The railway was extended to the north.</td>
            </tr>
            <tr>
              <td>Convert</td>
              <td>Chuyển đổi</td>
              <td>Farmland was converted into residential areas.</td>
            </tr>
            <tr>
              <td>Erect</td>
              <td>Dựng lên</td>
              <td>A monument was erected in the town center.</td>
            </tr>
            <tr>
              <td>Redesign</td>
              <td>Thiết kế lại</td>
              <td>The park was redesigned to include a playground.</td>
            </tr>
            <tr>
              <td>Upgrade</td>
              <td>Nâng cấp</td>
              <td>The road network was upgraded in 2022.</td>
            </tr>
          </table>

          <h3>6. Từ vựng mô tả bảng số liệu (Table)</h3>
          <table class="table vocabulary-table">
            <tr>
              <th>Từ vựng</th>
              <th>Nghĩa</th>
              <th>Ví dụ</th>
            </tr>
            <tr>
              <td>Illustrate</td>
              <td>Minh họa</td>
              <td>The table illustrates spending patterns across regions.</td>
            </tr>
            <tr>
              <td>Highlight</td>
              <td>Làm nổi bật</td>
              <td>The data highlights a rise in urban population.</td>
            </tr>
            <tr>
              <td>Compare</td>
              <td>So sánh</td>
              <td>The table compares literacy rates between 2010 and 2020.</td>
            </tr>
            <tr>
              <td>Reveal</td>
              <td>Tiết lộ</td>
              <td>The figures reveal a decline in unemployment.</td>
            </tr>
            <tr>
              <td>Indicate</td>
              <td>Chỉ ra</td>
              <td>The table indicates higher sales in the second quarter.</td>
            </tr>
            <tr>
              <td>Vary</td>
              <td>Thay đổi</td>
              <td>Income levels vary significantly across provinces.</td>
            </tr>
            <tr>
              <td>Correlate</td>
              <td>Tương quan</td>
              <td>Education levels correlate with employment rates.</td>
            </tr>
            <tr>
              <td>Summarize</td>
              <td>Tóm tắt</td>
              <td>The table summarizes energy consumption by sector.</td>
            </tr>
            <tr>
              <td>Demonstrate</td>
              <td>Thể hiện</td>
              <td>The data demonstrates a shift in consumer preferences.</td>
            </tr>
            <tr>
              <td>Reflect</td>
              <td>Phản ánh</td>
              <td>The table reflects changes in population density.</td>
            </tr>
            <tr>
              <td>Disclose</td>
              <td>Bộc lộ</td>
              <td>The statistics disclose a rise in healthcare costs.</td>
            </tr>
            <tr>
              <td>Contrast</td>
              <td>Tương phản</td>
              <td>The table contrasts export volumes between countries.</td>
            </tr>
          </table>
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