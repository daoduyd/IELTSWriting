<?php
session_start();

// Kiểm tra xem người dùng đã đăng nhập hay chưa
$logged_in = isset($_SESSION['user_id']);
// Kết nối đến cơ sở dữ liệu
require 'config.php';

// Hàm tạo guest_id nếu chưa có
function ensureGuestId() {
    if (!isset($_COOKIE['guest_id'])) {
        $guest_id = uniqid('guest_', true);
        setcookie('guest_id', $guest_id, time() + (86400 * 30), "/"); // Cookie tồn tại 30 ngày
        return $guest_id;
    }
    return $_COOKIE['guest_id'];
}

// Đảm bảo có guest_id cho người dùng chưa đăng nhập
if (!$logged_in) {
    $guest_id = ensureGuestId();
}

// Lấy thông tin tiến độ học tập cho người dùng chưa đăng nhập
function getGuestProgress($pdo, $guest_id) {
    // Kiểm tra xem bảng guest_progress đã tồn tại chưa
    $stmt = $pdo->prepare("SHOW TABLES LIKE 'guest_progress'");
    $stmt->execute();
    if ($stmt->rowCount() == 0) {
        // Tạo bảng nếu chưa tồn tại
        $sql = "CREATE TABLE guest_progress (
            id INT AUTO_INCREMENT PRIMARY KEY,
            guest_id VARCHAR(100) NOT NULL,
            question_id INT NOT NULL,
            completed BOOLEAN DEFAULT 1,
            score FLOAT NULL,
            submission_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            response_text TEXT,
            feedback TEXT,
            UNIQUE KEY (guest_id, question_id)
        )";
        $pdo->exec($sql);
    }
    // Lấy số lượng bài đã hoàn thành
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM guest_progress WHERE guest_id = ?");
    $stmt->execute([$guest_id]);
    $completed = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    // Lấy danh sách các bài đã làm gần đây
    $stmt = $pdo->prepare("SELECT gp.id, q.question, gp.submission_time, gp.score 
                         FROM guest_progress gp 
                         JOIN ds_cau_hoi q ON gp.question_id = q.id 
                         WHERE gp.guest_id = ? 
                         ORDER BY gp.submission_time DESC 
                         LIMIT 3");
    $stmt->execute([$guest_id]);
    $recent_essays = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return [
        'completed' => $completed,
        'recent_essays' => $recent_essays
    ];
}

