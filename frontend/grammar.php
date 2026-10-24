<?php
session_start();
$logged_in = isset($_SESSION['user_id']);
require 'config.php';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <title>Grammar Guide - IELTS Writing Practice</title>
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
    .grammar-box {
      padding: 20px;
      border-radius: 8px;
      margin-bottom: 20px;
      background-color: #f8f9fa;
      border-left: 4px solid #3d5a80;
    }
    .example {
      background-color: #e9ecef;
      padding: 10px;
      border-radius: 5px;
      margin-top: 10px;
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
          
          <li class="nav-item">
            <a class="nav-link" href="tests.php">Tests</a>
          </li>
          
          <li class="nav-item active">
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

  <!-- Grammar Content -->
  <div class="container mt-5">
    <h1 class="section-title">Hướng Dẫn Ngữ Pháp Tiếng Anh</h1>
    <p>Phần này cung cấp tổng quan chi tiết và đầy đủ nhất về các loại từ, thì động từ, và cấu trúc câu trong tiếng Anh.</p>

    <!-- Word Classes -->
    <h2 class="section-title">1. Các Loại Từ (Word Classes)</h2>

    <div class="grammar-box">
      <h4>1.1 Danh Từ (Nouns)</h4>
      <p><strong>Định nghĩa:</strong> Danh từ chỉ người, vật, nơi chốn, khái niệm, hoặc ý tưởng (e.g., teacher, book, London, happiness).</p>
      <p><strong>Phân loại:</strong></p>
      <ul>
        <li><strong>Danh từ cụ thể (Concrete Nouns):</strong> Chỉ vật thể hoặc con người hữu hình (e.g., dog, car, John).</li>
        <li><strong>Danh từ trừu tượng (Abstract Nouns):</strong> Chỉ khái niệm, cảm xúc, hoặc trạng thái (e.g., love, freedom, courage).</li>
        <li><strong>Danh từ đếm được (Countable Nouns):</strong> Có thể đếm được và có dạng số nhiều (e.g., apples, chairs).</li>
        <li><strong>Danh từ không đếm được (Uncountable Nouns):</strong> Không thể đếm trực tiếp, thường ở dạng số ít (e.g., water, advice).</li>
        <li><strong>Danh từ riêng (Proper Nouns):</strong> Chỉ tên riêng của người, địa danh, hoặc tổ chức (e.g., Vietnam, Mary).</li>
        <li><strong>Danh từ chung (Common Nouns):</strong> Chỉ các vật thể hoặc khái niệm chung (e.g., table, beauty).</li>
      </ul>
    </div>

    <div class="grammar-box">
      <h4>1.2 Động Từ (Verbs)</h4>
      <p><strong>Định nghĩa:</strong> Động từ biểu thị hành động, trạng thái, hoặc sự kiện (e.g., run, is, think).</p>
      <p><strong>Phân loại:</strong></p>
      <ul>
        <li><strong>Động từ hành động (Action Verbs):</strong> Biểu thị hành động vật lý hoặc tinh thần (e.g., jump, write, consider).</li>
        <li><strong>Động từ trạng thái (Stative Verbs):</strong> Biểu thị trạng thái, cảm xúc, hoặc sở hữu (e.g., know, love, belong).</li>
        <li><strong>Động từ nối (Linking Verbs):</strong> Kết nối chủ ngữ với thông tin bổ sung (e.g., is, seem, become).</li>
        <li><strong>Động từ khuyết thiếu (Modal Verbs):</strong> Biểu thị khả năng, phép lịch sự (e.g., can, might, should).</li>
      </ul>
    </div>

    <div class="grammar-box">
      <h4>1.3 Tính Từ (Adjectives)</h4>
      <p><strong>Định nghĩa:</strong> Tính từ mô tả đặc điểm, số lượng, hoặc mức độ của danh từ (e.g., tall, red, many).</p>
      <p><strong>Phân loại:</strong></p>
      <ul>
        <li><strong>Tính từ miêu tả (Descriptive Adjectives):</strong> Mô tả đặc tính (e.g., beautiful, small).</li>
        <li><strong>Tính từ chỉ số lượng (Quantitative Adjectives):</strong> Chỉ số lượng (e.g., few, several).</li>
        <li><strong>Tính từ chỉ thứ tự (Ordinal Adjectives):</strong> Chỉ thứ tự (e.g., first, second).</li>
        <li><strong>Tính từ ghép (Compound Adjectives):</strong> Kết hợp hai hoặc nhiều từ (e.g., well-known, high-quality).</li>
      </ul>
    </div>

    <div class="grammar-box">
      <h4>1.4 Giới Từ (Prepositions)</h4>
      <p><strong>Định nghĩa:</strong> Giới từ chỉ mối quan hệ về thời gian, không gian, nguyên nhân, hoặc mục đích (e.g., in, on, at, because of).</p>
      <p><strong>Phân loại:</strong></p>
      <ul>
        <li><strong>Giới từ thời gian:</strong> in (tháng/năm), on (ngày), at (giờ) (e.g., at 5 p.m., on Monday, in 2023).</li>
        <li><strong>Giới từ nơi chốn:</strong> in (bên trong), on (trên), at (tại) (e.g., in the room, on the table, at the station).</li>
        <li><strong>Giới từ chỉ hướng:</strong> to, toward, into (e.g., to the park, toward the goal).</li>
        <li><strong>Giới từ nguyên nhân/mục đích:</strong> because of, for (e.g., because of the rain, for her birthday).</li>
      </ul>
    </div>

    <div class="grammar-box">
      <h4>1.5 Đại Từ (Pronouns)</h4>
      <p><strong>Định nghĩa:</strong> Đại từ thay thế cho danh từ để tránh lặp lại (e.g., he, she, it, they).</p>
      <p><strong>Phân loại:</strong></p>
      <ul>
        <li><strong>Đại từ nhân xưng (Personal Pronouns):</strong> I, you, he, she, it, we, they (e.g., "She is here.")</li>
        <li><strong>Đại từ sở hữu (Possessive Pronouns):</strong> mine, yours, his, hers, its, ours, theirs (e.g., "This book is mine.")</li>
        <li><strong>Đại từ phản thân (Reflexive Pronouns):</strong> myself, yourself, himself, herself, itself, ourselves, themselves (e.g., "I did it myself.")</li>
        <li><strong>Đại từ chỉ định (Demonstrative Pronouns):</strong> this, that, these, those (e.g., "This is my bag.")</li>
        <li><strong>Đại từ bất định (Indefinite Pronouns):</strong> someone, anyone, nothing, everything (e.g., "Someone called.")</li>
      </ul>
    </div>

    <div class="grammar-box">
      <h4>1.6 Trạng Từ (Adverbs)</h4>
      <p><strong>Định nghĩa:</strong> Trạng từ bổ sung thông tin về động từ, tính từ, hoặc trạng từ khác (e.g., quickly, very, well).</p>
      <p><strong>Phân loại:</strong></p>
      <ul>
        <li><strong>Trạng từ chỉ cách (Adverbs of Manner):</strong> quickly, slowly (e.g., "She runs quickly.")</li>
        <li><strong>Trạng từ chỉ thời gian (Adverbs of Time):</strong> now, yesterday (e.g., "He arrived yesterday.")</li>
        <li><strong>Trạng từ chỉ nơi chốn (Adverbs of Place):</strong> here, there (e.g., "They live there.")</li>
        <li><strong>Trạng từ chỉ mức độ (Adverbs of Degree):</strong> very, too (e.g., "It is very cold.")</li>
      </ul>
    </div>

    <div class="grammar-box">
      <h4>1.7 Mạo Từ (Articles)</h4>
      <p><strong>Định nghĩa:</strong> Mạo từ xác định hoặc không xác định danh từ (e.g., a, an, the).</p>
      <p><strong>Phân loại:</strong></p>
      <ul>
        <li><strong>Mạo từ không xác định (Indefinite Articles):</strong> a (trước phụ âm), an (trước nguyên âm) (e.g., "a dog", "an apple")</li>
        <li><strong>Mạo từ xác định (Definite Article):</strong> the (e.g., "the sun")</li>
        <li><strong>Không dùng mạo từ:</strong> Với danh từ không đếm được hoặc danh từ số nhiều chung chung (e.g., "Water is essential.")</li>
      </ul>
    </div>

    <div class="grammar-box">
      <h4>1.8 Lượng Từ (Quantifiers)</h4>
      <p><strong>Định nghĩa:</strong> Lượng từ chỉ số lượng hoặc mức độ của danh từ (e.g., some, many, few).</p>
      <p><strong>Phân loại:</strong></p>
      <ul>
        <li><strong>Với danh từ đếm được:</strong> many, few, several (e.g., "Many people attended.")</li>
        <li><strong>Với danh từ không đếm được:</strong> much, little, a lot of (e.g., "Much effort was made.")</li>
        <li><strong>Chung cho cả hai:</strong> some, any, a lot of (e.g., "Some water is needed.")</li>
      </ul>
    </div>

    <!-- Verb Tenses -->
    <h2 class="section-title">2. Các Thì Động Từ (Verb Tenses)</h2>

    <div class="grammar-box">
      <h4>2.1 Hiện Tại Đơn (Present Simple)</h4>
      <p><strong>Định nghĩa:</strong> Thì hiện tại đơn diễn tả thói quen, sự thật hiển nhiên, cảm xúc, trạng thái, hoặc lịch trình cố định.</p>
      <p><strong>Cách sử dụng:</strong> Thói quen hàng ngày, sự thật vĩnh cửu, cảm xúc, trạng thái, lịch trình (e.g., "The Earth rotates around the Sun.")</p>
      <p><strong>Trường hợp sử dụng:</strong> Thói quen (e.g., "I walk to school every day."), sự thật (e.g., "Water boils at 100°C."), cảm xúc (e.g., "She loves music.")</p>
      <p><strong>Cấu trúc:</strong></p>
      <ul>
        <li><strong>Khẳng định:</strong> S + V(s/es) (e.g., "She walks to the park.")</li>
        <li><strong>Phủ định:</strong> S + do/does + not + V (e.g., "He does not play football.")</li>
        <li><strong>Hỏi:</strong> Do/Does + S + V? (e.g., "Do you like coffee?")</li>
      </ul>
      <p><strong>Từ/trạng từ/cụm từ thường dùng:</strong> always, often, usually, sometimes, every day/week, never (e.g., "I always wake up at 7 a.m.")</p>
      <p><strong>Lưu ý:</strong> Thêm -s/-es với chủ ngữ số ít (he, she, it); không dùng với động từ trạng thái (know, like) trong tiếp diễn.</p>
    </div>

    <div class="grammar-box">
      <h4>2.2 Hiện Tại Tiếp Diễn (Present Continuous)</h4>
      <p><strong>Định nghĩa:</strong> Thì hiện tại tiếp diễn diễn tả hành động đang xảy ra tại thời điểm nói, hành động tạm thời, xu hướng, hoặc kế hoạch tương lai gần.</p>
      <p><strong>Cách sử dụng:</strong> Hành động hiện tại (e.g., "I am writing now."), xu hướng (e.g., "Prices are rising."), kế hoạch (e.g., "We are meeting at 5 p.m.")</p>
      <p><strong>Trường hợp sử dụng:</strong> Hành động đang diễn ra (e.g., "She is reading a book."), hành động tạm thời (e.g., "I am staying with friends this week.")</p>
      <p><strong>Cấu trúc:</strong></p>
      <ul>
        <li><strong>Khẳng định:</strong> S + am/is/are + V-ing (e.g., "They are playing soccer.")</li>
        <li><strong>Phủ định:</strong> S + am/is/are + not + V-ing (e.g., "He is not watching TV.")</li>
        <li><strong>Hỏi:</strong> Am/Is/Are + S + V-ing? (e.g., "Are you studying?")</li>
      </ul>
      <p><strong>Từ/trạng từ/cụm từ thường dùng:</strong> now, at the moment, currently, today (e.g., "She is working today.")</p>
      <p><strong>Lưu ý:</strong> Không dùng với động từ trạng thái (know, love) trừ ngữ cảnh đặc biệt.</p>
    </div>

    <div class="grammar-box">
      <h4>2.3 Quá Khứ Đơn (Past Simple)</h4>
      <p><strong>Định nghĩa:</strong> Thì quá khứ đơn diễn tả hành động hoàn tất trong quá khứ tại một thời điểm cụ thể.</p>
      <p><strong>Cách sử dụng:</strong> Sự kiện đã xảy ra (e.g., "I visited Paris last year."), thói quen quá khứ (e.g., "We played outside as kids.")</p>
      <p><strong>Trường hợp sử dụng:</strong> Kể chuyện (e.g., "She laughed and danced."), sự kiện lịch sử (e.g., "He died in 1990.")</p>
      <p><strong>Cấu trúc:</strong></p>
      <ul>
        <li><strong>Khẳng định:</strong> S + V-ed/V2 (e.g., "They watched a movie.")</li>
        <li><strong>Phủ định:</strong> S + did + not + V (e.g., "She did not call.")</li>
        <li><strong>Hỏi:</strong> Did + S + V? (e.g., "Did you see him?")</li>
      </ul>
      <p><strong>Từ/trạng từ/cụm từ thường dùng:</strong> yesterday, last week, in 1990, ago (e.g., "I saw him two days ago.")</p>
      <p><strong>Lưu ý:</strong> Sử dụng V2 (bảng động từ bất quy tắc) cho một số động từ (e.g., go → went).</p>
    </div>

    <div class="grammar-box">
      <h4>2.4 Quá Khứ Tiếp Diễn (Past Continuous)</h4>
      <p><strong>Định nghĩa:</strong> Thì quá khứ tiếp diễn diễn tả hành động đang xảy ra tại một thời điểm cụ thể trong quá khứ.</p>
      <p><strong>Cách sử dụng:</strong> Hành động bị gián đoạn (e.g., "I was reading when she called."), hai hành động đồng thời (e.g., "We were studying while they played.")</p>
      <p><strong>Trường hợp sử dụng:</strong> Bối cảnh quá khứ (e.g., "It was raining all day."), hành động kéo dài (e.g., "He was working late.")</p>
      <p><strong>Cấu trúc:</strong></p>
      <ul>
        <li><strong>Khẳng định:</strong> S + was/were + V-ing (e.g., "She was cooking dinner.")</li>
        <li><strong>Phủ định:</strong> S + was/were + not + V-ing (e.g., "They were not sleeping.")</li>
        <li><strong>Hỏi:</strong> Was/Were + S + V-ing? (e.g., "Were you listening?")</li>
      </ul>
      <p><strong>Từ/trạng từ/cụm từ thường dùng:</strong> while, when, at that moment, all day (e.g., "I was working while it rained.")</p>
      <p><strong>Lưu ý:</strong> Thường kết hợp với quá khứ đơn để chỉ hành động gián đoạn.</p>
    </div>

    <div class="grammar-box">
      <h4>2.5 Quá Khứ Hoàn Thành (Past Perfect)</h4>
      <p><strong>Định nghĩa:</strong> Thì quá khứ hoàn thành diễn tả hành động xảy ra và hoàn tất trước một thời điểm hoặc hành động khác trong quá khứ.</p>
      <p><strong>Cách sử dụng:</strong> Thứ tự sự kiện (e.g., "I had finished before he arrived."), nguyên nhân (e.g., "She was tired because she had worked.")</p>
      <p><strong>Trường hợp sử dụng:</strong> Kể chuyện với thứ tự (e.g., "He had left when I got there."), tường thuật (e.g., "She said she had seen him.")</p>
      <p><strong>Cấu trúc:</strong></p>
      <ul>
        <li><strong>Khẳng định:</strong> S + had + V3/ed (e.g., "They had eaten before the show.")</li>
        <li><strong>Phủ định:</strong> S + had + not + V3/ed (e.g., "I had not seen the movie.")</li>
        <li><strong>Hỏi:</strong> Had + S + V3/ed? (e.g., "Had you finished?")</li>
      </ul>
      <p><strong>Từ/trạng từ/cụm từ thường dùng:</strong> before, after, by the time, already (e.g., "She had already left by then.")</p>
      <p><strong>Lưu ý:</strong> Thường dùng với "had" cho cả chủ ngữ số ít và số nhiều.</p>
    </div>

    <div class="grammar-box">
      <h4>2.6 Tương Lai Đơn (Future Simple)</h4>
      <p><strong>Định nghĩa:</strong> Thì tương lai đơn diễn tả dự đoán, quyết định tức thời, lời hứa, hoặc đề nghị.</p>
      <p><strong>Cách sử dụng:</strong> Dự đoán (e.g., "It will rain."), quyết định (e.g., "I will help you."), lời hứa (e.g., "I will call you.")</p>
      <p><strong>Trường hợp sử dụng:</strong> Dự đoán không có bằng chứng (e.g., "She will win."), lời đề nghị (e.g., "I will carry your bag.")</p>
      <p><strong>Cấu trúc:</strong></p>
      <ul>
        <li><strong>Khẳng định:</strong> S + will + V (e.g., "They will travel tomorrow.")</li>
        <li><strong>Phủ định:</strong> S + will + not + V (e.g., "He will not come.")</li>
        <li><strong>Hỏi:</strong> Will + S + V? (e.g., "Will you join us?")</li>
      </ul>
      <p><strong>Từ/trạng từ/cụm từ thường dùng:</strong> tomorrow, next week, soon, probably (e.g., "I will probably stay home.")</p>
      <p><strong>Lưu ý:</strong> "Shall" có thể dùng với I/we trong văn phong trang trọng (e.g., "We shall overcome.")</p>
    </div>

    <div class="grammar-box">
      <h4>2.7 Tương Lai Gần (Near Future)</h4>
      <p><strong>Định nghĩa:</strong> Thì tương lai gần diễn tả kế hoạch đã định trước hoặc dự đoán dựa trên bằng chứng hiện tại.</p>
      <p><strong>Cách sử dụng:</strong> Kế hoạch (e.g., "I am going to visit Paris."), dự đoán (e.g., "Look, it is going to rain.")</p>
      <p><strong>Trường hợp sử dụng:</strong> Ý định cá nhân (e.g., "We are going to start a business."), dấu hiệu hiện tại (e.g., "The sky is dark; it is going to storm.")</p>
      <p><strong>Cấu trúc:</strong></p>
      <ul>
        <li><strong>Khẳng định:</strong> S + am/is/are + going to + V (e.g., "She is going to cook dinner.")</li>
        <li><strong>Phủ định:</strong> S + am/is/are + not + going to + V (e.g., "They are not going to wait.")</li>
        <li><strong>Hỏi:</strong> Am/Is/Are + S + going to + V? (e.g., "Are you going to help?")</li>
      </ul>
      <p><strong>Từ/trạng từ/cụm từ thường dùng:</strong> tomorrow, next month, look!, listen! (e.g., "Listen, he is going to sing!")</p>
      <p><strong>Lưu ý:</strong> Không dùng với quyết định tức thời (dùng "will" thay).</p>
    </div>

    <div class="grammar-box">
      <h4>2.8 Tương Lai Hoàn Thành (Future Perfect)</h4>
      <p><strong>Định nghĩa:</strong> Thì tương lai hoàn thành diễn tả hành động sẽ hoàn thành trước một thời điểm cụ thể trong tương lai.</p>
      <p><strong>Cách sử dụng:</strong> Hoàn thành trước mốc thời gian (e.g., "I will have finished by 5 p.m."), dự đoán (e.g., "She will have left by now.")</p>
      <p><strong>Trường hợp sử dụng:</strong> Kế hoạch dài hạn (e.g., "By next year, we will have built the house.")</p>
      <p><strong>Cấu trúc:</strong></p>
      <ul>
        <li><strong>Khẳng định:</strong> S + will + have + V3/ed (e.g., "They will have arrived by then.")</li>
        <li><strong>Phủ định:</strong> S + will + not + have + V3/ed (e.g., "I will not have finished.")</li>
        <li><strong>Hỏi:</strong> Will + S + have + V3/ed? (e.g., "Will you have completed it?")</li>
      </ul>
      <p><strong>Từ/trạng từ/cụm từ thường dùng:</strong> by the time, by next year, by then (e.g., "By 2030, he will have retired.")</p>
      <p><strong>Lưu ý:</strong> Thường kết hợp với "by" để chỉ mốc thời gian.</p>
    </div>

    <div class="grammar-box">
      <h4>2.9 Hiện Tại Hoàn Thành (Present Perfect)</h4>
      <p><strong>Định nghĩa:</strong> Thì hiện tại hoàn thành diễn tả hành động đã xảy ra và có liên quan đến hiện tại, kinh nghiệm sống, hoặc hành động vừa hoàn thành.</p>
      <p><strong>Cách sử dụng:</strong> Kinh nghiệm (e.g., "I have been to Japan."), kết quả hiện tại (e.g., "She has lost her keys."), hành động lặp lại (e.g., "We have seen this movie three times.")</p>
      <p><strong>Trường hợp sử dụng:</strong> Kể chuyện gần đây (e.g., "I have just eaten."), thời gian không xác định (e.g., "He has written a book.")</p>
      <p><strong>Cấu trúc:</strong></p>
      <ul>
        <li><strong>Khẳng định:</strong> S + have/has + V3/ed (e.g., "They have gone home.")</li>
        <li><strong>Phủ định:</strong> S + have/has + not + V3/ed (e.g., "She has not arrived.")</li>
        <li><strong>Hỏi:</strong> Have/Has + S + V3/ed? (e.g., "Have you eaten?")</li>
      </ul>
      <p><strong>Từ/trạng từ/cụm từ thường dùng:</strong> already, yet, just, ever, never, since, for (e.g., "I have lived here since 2010.")</p>
      <p><strong>Lưu ý:</strong> Không dùng với thời gian đã kết thúc (dùng quá khứ đơn thay).</p>
    </div>

    <!-- Sentence Structures -->
    <h2 class="section-title">3. Cấu Trúc Câu (Sentence Structures)</h2>

    <div class="grammar-box">
      <h4>3.1 Câu Đơn (Simple Sentences)</h4>
      <p><strong>Định nghĩa:</strong> Một mệnh đề độc lập có chủ ngữ và động từ, diễn đạt một ý tưởng hoàn chỉnh (e.g., She runs).</p>
      <p><strong>Cấu trúc:</strong> Subject + Verb + (Object/Complement) (e.g., "The dog barks.")</p>
    </div>

    <div class="grammar-box">
      <h4>3.2 Câu Ghép (Compound Sentences)</h4>
      <p><strong>Định nghĩa:</strong> Hai hoặc nhiều mệnh đề độc lập được nối bằng liên từ (e.g., and, but, or).</p>
      <p><strong>Cấu trúc:</strong> Independent Clause + Coordinating Conjunction + Independent Clause (e.g., "I wanted to go, but it rained.")</p>
    </div>

    <div class="grammar-box">
      <h4>3.3 Câu Phức (Complex Sentences)</h4>
      <p><strong>Định nghĩa:</strong> Một mệnh đề độc lập và ít nhất một mệnh đề phụ thuộc (e.g., because, if).</p>
      <p><strong>Cấu trúc:</strong> Main Clause + Subordinate Clause (e.g., "She succeeded because she worked hard.")</p>
    </div>

    <div class="grammar-box">
      <h4>3.4 Câu So Sánh Hơn (Comparative Sentences)</h4>
      <p><strong>Định nghĩa:</strong> So sánh hai đối tượng để chỉ mức độ hơn (e.g., taller, more beautiful).</p>
      <p><strong>Cấu trúc:</strong></p>
      <ul>
        <li>Tính từ ngắn: S + be + tính từ-er + than (e.g., "She is taller than her brother.")</li>
        <li>Tính từ dài: S + be + more + tính từ + than (e.g., "This book is more interesting than that one.")</li>
      </ul>
      <p><strong>Ví dụ:</strong> "His car is faster than mine."</p>
    </div>

    <div class="grammar-box">
      <h4>3.5 Câu So Sánh Nhất (Superlative Sentences)</h4>
      <p><strong>Định nghĩa:</strong> So sánh một đối tượng với tất cả các đối tượng khác (e.g., tallest, most beautiful).</p>
      <p><strong>Cấu trúc:</strong></p>
      <ul>
        <li>Tính từ ngắn: S + be + the + tính từ-est (e.g., "She is the tallest in the class.")</li>
        <li>Tính từ dài: S + be + the most + tính từ (e.g., "This is the most beautiful painting.")</li>
      </ul>
      <p><strong>Ví dụ:</strong> "Mount Everest is the highest mountain in the world."</p>
    </div>

    <div class="grammar-box">
      <h4>3.6 Câu Điều Ước (Wishlist Sentences)</h4>
      <p><strong>Định nghĩa:</strong> Diễn tả mong muốn không có thật (e.g., I wish, If only).</p>
      <p><strong>Cấu trúc:</strong></p>
      <ul>
        <li>Hiện tại: S + wish + S + were/V-ed (e.g., "I wish I were rich.")</li>
        <li>Quá khứ: S + wish + S + had + V3/ed (e.g., "I wish I had studied harder.")</li>
      </ul>
      <p><strong>Ví dụ:</strong> "If only it would stop raining!"</p>
    </div>

    <div class="grammar-box">
      <h4>3.7 Câu Mệnh Lệnh (Imperative Sentences)</h4>
      <p><strong>Định nghĩa:</strong> Dùng để ra lệnh, yêu cầu, hoặc lời khuyên (e.g., Sit down, Please help me).</p>
      <p><strong>Cấu trúc:</strong> Verb + Object (e.g., "Close the door.")</p>
      <p><strong>Ví dụ:</strong> "Please be quiet."</p>
    </div>

    <div class="grammar-box">
      <h4>3.8 Câu Tường Thuật Trực Tiếp và Gián Tiếp (Direct and Indirect Speech)</h4>
      <p><strong>Định nghĩa:</strong> Trực tiếp: Lặp lại chính xác lời nói (e.g., "She said, 'I am tired.'"). Gián tiếp: Biến đổi lời nói (e.g., "She said that she was tired.")</p>
      <p><strong>Cấu trúc:</strong></p>
      <ul>
        <li><strong>Trực tiếp:</strong> "I will call you," he said. (e.g., "He said, 'I will call you.'")</li>
        <li><strong>Gián tiếp:</strong> S + said + that + S + V (thay đổi thì) (e.g., "He said that he would call me.")</li>
      </ul>
      <p><strong>Thay đổi:</strong> Thì (present → past), đại từ, thời gian (today → that day).</p>
      <p><strong>Ví dụ:</strong></p>
      <ul>
        <li>Trực tiếp: "I am going home," she said.</li>
        <li>Gián tiếp: "She said that she was going home."</li>
      </ul>
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

    <!-- JavaScript -->
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