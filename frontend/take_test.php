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

$test_id = $_GET['id'];

// Fetch one question for Task 1 associated with the test
$stmt_task1 = $pdo->prepare("
    SELECT ch.* 
    FROM ds_cau_hoi ch
    JOIN ds_de_thi_cau_hoi dth ON ch.id = dth.cau_hoi_id
    WHERE dth.de_thi_id = ? AND ch.category = 'Writing Task 1' 
    LIMIT 1
");
$stmt_task1->execute([$test_id]);
$question_task1 = $stmt_task1->fetch();

// Fetch one question for Task 2 associated with the test
$stmt_task2 = $pdo->prepare("
    SELECT ch.* 
    FROM ds_cau_hoi ch
    JOIN ds_de_thi_cau_hoi dth ON ch.id = dth.cau_hoi_id
    WHERE dth.de_thi_id = ? AND ch.category = 'Writing Task 2' 
    LIMIT 1
");
$stmt_task2->execute([$test_id]);
$question_task2 = $stmt_task2->fetch();

if (!$question_task1 || !$question_task2) {
    header("Location: index.php");
    exit;
}

// Lưu nội dung và thời gian vào session
$content_task1 = '';
$content_task2 = '';
$start_time = time();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $user_id = $_SESSION['user_id'];
    $content_task1 = trim($_POST['essay_content_task1'] ?? '');
    $content_task2 = trim($_POST['essay_content_task2'] ?? '');
    $start_time = (int)$_POST['start_time'];
    $time_taken = ceil((time() - $start_time) / 60);
    $word_count_task1 = !empty($content_task1) ? str_word_count($content_task1) : 0;
    $word_count_task2 = !empty($content_task2) ? str_word_count($content_task2) : 0;

    // Khởi tạo biến $error
    $error = '';

    // Kiểm tra số từ tối thiểu cho cả hai task
    if ($word_count_task1 < 150) {
        $error .= "Bài viết Task 1 phải có ít nhất 150 từ. Hiện tại: {$word_count_task1} từ. ";
    }
    if ($word_count_task2 < 250) {
        $error .= "Bài viết Task 2 phải có ít nhất 250 từ. Hiện tại: {$word_count_task2} từ. ";
    }

    // Chỉ lưu bài nếu không có lỗi về số từ
    if (empty($error)) {
        // Insert response for Task 1
        $stmt = $pdo->prepare("
            INSERT INTO essay_responses (
                user_id, question_id, response_text, word_count, time_taken, submission_time
            ) VALUES (?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([$user_id, $question_task1['id'], $content_task1, $word_count_task1, $time_taken]);

        // Insert response for Task 2
        $stmt = $pdo->prepare("
            INSERT INTO essay_responses (
                user_id, question_id, response_text, word_count, time_taken, submission_time
            ) VALUES (?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([$user_id, $question_task2['id'], $content_task2, $word_count_task2, $time_taken]);

        // Lưu vào bảng test_attempts (sử dụng created_at tự động)
        $content_combined = "Task 1:\n" . $content_task1 . "\n\nTask 2:\n" . $content_task2;
        $stmt = $pdo->prepare("
            INSERT INTO test_attempts (
                user_id, de_thi_id, content
            ) VALUES (?, ?, ?)
        ");
        $stmt->execute([$user_id, $test_id, $content_combined]);

        // Xóa session sau khi nộp thành công
        unset($_SESSION['content_task1']);
        unset($_SESSION['content_task2']);
        unset($_SESSION['start_time']);
        header("Location: my_essays.php?success=1");
        exit;
    } else {
        // Lưu nội dung vào session nếu có lỗi
        $_SESSION['content_task1'] = $content_task1;
        $_SESSION['content_task2'] = $content_task2;
        $_SESSION['start_time'] = $start_time;
    }
} elseif (isset($_SESSION['content_task1']) || isset($_SESSION['content_task2'])) {
    $content_task1 = $_SESSION['content_task1'] ?? '';
    $content_task2 = $_SESSION['content_task2'] ?? '';
    $start_time = isset($_SESSION['start_time']) ? $_SESSION['start_time'] : time();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>Làm bài IELTS Writing - Đề số #<?php echo $test_id; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
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

        .section-title {
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #1565c0;
            color: #1565c0;
        }

        .card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
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

        #word_count_task1, #word_count_task2 {
            font-size: 14px;
            color: #666;
            margin: 5px 0;
        }

        .footer {
            margin-top: 50px;
            padding: 20px 0;
            background-color: #293241;
            color: white;
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
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="practicesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Practices
                        </a>
                        <div class="dropdown-menu" aria-labelledby="practicesDropdown">
                            <a class="dropdown-item" href="practice_task1.php">Writing Task 1</a>
                            <a class="dropdown-item" href="practice_task2.php">Writing Task 2</a>
                        </div>
                    </li>
                    <li class="nav-item active">
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
            <i class="fas fa-pen mr-2"></i>Làm bài IELTS Writing - Đề số #<?php echo $test_id; ?>
        </h3>
        <div class="row">
            <div class="col-md-12">
                <!-- Writing Task 1 -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Writing Task 1: <?php echo htmlspecialchars($question_task1['question']); ?></h5>
                        <?php if (!empty($question_task1['question_image']) && file_exists($question_task1['question_image'])): ?>
                            <img src="<?php echo htmlspecialchars($question_task1['question_image']); ?>" alt="Task 1 Question Image" class="question-image">
                            <?php if (!empty($question_task1['caption'])): ?>
                                <p class="text-center text-muted"><?php echo htmlspecialchars($question_task1['caption']); ?></p>
                            <?php endif; ?>
                        <?php endif; ?>
                        <p><i class="far fa-clock mr-1"></i>Thời gian khuyến nghị: 20 phút</p>
                        <p><i class="fas fa-font mr-1"></i>Số từ tối thiểu: <?php echo $question_task1['word_count']; ?> từ</p>
                        <div class="timer" id="timer">Thời gian còn lại: 60:00</div>
                        <p id="word_count_task1">Số từ: 0</p>
                    </div>
                </div>

                <!-- Writing Task 2 -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Writing Task 2: <?php echo htmlspecialchars($question_task2['question']); ?></h5>
                        <?php if (!empty($question_task2['question_image']) && file_exists($question_task2['question_image'])): ?>
                            <img src="<?php echo htmlspecialchars($question_task2['question_image']); ?>" alt="Task 2 Question Image" class="question-image">
                            <?php if (!empty($question_task2['caption'])): ?>
                                <p class="text-center text-muted"><?php echo htmlspecialchars($question_task2['caption']); ?></p>
                            <?php endif; ?>
                        <?php endif; ?>
                        <p><i class="far fa-clock mr-1"></i>Thời gian khuyến nghị: 40 phút</p>
                        <p><i class="fas fa-font mr-1"></i>Số từ tối thiểu: <?php echo $question_task2['word_count']; ?> từ</p>
                        <p id="word_count_task2">Số từ: 0</p>
                    </div>
                </div>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <form id="writing_form" method="POST" action="">
                    <input type="hidden" id="start_time" name="start_time" value="<?php echo $start_time; ?>">
                    <!-- Task 1 Submission -->
                    <div class="form-group">
                        <label for="essay_content_task1"><strong>Bài viết Task 1:</strong></label>
                        <textarea class="form-control" id="essay_content_task1" name="essay_content_task1" rows="15" placeholder="Nhập bài viết Task 1 của bạn tại đây..."><?php echo htmlspecialchars($content_task1); ?></textarea>
                    </div>

                    <!-- Task 2 Submission -->
                    <div class="form-group">
                        <label for="essay_content_task2"><strong>Bài viết Task 2:</strong></label>
                        <textarea class="form-control" id="essay_content_task2" name="essay_content_task2" rows="15" placeholder="Nhập bài viết Task 2 của bạn tại đây..."><?php echo htmlspecialchars($content_task2); ?></textarea>
                    </div>

                    <button type="submit" name="submit" id="submit_button" class="btn btn-submit"><i class="fas fa-paper-plane mr-2"></i>Nộp bài</button>
                </form>
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

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            let hasSubmitted = false; // Flag to prevent multiple submissions

            // Timer for 60 minutes
            let startTime = <?php echo $start_time; ?>;
            let timeLimit = 60 * 60; // 60 minutes in seconds
            let fiveMinWarning = false;
            let oneMinWarning = false;

            function updateTimer() {
                let currentTime = Math.floor(Date.now() / 1000);
                let timeElapsed = currentTime - startTime;
                let timeRemaining = timeLimit - timeElapsed;
                if (timeRemaining < 0) {
                    timeRemaining = 0;
                }
                const minutes = Math.floor(timeRemaining / 60);
                const seconds = timeRemaining % 60;
                $('#timer').text(`Thời gian còn lại: ${minutes}:${seconds < 10 ? '0' + seconds : seconds}`);

                if (timeRemaining <= 5 * 60 && timeRemaining > 4 * 60 + 59 && !fiveMinWarning) {
                    alert("Cảnh báo: Còn 5 phút để hoàn thành bài thi!");
                    fiveMinWarning = true;
                }
                if (timeRemaining <= 60 && timeRemaining > 59 && !oneMinWarning) {
                    alert("Cảnh báo: Chỉ còn 1 phút để nộp bài!");
                    oneMinWarning = true;
                }

                if (timeRemaining <= 0 && !hasSubmitted) {
                    hasSubmitted = true;
                    alert("Hết thời gian! Bài của bạn sẽ được nộp tự động.");
                    $('#writing_form').submit();
                }
            }
            setInterval(updateTimer, 1000);
            updateTimer();

            // Word count for Task 1
            function updateWordCountTask1() {
                const text = $('#essay_content_task1').val().trim();
                const wordCount = text ? text.split(/\s+/).length : 0;
                $('#word_count_task1').text(`Số từ: ${wordCount}`);
                return wordCount;
            }
            $('#essay_content_task1').on('input', updateWordCountTask1);
            updateWordCountTask1(); // Initial word count for Task 1

            // Word count for Task 2
            function updateWordCountTask2() {
                const text = $('#essay_content_task2').val().trim();
                const wordCount = text ? text.split(/\s+/).length : 0;
                $('#word_count_task2').text(`Số từ: ${wordCount}`);
                return wordCount;
            }
            $('#essay_content_task2').on('input', updateWordCountTask2);
            updateWordCountTask2(); // Initial word count for Task 2

            // Confirm submission
            $('#writing_form').on('submit', function(e) {
                if (!hasSubmitted) {
                    const wordCountTask1 = updateWordCountTask1();
                    const wordCountTask2 = updateWordCountTask2();
                    if (wordCountTask1 >= 150 && wordCountTask2 >= 250) {
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
                    let confirmationMessage = "Bạn chắc chắn muốn thoát khỏi bài kiểm tra này? Nếu đồng ý thoát, bài làm sẽ không được lưu.";
                    (e || window.event).returnValue = confirmationMessage;
                    return confirmationMessage;
                }
            });

            // Dropdown submenu handling
            $('.dropdown-submenu a.dropdown-toggle').on('click', function(e) {
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