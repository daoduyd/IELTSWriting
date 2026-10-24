<?php
session_start();

// Kiểm tra người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Kết nối đến cơ sở dữ liệu
require 'config.php';

// Hàm lấy tổng số câu hỏi
function getTotalQuestions($pdo, $search = '', $topic = '', $question_type = '', $difficulty_level = '') {
    $sql = "SELECT COUNT(*) as total FROM ds_cau_hoi WHERE category = 'Writing Task 2'";
    $params = array();
    
    if (!empty($search)) {
        $sql .= " AND question LIKE :search";
        $params[':search'] = '%' . $search . '%';
    }
    
    if (!empty($topic) && $topic !== 'Others') {
        $sql .= " AND topic = :topic";
        $params[':topic'] = $topic;
    } elseif ($topic === 'Others') {
        $sql .= " AND (topic IS NULL OR topic NOT IN (SELECT DISTINCT topic FROM ds_cau_hoi WHERE topic IS NOT NULL AND category = 'Writing Task 2'))";
    }
    
    if (!empty($question_type)) {
        $sql .= " AND question_type = :question_type";
        $params[':question_type'] = $question_type;
    }
    
    if (!empty($difficulty_level)) {
        $sql .= " AND difficulty_level = :difficulty_level";
        $params[':difficulty_level'] = $difficulty_level;
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return $result['total'];
}

// Hàm lấy danh sách câu hỏi
function getQuestions($pdo, $page, $perPage, $search = '', $topic = '', $question_type = '', $difficulty_level = '') {
    $offset = ($page - 1) * $perPage;
    
    $sql = "SELECT * FROM ds_cau_hoi WHERE category = 'Writing Task 2'";
    $params = array();
    
    if (!empty($search)) {
        $sql .= " AND question LIKE :search";
        $params[':search'] = '%' . $search . '%';
    }
    
    if (!empty($topic) && $topic !== 'Others') {
        $sql .= " AND topic = :topic";
        $params[':topic'] = $topic;
    } elseif ($topic === 'Others') {
        $sql .= " AND (topic IS NULL OR topic NOT IN (SELECT DISTINCT topic FROM ds_cau_hoi WHERE topic IS NOT NULL AND category = 'Writing Task 2'))";
    }
    
    if (!empty($question_type)) {
        $sql .= " AND question_type = :question_type";
        $params[':question_type'] = $question_type;
    }
    
    if (!empty($difficulty_level)) {
        $sql .= " AND difficulty_level = :difficulty_level";
        $params[':difficulty_level'] = $difficulty_level;
    }
    
    $sql .= " ORDER BY created_at ASC LIMIT :offset, :perPage";
    $stmt = $pdo->prepare($sql);
    
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
    
    $stmt->execute();
    $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Gán số thứ tự practice_number trong PHP
    $practiceNumber = $offset + 1; // Bắt đầu từ số thứ tự của trang hiện tại
    foreach ($questions as &$question) {
        $question['practice_number'] = $practiceNumber++;
    }
    unset($question); // Xóa tham chiếu để tránh ảnh hưởng ngoài vòng lặp
    
    return $questions;
}

// Hàm lấy danh sách chủ đề
function getTopics() {
    return [
        'Education', 'Crime', 'Health', 'Work', 'Technology', 'Science', 'Advertising', 'Environment',
        'Money', 'Food & Drinks', 'Sports & Leisure', 'Art & Culture', 'Society', 'Transport',
        'Personality', 'Language', 'Tourism', 'Gender'
    ];
}

// Hàm lấy danh sách dạng bài
function getQuestionTypes($pdo) {
    $sql = "SELECT DISTINCT question_type FROM ds_cau_hoi WHERE question_type IS NOT NULL AND category = 'Writing Task 2' ORDER BY question_type";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

// Thiết lập giá trị mặc định
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$perPage = 10;
$search = isset($_GET['search']) ? $_GET['search'] : '';
$topic = isset($_GET['topic']) ? $_GET['topic'] : '';
$question_type = isset($_GET['question_type']) ? $_GET['question_type'] : '';
$difficulty_level = isset($_GET['difficulty_level']) ? $_GET['difficulty_level'] : '';

// Tính tổng số trang
$totalQuestions = getTotalQuestions($pdo, $search, $topic, $question_type, $difficulty_level);
$totalPages = ceil($totalQuestions / $perPage);

// Lấy dữ liệu
$questions = getQuestions($pdo, $page, $perPage, $search, $topic, $question_type, $difficulty_level);
$topics = getTopics();
$question_types = getQuestionTypes($pdo);
$difficulty_levels = ['Beginner', 'Intermediate', 'Advanced'];
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <title>Luyện tập IELTS Writing Task 2</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
  <style>
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
    body {
      background-color: #f8f9fa;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .card {
      margin-bottom: 20px;
      border-radius: 10px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    .card-header {
      background-color: #e8f5e9;
      color: #1565c0;
      font-weight: bold;
    }
    .btn-start {
      background-color: #1565c0;
      color: white;
    }
    .btn-start:hover {
      background-color: #0d47a1;
      color: white;
    }
    .section-title {
      margin-bottom: 20px;
      padding-bottom: 10px;
      border-bottom: 2px solid #1565c0;
      color: #1565c0;
    }
    .question-info {
      display: flex;
      justify-content: space-between;
      margin-top: 10px;
      color: #666;
    }
    .footer {
      margin-top: 50px;
      background-color: #f1f1f1;
      padding: 20px 0;
      color: #666;
    }
    .pagination {
      justify-content: center;
      margin-top: 30px;
    }
    .question-tag {
      display: inline-block;
      background-color: #e8f5e9;
      color: #1565c0;
      padding: 2px 8px;
      border-radius: 10px;
      font-size: 12px;
      margin-right: 5px;
    }
    .filters {
      margin-bottom: 20px;
      padding: 15px;
      background-color: #f5f5f5;
      border-radius: 4px;
    }
    .question-meta .badge {
      margin-right: 5px;
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
          <li class="nav-item dropdown">
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
          <li class="nav-item dropdown active">
            <a class="nav-link dropdown-toggle" href="#" id="practicesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              Practices
            </a>
            <div class="dropdown-menu" aria-labelledby="practicesDropdown">
              <a class="dropdown-item" href="practice_task1.php">Writing Task 1</a>
              <a class="dropdown-item active" href="practice_task2.php">Writing Task 2</a>
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
                <strong>Xin chào, <?php echo htmlspecialchars($_SESSION['username']); ?>!</strong>
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
    <!-- Search and Filters -->
    <div class="filters">
      <form method="GET" action="practice_task2.php" class="form-horizontal">
        <div class="row">
          <div class="col-md-3">
            <div class="form-group">
              <label for="search" class="control-label">Tìm kiếm:</label>
              <input type="text" class="form-control" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Tìm kiếm câu hỏi...">
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label for="topic" class="control-label">Chủ đề:</label>
              <select class="form-control" id="topic" name="topic">
                <option value="">Tất cả chủ đề</option>
                <?php foreach ($topics as $top): ?>
                <option value="<?php echo htmlspecialchars($top); ?>" <?php echo ($topic == $top) ? 'selected' : ''; ?>>
                  <?php echo htmlspecialchars($top); ?>
                </option>
                <?php endforeach; ?>
                <option value="Others" <?php echo ($topic == 'Others') ? 'selected' : ''; ?>>Khác</option>
              </select>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label for="question_type" class="control-label">Dạng bài:</label>
              <select class="form-control" id="question_type" name="question_type">
                <option value="">Tất cả dạng bài</option>
                <?php foreach ($question_types as $type): ?>
                <option value="<?php echo htmlspecialchars($type); ?>" <?php echo ($question_type == $type) ? 'selected' : ''; ?>>
                  <?php echo htmlspecialchars($type); ?>
                </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label for="difficulty_level" class="control-label">Độ khó:</label>
              <select class="form-control" id="difficulty_level" name="difficulty_level">
                <option value="">Tất cả cấp độ</option>
                <?php foreach ($difficulty_levels as $level): ?>
                <option value="<?php echo htmlspecialchars($level); ?>" <?php echo ($difficulty_level == $level) ? 'selected' : ''; ?>>
                  <?php echo htmlspecialchars($level); ?>
                </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div class="col-md-1">
            <div class="form-group">
              <label class="control-label"> </label>
              <button type="submit" class="btn btn-primary form-control">Lọc</button>
            </div>
          </div>
        </div>
      </form>
    </div>

    <div class="row">
      <div class="col-md-8">
        <div class="alert alert-info">
          <h5><i class="fas fa-info-circle mr-2"></i>Giới thiệu về IELTS Writing Task 2</h5>
          <p>Task 2 yêu cầu bạn viết một bài luận thể hiện quan điểm về một vấn đề xã hội, giáo dục, môi trường hoặc văn hóa. Bạn cần viết ít nhất 250 từ trong khoảng 40 phút.</p>
          <ul>
            <li>Phân tích đề bài một cách chính xác</li>
            <li>Phát triển ý tưởng và quan điểm rõ ràng</li>
            <li>Cung cấp ví dụ và lập luận thuyết phục</li>
            <li>Sử dụng ngôn ngữ học thuật và cấu trúc bài luận hợp lý</li>
          </ul>
        </div>

        <?php if (count($questions) > 0): ?>
          <?php foreach ($questions as $question): ?>
            <div class="card mb-4">
              <div class="card-header">
                Bài luyện tập số #<?php echo $question['practice_number']; ?>
              </div>
              <div class="card-body">
                <h5 class="card-title"><?php echo htmlspecialchars($question['question']); ?></h5>
                <div class="question-meta">
                  <span class="badge badge-info"><?php echo htmlspecialchars($question['topic'] ?? 'Khác'); ?></span>
                  <span class="badge badge-warning"><?php echo htmlspecialchars($question['question_type'] ?? 'N/A'); ?></span>
                  <span class="badge badge-success"><?php echo htmlspecialchars($question['difficulty_level'] ?? 'N/A'); ?></span>
                </div>
                
                <?php if (!empty($question['question_image'])): ?>
                  <img src="<?php echo htmlspecialchars($question['question_image']); ?>" alt="Question Image" class="img-fluid my-3">
                  <?php if (!empty($question['caption'])): ?>
                    <p class="text-center text-muted"><?php echo htmlspecialchars($question['caption']); ?></p>
                  <?php endif; ?>
                <?php endif; ?>
                
                <div class="question-info">
                  <span><i class="far fa-clock mr-1"></i><?php echo $question['time_limit']; ?> phút</span>
                  <span><i class="fas fa-font mr-1"></i>Tối thiểu <?php echo $question['word_count']; ?> từ</span>
                </div>
                
                <div class="mt-3">
                  <a href="writing_test.php?id=<?php echo $question['id']; ?>" class="btn btn-start">
                    <i class="fas fa-pen mr-1"></i>Bắt đầu làm bài
                  </a>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
          
          <!-- Pagination -->
          <?php if ($totalPages > 1): ?>
          <nav>
            <ul class="pagination">
              <?php if ($page > 1): ?>
              <li class="page-item">
                <a class="page-link" href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&topic=<?php echo urlencode($topic); ?>&question_type=<?php echo urlencode($question_type); ?>&difficulty_level=<?php echo urlencode($difficulty_level); ?>">Trang trước</a>
              </li>
              <?php endif; ?>
              
              <?php for ($i = 1; $i <= $totalPages; $i++): ?>
              <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&topic=<?php echo urlencode($topic); ?>&question_type=<?php echo urlencode($question_type); ?>&difficulty_level=<?php echo urlencode($difficulty_level); ?>"><?php echo $i; ?></a>
              </li>
              <?php endfor; ?>
              
              <?php if ($page < $totalPages): ?>
              <li class="page-item">
                <a class="page-link" href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&topic=<?php echo urlencode($topic); ?>&question_type=<?php echo urlencode($question_type); ?>&difficulty_level=<?php echo urlencode($difficulty_level); ?>">Trang sau</a>
              </li>
              <?php endif; ?>
            </ul>
          </nav>
          <?php endif; ?>
          
        <?php else: ?>
          <div class="alert alert-warning">
            <p>Hiện tại chưa có câu hỏi Task 2 nào phù hợp với bộ lọc. Vui lòng thử điều chỉnh bộ lọc.</p>
          </div>
        <?php endif; ?>
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
            <i class="fas fa-lightbulb mr-2"></i>Mẹo làm bài Task 2
          </div>
          <div class="card-body">
            <ul class="list-group list-group-flush">
              <li class="list-group-item">
                <b>Phân tích đề bài:</b> Hiểu chính xác yêu cầu của đề bài (quan điểm, so sánh, ưu nhược điểm)
              </li>
              <li class="list-group-item">
                <b>Lập dàn ý:</b> Dành 5 phút đầu để lập dàn ý trước khi viết
              </li>
              <li class="list-group-item">
                <b>Cấu trúc rõ ràng:</b> Mở bài, 2-3 thân bài với ý chính, kết luận
              </li>
              <li class="list-group-item">
                <b>Luận điểm mạnh:</b> Mỗi đoạn chỉ tập trung vào một ý chính với ví dụ cụ thể
              </li>
              <li class="list-group-item">
                <b>Từ vựng phong phú:</b> Sử dụng từ vựng học thuật và tránh lặp lại
              </li>
              <li class="list-group-item">
                <b>Đa dạng ngữ pháp:</b> Sử dụng nhiều cấu trúc câu phức, mệnh đề
              </li>
              <li class="list-group-item">
                <b>Quản lý thời gian:</b> Dành ít nhất 5 phút để kiểm tra lại bài viết
              </li>
            </ul>
          </div>
        </div>
        
        <div class="card mt-4">
          <div class="card-header">
            <i class="fas fa-book mr-2"></i>Tài liệu học tập
          </div>
          <div class="card-body">
            <ul class="list-group list-group-flush">
              <li class="list-group-item">
                <a href="lessons_task2_topics.php#difficultyModalEducation">Hướng dẫn viết bài luận về Giáo dục</a>
              </li>
              <li class="list-group-item">
                <a href="lessons_task2_types.php#difficultyModalAgreeDisagree">Hướng dẫn viết bài luận Agree/Disagree</a>
              </li>
              <li class="list-group-item">
                <a href="lessons_task2_topics.php#difficultyModalEnvironment">Hướng dẫn viết bài luận về Môi trường</a>
              </li>
              <li class="list-group-item">
                <a href="lessons_task2_types.php#difficultyModalDiscussion">Hướng dẫn viết bài luận Discussion</a>
              </li>
              <li class="list-group-item">
                <a href="lessons_task2_topics.php#difficultyModalTechnology">Hướng dẫn viết bài luận về Công nghệ</a>
              </li>
            </ul>
            <a href="lessons_task2_types.php" class="btn btn-outline-primary btn-sm mt-3">
              <i class="fas fa-list mr-1"></i>Xem tất cả tài liệu
            </a>
          </div>
        </div>
        
        <div class="card mt-4">
          <div class="card-header">
            <i class="fas fa-graduation-cap mr-2"></i>Các loại bài Task 2
          </div>
          <div class="card-body">
            <div class="list-group">
              <a href="#" class="list-group-item list-group-item-action">
                <div class="d-flex w-100 justify-content-between">
                  <h6 class="mb-1">Bài luận quan điểm (Opinion Essay)</h6>
                </div>
                <small>Bày tỏ quan điểm cá nhân về một vấn đề</small>
              </a>
              <a href="#" class="list-group-item list-group-item-action">
                <div class="d-flex w-100 justify-content-between">
                  <h6 class="mb-1">Bài luận tranh luận (Discussion Essay)</h6>
                </div>
                <small>Thảo luận cả hai mặt của vấn đề</small>
              </a>
              <a href="#" class="list-group-item list-group-item-action">
                <div class="d-flex w-100 justify-content-between">
                  <h6 class="mb-1">Bài luận ưu nhược điểm (Advantages/Disadvantages)</h6>
                </div>
                <small>Phân tích lợi ích và hạn chế</small>
              </a>
              <a href="#" class="list-group-item list-group-item-action">
                <div class="d-flex w-100 justify-content-between">
                  <h6 class="mb-1">Bài luận nguyên nhân và giải pháp (Problem/Solution)</h6>
                </div>
                <small>Trình bày vấn đề và đề xuất giải pháp</small>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <div class="footer">
    <div class="container">
      <div class="row">
        <div class="col-md-12 text-center">
          <p>© <?php echo date('Y'); ?> IELTS Writing Practice. Tất cả quyền được bảo lưu.</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Scripts -->
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