<?php
session_start();

// Kiểm tra người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Kết nối đến cơ sở dữ liệu
require 'config.php';

// Lấy danh sách bài làm (bao gồm bài luyện tập và full test)
$user_id = $_SESSION['user_id'];

// Truy vấn bài luyện tập (essay_responses không thuộc test_attempts)
$stmt_essays = $pdo->prepare("
    SELECT er.id, q.question, er.submission_time, er.score 
    FROM essay_responses er 
    JOIN ds_cau_hoi q ON er.question_id = q.id 
    WHERE er.user_id = ? 
    AND er.question_id NOT IN (
        SELECT ch.id 
        FROM ds_cau_hoi ch
        JOIN ds_de_thi_cau_hoi dth ON ch.id = dth.cau_hoi_id
        JOIN test_attempts ta ON dth.de_thi_id = ta.de_thi_id
        WHERE ta.user_id = ?
    )
    ORDER BY er.submission_time DESC
");
$stmt_essays->execute([$user_id, $user_id]);
$essays = $stmt_essays->fetchAll();

// Truy vấn bài full test (test_attempts)
$stmt_tests = $pdo->prepare("
    SELECT ta.id, dt.ten_de_thi AS title, ta.submission_time, ta.score
    FROM test_attempts ta
    JOIN ds_de_thi dt ON ta.de_thi_id = dt.id
    WHERE ta.user_id = ?
    ORDER BY ta.submission_time DESC
");
$stmt_tests->execute([$user_id]);
$tests = $stmt_tests->fetchAll();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>Bài viết của tôi</title>
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
            margin-bottom: 20px;
        }
        .footer {
            margin-top: 50px;
            background-color: #f1f1f1;
            padding: 20px 0;
            color: #666;
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
            <i class="fas fa-file-alt mr-2"></i>Bài viết của tôi
        </h3>
        <div class="row">
            <div class="col-md-12">
                <?php if (count($essays) > 0 || count($tests) > 0): ?>
                    <!-- Hiển thị bài full test -->
                    <?php foreach ($tests as $test): ?>
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo substr($test['title'], 0, 80) . '...'; ?> (Full Test)</h5>
                                <p class="card-text text-muted">Nộp lúc: <?php echo date('d/m/Y H:i', strtotime($test['submission_time'])); ?></p>
                                <?php if ($test['score']): ?>
                                    <?php 
                                        $display_score = $test['score'];
                                        if (is_numeric($display_score) && floor($display_score) == $display_score) {
                                            $display_score = number_format($display_score, 1);
                                        }
                                    ?>
                                    <p><span class="badge badge-success">Điểm: <?php echo $display_score; ?></span></p>
                                <?php else: ?>
                                    <p><span class="badge badge-warning">Chưa chấm điểm</span></p>
                                <?php endif; ?>
                                <?php if (isset($test['id']) && !empty($test['id'])): ?>
                                    <a href="view_test.php?id=<?php echo $test['id']; ?>" class="btn btn-sm btn-outline-primary">Xem chi tiết (ID: <?php echo $test['id']; ?>)</a>
                                <?php else: ?>
                                    <p class="text-danger">Lỗi: ID bài kiểm tra không hợp lệ</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <!-- Hiển thị bài luyện tập -->
                    <?php foreach ($essays as $essay): ?>
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo substr($essay['question'], 0, 80) . '...'; ?> (Practice)</h5>
                                <p class="card-text text-muted">Nộp lúc: <?php echo date('d/m/Y H:i', strtotime($essay['submission_time'])); ?></p>
                                <?php if ($essay['score']): ?>
                                    <?php 
                                        $display_score = $essay['score'];
                                        if (is_numeric($display_score) && floor($display_score) == $display_score) {
                                            $display_score = number_format($display_score, 1);
                                        }
                                    ?>
                                    <p><span class="badge badge-success">Điểm: <?php echo $display_score; ?></span></p>
                                <?php else: ?>
                                    <p><span class="badge badge-warning">Chưa chấm điểm</span></p>
                                <?php endif; ?>
                                <a href="view_essay.php?id=<?php echo $essay['id']; ?>" class="btn btn-sm btn-outline-primary">Xem chi tiết</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="alert alert-info">Bạn chưa làm bài tập hoặc bài kiểm tra nào. Hãy bắt đầu luyện tập ngay!</div>
                <?php endif; ?>
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