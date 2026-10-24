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
  <title>Lỗi thường gặp trong IELTS Writing Task 1 - IELTS Writing Practice</title>
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
    /* Thiết kế cho thanh navigation giống index.php */
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
      color: #e74c3c; /* Màu đỏ cho "IELTS" */
    }
    .navbar-brand span.blue {
      color: #1565c0; /* Màu xanh cho "Writing Pro" */
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
    .example-box {
      background-color: #f8f9fa;
      padding: 15px;
      border-left: 4px solid #1565c0;
      margin-bottom: 20px;
    }
    .error-text {
      color: #dc3545;
      font-weight: bold;
    }
    .correct-text {
      color: #28a745;
      font-weight: bold;
    }
    /* Thiết kế cho phần tương tác với NotebookLM */
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
    .notebooklm-interaction {
      position: fixed;
      bottom: 90px;
      right: 20px;
      width: 350px;
      background-color: white;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
      display: none;
      z-index: 1000;
    }
    .notebooklm-interaction.active {
      display: block;
    }
    .notebooklm-interaction-header {
      background-color: #1565c0;
      color: white;
      padding: 10px 15px;
      border-top-left-radius: 10px;
      border-top-right-radius: 10px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .notebooklm-interaction-header h2 {
      font-size: 1.2rem;
      margin: 0;
    }
    .notebooklm-interaction-header .close-chat {
      cursor: pointer;
      font-size: 1.2rem;
    }
    .notebooklm-interaction-body {
      padding: 15px;
    }
    .notebooklm-interaction .form-group {
      margin-bottom: 15px;
    }
    .notebooklm-interaction textarea {
      height: 100px;
      resize: none;
    }
    .notebooklm-interaction .btn-view {
      width: 100%;
    }
    .notebooklm-instruction {
      background-color: #f8f9fa;
      padding: 10px;
      border-left: 4px solid #1565c0;
      margin-top: 15px;
    }
    .notebooklm-instruction p {
      margin: 0;
    }
  </style>
</head>
<body>
  <!-- Navigation Bar (Giống hệt index.php) -->
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
              <!-- Writing Task 1 -->
              <div class="dropdown-submenu">
                <a class="dropdown-item active dropdown-toggle" href="#" id="task1Dropdown" data-toggle="dropdown">Writing Task 1</a>
                <div class="dropdown-menu">
                  <a class="dropdown-item" href="lessons_task1_types.php">Các dạng bài</a>
                  <a class="dropdown-item" href="lessons_task1_topics.php">Chủ đề</a>
                </div>
              </div>
              <!-- Writing Task 2 -->
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
        <h1 class="main-title">Lỗi thường gặp trong IELTS Writing Task 1</h1>
        <p class="sub-title">Tìm hiểu những lỗi phổ biến khi viết bài IELTS Writing Task 1 và cách khắc phục để đạt điểm cao hơn.</p>
      </div>
    </div>
  </div>

  <!-- Main Content -->
  <div class="container">
    <div class="row">
      <div class="col-md-8">
        <div class="guide-content">
          <h2>Các lỗi thường gặp và cách khắc phục</h2>

          <h3>1. Không viết đoạn tổng quan (Overview)</h3>
          <p><strong>Lỗi</strong>: Nhiều thí sinh bỏ qua đoạn tổng quan, dẫn đến bài viết thiếu sự khái quát tổng thể, làm giảm điểm "Task Achievement" vì không đáp ứng đầy đủ yêu cầu của bài thi.</p>
          <p><strong>Ví dụ sai</strong>: <span class="error-text">In 2010, the population was 5 million. In 2015, it increased to 6 million. In 2020, it reached 7 million...</span></p>
          <p><strong>Nguyên nhân</strong>: Thí sinh thường tập trung mô tả chi tiết mà quên rằng đoạn tổng quan là phần quan trọng để giới thiệu xu hướng chính. Điều này có thể do thiếu hiểu biết về cấu trúc bài hoặc không lập kế hoạch trước khi viết.</p>
          <p><strong>Khắc phục</strong>: Luôn dành 2-3 câu ở đoạn đầu để viết tổng quan, nêu rõ xu hướng chung hoặc điểm nổi bật của biểu đồ/sơ đồ. Sử dụng từ ngữ như "overall", "in general", hoặc "the main trend is" để bắt đầu. Đảm bảo đoạn tổng quan không chứa số liệu cụ thể mà tập trung vào bức tranh tổng thể.</p>
          <p><strong>Ví dụ đúng</strong>: <span class="correct-text">Overall, the population exhibited a consistent upward trend over the decade, with the most notable increase occurring between 2015 and 2020.</span></p>
          <p><strong>Mẹo</strong>: Luyện tập viết tổng quan cho các loại biểu đồ (biểu đồ cột, đường, tròn, quy trình) trong 2-3 phút để làm quen với cách khái quát thông tin nhanh chóng.</p>

          <h3>2. Sao chép toàn bộ dữ liệu từ biểu đồ</h3>
          <p><strong>Lỗi</strong>: Thí sinh liệt kê tất cả số liệu mà không phân tích hoặc so sánh, khiến bài viết trở thành bản sao của biểu đồ, không thể hiện kỹ năng ngôn ngữ.</p>
          <p><strong>Ví dụ sai</strong>: <span class="error-text">In 2010, sales were $100. In 2011, sales were $120. In 2012, sales were $130. In 2013, sales were $150...</span></p>
          <p><strong>Nguyên nhân</strong>: Thiếu kỹ năng chọn lọc thông tin quan trọng và không hiểu rằng IELTS yêu cầu phân tích, không phải sao chép. Một số thí sinh cũng lo sợ bỏ sót thông tin nên cố gắng liệt kê hết mọi số liệu.</p>
          <p><strong>Khắc phục</strong>: Chọn 2-3 dữ liệu quan trọng nhất (điểm cao nhất, thấp nhất, hoặc thay đổi lớn) và so sánh chúng để làm rõ xu hướng. Sử dụng từ nối (e.g., "meanwhile", "in contrast") để tạo sự mạch lạc. Tránh liệt kê liên tục mà không có phân tích.</p>
          <p><strong>Ví dụ đúng</strong>: <span class="correct-text">Sales rose steadily from $100 in 2010 to $150 in 2013, with the largest annual increase of $30 observed in 2012.</span></p>
          <p><strong>Mẹo</strong>: Luyện tập viết 2 đoạn thân bài, mỗi đoạn tập trung vào một giai đoạn hoặc khía cạnh chính của dữ liệu. Dành 1 phút để xác định các điểm nổi bật trước khi viết.</p>

          <h3>3. Sử dụng từ vựng không phù hợp</h3>
          <p><strong>Lỗi</strong>: Dùng từ không chính xác để mô tả xu hướng hoặc tỷ lệ, làm giảm điểm "Lexical Resource" (từ vựng).</p>
          <p><strong>Ví dụ sai</strong>: <span class="error-text">The number of students jumped to 50%.</span> ("Jump" thường dùng cho số lượng, không phù hợp với phần trăm.)</p>
          <p><strong>Nguyên nhân</strong>: Thiếu vốn từ vựng chuyên biệt hoặc không hiểu ngữ cảnh sử dụng. Một số thí sinh lạm dụng từ vựng nâng cao mà không nắm rõ nghĩa, dẫn đến sai lệch.</p>
          <p><strong>Khắc phục</strong>: Học các từ vựng phù hợp với từng loại dữ liệu (số lượng: increase/decrease, phần trăm: rise/fall, tỷ lệ: proportion). Tránh lặp lại từ "increase" quá nhiều, thay bằng "grow", "climb", "surge", v.v. Sử dụng từ điển để kiểm tra ngữ cảnh trước khi dùng từ mới.</p>
          <p><strong>Ví dụ đúng</strong>: <span class="correct-text">The proportion of students rose to 50% over the period.</span></p>
          <p><strong>Mẹo</strong>: Tạo danh sách từ vựng theo nhóm (xu hướng, tỷ lệ, so sánh) và luyện tập sử dụng chúng trong các bài viết mẫu.</p>

          <h3>4. Không sử dụng từ liên kết</h3>
          <p><strong>Lỗi</strong>: Thiếu từ nối khiến bài viết rời rạc, khó theo dõi, giảm điểm "Coherence and Cohesion".</p>
          <p><strong>Ví dụ sai</strong>: <span class="error-text">In 2015, sales were high. Sales dropped in 2016. Sales recovered in 2017.</span></p>
          <p><strong>Nguyên nhân</strong>: Thí sinh không chú ý đến việc liên kết ý tưởng giữa các câu hoặc không quen sử dụng từ nối trong văn viết học thuật.</p>
          <p><strong>Khắc phục</strong>: Sử dụng từ nối phù hợp (e.g., "however", "in addition", "subsequently") để kết nối các ý. Chia bài viết thành 4 đoạn: mở bài, tổng quan, thân bài 1, thân bài 2, đảm bảo mỗi đoạn có ít nhất 1-2 từ nối.</p>
          <p><strong>Ví dụ đúng</strong>: <span class="correct-text">In 2015, sales were high; however, they dropped significantly in 2016 before recovering subsequently in 2017.</span></p>
          <p><strong>Mẹo</strong>: Luyện tập viết với ít nhất 5-7 từ nối trong mỗi bài. Học danh sách từ nối phổ biến (e.g., "on the other hand", "whereas") và áp dụng vào bài viết.</p>

          <h3>5. Viết quá dài hoặc quá ngắn</h3>
          <p><strong>Lỗi</strong>: Bài viết dưới 150 từ hoặc vượt quá 200 từ, dẫn đến mất điểm hoặc không đủ thời gian cho Task 2.</p>
          <p><strong>Ví dụ sai</strong>: <span class="error-text">Bài viết chỉ 120 từ, thiếu đoạn tổng quan và phân tích.</span></p>
          <p><strong>Nguyên nhân</strong>: Không lập kế hoạch hoặc không quản lý thời gian hiệu quả. Một số thí sinh viết lan man, trong khi số khác không biết cách phát triển ý để đạt số từ tối thiểu.</p>
          <p><strong>Khắc phục</strong>: Viết khoảng 160-180 từ, lập dàn ý trước (2 phút), viết (15 phút), kiểm tra (3 phút). Tập trung vào các ý chính, tránh lan man. Mỗi đoạn nên có 3-4 câu để đảm bảo đủ độ dài.</p>
          <div class="example-box">
            <p><strong>Mẹo</strong>: Dùng đồng hồ đếm ngược khi luyện tập để làm quen với giới hạn thời gian. Đếm số từ sau mỗi bài để kiểm tra độ dài.</p>
          </div>

          <h3>6. Không diễn giải dữ liệu một cách rõ ràng</h3>
          <p><strong>Lỗi</strong>: Chỉ nêu số liệu mà không giải thích ý nghĩa hoặc xu hướng, làm bài viết thiếu chiều sâu.</p>
          <p><strong>Ví dụ sai</strong>: <span class="error-text">In 2010, the temperature was 20°C. In 2020, it was 25°C.</span></p>
          <p><strong>Nguyên nhân</strong>: Thiếu kỹ năng phân tích và diễn giải số liệu, hoặc không hiểu rõ mục đích của Task 1 là làm nổi bật ý nghĩa của dữ liệu.</p>
          <p><strong>Khắc phục</strong>: Thêm giải thích hoặc so sánh để làm rõ ý nghĩa (e.g., "This represents a 25% increase, indicating a warming trend."). Sử dụng các cụm từ như "this suggests", "indicating", hoặc "reflecting" để diễn giải.</p>
          <p><strong>Ví dụ đúng</strong>: <span class="correct-text">The temperature rose from 20°C in 2010 to 25°C in 2020, reflecting a 25% increase that suggests a warming trend over the decade.</span></p>
          <p><strong>Mẹo</strong>: Luyện tập với các câu hỏi mô tả biểu đồ có số liệu phức tạp và tập trung vào việc giải thích ý nghĩa thay vì chỉ liệt kê số liệu.</p>

          <h3>7. Lỗi ngữ pháp và chính tả</h3>
          <p><strong>Lỗi</strong>: Mắc lỗi ngữ pháp (sai thì, thiếu chủ ngữ) hoặc chính tả, làm giảm điểm "Grammatical Range and Accuracy".</p>
          <p><strong>Ví dụ sai</strong>: <span class="error-text">The population growed by 1 million.</span></p>
          <p><strong>Nguyên nhân</strong>: Thiếu luyện tập hoặc không kiểm tra lại bài viết. Một số thí sinh không nắm rõ quy tắc ngữ pháp cơ bản như chia động từ hoặc sự hòa hợp chủ-ngữ.</p>
          <p><strong>Khắc phục</strong>: Sử dụng thì phù hợp (thường là hiện tại hoặc quá khứ đơn), kiểm tra lại lỗi chính tả và câu cú trước khi nộp. Học các cấu trúc ngữ pháp phổ biến trong Task 1 (e.g., "There was a significant rise in...").</p>
          <p><strong>Ví dụ đúng</strong>: <span class="correct-text">The population grew by 1 million over the period.</span></p>
          <p><strong>Mẹo</strong>: Dành 2-3 phút cuối để đọc lại và sửa lỗi. Sử dụng công cụ kiểm tra ngữ pháp (như Grammarly) khi luyện tập để nhận diện lỗi thường gặp.</p>

          <h3>8. Không phân chia đoạn văn hợp lý</h3>
          <p><strong>Lỗi</strong>: Viết bài không chia đoạn hoặc các đoạn không rõ ràng, làm bài viết mất cấu trúc, giảm điểm "Coherence and Cohesion".</p>
          <p><strong>Ví dụ sai</strong>: <span class="error-text">Viết tất cả thông tin trong một đoạn duy nhất, không có mở bài, tổng quan hay thân bài riêng biệt.</span></p>
          <p><strong>Nguyên nhân</strong>: Không hiểu cấu trúc chuẩn của bài Task 1 hoặc không lập dàn ý trước khi viết.</p>
          <p><strong>Khắc phục</strong>: Luôn chia bài viết thành ít nhất 3-4 đoạn: mở bài (1 câu), tổng quan (2-3 câu), thân bài 1 và 2 (mỗi đoạn 3-4 câu). Mỗi đoạn nên tập trung vào một ý chính (e.g., thân bài 1 mô tả xu hướng, thân bài 2 so sánh các nhóm dữ liệu).</p>
          <p><strong>Ví dụ đúng</strong>: <span class="correct-text">Đoạn 1: The chart illustrates population growth from 2010 to 2020. Đoạn 2: Overall, the population increased steadily. Đoạn 3: From 2010 to 2015, the population grew from 5 million to 6 million...</span></p>
          <p><strong>Mẹo</strong>: Lập dàn ý trước khi viết, xác định rõ nội dung mỗi đoạn. Luyện tập viết bài với cấu trúc chuẩn trong 20 phút.</p>

          <h3>9. Sử dụng từ ngữ không mang tính học thuật</h3>
          <p><strong>Lỗi</strong>: Sử dụng từ ngữ thông tục hoặc không phù hợp với văn phong học thuật, làm giảm điểm "Lexical Resource".</p>
          <p><strong>Ví dụ sai</strong>: <span class="error-text">The number of people went up a lot.</span> ("A lot" là từ ngữ thông tục, không phù hợp với bài viết học thuật.)</p>
          <p><strong>Nguyên nhân</strong>: Thiếu vốn từ vựng học thuật hoặc quen sử dụng văn phong nói trong văn viết.</p>
          <p><strong>Khắc phục</strong>: Sử dụng từ ngữ học thuật (e.g., "considerably", "substantially") thay vì từ ngữ thông tục. Tránh các từ như "a lot", "very", "stuff", và thay bằng "significant", "notable", "items", v.v.</p>
          <p><strong>Ví dụ đúng</strong>: <span class="correct-text">The number of people increased substantially over the period.</span></p>
          <p><strong>Mẹo</strong>: Đọc các bài mẫu band 7+ để học cách sử dụng từ ngữ học thuật. Tạo thói quen thay thế từ thông tục bằng từ học thuật khi luyện tập.</p>

          <h3>10. Không quản lý thời gian hiệu quả</h3>
          <p><strong>Lỗi</strong>: Dành quá nhiều thời gian cho Task 1, dẫn đến không đủ thời gian cho Task 2, hoặc viết vội vàng, thiếu kiểm tra lỗi.</p>
          <p><strong>Ví dụ sai</strong>: <span class="error-text">Bài viết Task 1 mất 30 phút, chỉ còn 30 phút cho Task 2, dẫn đến bài Task 2 không hoàn thiện.</span></p>
          <p><strong>Nguyên nhân</strong>: Không luyện tập dưới áp lực thời gian hoặc không lập kế hoạch viết bài hợp lý.</p>
          <p><strong>Khắc phục</strong>: Phân bổ thời gian hợp lý: 2 phút lập dàn ý, 15 phút viết, 3 phút kiểm tra. Task 1 chỉ nên chiếm 20 phút để dành 40 phút cho Task 2 (vì Task 2 chiếm 2/3 tổng điểm).</p>
          <div class="example-box">
            <p><strong>Mẹo</strong>: Luyện tập với đồng hồ bấm giờ, đặt mục tiêu hoàn thành Task 1 trong 20 phút. Nếu vượt thời gian, phân tích lý do và điều chỉnh cách viết.</p>
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

  <!-- NotebookLM Interaction Section -->
  <div class="notebooklm-chat-button" id="notebooklmChatButton">
    <i class="fas fa-book"></i>
  </div>
  <div class="notebooklm-interaction" id="notebooklmInteraction">
    <div class="notebooklm-interaction-header">
      <h2>Hỏi NotebookLM về IELTS Writing Task 1</h2>
      <span class="close-chat" id="closeChat">×</span>
    </div>
    <div class="notebooklm-interaction-body">
      <div class="form-group">
        <label for="notebooklmQuery">Đặt câu hỏi liên quan đến bài học:</label>
        <textarea class="form-control" id="notebooklmQuery" rows="3" placeholder="Ví dụ: Làm thế nào để viết đoạn tổng quan cho bài Task 1?"></textarea>
      </div>
      <button class="btn btn-view" id="submitNotebooklmQuery"><i class="fas fa-book"></i> Mở NotebookLM</button>
      <div class="notebooklm-instruction">
        <p><strong>Hướng dẫn:</strong> Nhấn "Mở NotebookLM" để chuyển đến Google NotebookLM. Sao chép câu hỏi của bạn và dán vào giao diện NotebookLM. Bạn có thể tải tài liệu liên quan (PDF, Google Docs) để nhận câu trả lời chi tiết hơn.</p>
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

      // Xử lý mở/đóng cửa sổ NotebookLM
      $('#notebooklmChatButton').click(function() {
        $('#notebooklmInteraction').toggleClass('active');
      });

      $('#closeChat').click(function() {
        $('#notebooklmInteraction').removeClass('active');
      });

      // Xử lý mở NotebookLM với câu hỏi
      $('#submitNotebooklmQuery').click(function() {
        var query = $('#notebooklmQuery').val().trim();
        if (query === '') {
          $('.notebooklm-instruction').html('<p>Vui lòng nhập câu hỏi!</p>');
          return;
        }
        // Mở NotebookLM trong tab mới
        var notebooklmUrl = 'https://notebooklm.google/';
        window.open(notebooklmUrl, '_blank');
        // Lưu câu hỏi để người dùng sao chép
        $('.notebooklm-instruction').html('<p><strong>Câu hỏi của bạn:</strong> ' + query + '<br><strong>Hướng dẫn:</strong> Sao chép câu hỏi này, dán vào NotebookLM và tải tài liệu liên quan (nếu có) để nhận câu trả lời.</p>');
        $('#notebooklmQuery').val(''); // Clear input
      });
    });
  </script>
</body>
</html>