// Lấy tổng số bài có trong hệ thống
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM ds_cau_hoi");
$stmt->execute();
$total_questions = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Lấy tiến độ học tập dựa trên trạng thái đăng nhập
if ($logged_in) {
    $user_id = $_SESSION['user_id'];
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM essay_responses WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $completed = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    $stmt = $pdo->prepare("SELECT er.id, q.question, er.submission_time, er.score 
                         FROM essay_responses er 
                         JOIN ds_cau_hoi q ON er.question_id = q.id 
                         WHERE er.user_id = ? 
                         ORDER BY er.submission_time DESC 
                         LIMIT 3");
    $stmt->execute([$user_id]);
    $recent_essays = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $guest_progress = getGuestProgress($pdo, $guest_id);
    $completed = $guest_progress['completed'];
    $recent_essays = $guest_progress['recent_essays'];
}

$percentage = ($total_questions > 0) ? round(($completed / $total_questions) * 100) : 0;

// Lấy danh sách các bài giảng
$stmt = $pdo->prepare("SELECT id, title, category FROM lectures ORDER BY id DESC LIMIT 3");
$stmt->execute();
$lectures = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <title>IELTS Writing Practice</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
  <script>
    // JavaScript để xử lý lưu trữ tiến độ học tập cho người dùng chưa đăng nhập
    function syncGuestProgress() {
      // Chỉ thực hiện cho người dùng chưa đăng nhập
      if (<?php echo $logged_in ? 'false' : 'true'; ?>) {
        // Lấy dữ liệu từ localStorage nếu có
        const localProgress = localStorage.getItem('guestProgress');
        if (localProgress) {
          // Gửi dữ liệu đến máy chủ để đồng bộ hóa
          fetch('sync_guest_progress.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify({
              guest_id: '<?php echo isset($guest_id) ? $guest_id : ""; ?>',
              progress: JSON.parse(localProgress)
            })
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              console.log('Đã đồng bộ tiến độ học tập thành công');
            }
          })
          .catch(error => {
            console.error('Lỗi khi đồng bộ tiến độ:', error);
          });
        }
      }
    }
    // Gọi hàm đồng bộ hóa khi trang được tải
    window.addEventListener('load', syncGuestProgress);
  </script>
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
      padding: 60px 0;
      background-color: #f0f5ff;
      border-radius: 10px;
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

    /* Star rating styles */
    .star-rating {
      display: inline-flex;
      font-size: 20px;
      cursor: pointer;
    }

    .star-rating .fa-star {
      color: #ccc;
      margin-right: 5px;
    }

    .star-rating .fa-star.filled {
      color: #f5c518;
    }

    .star-rating .fa-star:hover,
    .star-rating .fa-star:hover ~ .fa-star {
      color: #f5c518;
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
          <li class="nav-item active">
            <a class="nav-link" href="index.php">Home</a>
          </li>
          
          <li class="nav-item dropdown">
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

  <!-- Hero Section -->
  <div class="container hero-section">
    <div class="row">
      <div class="col-md-8 offset-md-2">
        <h1 class="main-title">Luyện thi IELTS Writing Online miễn phí mỗi ngày</h1>
        <p class="sub-title">IELTS TEST PRO là một website / ứng dụng luyện thi IELTS trực tuyến miễn phí với các câu hỏi và bài kiểm tra hỗ trợ người học luyện tập mọi lúc mọi nơi. Bạn có thể làm bài kiểm tra IELTS miễn phí với đầy đủ 4 kỹ năng nghe, nói, đọc, viết có đáp án giúp bạn tra cứu dễ dàng. Luyện tập IELTS ngay hôm nay!!!</p>
      </div>
    </div>
  </div>

  <div class="container">
    <div class="row">
      <div class="col-md-8">
        <h2 class="section-title mb-4">Chức năng của Web</h2>
        
        <!-- New Features Section -->
        <div class="row">
          <div class="col-md-6">
            <div class="feature-box">
              <div class="d-flex align-items-center mb-3">
                <i class="fas fa-question-circle fa-2x feature-icon"></i>
                <h4>Ngân hàng câu hỏi</h4>
              </div>
              <p>Hàng nghìn câu hỏi luyện tập giúp bạn không chỉ làm quen với dạng bài, độ khó của bài thi mà còn nâng cao và thành thạo tất cả các kỹ năng cần có cho kỳ thi của mình.</p>
            </div>
          </div>
          
          <div class="col-md-6">
            <div class="feature-box">
              <div class="d-flex align-items-center mb-3">
                <i class="fas fa-chart-line fa-2x feature-icon"></i>
                <h4>Báo cáo tiến trình</h4>
              </div>
              <p>Theo dõi hiệu suất và tiến độ của bạn một cách thường xuyên thông qua báo cáo tiến độ toàn diện được cá nhân hóa.</p>
            </div>
          </div>
          
          <div class="col-md-6">
            <div class="feature-box">
              <div class="d-flex align-items-center mb-3">
                <i class="fas fa-book fa-2x feature-icon"></i>
                <h4>Nền tảng học và luyện thi toàn diện</h4>
              </div>
              <p>Nền tảng ôn tập và luyện thi toàn diện và đầy đủ nhất, bao gồm các bài luyện tập, giải thích chi tiết, các bài kiểm tra mô phỏng đa dạng, cùng nhiều bài tập bổ trợ khác.</p>
            </div>
          </div>
          
          <div class="col-md-6">
            <div class="feature-box">
              <div class="d-flex align-items-center mb-3">
                <i class="fas fa-chart-bar fa-2x feature-icon"></i>
                <h4>Có tích hợp với AI</h4>
              </div>
              <p>Trong quá trình học tập, bạn sẽ được tương tác với AI để học tập hiệu quả hơn, đồng thời AI sẽ kiểm tra bài làm của bạn có phụ thuộc vào nó không.</p>
            </div>
          </div>
          
          <div class="col-md-6">
            <div class="feature-box">
              <div class="d-flex align-items-center mb-3">
                <i class="fas fa-graduation-cap fa-2x feature-icon"></i>
                <h4>Thống kê kết quả tức thì</h4>
              </div>
              <p>Ngay sau khi hoàn thành mỗi bài luyện tập, bạn có thể xem thống kê chi tiết kết quả bài làm của mình.</p>
            </div>
          </div>
          
          <div class="col-md-6">
            <div class="feature-box">
              <div class="d-flex align-items-center mb-3">
                <i class="fas fa-user-graduate fa-2x feature-icon"></i>
                <h4>Giải thích chi tiết</h4>
              </div>
              <p>Giải thích chi tiết cho các câu hỏi giúp bạn hiểu và ghi nhớ kiến thức tốt hơn.</p>
            </div>
          </div>
        </div>
        
        <!-- IELTS Introduction -->
        <h2 class="section-title mt-5">Giới thiệu về IELTS</h2>
        <p>IELTS (International English Language Testing System) là một trong những bài kiểm tra tiếng Anh phổ biến nhất thế giới dành cho mục đích du học, định cư và làm việc tại các quốc gia nói tiếng Anh. Bài thi IELTS đánh giá bốn kỹ năng: Nghe (Listening), Nói (Speaking), Đọc (Reading) và Viết (Writing).</p>
        
        <h3 class="mt-4">IELTS Writing là gì?</h3>
        <p>IELTS Writing là một trong bốn phần của bài thi IELTS, đánh giá khả năng viết tiếng Anh của thí sinh. Phần thi này gồm hai nhiệm vụ:</p>
        <ul>
          <li><strong>Task 1 (20 phút):</strong> Mô tả biểu đồ, bảng, sơ đồ hoặc quy trình bằng tiếng Anh học thuật. Yêu cầu viết ít nhất 150 từ.</li>
          <li><strong>Task 2 (40 phút):</strong> Viết một bài luận về một chủ đề cho trước. Yêu cầu viết ít nhất 250 từ.</li>
        </ul>

        <!-- New Section: Key Information About IELTS Writing -->
        <h2 class="section-title mt-5">Các thông tin cần biết về IELTS Writing (Cập nhật mới nhất năm 2025)</h2>
        <h3 class="mt-4">Tiêu chí chấm điểm</h3>
        <p>IELTS Writing được chấm dựa trên 4 tiêu chí chính, mỗi tiêu chí chiếm 25% tổng điểm:</p>
        <ul>
          <li><strong>Task Achievement/Response:</strong> Đáp ứng yêu cầu đề bài, trình bày ý tưởng rõ ràng và đầy đủ.</li>
          <li><strong>Coherence and Cohesion:</strong> Sắp xếp ý tưởng logic, sử dụng từ nối phù hợp.</li>
          <li><strong>Lexical Resource:</strong> Sử dụng từ vựng đa dạng, chính xác và phù hợp ngữ cảnh.</li>
          <li><strong>Grammatical Range and Accuracy:</strong> Sử dụng ngữ pháp chính xác, đa dạng cấu trúc câu.</li>
        </ul>
        <h3 class="mt-4">Thay đổi mới trong năm 2025</h3>
        <p>Tính đến năm 2025, IELTS Writing có một số cập nhật quan trọng:</p>
        <ul>
          <li><strong>Tích hợp công nghệ AI:</strong> Một số trung tâm khảo thí thử nghiệm sử dụng AI để hỗ trợ chấm bài, đặc biệt trong việc đánh giá từ vựng và ngữ pháp.</li>
          <li><strong>Đề bài đa dạng hơn:</strong> Các chủ đề trong Task 2 được cập nhật để phản ánh các vấn đề toàn cầu mới, như trí tuệ nhân tạo, biến đổi khí hậu, và giáo dục trực tuyến.</li>
          <li><strong>Yêu cầu chặt chẽ hơn về tính nguyên bản:</strong> Các bài viết có dấu hiệu sao chép hoặc phụ thuộc quá nhiều vào AI sẽ bị trừ điểm nặng ở tiêu chí Task Response.</li>
        </ul>
        <h3 class="mt-4">Mẹo luyện thi hiệu quả</h3>
        <p>Để đạt điểm cao trong IELTS Writing năm 2025, bạn nên:</p>
        <ul>
          <li>Luyện viết hàng ngày với các đề bài đa dạng để làm quen với các chủ đề mới.</li>
          <li>Sử dụng công cụ AI một cách hợp lý để cải thiện từ vựng và ngữ pháp, nhưng tránh phụ thuộc hoàn toàn.</li>
          <li>Tập trung vào việc phát triển ý tưởng độc lập, tránh sao chép mẫu hoặc ý tưởng từ nguồn khác.</li>
        </ul>
        
        <h2 class="section-title mt-5">Lộ trình học IELTS Writing hiệu quả</h2>
        
        <div class="roadmap-steps mt-4">
          <div class="roadmap-step">
            <div class="roadmap-number">1</div>
            <h4>Làm quen với cấu trúc đề thi</h4>
            <p>Tìm hiểu kỹ về cấu trúc đề thi, tiêu chí chấm điểm và yêu cầu của từng loại bài. Đây là bước đầu tiên và vô cùng quan trọng trong quá trình luyện thi IELTS Writing.</p>
          </div>
          
          <div class="roadmap-step">
            <div class="roadmap-number">2</div>
            <h4>Học từ vựng và cấu trúc ngữ pháp</h4>
            <p>Xây dựng vốn từ vựng học thuật phong phú và nắm vững các cấu trúc ngữ pháp cơ bản đến nâng cao để trình bày ý tưởng một cách chính xác và đa dạng.</p>
          </div>
          
          <div class="roadmap-step">
            <div class="roadmap-number">3</div>
            <h4>Học cách phân tích đề bài</h4>
            <p>Rèn luyện kỹ năng phân tích đề bài, xác định yêu cầu chính và lên ý tưởng cho bài viết một cách nhanh chóng và hiệu quả.</p>
          </div>
          
          <div class="roadmap-step">
            <div class="roadmap-number">4</div>
            <h4>Thực hành viết hàng ngày</h4>
            <p>Luyện tập viết thường xuyên với nhiều chủ đề khác nhau, áp dụng các kỹ thuật viết đã học và quản lý thời gian hiệu quả.</p>
          </div>
          
          <div class="roadmap-step">
            <div class="roadmap-number">5</div>
            <h4>Nhận xét và sửa lỗi</h4>
            <p>Tìm kiếm nhận xét từ giáo viên hoặc người có kinh nghiệm, học cách tự đánh giá bài viết của mình và cải thiện liên tục.</p>
          </div>
        </div>
      </div>
      
      <div class="col-md-4">
        <h3 class="section-title">Bài viết gần đây</h3>
        
        <?php
        if (count($recent_essays) > 0) {
            foreach ($recent_essays as $essay) {
                echo '<div class="card">';
                echo '<div class="card-body">';
                echo '<h5 class="card-title">' . substr($essay['question'], 0, 80) . '...</h5>';
                echo '<p class="card-text text-muted">Nộp lúc: ' . date('d/m/Y H:i', strtotime($essay['submission_time'])) . '</p>';
                
                if ($essay['score']) {
                    $display_score = $essay['score'];
                    if (is_numeric($display_score) && floor($display_score) == $display_score) {
                        $display_score = number_format($display_score, 1);
                    }
                    echo '<p><span class="badge badge-success">Điểm: ' . $display_score . '</span></p>';
                } else {
                    echo '<p><span class="badge badge-warning">Chưa chấm điểm</span></p>';
                }
                
                $view_url = $logged_in ? "view_essay.php?id=" . $essay['id'] : "view_guest_essay.php?id=" . $essay['id'];
                echo '<a href="' . $view_url . '" class="btn btn-sm btn-outline-primary">Xem chi tiết</a>';
                echo '</div>';
                echo '</div>';
            }
        } else {
            echo '<div class="alert alert-info">Bạn chưa làm bài tập nào. Hãy bắt đầu luyện tập ngay!</div>';
            // Hiển thị thông báo cho người dùng chưa đăng nhập
            if (!$logged_in) {
                echo '<div class="alert alert-warning mt-3">';
                echo '<i class="fas fa-info-circle mr-2"></i> Tiến độ học tập của bạn được lưu trữ tạm thời. ';
                echo '<a href="register.php" class="alert-link">Đăng ký tài khoản</a> để lưu tiến độ vĩnh viễn!';
                echo '</div>';
            }
        }
        ?>
        <div class="progress-container">
          <h3 class="section-title">Tiến độ học tập</h3>
          
          <?php
          echo '<p>Bạn đã hoàn thành ' . $completed . '/' . $total_questions . ' bài tập</p>';
          echo '<div class="progress">';
          echo '<div class="进度条 bg-success" role="progressbar" style="width: ' . $percentage . '%" aria-valuenow="' . $percentage . '" aria-valuemin="0" aria-valuemax="100">' . $percentage . '%</div>';
          echo '</div>';
          // Hiển thị nút đồng bộ hóa tiến độ cho người dùng chưa đăng nhập
          if (!$logged_in) {
              echo '<div class="mt-2">';
              echo '<button class="btn btn-sm btn-outline-info" onclick="syncGuestProgress()"><i class="fas fa-sync mr-1"></i> Đồng bộ tiến độ</button>';
              echo '</div>';
          }
          ?>
          <div class="mt-4">
            <h5>Tài liệu học tập</h5>
            <ul class="list-group">
              <?php
              foreach ($lectures as $lecture) {
                  echo '<li class="list-group-item d-flex justify-content-between align-items-center">';
                  echo '<a href="view_lecture.php?id=' . $lecture['id'] . '">' . $lecture['title'] . '</a>';
                  echo '<span class="badge badge-primary badge-pill">' . $lecture['category'] . '</span>';
                  echo '</li>';
              }
              ?>
            </ul>
          </div>
          
          <?php if ($logged_in): ?>
          <div class="user-feedback mt-4">
            <h5>Đánh giá trải nghiệm</h5>
            <div class="card">
              <div class="card-body">
                <p>Chia sẻ đánh giá của bạn về trải nghiệm học tập!</p>
                <form id="userFeedbackForm">
                  <div class="form-group">
                    <label for="userRating">Đánh giá:</label>
                    <div class="star-rating" id="starRating">
                      <i class="fas fa-star" data-rating="1" data-toggle="tooltip" title="Không hài lòng"></i>
                      <i class="fas fa-star" data-rating="2" data-toggle="tooltip" title="Chưa hài lòng"></i>
                      <i class="fas fa-star" data-rating="3" data-toggle="tooltip" title="Bình thường"></i>
                      <i class="fas fa-star" data-rating="4" data-toggle="tooltip" title="Hài lòng"></i>
                      <i class="fas fa-star" data-rating="5" data-toggle="tooltip" title="Rất hài lòng"></i>
                    </div>
                    <input type="hidden" id="userRating" name="rating" value="0">
                  </div>
                  <div class="form-group">
                    <label for="userComment">Nhận xét (không bắt buộc):</label>
                    <textarea class="form-control" id="userComment" name="comment" rows="2"></textarea>
                  </div>
                  <button type="submit" class="btn btn-primary btn-sm">Gửi đánh giá</button>
                </form>
              </div>
            </div>
          </div>
          
          <script>
            // Xử lý đánh giá sao
            document.querySelectorAll('#starRating .fa-star').forEach(star => {
              star.addEventListener('click', function() {
                const rating = this.getAttribute('data-rating');
                document.getElementById('userRating').value = rating;
                // Xóa lớp filled khỏi tất cả các sao
                document.querySelectorAll('#starRating .fa-star').forEach(s => s.classList.remove('filled'));
                // Thêm lớp filled cho các sao từ 1 đến rating
                for (let i = 1; i <= rating; i++) {
                  document.querySelector(`#starRating .fa-star[data-rating="${i}"]`).classList.add('filled');
                }
              });
            });

            // Kích hoạt tooltip
            $(document).ready(function(){
              $('[data-toggle="tooltip"]').tooltip();
            });

            // Xử lý form đánh giá cho người dùng đã đăng nhập
            document.getElementById('userFeedbackForm').addEventListener('submit', function(e) {
              e.preventDefault();
              
              const rating = document.getElementById('userRating').value;
              const comment = document.getElementById('userComment').value;
              
              if (rating == 0) {
                alert('Vui lòng chọn số sao để đánh giá!');
                return;
              }

              fetch('save_user_feedback.php', {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                  user_id: '<?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : ""; ?>',
                  rating: rating,
                  comment: comment
                })
              })
              .then(response => response.json())
              .then(data => {
                if (data.success) {
                  alert('Cảm ơn bạn đã gửi đánh giá!');
                  document.getElementById('userComment').value = '';
                  // Reset sao
                  document.querySelectorAll('#starRating .fa-star').forEach(s => s.classList.remove('filled'));
                  document.getElementById('userRating').value = '0';
                } else {
                  alert('Đã xảy ra lỗi. Vui lòng thử lại sau.');
                }
              })
              .catch(error => {
                console.error('Error:', error);
                alert('Đã xảy ra lỗi khi gửi đánh giá.');
              });
            });
          </script>
          <?php endif; ?>
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

  <!-- Load JavaScript files at end of body for better performance -->
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