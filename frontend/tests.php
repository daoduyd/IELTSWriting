<?php
session_start(); // Khởi động session để kiểm tra trạng thái đăng nhập
// Kiểm tra người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
// Kết nối đến cơ sở dữ liệu
require 'config.php';
// Lấy thông tin người dùng
$user_id = $_SESSION['user_id'];
// Lấy danh sách các bài kiểm tra từ bảng ds_de_thi
$stmt = $pdo->prepare("SELECT * FROM ds_de_thi ORDER BY created_at DESC");
$stmt->execute();
$tests = $stmt->fetchAll();
// Kiểm tra xem người dùng đã làm những bài kiểm tra nào
$stmt = $pdo->prepare("SELECT de_thi_id FROM test_attempts WHERE user_id = ?");
$stmt->execute([$user_id]);
$attempted_tests = $stmt->fetchAll(PDO::FETCH_COLUMN);
// Load API key directly
$gemini_api_keyAsString = 'AIzaSyCpSVknxRLhGZD1f-pcd_zH6sxGin38Bk0'; // Hard-coded API key
// Function to check if endpoint is valid
function isValidEndpoint($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return $http_code == 200 || $http_code == 405;
}
// Function to call Gemini API for IELTS scoring and AI dependency analysis
function getGeminiIELTSScore($essay_text, $category, $required_word_count, $api_key, $retries = 5, $delay = 60) {
    if (empty($essay_text)) {
        return [
            'success' => false,
            'error' => 'Bài viết rỗng'
        ];
    }

    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=$api_key";
    error_log("Using endpoint: $url");

    $prompt = <<<EOD
You are an expert IELTS examiner and AI detection specialist. Evaluate the following IELTS Writing essay and perform a detailed AI dependency analysis. The essay is for: $category. Required word count: $required_word_count words.

### Part 1: IELTS Scoring Instructions
1. **Determine the task type**:
   - If the category contains "Task 1", evaluate using "Task Achievement".
   - If the category contains "Task 2", evaluate using "Task Response".

2. **Evaluate each criterion**:
   - **Task Achievement (Task 1)**: Assess data summarization, key trends, and word count. Provide specific examples from the essay for strengths, weaknesses, errors, and suggested improvements.
     - Band 3.0: Almost no relevant data description, major inaccuracies, far below word count.
     - Band 3.5: Minimal data description, significant inaccuracies, severely underlength.
     - Band 4.0: Minimal description, lacks clarity, significant underlength or irrelevant content.
     - Band 4.5: Some relevant data, unclear trends, frequent inaccuracies, underlength.
     - Band 5.0: Limited description of data, unclear trends, insufficient word count.
     - Band 5.5: Basic description, some trends identified, minor inaccuracies, slightly underlength.
     - Band 6.0: Clear trends, may lack detail or have minor inaccuracies.
     - Band 6.5: Clear trends with adequate detail, minor lapses in accuracy.
     - Band 7.0: Accurate with sufficient detail, covers all key trends.
     - Band 7.5: Accurate, detailed, with minor lapses, strong coverage of trends.
     - Band 8.0: Precise, detailed, well-developed with clear comparisons.
     - Band 8.5: Highly accurate, insightful, near-perfect coverage of all task requirements.
   - **Task Response (Task 2)**: Assess prompt addressing, position clarity, and argument development. Provide specific examples for strengths, weaknesses, errors, and improvements.
     - Band 3.0: Fails to address task, incoherent or completely off-topic.
     - Band 3.5: Barely addresses task, ideas incoherent, no clear position.
     - Band 4.0: Barely addresses task, incoherent or off-topic responses.
     - Band 4.5: Partially addresses task, ideas vague, minimal position clarity.
     - Band 5.0: Partially addresses task, ideas unclear or repetitive.
     - Band 5.5: Addresses task with basic ideas, some clarity but limited development.
     - Band 6.0: Addresses task, underdeveloped ideas, some irrelevant content.
     - Band 6.5: Addresses task with clear position, some ideas underdeveloped.
     - Band 7.0: Clear position, well-supported arguments, minor lapses.
     - Band 7.5: Clear, well-developed position, strong arguments with minor lapses.
     - Band 8.0: Fully addresses task, well-developed and extended ideas.
     - Band 8.5: Sophisticated arguments, fully extended, minimal lapses.
   - **Coherence and Cohesion**: Evaluate organization, paragraphing, and linking devices. Cite specific examples for strengths, weaknesses, errors, and improvements.
     - Band 3.0: No organization, no paragraphing, no linking devices.
     - Band 3.5: Severely disorganized, minimal paragraphing, no linking devices.
     - Band 4.0: Disorganized, minimal or no paragraphing, no linking devices.
     - Band 4.5: Poor organization, faulty paragraphing, minimal linking devices.
     - Band 5.0: Disorganized, limited or faulty linking devices.
     - Band 5.5: Basic organization, some linking devices, frequent cohesion issues.
     - Band 6.0: Logical but faulty cohesion, some linking issues.
     - Band 6.5: Mostly logical organization, effective but limited cohesive devices.
     - Band 7.0: Effective cohesive devices, mostly logical organization.
     - Band 7.5: Strong organization, varied cohesive devices with minor lapses.
     - Band 8.0: Seamless organization, varied and accurate cohesive devices.
     - Band 8.5: Flawless organization, highly sophisticated linking devices.
   - **Lexical Resource**: Assess vocabulary range, accuracy, collocation. Highlight specific examples for strengths, weaknesses, errors, and improvements.
     - Band 3.0: Extremely limited vocabulary, major errors impede communication.
     - Band 3.5: Very limited vocabulary, frequent errors, no collocations.
     - Band 4.0: Very limited vocabulary, frequent errors, no collocations.
     - Band 4.5: Limited vocabulary, frequent errors, basic collocations attempted.
     - Band 5.0: Limited vocabulary, frequent errors, basic collocations.
     - Band 5.5: Adequate vocabulary, some errors, basic collocations with issues.
     - Band 6.0: Adequate range, some errors, attempts at varied vocabulary.
     - Band 6.5: Varied vocabulary, some errors, generally appropriate collocations.
     - Band 7.0: Varied vocabulary, occasional errors, appropriate collocations.
     - Band 7.5: Wide vocabulary range, minimal errors, strong collocations.
     - Band 8.0: Wide range, precise word choice, minimal errors.
     - Band 8.5: Extensive and precise vocabulary, virtually error-free.
   - **Grammatical Range and Accuracy**: Assess sentence variety, complexity, accuracy. Cite specific examples for strengths, weaknesses, errors, and improvements.
     - Band 3.0: No sentence variety, major errors prevent communication.
     - Band 3.5: Basic structures, major errors dominate, communication impeded.
     - Band 4.0: Basic structures, frequent major errors, communication impeded.
     - Band 4.5: Simple structures, frequent errors, limited range.
     - Band 5.0: Simple structures, frequent errors, limited range.
     - Band 5.5: Simple to mixed structures, frequent errors, some complexity.
     - Band 6.0: Mixed sentences, some errors, moderate complexity.
     - Band 6.5: Varied structures, some errors, good control with lapses.
     - Band 7.0: Varied structures, minimal errors, good control.
     - Band 7.5: Wide range of structures, minimal errors, strong control.
     - Band 8.0: Wide range of complex structures, highly accurate.
     - Band 8.5: Extensive range, near-perfect accuracy, complex structures.

3. **Scoring**:
   - Provide band scores (0-9, nearest 0.5) for each criterion.
   - For each criterion, provide a detailed explanation (4-5 sentences) citing:
     - **Strengths**: Specific examples of what the essay does well.
     - **Weaknesses**: Specific examples of shortcomings or errors.
     - **Errors**: Quote specific errors (e.g., grammar, vocabulary, cohesion issues).
     - **Improvements**: Suggest specific ways to address errors (e.g., use synonyms, vary sentence structures).
   - Calculate overall score as the average of the four criteria, rounded to the nearest 0.5.

4. **Error Analysis and Suggestions**:
   - Identify specific errors for each criterion (e.g., repetitive vocabulary, weak transitions, grammatical inaccuracies).
   - Provide actionable suggestions for improvement (e.g., use varied synonyms, improve paragraph transitions).

5. **Feedback and Advice**:
   - General feedback (2-3 sentences summarizing overall strengths and weaknesses).
   - Specific advice (2-3 sentences with practical improvement tips).

### Part 2: AI Dependency Analysis
1. **Objective**: Assess whether the essay was AI-generated or influenced with high accuracy.
2. **Criteria**:
   - **Writing style**: 
     - AI-generated: Overly polished, formulaic, repetitive sentence structures, or unnatural flow (e.g., frequent use of phrases like "It is indisputable that", "In conclusion, it is evident that", "Moreover").
     - Human-written: Natural flow, varied tone, personal expressions, minor inconsistencies, or culturally specific language.
   - **Template usage**: Identify AI-specific patterns, such as overused transitions (e.g., "Furthermore", "Consequently"), formulaic paragraph structures (e.g., identical introduction and conclusion phrasing), or predictable argument patterns.
   - **Consistency**: Check for unnatural uniformity in tone, vocabulary, sentence length, or structure, which is typical of AI-generated text.
   - **Human-like errors**: Look for typos, minor grammatical errors, colloquialisms, or culturally specific expressions that AI rarely produces (e.g., slang, personal anecdotes).
   - **Contextual clues**: Identify personal anecdotes, emotional tone, context-specific knowledge, or unique phrasing that suggests human input.
   - **Lexical patterns**: Compare vocabulary usage with known AI-generated text patterns (e.g., overuse of formal academic vocabulary without context) versus human-like varied or context-specific word choice.
   - **Structural patterns**: Analyze paragraph and essay structure for signs of AI generation (e.g., overly balanced paragraphs, lack of deviation from standard essay templates).

3. **Scoring**:
   - Assign an AI dependency score (0-100, where 0 = fully human-written, 100 = fully AI-generated).
   - Assign levels based on score:
     - 0–20: None (clearly human-written, natural tone, human-like errors or personal tone).
     - 21–50: Low (minimal AI influence, some polished phrases or structures, but human characteristics dominate).
     - 51–80: Medium (significant AI influence, formulaic patterns, limited human-like errors or personalization).
     - 81–100: High (likely fully AI-generated, highly polished, formulaic, lacks human-like errors).
   - Provide a detailed explanation (4-5 sentences) with specific examples from the essay:
     - Quote specific phrases or sentences that indicate AI or human influence.
     - Highlight patterns (e.g., repetitive transitions, uniform sentence lengths).
     - Note presence or absence of human-like errors or personal tone.
   - Flag for review: Yes if score > 50 or if specific AI patterns are detected (e.g., formulaic phrases), No otherwise.

4. **Output**:
   - AI dependency score (0-100).
   - AI assistance level: None, Low, Medium, High.
   - Detailed explanation with specific examples from the essay.
   - Flag for review (Yes/No).

### Essay:
$essay_text

### Response Format:
{
  "scores": {
    "task_achievement": {"score": X, "explanation": "..."},
    "task_response": {"score": X, "explanation": "..."},
    "coherence_cohesion": {"score": X, "explanation": "..."},
    "lexical_resource": {"score": X, "explanation": "..."},
    "grammatical_range": {"score": X, "explanation": "..."}
  },
  "errors_and_suggestions": {
    "task_achievement": {"errors": "...", "suggestions": "..."},
    "task_response": {"errors": "...", "suggestions": "..."},
    "coherence_cohesion": {"errors": "...", "suggestions": "..."},
    "lexical_resource": {"errors": "...", "suggestions": "..."},
    "grammatical_range": {"errors": "...", "suggestions": "..."}
  },
  "overall": X,
  "feedback": "...",
  "advice": "...",
  "ai_dependency": {
    "score": X,
    "level": "None|Low|Medium|High",
    "explanation": "...",
    "flag_for_review": "Yes|No"
  }
}
EOD;

    $data = [
        'contents' => [
            [
                'parts' => [
                    ['text' => $prompt]
                ]
            ]
        ],
        'generationConfig' => [
            'temperature' => 0.5,
            'maxOutputTokens' => 4000
        ]
    ];

    $headers = [
        "Content-Type: application/json"
    ];

    $attempt = 0;
    while ($attempt < $retries) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        error_log("API HTTP Code: $http_code");
        error_log("API Response: " . ($response ?: 'No response'));

        if ($response === false) {
            $attempt++;
            error_log("Gemini API request failed. HTTP Code: $http_code. Attempt: $attempt.");
            if ($http_code == 429) {
                error_log("Quota exceeded for Gemini API. Retrying after $delay seconds...");
                sleep($delay);
                continue;
            }
            if ($attempt === $retries) {
                return [
                    'success' => false,
                    'error' => "Không thể kết nối với Gemini API. Vui lòng thử lại sau."
                ];
            }
            sleep($delay);
            continue;
        }

        $result = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("Failed to decode API response as JSON: " . json_last_error_msg() . " | Response: $response");
            return [
                'success' => false,
                'error' => 'Phản hồi API không hợp lệ. Vui lòng thử lại.'
            ];
        }

        if (isset($result['error'])) {
            error_log("API error: " . json_encode($result['error']));
            if ($http_code == 429) {
                $attempt++;
                error_log("Quota exceeded for Gemini API. Retrying after $delay seconds...");
                sleep($delay);
                continue;
            } elseif ($http_code == 400 && strpos($result['error']['message'], 'API key') !== false) {
                return [
                    'success' => false,
                    'error' => 'Lỗi hệ thống: Khóa API không hợp lệ. Vui lòng liên hệ quản trị viên.'
                ];
            }
            return [
                'success' => false,
                'error' => 'Lỗi từ API: ' . ($result['error']['message'] ?? 'Không xác định')
            ];
        }

        $parsed_text = '';
        if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
            $parsed_text = $result['candidates'][0]['content']['parts'][0]['text'];
            if (strpos($parsed_text, '```json') !== false) {
                $parsed_text = trim(str_replace(['```json', '```'], '', $parsed_text));
            }
        }

        if (empty($parsed_text)) {
            error_log("No valid text in API response: " . json_encode($result));
            return [
                'success' => false,
                'error' => 'Cấu trúc phản hồi API không đúng. Vui lòng thử lại.'
            ];
        }

        error_log("Parsed text before JSON decode: " . $parsed_text);
        $parsed_result = json_decode($parsed_text, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("JSON decode error: " . json_last_error_msg() . " | Parsed text: " . $parsed_text);
            return [
                'success' => false,
                'error' => 'Lỗi phân tích JSON từ phản hồi API. Vui lòng thử lại.'
            ];
        }

        if (!isset($parsed_result['scores']) || !isset($parsed_result['overall'])) {
            error_log("Gemini API response missing required fields: " . json_encode($parsed_result));
            return [
                'success' => false,
                'error' => 'Phản hồi API thiếu thông tin cần thiết. Vui lòng thử lại.'
            ];
        }

        $overall_score = round($parsed_result['overall'] * 2) / 2;

        $task_achievement = null;
        $task_response = null;
        if (isset($parsed_result['scores']['task_achievement'])) {
            $task_achievement = round($parsed_result['scores']['task_achievement']['score'] * 2) / 2;
        } elseif (isset($parsed_result['scores']['task_response'])) {
            $task_response = round($parsed_result['scores']['task_response']['score'] * 2) / 2;
        }

        $coherence_cohesion = isset($parsed_result['scores']['coherence_cohesion']) ? round($parsed_result['scores']['coherence_cohesion']['score'] * 2) / 2 : 0;
        $lexical_resource = isset($parsed_result['scores']['lexical_resource']) ? round($parsed_result['scores']['lexical_resource']['score'] * 2) / 2 : 0;
        $grammatical_range = isset($parsed_result['scores']['grammatical_range']) ? round($parsed_result['scores']['grammatical_range']['score'] * 2) / 2 : 0;

        $result = [
            'success' => true,
            'scores' => [
                'task_achievement' => $task_achievement ?? $task_response,
                'coherence_cohesion' => $coherence_cohesion,
                'lexical_resource' => $lexical_resource,
                'grammatical_range' => $grammatical_range
            ],
            'explanations' => [
                'task_achievement' => $parsed_result['scores']['task_achievement']['explanation'] ?? $parsed_result['scores']['task_response']['explanation'] ?? '',
                'coherence_cohesion' => $parsed_result['scores']['coherence_cohesion']['explanation'] ?? '',
                'lexical_resource' => $parsed_result['scores']['lexical_resource']['explanation'] ?? '',
                'grammatical_range' => $parsed_result['scores']['grammatical_range']['explanation'] ?? ''
            ],
            'errors_and_suggestions' => $parsed_result['errors_and_suggestions'] ?? [
                'task_achievement' => ['errors' => '', 'suggestions' => ''],
                'task_response' => ['errors' => '', 'suggestions' => ''],
                'coherence_cohesion' => ['errors' => '', 'suggestions' => ''],
                'lexical_resource' => ['errors' => '', 'suggestions' => ''],
                'grammatical_range' => ['errors' => '', 'suggestions' => '']
            ],
            'overall' => $overall_score,
            'feedback' => $parsed_result['feedback'] ?? '',
            'advice' => $parsed_result['advice'] ?? '',
            'ai_dependency' => $parsed_result['ai_dependency'] ?? [
                'score' => 0,
                'level' => 'None',
                'explanation' => 'No AI dependency analysis available.',
                'flag_for_review' => 'No'
            ]
        ];

        if ($result['ai_dependency']['score'] === 20) {
            error_log("Warning: AI dependency score is 20%. Possible issue with prompt or API response.");
        }

        return $result;
    }

    return [
        'success' => false,
        'error' => 'Đã vượt quá giới hạn thử lại. Vui lòng thử lại sau.'
    ];
}
// Handle AJAX request for essay analysis
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax']) && $_POST['ajax'] === 'analyze_essay') {
    $response = ['success' => false, 'error' => '', 'data' => null];
    
    $essay_text = trim($_POST['essayText'] ?? '');
    $category = trim($_POST['category'] ?? 'Task 2');
    $required_word_count = $category === 'Task 1' ? 150 : 250;

    if (strlen($essay_text) < 50) {
        $response['error'] = "Vui lòng nhập ít nhất 50 ký tự để phân tích.";
        echo json_encode($response);
        exit;
    }

    try {
        $pdo->beginTransaction();

        // Insert essay response
        $stmt = $pdo->prepare("
            INSERT INTO essay_responses (user_id, question_id, response_text, word_count, submission_time, category)
            VALUES (?, ?, ?, ?, NOW(), ?)
        ");
        $word_count = str_word_count($essay_text);
        $question_id = 0; // Placeholder, as no specific question is associated
        $stmt->execute([$user_id, $question_id, $essay_text, $word_count, $category]);
        $essay_id = $pdo->lastInsertId();

        // Call Gemini API
        $score_result = getGeminiIELTSScore($essay_text, $category, $required_word_count, $gemini_api_keyAsString);

        if (!$score_result['success']) {
            throw new Exception($score_result['error']);
        }
        // Construct review_comments
        $task_key = isset($score_result['errors_and_suggestions']['task_achievement']) ? 'task_achievement' : 'task_response';
        $comments = "Task Achievement/Task Response Errors: {$score_result['errors_and_suggestions'][$task_key]['errors']}\n" .
                    "Task Achievement/Task Response Suggestions: {$score_result['errors_and_suggestions'][$task_key]['suggestions']}\n" .
                    "Coherence and Cohesion Errors: {$score_result['errors_and_suggestions']['coherence_cohesion']['errors']}\n" .
                    "Coherence and Cohesion Suggestions: {$score_result['errors_and_suggestions']['coherence_cohesion']['suggestions']}\n" .
                    "Lexical Resource Errors: {$score_result['errors_and_suggestions']['lexical_resource']['errors']}\n" .
                    "Lexical Resource Suggestions: {$score_result['errors_and_suggestions']['lexical_resource']['suggestions']}\n" .
                    "Grammatical Range and Accuracy Errors: {$score_result['errors_and_suggestions']['grammatical_range']['errors']}\n" .
                    "Grammatical Range and Accuracy Suggestions: {$score_result['errors_and_suggestions']['grammatical_range']['suggestions']}";
        // Update essay_responses
        $stmt = $pdo->prepare("
            UPDATE essay_responses
            SET ai_assistance_level = ?, ai_detection_score = ?, score = ?
            WHERE id = ?
        ");
        $stmt->execute([
            $score_result['ai_dependency']['level'],
            $score_result['ai_dependency']['score'],
            $score_result['overall'],
            $essay_id
        ]);
        // Log AI interaction
        $stmt = $pdo->prepare("
            INSERT INTO ai_interaction_logs (
                user_id, essay_id, ai_model, interaction_type, prompt, ai_response, similarity_score,
                is_potential_plagiarism, ai_assistance_level, created_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([
            $user_id,
            $essay_id,
            'Gemini-1.5-Flash',
            'writing_assistance',
            'AI analysis for submitted essay',
            $score_result['ai_dependency']['explanation'],
            $score_result['ai_dependency']['score'],
            $score_result['ai_dependency']['flag_for_review'] === 'Yes' ? 'Yes' : 'No',
            $score_result['ai_dependency']['level']
        ]);
        // Insert review data
        $stmt = $pdo->prepare("
            INSERT INTO essay_reviews (
                essay_id, ai_generated_score, ai_task_achievement, ai_coherence_cohesion, ai_lexical_resource, ai_grammatical_range,
                review_comments, ai_dependency_comments, feedback, advice, task_achievement_explanation, coherence_cohesion_explanation,
                lexical_resource_explanation, grammatical_range_explanation, reviewed_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([
            $essay_id,
            $score_result['overall'],
            $score_result['scores']['task_achievement'],
            $score_result['scores']['coherence_cohesion'],
            $score_result['scores']['lexical_resource'],
            $score_result['scores']['grammatical_range'],
            $comments,
            $score_result['ai_dependency']['explanation'],
            $score_result['feedback'],
            $score_result['advice'],
            $score_result['explanations']['task_achievement'],
            $score_result['explanations']['coherence_cohesion'],
            $score_result['explanations']['lexical_resource'],
            $score_result['explanations']['grammatical_range']
        ]);

        $stmt = $pdo->prepare("SELECT * FROM essay_reviews WHERE essay_id = ?");
        $stmt->execute([$essay_id]);
        $review_result = $stmt->fetch(PDO::FETCH_ASSOC);
        $review_result['essay_text'] = $essay_text;

        $pdo->commit();

        $response['success'] = true;
        $response['data'] = $review_result;
        $response['data']['ai_dependency'] = $score_result['ai_dependency'];
    } catch (Exception $e) {
        $pdo->rollBack();
        error_log("Lỗi xử lý phân tích bài viết: " . $e->getMessage());
        $response['error'] = $e->getMessage();
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <title>Bài Kiểm Tra IELTS Writing | IELTS Writing Pro</title>
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

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f8f9fa;
      color: #333;
    }
    /* Hero section */
    .hero-section {
      padding: 3rem 0;
      text-align: center;
    }

    .main-title {
      font-weight: 700;
      color: #212529;
      margin-bottom: 1rem;
    }

    .sub-title {
      color: #6c757d;
      font-size: 1.1rem;
      margin-bottom: 2rem;
    }
    /* Filter section */
    .filter-section {
      background-color: #ffffff;
      padding: 1.5rem;
      border-radius: 0.5rem;
      box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
      margin-bottom: 2rem;
    }
    /* AI Feature */
    .ai-feature {
      background-color: #f0f7ff;
      padding: 1.5rem;
      border-radius: 0.5rem;
      border-left: 4px solid #1a75ff;
    }
    /* Test cards */
    .test-card {
      margin-bottom: 1.5rem;
      border: none;
      border-radius: 0.5rem;
      box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .test-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.1);
    }

    .test-card .card-header {
      background-color: #f8f9fa;
      border-bottom: 1px solid #eaeaea;
      border-radius: 0.5rem 0.5rem 0 0;
      padding: 0.75rem 1rem;
    }

    .test-details {
      display: flex;
      justify-content: space-between;
      margin: 1rem 0;
      font-size: 0.9rem;
      color: #6c757d;
    }

    .test-icon {
      color: #1a75ff;
    }
    /* Badges */
    .badge-free {
      background-color: #28a745;
      color: white;
    }

    .badge-difficulty {
      background-color: #17a2b8;
      color: white;
    }

    .attempted-badge {
      background-color: #6c757d;
      color: white;
    }
    /* Footer */
    .footer {
      background-color: #343a40;
      color: #ffffff;
      padding: 2rem 0;
      margin-top: 3rem;
    }
    /* Button styles */
    .btn-primary {
      background-color: #1a75ff;
      border-color: #1a75ff;
    }

    .btn-primary:hover {
      background-color: #0056b3;
      border-color: #0056b3;
    }

    .btn-outline-secondary {
      color: #6c757d;
      border-color: #6c757d;
    }

    .btn-outline-secondary:hover {
      background-color: #6c757d;
      color: white;
    }

    .btn-outline-info {
      color: #17a2b8;
      border-color: #17a2b8;
    }

    .btn-outline-info:hover {
      background-color: #17a2b8;
      color: white;
    }
    /* FAQ Section */
    .faq-section {
      margin-top: 3rem;
      padding: 2rem;
      background-color: #ffffff;
      border-radius: 0.5rem;
      box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
    }

    .faq-section h3 {
      color: #212529;
      margin-bottom: 1.5rem;
    }

    .faq-section .card {
      border: none;
      margin-bottom: 1rem;
    }

    .faq-section .card-header {
      background-color: #f8f9fa;
      border: none;
      padding: 0;
    }

    .faq-section .btn-link {
      color: #1a75ff;
      text-decoration: none;
      width: 100%;
      text-align: left;
      padding: 1rem;
    }

    .faq-section .btn-link:hover {
      color: #0056b3;
      text-decoration: none;
    }

    .faq-section .card-body {
      padding: 1rem 1rem 1.5rem;
    }
    /* Analysis Section */
    .analysis-section {
      background-color: #f1f8ff;
      padding: 15px;
      border-radius: 5px;
      margin-top: 20px;
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
      <div class="col-md-8 offset-md-2">
        <h1 class="main-title">Bài Kiểm Tra IELTS Writing</h1>
        <p class="sub-title">Thực hành với các bài kiểm tra IELTS Writing đầy đủ, được thiết kế giống như bài thi thật. Nhận phản hồi chi tiết và đánh giá từ AI cùng các phương pháp kiểm tra độ độc lập của bài viết.</p>
      </div>
    </div>
  </div>

  <div class="container">
    <!-- Filter Section -->
    <div class="filter-section">
      <div class="row">
        <div class="col-md-4">
          <div class="form-group">
            <label for="difficulty">Cấp độ:</label>
            <select class="form-control" id="difficulty">
              <option value="all">Tất cả</option>
              <option value="Beginner">Beginner</option>
              <option value="Intermediate">Intermediate</option>
              <option value="Advanced">Advanced</option>
            </select>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            <label for="status">Trạng thái:</label>
            <select class="form-control" id="status">
              <option value="all">Tất cả</option>
              <option value="attempted">Đã làm</option>
              <option value="not_attempted">Chưa làm</option>
            </select>
          </div>
        </div>
        <div class="col-md-4 d-flex align-items-end">
          <button type="button" class="btn btn-primary btn-block" id="filterButton">
            <i class="fas fa-filter mr-2"></i> Lọc kết quả
          </button>
        </div>
      </div>
    </div>
    <!-- AI Feature Highlight -->
    <div class="ai-feature mb-4">
      <h4><i class="fas fa-robot mr-2"></i> Trợ lý AI Thông minh</h4>
      <p>Hệ thống của chúng tôi sử dụng AI để:</p>
      <ul>
        <li><strong>Hỗ trợ học tập:</strong> Cung cấp gợi ý và phản hồi chi tiết giúp cải thiện kỹ năng viết</li>
        <li><strong>Kiểm tra độ độc lập:</strong> Phát hiện nếu bài viết của bạn sử dụng quá nhiều nội dung từ ChatGPT hay AI khác</li>
        <li><strong>Đánh giá khách quan:</strong> Cung cấp điểm số và nhận xét dựa trên tiêu chí chấm điểm IELTS chính thức</li>
      </ul>
    </div>
    <!-- Tests List -->
    <div class="row" id="testsList">
      <?php if (count($tests) > 0): ?>
        <?php foreach ($tests as $test): ?>
          <?php 
            $isAttempted = in_array($test['id'], $attempted_tests);
            // Xác định loại bài kiểm tra dựa trên category
            $testTypeClass = 'bg-primary'; // Mặc định
            $testTypeName = 'Đề thi';
            
            if (isset($test['category'])) {
              if (stripos($test['category'], 'task1') !== false) {
                $testTypeClass = 'bg-info';
                $testTypeName = 'Task 1';
              } elseif (stripos($test['category'], 'task2') !== false) {
                $testTypeClass = 'bg-primary';
                $testTypeName = 'Task 2';
              } elseif (stripos($test['category'], 'full') !== false) {
                $testTypeClass = 'bg-success';
                $testTypeName = 'Task 1 & 2';
              }
            }
            // Hiển thị "Miễn phí" cho Task 1 hoặc Task 2, còn Full Test thì hiển thị difficulty_level
            if (stripos($test['category'], 'full') !== false) {
                $badgeClass = 'badge-difficulty';
                $badgeText = htmlspecialchars($test['difficulty_level'] ?? 'N/A');
            } else {
                $badgeClass = 'badge-free';
                $badgeText = 'Miễn phí';
            }
          ?>
          <div class="col-md-6">
            <div class="card test-card">
              <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-pen-fancy test-icon"></i> <?php echo $testTypeName; ?></span>
                <div>
                  <span class="badge <?php echo $badgeClass; ?> mr-2"><?php echo $badgeText; ?></span>
                  <?php if ($isAttempted): ?>
                    <span class="badge attempted-badge">Đã làm</span>
                  <?php endif; ?>
                </div>
              </div>
              <div class="card-body">
                <h5 class="card-title"><?php echo htmlspecialchars($test['ten_de_thi']); ?></h5>
                <p class="card-text"><?php echo htmlspecialchars($test['category']); ?></p>
                
                <div class="test-details">
                  <div>
                    <i class="fas fa-clock text-muted"></i> 60 phút
                  </div>
                  <div>
                    <i class="fas fa-tag text-muted"></i> <?php echo htmlspecialchars($test['category']); ?>
                  </div>
                  <div>
                    <i class="fas fa-calendar-alt text-muted"></i> <?php echo date('d/m/Y', strtotime($test['created_at'])); ?>
                  </div>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                  <a href="take_test.php?id=<?php echo $test['id']; ?>" class="btn btn-primary">
                    <?php echo $isAttempted ? '<i class="fas fa-redo mr-2"></i> Làm lại' : '<i class="fas fa-play mr-2"></i> Bắt đầu làm bài'; ?>
                  </a>
                  
                  <?php if ($isAttempted): ?>
                    <a href="test_results.php?test_id=<?php echo $test['id']; ?>" class="btn btn-outline-secondary">
                      <i class="fas fa-chart-bar mr-2"></i> Xem kết quả
                    </a>
                  <?php else: ?>
                    <button class="btn btn-outline-info" data-toggle="modal" data-target="#previewModal<?php echo $test['id']; ?>">
                      <i class="fas fa-eye mr-2"></i> Xem trước
                    </button>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>

          <!-- Preview Modal -->
          <div class="modal fade" id="previewModal<?php echo $test['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="previewModalLabel<?php echo $test['id']; ?>" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="previewModalLabel<?php echo $test['id']; ?>"><?php echo htmlspecialchars($test['ten_de_thi']); ?></h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                  </button>
                </div>
                <div class="modal-body">
                  <h6>Thông tin bài kiểm tra:</h6>
                  <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                      Loại bài:
                      <span class="badge badge-pill <?php echo $testTypeClass; ?> text-white"><?php echo $testTypeName; ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                      Danh mục:
                      <span><?php echo htmlspecialchars($test['20']); ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                      Thời gian:
                      <span>60 phút</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                      Ngày phát hành:
                      <span><?php echo date('d/m/Y', strtotime($test['created_at'])); ?></span>
                    </li>
                  </ul>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                  <a href="take_test.php?id=<?php echo $test['id']; ?>" class="btn btn-primary"><i class="fas fa-play mr-2"></i> Bắt đầu kiểm tra</a>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="col-12">
          <div class="alert alert-info">
            <i class="fas fa-info-circle mr-2"></i> Hiện tại chưa có bài kiểm tra nào. Vui lòng quay lại sau.
          </div>
        </div>
      <?php endif; ?>
    </div>
    <!-- FAQ Section -->
    <div class="faq-section">
      <h3>Các câu hỏi thường gặp trong bài thi IELTS Writing?</h3>
      <div class="accordion" id="faqAccordion">
        <div class="card">
          <div class="card-header" id="faqHeading1">
            <h5 class="mb-0">
              <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#faqCollapse1" aria-expanded="true" aria-controls="faqCollapse1">
                Bài thi IELTS Writing gồm những phần nào?
              </button>
            </h5>
          </div>
          <div id="faqCollapse1" class="collapse" aria-labelledby="faqHeading1" data-parent="#faqAccordion">
            <div class="card-body">
              Bài thi IELTS Writing gồm hai phần: Task 1 (bài viết ngắn, 150 từ, mô tả biểu đồ/dữ liệu) và Task 2 (bài luận, 250 từ, trình bày quan điểm hoặc giải quyết vấn đề). Thời gian hoàn thành là 60 phút.
            </div>
          </div>
        </div>
        <div class="card">
          <div class="card-header" id="faqHeading2">
            <h5 class="mb-0">
              <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#faqCollapse2" aria-expanded="false" aria-controls="faqCollapse2">
                Tiêu chí chấm điểm của IELTS Writing là gì?
              </button>
            </h5>
          </div>
          <div id="faqCollapse2" class="collapse" aria-labelledby="faqHeading2" data-parent="#faqAccordion">
            <div class="card-body">
              Bài thi được chấm dựa trên 4 tiêu chí: Task Achievement/Response (đáp ứng yêu cầu đề bài), Coherence and Cohesion (mạch lạc và liên kết), Lexical Resource (vốn từ vựng), và Grammatical Range and Accuracy (ngữ pháp).
            </div>
          </div>
        </div>
        <div class="card">
          <div class="card-header" id="faqHeading3">
            <h5 class="mb-0">
              <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#faqCollapse3" aria-expanded="false" aria-controls="faqCollapse3">
                Tôi nên phân bổ thời gian như thế nào cho Task 1 và Task 2?
              </button>
            </h5>
          </div>
          <div id="faqCollapse3" class="collapse" aria-labelledby="faqHeading3" data-parent="#faqAccordion">
            <div class="card-body">
              Bạn nên dành khoảng 20 phút cho Task 1 và 40 phút cho Task 2, vì Task 2 chiếm tỷ trọng điểm cao hơn. Hãy dành thời gian lập dàn ý trước khi viết để đảm bảo nội dung rõ ràng và logic.
            </div>
          </div>
        </div>
        <div class="card">
          <div class="card-header" id="faqHeading4">
            <h5 class="mb-0">
              <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#faqCollapse4" aria-expanded="true" aria-controls="faqCollapse4">
                Nếu bài luận của tôi ít hơn 150 từ (cho bài viết IELTS Writing Task 1) và 250 từ (cho bài viết IELTS Writing Task 2), có bị trừ điểm hay không?
              </button>
            </h5>
          </div>
          <div id="faqCollapse4" class="collapse" aria-labelledby="faqHeading4" data-parent="#faqAccordion">
            <div class="card-body">
              Có, nghĩa là bạn cần phải viết ít nhất là 150 từ cho IELTS Writing task 1, và 250 từ trở lên cho IELTS Writing task 2. Mặc dù không có giới hạn từ, bạn vẫn cần phải quản lý số từ của từng bài để bạn có thể hoàn thành bài thi IELTS Writing đúng thời gian.
            </div>
          </div>
        </div>
        <div class="card">
          <div class="card-header" id="faqHeading5">
            <h5 class="mb-0">
              <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#faqCollapse5" aria-expanded="false" aria-controls="faqCollapse5">
                Điều gì sẽ xảy ra nếu có bất kỳ lỗi chính tả nào? 
              </button>
            </h5>
          </div>
          <div id="faqCollapse5" class="collapse" aria-labelledby="faqHeading5" data-parent="#faqAccordion">
            <div class="card-body">
              Một vài lỗi chính tả có thể không ảnh hưởng đến điểm tổng thể của bạn trong bài thi viết IELTS, nhưng bạn nên cố gắng xác định chắc chắn từng từ bạn sẽ viết để tối đa hóa điểm số của mình.
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- AI Writing Analysis Section -->
    <div class="row mt-5">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header bg-primary text-white">
            <h4 class="mb-0"><i class="fas fa-robot mr-2"></i> Phân Tích và Chấm Điểm Bài Viết</h4>
          </div>
          <div class="card-body">
            <h5>Chấm điểm và kiểm tra bài viết với AI</h5>
            <p>Dán bài viết IELTS của bạn vào đây để nhận điểm số theo tiêu chí IELTS và kiểm tra mức độ phụ thuộc vào AI:</p>
            <ul>
              <li>Chấm điểm theo các tiêu chí IELTS (Task Achievement/Response, Coherence and Cohesion, Lexical Resource, Grammatical Range)</li>
              <li>Phân tích lỗi ngữ pháp, từ vựng và cấu trúc</li>
              <li>Gợi ý cải thiện bài viết</li>
              <li>Kiểm tra xem bài viết có sử dụng nội dung từ AI (như ChatGPT) hay không</li>
            </ul>
            <form id="analyzeEssayForm">
              <div class="form-group">
                <label for="category">Loại bài viết:</label>
                <select class="form-control" id="category" name="category">
                  <option value="Task 2">Task 2 (250 từ)</option>
                  <option value="Task 1">Task 1 (150 từ)</option>
                </select>
              </div>
              <div class="form-group">
                <textarea class="form-control" id="essayTextarea" name="essayText" rows="8" placeholder="Dán bài viết của bạn vào đây..."></textarea>
              </div>
              <button type="submit" id="analyzeButton" class="btn btn-success">
                <i class="fas fa-check-circle mr-2"></i> Chấm điểm và kiểm tra AI
              </button>
            </form>
            <div id="analysisResult" class="analysis-section mt-3" style="display: none;"></div>
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
        // Filter functionality
        $("#filterButton").click(function(){
          const difficulty = $("#difficulty").val();
          const status = $("#status").val();
          alert("Filtering: Difficulty=" + difficulty + ", Status=" + status);
        });
        // Essay Analysis Form Submission
        $("#analyzeEssayForm").submit(function(e){
          e.preventDefault();
          const essayText = $("#essayTextarea").val().trim();
          const category = $("#category").val();
          
          if (essayText.length < 50) {
            $("#analysisResult")
              .html('<div class="alert alert-warning"><i class="fas fa-exclamation-circle mr-2"></i> Vui lòng nhập ít nhất 50 ký tự để phân tích.</div>')
              .show();
            return;
          }

          $("#analyzeButton")
            .html('<i class="fas fa-spinner fa-spin mr-2"></i> Đang phân tích...')
            .prop('disabled', true);

          const formData = new FormData();
          formData.append('ajax', 'analyze_essay');
          formData.append('essayText', essayText);
          formData.append('category', category);

          $.ajax({
            url: 'tests.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
              $("#analyzeButton")
                .html('<i class="fas fa-check-circle mr-2"></i> Chấm điểm và kiểm tra AI')
                .prop('disabled', false);

              if (response.success) {
                const data = response.data;
                const aiDependency = data.ai_dependency;
                const html = `
                  <h6><strong>Kết quả phân tích bài viết:</strong></h6>
                  <p><strong>Nội dung bài viết:</strong></p>
                  <p style="white-space: pre-wrap;">${$('<div>').text(data.essay_text).html()}</p>
                  <p><strong>Điểm tổng:</strong> ${parseFloat(data.ai_generated_score).toFixed(1)}</p>
                  <p><strong>Task Achievement/Task Response:</strong> ${parseFloat(data.ai_task_achievement).toFixed(1)}</p>
                  <p><strong>Coherence and Cohesion:</strong> ${parseFloat(data.ai_coherence_cohesion).toFixed(1)}</p>
                  <p><strong>Lexical Resource:</strong> ${parseFloat(data.ai_lexical_resource).toFixed(1)}</p>
                  <p><strong>Grammatical Range and Accuracy:</strong> ${parseFloat(data.ai_grammatical_range).toFixed(1)}</p>
                  <p><strong>Nhận xét chi tiết:</strong></p>
                  <p style="white-space: pre-wrap;">
                    <strong>Task Achievement/Task Response:</strong> ${$('<div>').text(data.task_achievement_explanation).html()}<br>
                    <strong>Coherence and Cohesion:</strong> ${$('<div>').text(data.coherence_cohesion_explanation).html()}<br>
                    <strong>Lexical Resource:</strong> ${$('<div>').text(data.lexical_resource_explanation).html()}<br>
                    <strong>Grammatical Range and Accuracy:</strong> ${$('<div>').text(data.grammatical_range_explanation).html()}
                  </p>
                  <p><strong>Phân tích lỗi và gợi ý cải thiện:</strong></p>
                  <p style="white-space: pre-wrap;">${$('<div>').text(data.review_comments).html()}</p>
                  <p><strong>Phân tích độ phụ thuộc AI:</strong></p>
                  <p><strong>Mức độ phụ thuộc:</strong> ${$('<div>').text(aiDependency.level).html()}</p>
                  <p><strong>Điểm AI Detection:</strong> ${parseFloat(aiDependency.score).toFixed(1)}%</p>
                  <p><strong>Nhận xét:</strong> ${$('<div>').text(aiDependency.explanation).html()}</p>
                  <p><strong>Cần xem xét thêm:</strong> ${$('<div>').text(aiDependency.flag_for_review).html()}</p>
                  <p><strong>Đánh giá tổng quan:</strong> ${$('<div>').text(data.feedback).html()}</p>
                  <p><strong>Lời khuyên:</strong> ${$('<div>').text(data.advice).html()}</p>
                `;
                $("#analysisResult").html(html).show();
              } else {
                $("#analysisResult")
                  .html(`<div class="alert alert-danger"><i class="fas fa-times-circle mr-2"></i> Lỗi: ${$('<div>').text(response.error).html()}</div>`)
                  .show();
              }
            },
            error: function(xhr, status, error) {
              $("#analyzeButton")
                .html('<i class="fas fa-check-circle mr-2"></i> Chấm điểm và kiểm tra AI')
                .prop('disabled', false);
              $("#analysisResult")
                .html('<div class="alert alert-danger"><i class="fas fa-times-circle mr-2"></i> Lỗi khi phân tích bài viết. Vui lòng thử lại.</div>')
                .show();
            }
          });
        });

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