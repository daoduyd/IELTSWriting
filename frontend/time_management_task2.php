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
  <title>Quản lý thời gian trong IELTS Writing Task 2 - IELTS Writing Practice</title>
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
        <h1 class="main-title">Quản lý thời gian trong IELTS Writing Task 2</h1>
        <p class="sub-title">Học cách phân bổ thời gian hiệu quả cho IELTS Writing Task 2 để viết bài luận chất lượng và tối ưu hóa điểm số của bạn.</p>
      </div>
    </div>
  </div>

  <!-- Main Content -->
  <div class="container">
    <div class="row">
      <div class="col-md-8">
        <div class="guide-content">
          <h2>Chiến lược quản lý thời gian</h2>

          <h3>1. Hiểu rõ thời gian phân bổ</h3>
          <p>Phần Writing IELTS kéo dài 60 phút, trong đó Task 2 nên chiếm khoảng 40 phút vì chiếm 2/3 tổng điểm. Điều này đòi hỏi bạn phải tập trung vào việc lập kế hoạch và viết bài luận chất lượng, đồng thời dành đủ thời gian cho Task 1 (20 phút).</p>
          <p><strong>Tầm quan trọng</strong>: Task 2 yêu cầu viết một bài luận dài hơn (tối thiểu 250 từ) và cần phân tích sâu sắc, do đó cần ưu tiên thời gian để đảm bảo bài viết đạt điểm cao.</p>
          <div class="example-box">
            <p><strong>Mẹo</strong>: Luôn kiểm tra thời gian sau khi hoàn thành Task 1. Nếu Task 1 vượt quá 20 phút, bạn cần điều chỉnh tốc độ viết để dành đủ 40 phút cho Task 2.</p>
          </div>

          <h3>2. Phân tích đề bài và lập kế hoạch (4-5 phút)</h3>
          <p>Trước khi viết, dành 4-5 phút để phân tích đề bài và lập dàn ý. Đây là bước quan trọng để tránh lạc đề và đảm bảo bài viết có cấu trúc rõ ràng.</p>
          <ul>
            <li><strong>Xác định loại đề</strong>: Xác định xem đó là bài Opinion, Discussion, Problem-Solution, hay Advantages/Disadvantages.</li>
            <li><strong>Ghi chú ý chính</strong>: Gạch chân từ khóa trong đề (e.g., "advantages", "disadvantages", "to what extent") và quyết định quan điểm của bạn (nếu cần).</li>
            <li><strong>Lập cấu trúc bài viết</strong>: Quyết định bài viết sẽ có 4 đoạn: mở bài (2-3 câu), thân bài 1 (4-5 câu), thân bài 2 (4-5 câu), kết bài (2-3 câu).</li>
            <li><strong>Lên ý tưởng nhanh</strong>: Ghi chú 2-3 ý chính và ví dụ hỗ trợ cho mỗi đoạn thân bài.</li>
          </ul>
          <div class="example-box">
            <p><strong>Ví dụ dàn ý cho Discussion Essay</strong>: 
              <ul>
                <li>Mở bài: Introduce the topic of technology and state that both advantages and disadvantages will be discussed.</li>
                <li>Thân bài 1: Advantages (e.g., improved communication, access to information).</li>
                <li>Thân bài 2: Disadvantages (e.g., privacy issues, over-reliance).</li>
                <li>Kết bài: Summarize both sides and give a balanced view.</li>
              </ul>
            </p>
            <p><strong>Mẹo</strong>: Sử dụng giấy nháp để ghi nhanh các từ khóa và ý tưởng. Điều này giúp bạn không bị quên ý khi viết.</p>
          </div>

          <h3>3. Viết bài Task 2 (30 phút)</h3>
          <p>Chia thời gian viết như sau để đảm bảo bài viết hoàn chỉnh và chất lượng:</p>
          <ul>
            <li><strong>Mở bài (5 phút)</strong>: Viết 2-3 câu giới thiệu chủ đề và nêu luận điểm chính (paraphrase đề bài). Ví dụ: "Technology has become an integral part of modern life, but its impacts are both positive and negative."</li>
            <li><strong>Thân bài 1 (10 phút)</strong>: Trình bày lý do chính ủng hộ quan điểm, kèm ví dụ cụ thể. Mỗi đoạn nên có 80-100 từ để đủ độ sâu.</li>
            <li><strong>Thân bài 2 (10 phút)</strong>: Trình bày lý do thứ hai hoặc phản biện ý kiến đối lập, cũng kèm ví dụ minh họa.</li>
            <li><strong>Kết bài (5 phút)</strong>: Tóm tắt ý chính từ hai đoạn thân và khẳng định lại quan điểm (nếu có). Tránh đưa ý mới.</li>
          </ul>
          <p><strong>Mục tiêu</strong>: Viết 260-280 từ. Nếu viết quá dài (trên 300 từ), bạn có thể mất thời gian kiểm tra bài.</p>
          <div class="example-box">
            <p><strong>Ví dụ đoạn thân bài</strong>: One major advantage of technology is its ability to enhance communication. For instance, platforms like Zoom allow people to connect instantly across the globe, which has revolutionized both personal and professional interactions.</p>
            <p><strong>Mẹo</strong>: Nếu không đủ thời gian cho đoạn thân bài 2, hãy viết ngắn gọn nhưng đảm bảo có kết bài. Kết bài là phần không thể thiếu.</p>
          </div>

          <h3>4. Kiểm tra và sửa lỗi (4-5 phút)</h3>
          <p>Sử dụng 4-5 phút cuối để kiểm tra và sửa lỗi, đảm bảo bài viết đạt chất lượng cao:</p>
          <ul>
            <li><strong>Kiểm tra ngữ pháp</strong>: Xem lại câu phức, thì động từ, và sự hòa hợp chủ-ngữ (e.g., "The government has..." thay vì "have").</li>
            <li><strong>Đảm bảo từ vựng đa dạng</strong>: Thay các từ lặp lại (e.g., "good" → "beneficial", "bad" → "detrimental").</li>
            <li><strong>Xác nhận tính mạch lạc</strong>: Đảm bảo bài viết có từ nối (e.g., "however", "moreover") và trả lời đúng đề bài.</li>
            <li><strong>Đếm số từ</strong>: Nếu dưới 250 từ, thêm 1-2 câu vào đoạn thân bài để đạt yêu cầu tối thiểu.</li>
          </ul>
          <div class="example-box">
            <p><strong>Ví dụ lỗi cần sửa</strong>: "Technology make life better" → "Technology makes life better."</p>
            <p><strong>Mẹo</strong>: Đánh dấu các lỗi thường gặp của bạn (e.g., quên "s" ở động từ ngôi thứ 3) và tập trung sửa những lỗi này trước.</p>
          </div>

          <h3>5. Luyện tập phân bổ thời gian</h3>
          <p>Để làm quen với áp lực thời gian, hãy luyện tập viết Task 2 trong 40 phút nhiều lần:</p>
          <ul>
            <li><strong>Bắt đầu với 45 phút</strong>: Nếu bạn mới luyện tập, hãy dành 45 phút để viết một bài hoàn chỉnh, sau đó rút ngắn dần xuống 40 phút.</li>
            <li><strong>Sử dụng đề thi thật</strong>: Tìm các đề Task 2 từ sách IELTS (e.g., Cambridge IELTS) để luyện tập.</li>
            <li><strong>Phân tích thời gian từng bước</strong>: Sau mỗi lần luyện, ghi lại thời gian dành cho từng phần (lập kế hoạch, viết, kiểm tra) để cải thiện.</li>
          </ul>
          <div class="example-box">
            <p><strong>Gợi ý</strong>: Luyện tập với đồng hồ bấm giờ. Nếu vượt 40 phút, xem lại phần nào mất nhiều thời gian nhất (e.g., viết mở bài) và điều chỉnh.</p>
          </div>

          <h3>6. Xử lý các tình huống bất ngờ</h3>
          <p>Trong bài thi thật, bạn có thể gặp các tình huống bất ngờ ảnh hưởng đến thời gian. Dưới đây là cách xử lý:</p>
          <ul>
            <li><strong>Đề bài khó</strong>: Nếu không hiểu rõ đề, dành thêm 1 phút để đọc lại và xác định từ khóa. Tập trung vào một khía cạnh bạn hiểu rõ nhất.</li>
            <li><strong>Thiếu ý tưởng</strong>: Nếu không nghĩ ra ví dụ, sử dụng trải nghiệm cá nhân (e.g., "In my country, technology has improved education by...").</li>
            <li><strong>Hết thời gian</strong>: Nếu chỉ còn 2 phút, nhanh chóng viết kết bài ngắn gọn (e.g., "In conclusion, technology has both benefits and drawbacks.") vì đây là phần quan trọng.</li>
          </ul>
          <div class="example-box">
            <p><strong>Ví dụ xử lý</strong>: Nếu quên từ "detrimental", thay bằng "harmful": "Overuse of technology can be harmful to mental health."</p>
            <p><strong>Mẹo</strong>: Luyện tập với các đề khó (e.g., chủ đề trừu tượng như "globalization") để làm quen với việc xử lý tình huống bất ngờ.</p>
          </div>

          <h3>7. Tối ưu hóa thời gian bằng cách chuẩn bị trước</h3>
          <p>Chuẩn bị trước khi thi có thể giúp bạn tiết kiệm thời gian trong ngày thi:</p>
          <ul>
            <li><strong>Học từ vựng theo chủ đề</strong>: Chuẩn bị danh sách từ vựng Task 2 phổ biến (e.g., "education": "curriculum", "literacy"; "environment": "sustainability", "pollution").</li>
            <li><strong>Luyện viết mẫu câu</strong>: Chuẩn bị sẵn các mẫu câu cho mở bài, kết bài (e.g., "To what extent do you agree" → "I partly agree that...").</li>
            <li><strong>Luyện viết tay</strong>: Nếu thi trên giấy, luyện viết tay để tăng tốc độ viết, tránh mất thời gian vì viết chậm hoặc mỏi tay.</li>
          </ul>
          <div class="example-box">
            <p><strong>Mẹo</strong>: Tạo danh sách 30 từ vựng Task 2 phổ biến và luyện viết 5 bài luận mẫu để ghi nhớ cách sử dụng từ.</p>
          </div>
        </div>
      </div>

      <!-- Sidebar -->
      <div class="col-md-4">
        <div class="card">
          <div class="card-header">
            <h5>Thông tin Writing Test</h5>
          </div>
          <div class="card-body">
            <ul class="list-group list-group-flush">
              <li class="list-group-item d-flex justify-content-between align-items-center">
                Tổng thời gian
                <span class="badge badge-primary badge-pill">60 phút</span>
              </li>
              <li class="list-group-item d-flex justify-content-between align-items-center">
                Task 1
                <span class="badge badge-primary badge-pill">20 phút</span>
              </li>
              <li class="list-group-item d-flex justify-content-between align-items-center">
                Task 2
                <span class="badge badge-primary badge-pill">40 phút</span>
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
      <h2>Hỏi NotebookLM về Quản lý thời gian Task 2</h2>
      <span class="close-chat" id="closeChat">×</span>
    </div>
    <div class="notebooklm-interaction-body">
      <div class="form-group">
        <label for="notebooklmQuery">Đặt câu hỏi liên quan đến bài học:</label>
        <textarea class="form-control" id="notebooklmQuery" rows="3" placeholder="Ví dụ: Làm thế nào để viết bài Task 2 trong 40 phút?"></textarea>
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