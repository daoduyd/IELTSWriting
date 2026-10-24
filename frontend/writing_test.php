<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
require 'config.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$question_id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM ds_cau_hoi WHERE id = ?");
$stmt->execute([$question_id]);
$question = $stmt->fetch();

if (!$question) {
    header("Location: index.php");
    exit;
}

// Lưu nội dung và thời gian vào session
$essay_content = '';
$start_time = time();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $user_id = $_SESSION['user_id'];
    $essay_content = trim($_POST['essay_content'] ?? '');
    $start_time = (int)$_POST['start_time'];
    $time_taken = ceil((time() - $start_time) / 60);
    $word_count = !empty($essay_content) ? str_word_count($essay_content) : 0;

    // Kiểm tra số từ tối thiểu
    $min_word_count = ($question['category'] === 'Writing Task 1') ? 150 : 250;
    if ($word_count < $min_word_count) {
        $error = "Bài viết phải có ít nhất {$min_word_count} từ. Hiện tại: {$word_count} từ.";
        $_SESSION['essay_content'] = $essay_content;
        $_SESSION['start_time'] = $start_time;
    } elseif (!empty($essay_content)) {
        $stmt = $pdo->prepare("
            INSERT INTO essay_responses (
                user_id, question_id, response_text, word_count, time_taken, submission_time
            ) VALUES (?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([$user_id, $question_id, $essay_content, $word_count, $time_taken]);
        unset($_SESSION['essay_content']);
        unset($_SESSION['start_time']);
        header("Location: my_essays.php?success=1");
        exit;
    } else {
        $error = "Vui lòng nhập nội dung bài viết trước khi nộp.";
        $_SESSION['essay_content'] = $essay_content;
        $_SESSION['start_time'] = $start_time;
    }
} elseif (isset($_SESSION['essay_content'])) {
    $essay_content = $_SESSION['essay_content'];
    $start_time = isset($_SESSION['start_time']) ? $_SESSION['start_time'] : time();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>Làm bài IELTS Writing - Đề số #<?php echo $question['id']; ?></title>
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
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #1565c0;
        color: #1565c0;
    }
    .card {
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    .btn-submit {
        background-color: #1565c0;
        color: white;
    }
    .btn-submit:hover {
        background-color: #0d47a1;
        color: white;
    }
    .question-image {
        max-width: 100%;
        height: auto;
        margin: 10px 0;
    }
    .timer {
        font-size: 1.2rem;
        font-weight: bold;
        color: #dc3545;
        margin: 5px 0;
    }
    #word_count {
        font-size: 14px;
        color: #666;
        margin: 5px 0;
    }
    .footer {
        margin-top: 50px;
        background-color: #f1f1f1;
        padding: 20px 0;
        color: #666;
    }
    /* Lexibot and NotebookLM Chat Button Styles */
    .notebooklm-chat-button, .lexibot-chat-button {
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
    .lexibot-chat-button {
      bottom: 90px; /* Position above NotebookLM button */
      background-color: #28a745; /* Different color to distinguish */
    }
    .notebooklm-chat-button:hover, .lexibot-chat-button:hover {
      background-color: #0d47a1;
    }
    .lexibot-chat-button:hover {
      background-color: #218838;
    }
    .notebooklm-interaction, .lexibot-interaction {
      position: fixed;
      bottom: 160px;
      right: 20px;
      width: 350px;
      background-color: white;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
      display: none;
      z-index: 1000;
    }
    .lexibot-interaction {
      bottom: 230px; /* Position above NotebookLM interaction */
    }
    .notebooklm-interaction.active, .lexibot-interaction.active {
      display: block;
    }
   YS .notebooklm-interaction-header, .lexibot-interaction-header {
      background-color: #1565c0;
      color: white;
      padding: 10px 15px;
      border-top-left-radius: 10px;
      border-top-right-radius: 10px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .lexibot-interaction-header {
      background-color: #28a745;
    }
    .notebooklm-interaction-header h2, .lexibot-interaction-header h2 {
      font-size: 1.2rem;
      margin: 0;
    }
    .notebooklm-interaction-header .close-chat, .lexibot-interaction-header .close-chat {
      cursor: pointer;
      font-size: 1.2rem;
    }
    .notebooklm-interaction-body, .lexibot-interaction-body {
      padding: 15px;
    }
    .notebooklm-interaction .form-group, .lexibot-interaction .form-group {
      margin-bottom: 15px;
    }
    .notebooklm-interaction textarea, .lexibot-interaction textarea {
      height: 100px;
      resize: none;
    }
    .notebooklm-interaction .btn-view, .lexibot-interaction .btn-view {
      width: 100%;
    }
    .lexibot-interaction .btn-view {
      background-color: #28a745;
    }
    .lexibot-interaction .btn-view:hover {
      background-color: #218838;
    }
    .notebooklm-instruction, .lexibot-instruction {
      background-color: #f8f9fa;
      padding: 10px;
      border-left: 4px solid #1565c0;
      margin-top: 15px;
    }
    .lexibot-instruction {
      border-left: 4px solid #28a745;
    }
    .notebooklm-instruction p, .lexibot-instruction p {
      margin: 0;
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

    <div class="container">
        <h3 class="section-title">
            <i class="fas fa-pen mr-2"></i>Làm bài IELTS Writing - Đề số #<?php echo $question['id']; ?>
        </h3>
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($question['question']); ?></h5>
                        <?php if (!empty($question['question_image'])): ?>
                            <img src="<?php echo htmlspecialchars($question['question_image']); ?>" alt="Question Image" class="question-image">
                            <?php if (!empty($question['caption'])): ?>
                                <p class="text-center text-muted"><?php echo htmlspecialchars($question['caption']); ?></p>
                            <?php endif; ?>
                        <?php endif; ?>
                        <p><i class="far fa-clock mr-1"></i>Thời gian khuyến nghị: <?php echo $question['time_limit']; ?> phút</p>
                        <p><i class="fas fa-font mr-1"></i>Số từ tối thiểu: <?php echo $question['word_count']; ?> từ</p>
                        <div class="timer" id="timer">Thời gian còn lại: <?php echo ($question['category'] === 'Writing Task 1' ? '20:00' : '40:00'); ?></div>
                        <p id="word_count">Số từ: 0</p>
                    </div>
                </div>
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                <form id="writing_form" method="POST" action="">
                    <input type="hidden" id="start_time" name="start_time" value="<?php echo $start_time; ?>">
                    <div class="form-group">
                        <label for="essay_content"><strong>Bài viết của bạn:</strong></label>
                        <textarea class="form-control" id="essay_content" name="essay_content" rows="15" placeholder="Nhập bài viết của bạn tại đây..."><?php echo htmlspecialchars($essay_content); ?></textarea>
                    </div>
                    <button type="submit" name="submit" id="submit_button" class="btn btn-submit"><i class="fas fa-paper-plane mr-2"></i>Nộp bài</button>
                </form>
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

    <div class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-12 text-center">
                    <p>&copy; <?php echo date('Y'); ?> IELTS Writing Practice. Tất cả quyền được bảo lưu.</p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
    $(document).ready(function() {
        let hasSubmitted = false; // Flag to prevent multiple submissions

        // Word count update
        function updateWordCount() {
            let text = $("#essay_content").val();
            let wordCount = text.trim().split(/\s+/).length;
            if (text === '') wordCount = 0;
            $("#word_count").text("Số từ: " + wordCount);
            return wordCount;
        }
        $("#essay_content").on('input', updateWordCount);
        updateWordCount(); // Cập nhật số từ ban đầu

        // Timer logic
        let startTime = <?php echo $start_time; ?>;
        let timeLimit = <?php echo ($question['category'] === 'Writing Task 1' ? 20 : 40) * 60; ?>;
        let fiveMinWarning = false;
        let oneMinWarning = false;

        function updateTimer() {
            let currentTime = Math.floor(Date.now() / 1000);
            let timeElapsed = currentTime - startTime;
            let timeRemaining = timeLimit - timeElapsed;
            if (timeRemaining < 0) {
                timeRemaining = 0;
            }
            let minutes = Math.floor(timeRemaining / 60);
            let seconds = timeRemaining % 60;
            seconds = seconds < 10 ? "0" + seconds : seconds;
            $("#timer").text("Thời gian còn lại: " + minutes + ":" + seconds);
            if (timeRemaining <= 5 * 60 && timeRemaining > 4 * 60 + 59 && !fiveMinWarning) {
                alert("Cảnh báo: Còn 5 phút để hoàn thành bài viết!");
                fiveMinWarning = true;
            }
            if (timeRemaining <= 60 && timeRemaining > 59 && !oneMinWarning) {
                alert("Cảnh báo: Chỉ còn 1 phút để nộp bài!");
                oneMinWarning = true;
            }
            if (timeRemaining <= 0 && !hasSubmitted) {
                hasSubmitted = true;
                alert("Hết thời gian! Bài của bạn sẽ được nộp tự động.");
                $("#writing_form").submit();
            }
        }
        setInterval(updateTimer, 1000);
        updateTimer();

        // Confirm submission
        $("#writing_form").on('submit', function(e) {
            if (!hasSubmitted) {
                let wordCount = updateWordCount();
                let minWordCount = <?php echo ($question['category'] === 'Writing Task 1' ? 150 : 250); ?>;
                if (wordCount >= minWordCount) {
                    if (!confirm("Bạn chắc chắn muốn nộp bài chứ?")) {
                        e.preventDefault();
                        return false;
                    }
                    hasSubmitted = true; // Prevent multiple submissions
                }
            }
        });

        // Confirm before leaving page
        $(window).on('beforeunload', function(e) {
            if (!hasSubmitted) {
                let confirmationMessage = "Bạn chắc chắn muốn thoát khỏi bài luyện tập này? Nếu đồng ý thoát, bài làm sẽ không được lưu.";
                (e || window.event).returnValue = confirmationMessage; // Standard for most browsers
                return confirmationMessage; // For some older browsers
            }
        });

        // Dropdown submenu handling
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