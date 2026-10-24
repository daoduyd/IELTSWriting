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
  <title>Lỗi thường gặp trong IELTS Writing Task 2 - IELTS Writing Practice</title>
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
      color: #2e7d32;
    }
    .btn-view {
      background-color: #2e7d32;
      color: white;
    }
    .btn-view:hover {
      background-color: #1b5e20;
      color: white;
    }
    .section-title {
      margin-bottom: 20px;
      padding-bottom: 10px;
      border-bottom: 2px solid #2e7d32;
      color: #2e7d32;
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
      color: #2e7d32;
      border-bottom: 2px solid #2e7d32;
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
      border-left: 4px solid #2e7d32;
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
      background-color: #2e7d32;
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
      background-color: #1b5e20;
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
      background-color: #2e7d32;
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
      border-left: 4px solid #2e7d32;
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
                <a class="dropdown-item dropdown-toggle" href="#" id="task1Dropdown" data-toggle="dropdown">Writing Task 1</a>
                <div class="dropdown-menu">
                  <a class="dropdown-item" href="lessons_task1_types.php">Các dạng bài</a>
                  <a class="dropdown-item" href="lessons_task1_topics.php">Chủ đề</a>
                </div>
              </div>
              <!-- Writing Task 2 -->
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
        <h1 class="main-title">Lỗi thường gặp trong IELTS Writing Task 2</h1>
        <p class="sub-title">Tìm hiểu những lỗi phổ biến khi viết bài IELTS Writing Task 2 và cách khắc phục để đạt điểm cao hơn.</p>
      </div>
    </div>
  </div>

  <!-- Main Content -->
  <div class="container">
    <div class="row">
      <div class="col-md-8">
        <div class="guide-content">
          <h2>Các lỗi thường gặp và cách khắc phục</h2>

          <h3>1. Lạc đề (Off-topic)</h3>
          <p><strong>Lỗi</strong>: Thí sinh không trả lời đúng trọng tâm câu hỏi hoặc hiểu sai yêu cầu (e.g., viết về ưu điểm mà đề yêu cầu cả ưu/nhược điểm).</p>
          <p><strong>Ví dụ sai</strong>: <span class="error-text">Đề bài: Discuss the advantages and disadvantages of technology. Bài viết chỉ nói về lợi ích mà không đề cập đến bất lợi, như "Technology helps us communicate better."</span></p>
          <p><strong>Nguyên nhân</strong>: Thiếu kỹ năng phân tích đề bài hoặc không lập dàn ý trước. Một số thí sinh không đọc kỹ đề, bỏ qua từ khóa quan trọng như "advantages and disadvantages".</p>
          <p><strong>Khắc phục</strong>: Đọc kỹ câu hỏi, xác định loại bài (opinion, discussion, problem-solution) và lập dàn ý với ít nhất 4 đoạn (mở bài, thân bài 1, thân bài 2, kết luận). Đảm bảo trả lời tất cả các phần của câu hỏi. Gạch chân từ khóa trong đề để không bỏ sót ý.</p>
          <p><strong>Ví dụ đúng</strong>: <span class="correct-text">Technology offers numerous benefits, such as improved communication, but also presents drawbacks, including reduced face-to-face interaction and potential privacy issues.</span></p>
          <p><strong>Mẹo</strong>: Dành 2-3 phút phân tích đề bài và lập dàn ý trước khi viết. Luyện tập với các dạng đề khác nhau (e.g., opinion, discussion) để làm quen với yêu cầu.</p>

          <h3>2. Không phát triển ý đầy đủ</h3>
          <p><strong>Lỗi</strong>: Các đoạn văn chỉ nêu ý chính mà không giải thích hoặc đưa ví dụ, dẫn đến thiếu chiều sâu và không thuyết phục.</p>
          <p><strong>Ví dụ sai</strong>: <span class="error-text">Education is important. It helps people get jobs.</span></p>
          <p><strong>Nguyên nhân</strong>: Thiếu kỹ năng mở rộng ý tưởng hoặc viết quá ngắn gọn. Một số thí sinh lo sợ vượt thời gian nên chỉ viết ý chính mà không triển khai chi tiết.</p>
          <p><strong>

Khắc phục</strong>: Mỗi đoạn cần có 2-3 câu: ý chính, giải thích, và ví dụ cụ thể. Sử dụng cấu trúc PEEL (Point, Explain, Example, Link). Đảm bảo mỗi đoạn có ít nhất 80-100 từ để đủ chiều sâu.</p>
          <p><strong>Ví dụ đúng</strong>: <span class="correct-text">Education is crucial as it equips individuals with skills needed for employment. For instance, university graduates often secure better job opportunities due to their specialized knowledge, which highlights its importance in modern society.</span></p>
          <p><strong>Mẹo</strong>: Luyện viết mỗi đoạn với cấu trúc PEEL. Tìm ví dụ thực tế (e.g., số liệu, sự kiện) để minh họa ý chính.</p>

          <h3>3. Sử dụng từ vựng không phù hợp</h3>
          <p><strong>Lỗi</strong>: Dùng từ không chính xác hoặc lặp lại từ đơn giản (e.g., "good", "bad"), giảm điểm "Lexical Resource".</p>
          <p><strong>Ví dụ sai</strong>: <span class="error-text">Technology is very good. It makes life very good.</span></p>
          <p><strong>Nguyên nhân</strong>: Thiếu vốn từ vựng học thuật hoặc không luyện tập đa dạng hóa từ. Một số thí sinh lạm dụng từ nâng cao mà không hiểu rõ nghĩa, dẫn đến sai ngữ cảnh.</p>
          <p><strong>Khắc phục</strong>: Học từ vựng học thuật (e.g., "enhance", "detrimental") và sử dụng từ đồng nghĩa. Tránh lặp từ bằng cách thay thế (e.g., "improve" → "boost", "advance"). Kiểm tra nghĩa và ngữ cảnh của từ trước khi dùng.</p>
          <p><strong>Ví dụ đúng</strong>: <span class="correct-text">Technology significantly enhances quality of life by facilitating communication and advancing access to information.</span></p>
          <p><strong>Mẹo</strong>: Sử dụng danh sách từ vựng IELTS và luyện viết với ít nhất 10 từ mới mỗi tuần. Đọc bài mẫu band 7+ để học cách dùng từ học thuật.</p>

          <h3>4. Lỗi ngữ pháp và cấu trúc câu</h3>
          <p><strong>Lỗi</strong>: Sử dụng câu đơn giản hoặc mắc lỗi ngữ pháp (e.g., sai thì, thiếu chủ ngữ), làm giảm điểm "Grammatical Range and Accuracy".</p>
          <p><strong>Ví dụ sai</strong>: <span class="error-text">People is happy because technology help they.</span></p>
          <p><strong>Nguyên nhân</strong>: Thiếu luyện tập cấu trúc câu phức tạp hoặc không kiểm tra lại. Một số thí sinh không nắm rõ quy tắc ngữ pháp cơ bản như thì động từ hoặc sự hòa hợp chủ-ngữ.</p>
          <p><strong>Khắc phục</strong>: Kết hợp câu đơn và câu phức (e.g., "Although technology helps, it also creates challenges"). Kiểm tra lỗi chính tả, thì, và sự hòa hợp chủ-ngữ. Học các cấu trúc câu phổ biến (e.g., mệnh đề quan hệ, câu điều kiện).</p>
          <p><strong>Ví dụ đúng</strong>: <span class="correct-text">People are happier because technology assists them in various aspects of life, despite some challenges it may pose.</span></p>
          <p><strong>Mẹo</strong>: Dành 5 phút cuối để sửa lỗi ngữ pháp. Sử dụng công cụ như Grammarly khi luyện tập để nhận diện lỗi thường gặp.</p>

          <h3>5. Không quản lý thời gian hiệu quả</h3>
          <p><strong>Lỗi</strong>: Dành quá nhiều thời gian cho một phần, dẫn đến bài viết chưa hoàn thiện hoặc thiếu kiểm tra.</p>
          <p><strong>Ví dụ sai</strong>: <span class="error-text">Bài viết chỉ có 2 đoạn vì không đủ thời gian hoàn thành.</span></p>
          <p><strong>Nguyên nhân</strong>: Không lập kế hoạch hoặc viết quá dài ở phần mở bài. Một số thí sinh mất quá nhiều thời gian để nghĩ ý tưởng hoặc sửa lỗi trong lúc viết.</p>
          <p><strong>Khắc phục</strong>: Phân bổ thời gian: 5 phút lập dàn ý, 30 phút viết (5 phút mở bài, 10 phút mỗi đoạn thân, 5 phút kết luận), 5 phút kiểm tra. Tập trung viết ý chính trước, sửa lỗi sau.</p>
          <div class="example-box">
            <p><strong>Mẹo</strong>: Sử dụng đồng hồ khi luyện tập để làm quen với 40 phút. Nếu vượt thời gian, phân tích lý do và điều chỉnh cách viết.</p>
          </div>

          <h3>6. Không viết kết luận rõ ràng</h3>
          <p><strong>Lỗi</strong>: Bỏ qua kết luận hoặc viết kết luận không tóm tắt lại quan điểm, làm bài viết thiếu trọn vẹn.</p>
          <p><strong>Ví dụ sai</strong>: <span class="error-text">Bài viết kết thúc đột ngột sau đoạn thân bài mà không có phần kết luận.</span></p>
          <p><strong>Nguyên nhân</strong>: Thiếu thời gian hoặc không hiểu tầm quan trọng của kết luận. Một số thí sinh nghĩ rằng kết luận không cần thiết hoặc không biết cách viết.</p>
          <p><strong>Khắc phục</strong>: Viết kết luận 2-3 câu, tóm tắt ý chính và khẳng định quan điểm (nếu có). Tránh đưa ý mới. Sử dụng các cụm từ như "In conclusion", "To sum up" để bắt đầu.</p>
          <p><strong>Ví dụ đúng</strong>: <span class="correct-text">In conclusion, while technology brings significant benefits, its drawbacks cannot be ignored, necessitating a balanced approach to its use.</span></p>
          <p><strong>Mẹo</strong>: Luyện viết kết luận trong 2 phút. Chuẩn bị sẵn 2-3 mẫu câu kết luận (e.g., "In conclusion, both sides should be considered...") để sử dụng nhanh.</p>

          <h3>7. Viết dưới hoặc trên số từ quy định</h3>
          <p><strong>Lỗi</strong>: Bài viết dưới 250 từ hoặc vượt quá 300 từ, ảnh hưởng đến điểm số và thời gian.</p>
          <p><strong>Ví dụ sai</strong>: <span class="error-text">Bài viết chỉ 200 từ, thiếu phân tích chi tiết.</span></p>
          <p><strong>Nguyên nhân</strong>: Không kiểm soát số từ hoặc viết lan man. Một số thí sinh không biết cách phát triển ý để đạt số từ tối thiểu.</p>
          <p><strong>Khắc phục</strong>: Nhắm đến 260-280 từ, lập dàn ý với số câu mỗi đoạn (e.g., 4-5 câu/đoạn). Mỗi đoạn thân bài nên có 80-100 từ, mở bài và kết luận mỗi phần 40-50 từ.</p>
          <p><strong>Mẹo</strong>: Đếm số từ khi luyện tập để quen với giới hạn. Nếu viết dưới 250 từ, luyện tập mở rộng ý bằng cách thêm ví dụ hoặc giải thích.</p>

          <h3>8. Không sử dụng từ nối và cấu trúc mạch lạc</h3>
          <p><strong>Lỗi</strong>: Thiếu từ nối hoặc cấu trúc câu không logic, làm bài viết rời rạc, giảm điểm "Coherence and Cohesion".</p>
          <p><strong>Ví dụ sai</strong>: <span class="error-text">Technology helps communication. It causes privacy issues. People use it a lot.</span></p>
          <p><strong>Nguyên nhân</strong>: Không quen sử dụng từ nối hoặc không lập dàn ý để sắp xếp ý tưởng logic. Một số thí sinh viết ngẫu nhiên theo ý nghĩ mà không tổ chức ý.</p>
          <p><strong>Khắc phục</strong>: Sử dụng từ nối (e.g., "moreover", "however", "therefore") để liên kết ý. Lập dàn ý trước để đảm bảo ý tưởng được trình bày logic. Mỗi đoạn nên có một ý chính rõ ràng.</p>
          <p><strong>Ví dụ đúng</strong>: <span class="correct-text">Technology enhances communication by enabling instant connectivity; however, it also raises privacy concerns due to data breaches. Consequently, its widespread use requires careful regulation.</span></p>
          <p><strong>Mẹo</strong>: Học danh sách từ nối phổ biến và luyện viết bài với ít nhất 5-7 từ nối. Đọc bài mẫu để học cách sắp xếp ý logic.</p>

          <h3>9. Sử dụng từ ngữ không mang tính học thuật</h3>
          <p><strong>Lỗi</strong>: Sử dụng từ ngữ thông tục hoặc không phù hợp với văn phong học thuật, làm giảm điểm "Lexical Resource".</p>
          <p><strong>Ví dụ sai</strong>: <span class="error-text">Technology is super cool because it helps a lot of stuff.</span></p>
          <p><strong>Nguyên nhân</strong>: Thiếu vốn từ vựng học thuật hoặc quen sử dụng văn phong nói. Một số thí sinh cố gắng dùng từ đơn giản để tránh sai nhưng làm bài viết thiếu chuyên nghiệp.</p>
          <p><strong>Khắc phục</strong>: Sử dụng từ ngữ học thuật (e.g., "beneficial", "facilitates") thay vì từ thông tục như "cool", "stuff". Tránh các cụm từ như "a lot", "very", thay bằng "considerable", "significant".</p>
          <p><strong>Ví dụ đúng</strong>: <span class="correct-text">Technology is highly beneficial as it facilitates numerous aspects of daily life.</span></p>
          <p><strong>Mẹo</strong>: Đọc các bài mẫu band 7+ để học từ vựng học thuật. Tạo thói quen thay thế từ thông tục bằng từ học thuật khi luyện tập.</p>

          <h3>10. Sao chép ý tưởng hoặc phụ thuộc vào AI</h3>
          <p><strong>Lỗi</strong>: Sao chép ý tưởng từ bài mẫu hoặc phụ thuộc quá nhiều vào công cụ AI, làm giảm điểm "Task Response" do thiếu tính nguyên bản (đặc biệt nghiêm trọng trong năm 2025).</p>
          <p><strong>Ví dụ sai</strong>: <span class="error-text">Sử dụng nguyên đoạn văn từ bài mẫu: "It is widely believed that technology has revolutionized our lives in numerous ways."</span></p>
          <p><strong>Nguyên nhân</strong>: Thiếu ý tưởng cá nhân hoặc không tự tin vào khả năng viết. Một số thí sinh dùng AI để viết toàn bộ bài mà không chỉnh sửa, dẫn đến bị phát hiện bởi công cụ chấm điểm AI.</p>
          <p><strong>Khắc phục</strong>: Phát triển ý tưởng cá nhân dựa trên trải nghiệm hoặc kiến thức của bản thân. Nếu sử dụng AI, chỉ dùng để gợi ý từ vựng hoặc cấu trúc, sau đó viết lại theo cách của mình. Đảm bảo bài viết mang dấu ấn cá nhân.</p>
          <p><strong>Ví dụ đúng</strong>: <span class="correct-text">Technology has transformed daily life by enabling faster communication, a change I have personally observed in my workplace where emails have replaced lengthy meetings.</span></p>
          <p><strong>Mẹo</strong>: Luyện viết mà không phụ thuộc vào AI trong 40 phút. Sử dụng các công cụ AI để kiểm tra từ vựng hoặc ngữ pháp, nhưng không sao chép ý tưởng.</p>

          <h3>11. Từ vựng nâng cao cho bài viết</h3>
          <p>Sử dụng từ vựng học thuật để nâng cao chất lượng bài viết. Dưới đây là một số từ vựng hữu ích:</p>
          <table class="table vocabulary-table">
            <tr>
              <th>Từ vựng</th>
              <th>Nghĩa</th>
              <th>Ví dụ</th>
            </tr>
            <tr>
              <td>Advocate</td>
              <td>Ủng hộ</td>
              <td>Many experts advocate sustainable development to protect the environment.</td>
            </tr>
            <tr>
              <td>Mitigate</td>
              <td>Giảm thiểu</td>
              <td>Government policies aim to mitigate the effects of climate change.</td>
            </tr>
            <tr>
              <td>Detrimental</td>
              <td>Có hại</td>
              <td>Overuse of social media can be detrimental to mental health.</td>
            </tr>
            <tr>
              <td>Alleviate</td>
              <td>Giảm nhẹ</td>
              <td>Education can alleviate poverty in disadvantaged communities.</td>
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

  <!-- NotebookLM Interaction Section -->
  <div class="notebooklm-chat-button" id="notebooklmChatButton">
    <i class="fas fa-book"></i>
  </div>
  <div class="notebooklm-interaction" id="notebooklmInteraction">
    <div class="notebooklm-interaction-header">
      <h2>Hỏi NotebookLM về IELTS Writing Task 2</h2>
      <span class="close-chat" id="closeChat">×</span>
    </div>
    <div class="notebooklm-interaction-body">
      <div class="form-group">
        <label for="notebooklmQuery">Đặt câu hỏi liên quan đến bài học:</label>
        <textarea class="form-control" id="notebooklmQuery" rows="3" placeholder="Ví dụ: Làm thế nào để tránh lạc đề trong bài Task 2?"></textarea>
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