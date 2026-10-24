<?php
session_start();

// Redirect to login if user is not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require 'config.php';

// Validate test ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: tests.php");
    exit;
}

$test_id = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Fetch test details
$stmt = $pdo->prepare("
    SELECT ta.*, dt.ten_de_thi, dt.category, u.username
    FROM test_attempts ta
    JOIN ds_de_thi dt ON ta.de_thi_id = dt.id
    JOIN users u ON ta.user_id = u.id
    WHERE ta.de_thi_id = ? AND ta.user_id = ?
");
$stmt->execute([$test_id, $user_id]);
$test = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$test) {
    header("Location: tests.php");
    exit;
}

// Fetch essay responses for the test
$stmt = $pdo->prepare("
    SELECT er.*, q.question, q.category, q.word_count AS required_word_count, q.question_image
    FROM essay_responses er
    JOIN ds_cau_hoi q ON er.question_id = q.id
    JOIN ds_de_thi_cau_hoi dtq ON q.id = dtq.cau_hoi_id
    WHERE dtq.de_thi_id = ? AND er.user_id = ?
    ORDER BY q.category
");
$stmt->execute([$test_id, $user_id]);
$essays = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Separate Task 1 and Task 2
$task1 = null;
$task2 = null;
foreach ($essays as $essay) {
    if (strpos($essay['category'], 'Task 1') !== false) {
        $task1 = $essay;
    } elseif (strpos($essay['category'], 'Task 2') !== false) {
        $task2 = $essay;
    }
}

// Fetch reviews for each essay
$reviews = [];
$errors_and_suggestions = [];
foreach ($essays as $essay) {
    $stmt = $pdo->prepare("SELECT * FROM essay_reviews WHERE essay_id = ?");
    $stmt->execute([$essay['id']]);
    $review = $stmt->fetch(PDO::FETCH_ASSOC);
    $reviews[$essay['id']] = $review;
    if ($review) {
        $errors_and_suggestions[$essay['id']] = parseReviewComments($review['review_comments']);
    }
}

// Load API key from config
$gemini_api_keyAsString = defined('GEMINI_API_KEY') ? GEMINI_API_KEY : 'AIzaSyCpSVknxRLhGZD1f-pcd_zH6sxGin38Bk0';

// Re-use functions from view_essay.php
function isValidEndpoint($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return $http_code == 200 || $http_code == 405;
}

function analyzeTextForAI($essayText) {
    $words = str_word_count(strtolower($essayText), 1);
    $wordCount = count($words);
    $wordFreq = array_count_values($words);
    
    $repetitionScore = 0;
    foreach ($wordFreq as $word => $count) {
        if ($count / $wordCount > 0.05) {
            $repetitionScore += $count;
        }
    }
    
    $complexWords = count(array_filter($words, function($word) {
        return strlen($word) > 7;
    }));
    $complexityRatio = $wordCount ? $complexWords / $wordCount : 0;
    
    return [
        'repetition_score' => $repetitionScore,
        'complexity_ratio' => $complexityRatio
    ];
}

function saveUserProgress($pdo, $userId, $essayId, $scores, $taskType, $taskCategory) {
    $stmt = $pdo->prepare("
        INSERT INTO user_progress (
            user_id, task_type, task_category, essay_id,
            task_achievement_response, coherence_cohesion,
            lexical_resource, grammatical_range_accuracy,
            overall_band, completed_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ");
    $stmt->execute([
        $userId,
        $taskType,
        $taskCategory,
        $essayId,
        $scores['task_achievement'] ?? $scores['task_response'],
        $scores['coherence_cohesion'],
        $scores['lexical_resource'],
        $scores['grammatical_range'],
        $scores['overall']
    ]);
}

function getUserProgress($pdo, $userId, $taskType = null, $taskCategory = null) {
    $sql = "
        SELECT task_type, task_category, task_achievement_response,
               coherence_cohesion, lexical_resource, grammatical_range_accuracy,
               overall_band, completed_at
        FROM user_progress
        WHERE user_id = ? 
        AND task_type = 'FullTest'
        AND overall_band != 0
        AND task_achievement_response != 0
        AND coherence_cohesion != 0
        AND lexical_resource != 0
        AND grammatical_range_accuracy != 0
    ";
    $params = [$userId];
    
    if ($taskCategory) {
        $sql .= " AND task_category = ?";
        $params[] = $taskCategory;
    }
    
    $sql .= " ORDER BY completed_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function generateProgressFeedback($currentProgress, $previousProgress) {
    if (!$previousProgress) {
        return '';
    }
    
    $feedback = '<h6><strong>Nhận xét về sự tiến bộ:</strong></h6>';
    $currentOverall = $currentProgress['overall_band'];
    $previousOverall = $previousProgress['overall_band'];
    
    if ($currentOverall > $previousOverall) {
        $feedback .= "<p>So với bài FullTest trước, điểm tổng của bạn tăng từ {$previousOverall} lên {$currentOverall}. Chúc mừng bạn đã cải thiện!</p>";
    } elseif ($currentOverall < $previousOverall) {
        $feedback .= "<p>So với bài FullTest trước, điểm tổng của bạn giảm từ {$previousOverall} xuống {$currentOverall}. Bạn nên xem lại các tiêu chí để cải thiện.</p>";
    } else {
        $feedback .= "<p>Điểm tổng của bạn vẫn là {$currentOverall}, tương đương với bài FullTest trước. Hãy cố gắng cải thiện ở các tiêu chí cụ thể!</p>";
    }
    
    $criteria = [
        'task_achievement_response' => 'Task Achievement/Task Response',
        'coherence_cohesion' => 'Coherence and Cohesion',
        'lexical_resource' => 'Lexical Resource',
        'grammatical_range_accuracy' => 'Grammatical Range and Accuracy'
    ];
    
    foreach ($criteria as $key => $name) {
        $currentScore = $currentProgress[$key];
        $previousScore = $previousProgress[$key];
        if ($currentScore > $previousScore) {
            $feedback .= "<p>Điểm {$name} tăng từ {$previousScore} lên {$currentScore}. Hãy tiếp tục phát huy!</p>";
        } elseif ($currentScore < $previousScore) {
            $feedback .= "<p>Điểm {$name} giảm từ {$previousScore} xuống {$currentScore}. Bạn nên tập trung cải thiện khía cạnh này.</p>";
        }
    }
    
    return $feedback;
}

function parseReviewComments($reviewComments) {
    $errors_and_suggestions = [
        'task_achievement' => ['errors' => '', 'suggestions' => ''],
        'task_response' => ['errors' => '', 'suggestions' => ''],
        'coherence_cohesion' => ['errors' => '', 'suggestions' => ''],
        'lexical_resource' => ['errors' => '', 'suggestions' => ''],
        'grammatical_range' => ['errors' => '', 'suggestions' => '']
    ];
    
    $lines = explode("\n", $reviewComments);
    foreach ($lines as $line) {
        if (strpos($line, 'Task Achievement/Task Response Errors:') === 0) {
            $errors_and_suggestions['task_achievement']['errors'] = trim(substr($line, strlen('Task Achievement/Task Response Errors:')));
        } elseif (strpos($line, 'Task Achievement/Task Response Suggestions:') === 0) {
            $errors_and_suggestions['task_achievement']['suggestions'] = trim(substr($line, strlen('Task Achievement/Task Response Suggestions:')));
        } elseif (strpos($line, 'Coherence and Cohesion Errors:') === 0) {
            $errors_and_suggestions['coherence_cohesion']['errors'] = trim(substr($line, strlen('Coherence and Cohesion Errors:')));
        } elseif (strpos($line, 'Coherence and Cohesion Suggestions:') === 0) {
            $errors_and_suggestions['coherence_cohesion']['suggestions'] = trim(substr($line, strlen('Coherence and Cohesion Suggestions:')));
        } elseif (strpos($line, 'Lexical Resource Errors:') === 0) {
            $errors_and_suggestions['lexical_resource']['errors'] = trim(substr($line, strlen('Lexical Resource Errors:')));
        } elseif (strpos($line, 'Lexical Resource Suggestions:') === 0) {
            $errors_and_suggestions['lexical_resource']['suggestions'] = trim(substr($line, strlen('Lexical Resource Suggestions:')));
        } elseif (strpos($line, 'Grammatical Range and Accuracy Errors:') === 0) {
            $errors_and_suggestions['grammatical_range']['errors'] = trim(substr($line, strlen('Grammatical Range and Accuracy Errors:')));
        } elseif (strpos($line, 'Grammatical Range and Accuracy Suggestions:') === 0) {
            $errors_and_suggestions['grammatical_range']['suggestions'] = trim(substr($line, strlen('Grammatical Range and Accuracy Suggestions:')));
        }
    }
    
    return $errors_and_suggestions;
}

function getGeminiIELTSScore($essay_text, $category, $required_word_count, $api_key, $taskType = 'FullTest', $retries = 5, $delay = 60) {
    if (empty($essay_text)) {
        return [
            'success' => false,
            'error' => 'Bài viết rỗng'
        ];
    }

    $aiAnalysis = analyzeTextForAI($essay_text);
    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=$api_key";
    error_log("Using endpoint: $url");

    $taskInstructions = "This is a full test submission. Evaluate strictly as per IELTS standards, emphasizing comprehensive analysis of all criteria and penalizing minor inconsistencies more rigorously.";

    $prompt = <<<EOD
You are an IELTS examiner trained to score essays accurately according to IELTS Writing criteria (Task Achievement/Task Response, Coherence and Cohesion, Lexical Resource, Grammatical Range and Accuracy). Your task is to evaluate the provided essay and assign band scores (0–9, rounded to nearest 0.5) for each criterion, calculate the overall band, and provide detailed feedback. Additionally, analyze the essay for AI dependency.

**Essay Details**:
- Text: {$essay_text}
- Category: {$category} (Task 1 or Task 2)
- Required Word Count: {$required_word_count}
- Submission Type: {$taskType}
- Quantitative Analysis:
  - Word repetition score: {$aiAnalysis['repetition_score']}
  - Vocabulary complexity ratio: {$aiAnalysis['complexity_ratio']}

**Scoring Instructions**:
{$taskInstructions}

1. **Task Achievement (Task 1) / Task Response (Task 2)**:
   - Task 1: Assess how well the essay summarizes key features of visual data (e.g., charts, processes). Example: A Band 6 response selects key features but lacks specific data; a Band 8.5 response covers all stages clearly with precise details.
   - Task 2: Evaluate how fully the essay addresses the prompt, develops arguments, and supports ideas with evidence. Example: A Band 4 response has unclear ideas; a Band 8.5 response develops ideas deeply with relevant examples.
   - Penalize if word count is significantly below the requirement.

2. **Coherence and Cohesion**:
   - Assess logical organization, paragraphing, and use of cohesive devices. Example: A Band 7.5 response uses cohesive devices well but overuses sequencers; a Band 4 response lacks progression.

3. **Lexical Resource**:
   - Evaluate vocabulary range, accuracy, and appropriateness. Example: A Band 6 response uses adequate vocabulary with errors; a Band 8 response uses sophisticated vocabulary with minimal errors.

4. **Grammatical Range and Accuracy**:
   - Assess sentence variety and accuracy. Example: A Band 5.5 response has frequent errors; a Band 8.5 response uses varied structures with rare errors.

**Feedback Requirements**:
- Provide a band score (0–9, rounded to 0.5) for each criterion and an overall band (average of the four, rounded to nearest 0.5).
- For each criterion, include:
  - Strengths: Highlight what the candidate did well.
  - Weaknesses: Identify specific issues.
  - Examples: Cite specific words, phrases, or sentences from the essay to support your evaluation.
  - Errors: List specific errors related to the criterion.
  - Suggestions: Offer actionable advice to improve.
- Example feedback structure:
  - Task Response: 6.0 – Addresses prompt but ideas underdeveloped. Example: “Online education benefits” lacks specific examples. Errors: Lack of specific examples. Suggestions: Include real-world examples.

**AI Dependency Analysis**:
Analyze the essay for signs of AI-generated content. Consider:
- Formulaic patterns: Repeated phrases like “it is no wonder”.
- Unnatural polish: Overly sophisticated vocabulary without errors.
- Inappropriate word choice: Words like “alimentation” used incorrectly.
- Lack of personalization: Absence of personal examples.
- Structural uniformity: Overly consistent sentence structures.
- Quantitative metrics: High repetition score or unnatural complexity ratio.

**AI Scoring**:
- Assign a score (0–100):
  - 0–20 (None): Human-like with natural errors.
  - 21–50 (Low): Minor AI influence.
  - 51–80 (Medium): Significant AI influence.
  - 81–100 (High): Likely AI-generated.
- Provide an explanation citing specific examples.
- Flag for review if score > 50 or formulaic patterns detected.

**Output Format**:
{
  "scores": {
    "task_achievement": {"score": X, "explanation": "...", "examples": ["example1", "example2"]},
    "task_response": {"score": X, "explanation": "...", "examples": ["example1", "example2"]},
    "coherence_cohesion": {"score": X, "explanation": "...", "examples": ["example1", "example2"]},
    "lexical_resource": {"score": X, "explanation": "...", "examples": ["example1", "example2"]},
    "grammatical_range": {"score": X, "explanation": "...", "examples": ["example1", "example2"]}
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
                error_log("Quota exceeded. Retrying after $delay seconds...");
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
            error_log("Failed to decode API response: " . json_last_error_msg() . " | Response: $response");
            return [
                'success' => false,
                'error' => 'Phản hồi API không hợp lệ. Vui lòng thử lại.'
            ];
        }

        if (isset($result['error'])) {
            error_log("API error: " . json_encode($result['error']));
            if ($http_code == 429) {
                $attempt++;
                error_log("Quota exceeded. Retrying after $delay seconds...");
                sleep($delay);
                continue;
            } elseif ($http_code == 400 && strpos($result['error']['message'], 'API key') !== false) {
                return [
                    'success' => false,
                    'error' => 'Lỗi hệ thống: Khóa API không hợp lệ.'
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

        error_log("Parsed text: " . $parsed_text);
        $parsed_result = json_decode($parsed_text, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("JSON decode error: " . json_last_error_msg() . " | Parsed text: " . $parsed_text);
            return [
                'success' => false,
                'error' => 'Lỗi phân tích JSON từ phản hồi API.'
            ];
        }

        if (!isset($parsed_result['scores']) || !isset($parsed_result['overall'])) {
            error_log("Response missing required fields: " . json_encode($parsed_result));
            return [
                'success' => false,
                'error' => 'Phản hồi API thiếu thông tin cần thiết.'
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
                'task_achievement' => $task_achievement,
                'task_response' => $task_response,
                'coherence_cohesion' => $coherence_cohesion,
                'lexical_resource' => $lexical_resource,
                'grammatical_range' => $grammatical_range,
                'overall' => $overall_score
            ],
            'explanations' => [
                'task_achievement' => $parsed_result['scores']['task_achievement']['explanation'] ?? $parsed_result['scores']['task_response']['explanation'] ?? '',
                'coherence_cohesion' => $parsed_result['scores']['coherence_cohesion']['explanation'] ?? '',
                'lexical_resource' => $parsed_result['scores']['lexical_resource']['explanation'] ?? '',
                'grammatical_range' => $parsed_result['scores']['grammatical_range']['explanation'] ?? ''
            ],
            'examples' => [
                'task_achievement' => $parsed_result['scores']['task_achievement']['examples'] ?? $parsed_result['scores']['task_response']['examples'] ?? [],
                'coherence_cohesion' => $parsed_result['scores']['coherence_cohesion']['examples'] ?? [],
                'lexical_resource' => $parsed_result['scores']['lexical_resource']['examples'] ?? [],
                'grammatical_range' => $parsed_result['scores']['grammatical_range']['examples'] ?? []
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

function calculateFinalScore($ai_score, $human_score) {
    if ($human_score === null || $human_score === '') {
        return number_format($ai_score, 1);
    }
    if ($ai_score === null || $ai_score === '') {
        return number_format($human_score, 1);
    }
    $average = ($ai_score + $human_score) / 2;
    return number_format(round($average * 2) / 2, 1);
}

function calculateTestOverallScore($task1_score, $task2_score) {
    if ($task1_score === null || $task2_score === null) {
        return null;
    }
    $average = ($task1_score + $task2_score) / 2;
    return number_format(round($average * 2) / 2, 1);
}

// Process AI scoring and save progress
$review_error = null;
foreach ($essays as $essay) {
    $essay_id = $essay['id'];
    if (!isset($reviews[$essay_id]) && $essay['response_text']) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM essay_reviews WHERE essay_id = ?");
            $stmt->execute([$essay_id]);
            $existing_review = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existing_review) {
                $reviews[$essay_id] = $existing_review;
                $errors_and_suggestions[$essay_id] = parseReviewComments($existing_review['review_comments']);
            } else {
                $pdo->beginTransaction();

                $taskType = 'FullTest';
                $taskCategory = strpos($essay['category'], 'Task 1') !== false ? 'Task1' : 'Task2';
                $score_result = getGeminiIELTSScore(
                    $essay['response_text'],
                    $essay['category'],
                    $essay['required_word_count'],
                    $gemini_api_keyAsString,
                    $taskType
                );

                if (!$score_result['success']) {
                    throw new Exception($score_result['error']);
                }

                saveUserProgress($pdo, $user_id, $essay_id, $score_result['scores'], $taskType, $taskCategory);

                $task_key = isset($score_result['errors_and_suggestions']['task_achievement']) ? 'task_achievement' : 'task_response';
                $comments = "Task Achievement/Task Response Errors: {$score_result['errors_and_suggestions'][$task_key]['errors']}\n" .
                            "Task Achievement/Task Response Suggestions: {$score_result['errors_and_suggestions'][$task_key]['suggestions']}\n" .
                            "Coherence and Cohesion Errors: {$score_result['errors_and_suggestions']['coherence_cohesion']['errors']}\n" .
                            "Coherence and Cohesion Suggestions: {$score_result['errors_and_suggestions']['coherence_cohesion']['suggestions']}\n" .
                            "Lexical Resource Errors: {$score_result['errors_and_suggestions']['lexical_resource']['errors']}\n" .
                            "Lexical Resource Suggestions: {$score_result['errors_and_suggestions']['lexical_resource']['suggestions']}\n" .
                            "Grammatical Range and Accuracy Errors: {$score_result['errors_and_suggestions']['grammatical_range']['errors']}\n" .
                            "Grammatical Range and Accuracy Suggestions: {$score_result['errors_and_suggestions']['grammatical_range']['suggestions']}";

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
                    'AI dependency analysis for essay',
                    $score_result['ai_dependency']['explanation'],
                    $score_result['ai_dependency']['score'],
                    $score_result['ai_dependency']['flag_for_review'] === 'Yes' ? 'Yes' : 'No',
                    $score_result['ai_dependency']['level']
                ]);

                $stmt = $pdo->prepare("
                    INSERT INTO essay_reviews (
                        essay_id, ai_generated_score, ai_task_achievement, ai_coherence_cohesion, ai_lexical_resource, ai_grammatical_range,
                        review_comments, ai_dependency_comments, feedback, advice, task_achievement_explanation, coherence_cohesion_explanation,
                        lexical_resource_explanation, grammatical_range_explanation, task_achievement_examples, coherence_cohesion_examples,
                        lexical_resource_examples, grammatical_range_examples, reviewed_at
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
                ");
                $stmt->execute([
                    $essay_id,
                    $score_result['overall'],
                    $score_result['scores']['task_achievement'] ?? $score_result['scores']['task_response'],
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
                    $score_result['explanations']['grammatical_range'],
                    json_encode($score_result['examples']['task_achievement']),
                    json_encode($score_result['examples']['coherence_cohesion']),
                    json_encode($score_result['examples']['lexical_resource']),
                    json_encode($score_result['examples']['grammatical_range'])
                ]);

                $stmt = $pdo->prepare("SELECT * FROM essay_reviews WHERE essay_id = ?");
                $stmt->execute([$essay_id]);
                $reviews[$essay_id] = $stmt->fetch(PDO::FETCH_ASSOC);
                $errors_and_suggestions[$essay_id] = $score_result['errors_and_suggestions'];

                $pdo->commit();
            }
        } catch (Exception $e) {
            $pdo->rollBack();
            error_log("Lỗi xử lý AI review cho essay $essay_id: " . $e->getMessage());
            $review_error = $e->getMessage();
        }
    }
}

// Calculate test overall score
$test_overall_score = null;
if ($task1 && $task2 && isset($reviews[$task1['id']]) && isset($reviews[$task2['id']])) {
    $task1_score = $reviews[$task1['id']]['final_score'] ?? $reviews[$task1['id']]['ai_generated_score'];
    $task2_score = $reviews[$task2['id']]['final_score'] ?? $reviews[$task2['id']]['ai_generated_score'];
    $test_overall_score = calculateTestOverallScore($task1_score, $task2_score);
}

// Fetch progress data
$progressData = getUserProgress($pdo, $user_id, 'FullTest');
$overallScores = array_column($progressData, 'overall_band');
$submissionTimes = array_map(function($time) {
    return date('d/m/Y H:i', strtotime($time));
}, array_column($progressData, 'completed_at'));

// Get progress feedback
$progressFeedback = '';
if (count($progressData) > 1) {
    $currentProgress = $progressData[0];
    $previousProgress = $progressData[1];
    $progressFeedback = generateProgressFeedback($currentProgress, $previousProgress);
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>Xem Bài Thi - IELTS Writing</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .navbar { background-color: white; box-shadow: 0 2px 4px rgba(0,0,0,0.1); padding: 10px 0; }
        .navbar-brand { font-weight: bold; font-size: 24px; }
        .navbar-brand span:first-child { color: #e74c3c; }
        .navbar-brand span.blue { color: #1565c0; }
        .navbar-nav .nav-item { margin: 0 5px; }
        .navbar-nav .nav-link { color: #555; font-weight: 500; padding: 8px 12px; transition: all 0.3s ease; }
        .navbar-nav .nav-link:hover, .navbar-nav .nav-item.active .nav-link { color: #3498db; }
        .dropdown-menu { border: none; box-shadow: 0 5px 15px rgba(0,0,0,0.1); padding: 8px 0; }
        .dropdown-item { padding: 8px 20px; color: #555; transition: all 0.2s ease; }
        .dropdown-item:hover { background-color: #f8f9fa; color: #3498db; }
        .dropdown-submenu { position: relative; }
        .dropdown-submenu .dropdown-menu { top: 0; left: 100%; margin-top: -1px; display: none; }
        .dropdown-submenu:hover .dropdown-menu { display: block; }
        @media (max-width: 991px) {
            .dropdown-submenu .dropdown-menu { position: static; left: 0; box-shadow: none; border: none; padding-left: 20px; }
        }
        .user-placeholder { width: 36px; height: 36px; background-color: #718096; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; cursor: pointer; }
        .vn-flag { height: 20px; margin-right: 15px; cursor: pointer; }
        .user-account .dropdown-menu { right: 0; left: auto; min-width: 200px; }
        .section-title { margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #1565c0; color: #1565c0; }
        .card { border-radius: 10px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); }
        .footer { margin-top: 50px; background-color: #293241; padding: 20px 0; color: white; }
        .score-badge { font-size: 1.2rem; padding: 10px; border-radius: 5px; }
        .feedback-section { background-color: #f1f8ff; padding: 15px; border-radius: 5px; margin-top: 20px; }
        .loading-spinner { display: none; margin: 20px auto; text-align: center; }
        .score-table th, .score-table td { vertical-align: middle; text-align: center; }
        .explanation-section { background-color: #e8f4f8; padding: 15px; border-radius: 5px; margin-top: 20px; }
        .ai-dependency-section { background-color: #f8d7da; padding: 15px; border-radius: 5px; margin-top: 20px; }
        .progress-section { background-color: #e9ecef; padding: 15px; border-radius: 5px; margin-top: 20px; }
        .progress-section table { background-color: white; }
        .question-image { max-width: 100%; height: auto; margin: 10px 0; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .image-error { color: #dc3545; font-style: italic; }
        .progress-feedback-section { background-color: #d4edda; padding: 15px; border-radius: 5px; margin-top: 20px; }
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
                        <a class="nav-link dropdown-toggle" href="index.php#lessons" id="lessonsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
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

    <div class="container">
        <h3 class="section-title">
            <i class="fas fa-file-alt mr-2"></i>Xem Bài Thi - <?php echo htmlspecialchars($test['ten_de_thi']); ?>
        </h3>
        <div class="row mb-3">
            <div class="col-md-12">
                <a href="tests.php" class="btn btn-secondary"><i class="fas fa-arrow-left mr-2"></i>Quay lại</a>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Thông tin bài thi</h5>
                        <p><strong>Tên đề thi: </strong><?php echo htmlspecialchars($test['ten_de_thi']); ?></p>
                        <p><strong>Loại bài: </strong><?php echo htmlspecialchars($test['category']); ?></p>
                        <?php
                        $total_time_taken = 0;
                        if ($task1 && isset($task1['time_taken'])) {
                            $total_time_taken += (int)$task1['time_taken'];
                        }
                        if ($task2 && isset($task2['time_taken'])) {
                            $total_time_taken += (int)$task2['time_taken'];
                        }
                        ?>
                        <p><strong>Thời gian làm bài: </strong><?php echo $total_time_taken > 0 ? htmlspecialchars($total_time_taken) . ' phút' : 'Chưa có dữ liệu'; ?></p>
                        <p><strong>Thời gian nộp: </strong><?php echo isset($test['submission_time']) && !empty($test['submission_time']) ? date('d/m/Y H:i', strtotime($test['submission_time'])) : 'Chưa có dữ liệu'; ?></p>
                        <?php if ($test_overall_score): ?>
                            <p><strong>Điểm tổng bài thi: </strong><span class="badge badge-primary score-badge"><?php echo $test_overall_score; ?></span></p>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if ($review_error): ?>
                    <div class="alert alert-warning">
                        Lỗi khi chấm điểm bài viết: <?php echo htmlspecialchars($review_error); ?>. Vui lòng thử lại sau.
                    </div>
                <?php endif; ?>

                <?php if ($task1): ?>
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Task 1: <?php echo htmlspecialchars($task1['question']); ?></h5>
                            <p><strong>Loại bài: </strong><?php echo htmlspecialchars($task1['category']); ?></p>
                            <p><strong>Thời gian làm bài: </strong><?php echo htmlspecialchars($task1['time_taken']); ?> phút</p>
                            <p><strong>Số từ: </strong><?php echo htmlspecialchars($task1['word_count']); ?></p>
                            <p><strong>Thời gian nộp: </strong><?php echo date('d/m/Y H:i', strtotime($task1['submission_time'])); ?></p>
                            <hr>
                            <?php if (!empty($task1['question_image'])): ?>
                                <div class="image-container">
                                    <img src="<?php echo htmlspecialchars($task1['question_image']); ?>" alt="Question Image" class="question-image" data-path="<?php echo htmlspecialchars($task1['question_image']); ?>">
                                    <p class="image-error" style="display: none;">Cảnh báo: Không tìm thấy file ảnh tại <?php echo htmlspecialchars($task1['question_image']); ?>. Đảm bảo file tồn tại trong thư mục IELTS Writing/backend/uploads/.</p>
                                </div>
                            <?php endif; ?>
                            <h6><strong>Nội dung bài làm:</strong></h6>
                            <p style="white-space: pre-wrap;"><?php echo htmlspecialchars($task1['response_text']); ?></p>
                        </div>
                    </div>

                    <?php if (isset($reviews[$task1['id']])): ?>
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="card-title">Kết quả chấm điểm Task 1</h5>
                                <table class="table table-bordered score-table">
                                    <thead>
                                        <tr>
                                            <th>Tiêu chí</th>
                                            <th>Điểm do AI chấm (Gemini)</th>
                                            <th>Điểm do giáo viên chấm</th>
                                            <th>Điểm cuối cùng</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Điểm tổng</td>
                                            <td><span class="badge badge-success score-badge"><?php echo number_format($reviews[$task1['id']]['ai_generated_score'], 1); ?></span></td>
                                            <td><span class="badge badge-info score-badge"><?php echo $reviews[$task1['id']]['human_score'] ? number_format($reviews[$task1['id']]['human_score'], 1) : 'Chưa có'; ?></span></td>
                                            <td><span class="badge badge-primary score-badge"><?php echo calculateFinalScore($reviews[$task1['id']]['ai_generated_score'], $reviews[$task1['id']]['human_score']); ?></span></td>
                                        </tr>
                                        <tr>
                                            <td>Task Achievement</td>
                                            <td><?php echo number_format($reviews[$task1['id']]['ai_task_achievement'], 1); ?></td>
                                            <td><?php echo $reviews[$task1['id']]['human_task_achievement'] ? number_format($reviews[$task1['id']]['human_task_achievement'], 1) : 'Chưa có'; ?></td>
                                            <td><?php echo calculateFinalScore($reviews[$task1['id']]['ai_task_achievement'], $reviews[$task1['id']]['human_task_achievement']); ?></td>
                                        </tr>
                                        <tr>
                                            <td>Coherence and Cohesion</td>
                                            <td><?php echo number_format($reviews[$task1['id']]['ai_coherence_cohesion'], 1); ?></td>
                                            <td><?php echo $reviews[$task1['id']]['human_coherence_cohesion'] ? number_format($reviews[$task1['id']]['human_coherence_cohesion'], 1) : 'Chưa có'; ?></td>
                                            <td><?php echo calculateFinalScore($reviews[$task1['id']]['ai_coherence_cohesion'], $reviews[$task1['id']]['human_coherence_cohesion']); ?></td>
                                        </tr>
                                        <tr>
                                            <td>Lexical Resource</td>
                                            <td><?php echo number_format($reviews[$task1['id']]['ai_lexical_resource'], 1); ?></td>
                                            <td><?php echo $reviews[$task1['id']]['human_lexical_resource'] ? number_format($reviews[$task1['id']]['human_lexical_resource'], 1) : 'Chưa có'; ?></td>
                                            <td><?php echo calculateFinalScore($reviews[$task1['id']]['ai_lexical_resource'], $reviews[$task1['id']]['human_lexical_resource']); ?></td>
                                        </tr>
                                        <tr>
                                            <td>Grammatical Range and Accuracy</td>
                                            <td><?php echo number_format($reviews[$task1['id']]['ai_grammatical_range'], 1); ?></td>
                                            <td><?php echo $reviews[$task1['id']]['human_grammatical_range'] ? number_format($reviews[$task1['id']]['human_grammatical_range'], 1) : 'Chưa có'; ?></td>
                                            <td><?php echo calculateFinalScore($reviews[$task1['id']]['ai_grammatical_range'], $reviews[$task1['id']]['human_grammatical_range']); ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                                <?php if ($reviews[$task1['id']]['ai_dependency_comments']): ?>
                                    <div class="ai-dependency-section">
                                        <h6><strong>Phân tích mức độ phụ thuộc vào AI từ Gemini:</strong></h6>
                                        <p><strong>Mức độ phụ thuộc:</strong> <?php echo htmlspecialchars($task1['ai_assistance_level']); ?></p>
                                        <p><strong>Điểm AI Detection:</strong> <?php echo number_format($task1['ai_detection_score'], 1); ?>%</p>
                                        <p><strong>Nhận xét:</strong> <?php echo htmlspecialchars($reviews[$task1['id']]['ai_dependency_comments']); ?></p>
                                        <p><strong>Cần xem xét thêm:</strong> <?php echo htmlspecialchars($task1['ai_detection_score'] > 50 ? 'Yes' : 'No'); ?></p>
                                    </div>
                                <?php endif; ?>
                                <?php if ($reviews[$task1['id']]['feedback']): ?>
                                    <div class="feedback-section">
                                        <h6><strong>Đánh giá tổng quan từ Gemini:</strong></h6>
                                        <p><?php echo htmlspecialchars($reviews[$task1['id']]['feedback']); ?></p>
                                    </div>
                                <?php endif; ?>
                                <?php if ($reviews[$task1['id']]['advice']): ?>
                                    <div class="feedback-section">
                                        <h6><strong>Lời khuyên từ Gemini:</strong></h6>
                                        <p><?php echo htmlspecialchars($reviews[$task1['id']]['advice']); ?></p>
                                    </div>
                                <?php endif; ?>
                                <?php if ($reviews[$task1['id']]['task_achievement_explanation'] || $reviews[$task1['id']]['coherence_cohesion_explanation'] || $reviews[$task1['id']]['lexical_resource_explanation'] || $reviews[$task1['id']]['grammatical_range_explanation']): ?>
                                    <div class="explanation-section">
                                        <h6><strong>Giải thích chi tiết điểm số (bằng tiếng Anh):</strong></h6>
                                        <?php 
                                        $task_key = isset($errors_and_suggestions[$task1['id']]['task_achievement']) && !empty($errors_and_suggestions[$task1['id']]['task_achievement']['errors']) ? 'task_achievement' : 'task_response';
                                        if ($reviews[$task1['id']]['task_achievement_explanation']): ?>
                                            <p><strong>Task Achievement/Task Response:</strong> <?php echo htmlspecialchars($reviews[$task1['id']]['task_achievement_explanation']); ?></p>
                                            <p><strong>Lỗi:</strong> <?php echo htmlspecialchars($errors_and_suggestions[$task1['id']][$task_key]['errors']); ?></p>
                                            <p><strong>Gợi ý cải thiện:</strong> <?php echo htmlspecialchars($errors_and_suggestions[$task1['id']][$task_key]['suggestions']); ?></p>
                                            <?php if ($reviews[$task1['id']]['task_achievement_examples']): ?>
                                                <p><strong>Ví dụ từ bài viết:</strong> <?php echo htmlspecialchars(implode(', ', json_decode($reviews[$task1['id']]['task_achievement_examples'], true))); ?></p>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        <?php if ($reviews[$task1['id']]['coherence_cohesion_explanation']): ?>
                                            <p><strong>Coherence and Cohesion:</strong> <?php echo htmlspecialchars($reviews[$task1['id']]['coherence_cohesion_explanation']); ?></p>
                                            <p><strong>Lỗi:</strong> <?php echo htmlspecialchars($errors_and_suggestions[$task1['id']]['coherence_cohesion']['errors']); ?></p>
                                            <p><strong>Gợi ý cải thiện:</strong> <?php echo htmlspecialchars($errors_and_suggestions[$task1['id']]['coherence_cohesion']['suggestions']); ?></p>
                                            <?php if ($reviews[$task1['id']]['coherence_cohesion_examples']): ?>
                                                <p><strong>Ví dụ từ bài viết:</strong> <?php echo htmlspecialchars(implode(', ', json_decode($reviews[$task1['id']]['coherence_cohesion_examples'], true))); ?></p>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        <?php if ($reviews[$task1['id']]['lexical_resource_explanation']): ?>
                                            <p><strong>Lexical Resource:</strong> <?php echo htmlspecialchars($reviews[$task1['id']]['lexical_resource_explanation']); ?></p>
                                            <p><strong>Lỗi:</strong> <?php echo htmlspecialchars($errors_and_suggestions[$task1['id']]['lexical_resource']['errors']); ?></p>
                                            <p><strong>Gợi ý cải thiện:</strong> <?php echo htmlspecialchars($errors_and_suggestions[$task1['id']]['lexical_resource']['suggestions']); ?></p>
                                            <?php if ($reviews[$task1['id']]['lexical_resource_examples']): ?>
                                                <p><strong>Ví dụ từ bài viết:</strong> <?php echo htmlspecialchars(implode(', ', json_decode($reviews[$task1['id']]['lexical_resource_examples'], true))); ?></p>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        <?php if ($reviews[$task1['id']]['grammatical_range_explanation']): ?>
                                            <p><strong>Grammatical Range and Accuracy:</strong> <?php echo htmlspecialchars($reviews[$task1['id']]['grammatical_range_explanation']); ?></p>
                                            <p><strong>Lỗi:</strong> <?php echo htmlspecialchars($errors_and_suggestions[$task1['id']]['grammatical_range']['errors']); ?></p>
                                            <p><strong>Gợi ý cải thiện:</strong> <?php echo htmlspecialchars($errors_and_suggestions[$task1['id']]['grammatical_range']['suggestions']); ?></p>
                                            <?php if ($reviews[$task1['id']]['grammatical_range_examples']): ?>
                                                <p><strong>Ví dụ từ bài viết:</strong> <?php echo htmlspecialchars(implode(', ', json_decode($reviews[$task1['id']]['grammatical_range_examples'], true))); ?></p>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info" id="scoringAlertTask1">
                            Bài viết Task 1 đang được chấm điểm bởi Gemini. Vui lòng chờ hoặc làm mới trang.
                            <div class="loading-spinner">
                                <i class="fas fa-spinner fa-spin"></i> Đang xử lý...
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

                <?php if ($task2): ?>
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Task 2: <?php echo htmlspecialchars($task2['question']); ?></h5>
                            <p><strong>Loại bài: </strong><?php echo htmlspecialchars($task2['category']); ?></p>
                            <p><strong>Thời gian làm bài: </strong><?php echo htmlspecialchars($task2['time_taken']); ?> phút</p>
                            <p><strong>Số từ: </strong><?php echo htmlspecialchars($task2['word_count']); ?></p>
                            <p><strong>Thời gian nộp: </strong><?php echo date('d/m/Y H:i', strtotime($task2['submission_time'])); ?></p>
                            <hr>
                            <?php if (!empty($task2['question_image'])): ?>
                                <div class="image-container">
                                    <img src="<?php echo htmlspecialchars($task2['question_image']); ?>" alt="Question Image" class="question-image" data-path="<?php echo htmlspecialchars($task2['question_image']); ?>">
                                    <p class="image-error" style="display: none;">Cảnh báo: Không tìm thấy file ảnh tại <?php echo htmlspecialchars($task2['question_image']); ?>. Đảm bảo file tồn tại trong thư mục IELTS Writing/backend/uploads/.</p>
                                </div>
                            <?php endif; ?>
                            <h6><strong>Nội dung bài làm:</strong></h6>
                            <p style="white-space: pre-wrap;"><?php echo htmlspecialchars($task2['response_text']); ?></p>
                        </div>
                    </div>

                    <?php if (isset($reviews[$task2['id']])): ?>
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="card-title">Kết quả chấm điểm Task 2</h5>
                                <table class="table table-bordered score-table">
                                    <thead>
                                        <tr>
                                            <th>Tiêu chí</th>
                                            <th>Điểm do AI chấm (Gemini)</th>
                                            <th>Điểm do giáo viên chấm</th>
                                            <th>Điểm cuối cùng</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Điểm tổng</td>
                                            <td><span class="badge badge-success score-badge"><?php echo number_format($reviews[$task2['id']]['ai_generated_score'], 1); ?></span></td>
                                            <td><span class="badge badge-info score-badge"><?php echo $reviews[$task2['id']]['human_score'] ? number_format($reviews[$task2['id']]['human_score'], 1) : 'Chưa có'; ?></span></td>
                                            <td><span class="badge badge-primary score-badge"><?php echo calculateFinalScore($reviews[$task2['id']]['ai_generated_score'], $reviews[$task2['id']]['human_score']); ?></span></td>
                                        </tr>
                                        <tr>
                                            <td>Task Response</td>
                                            <td><?php echo number_format($reviews[$task2['id']]['ai_task_achievement'], 1); ?></td>
                                            <td><?php echo $reviews[$task2['id']]['human_task_achievement'] ? number_format($reviews[$task2['id']]['human_task_achievement'], 1) : 'Chưa có'; ?></td>
                                            <td><?php echo calculateFinalScore($reviews[$task2['id']]['ai_task_achievement'], $reviews[$task2['id']]['human_task_achievement']); ?></td>
                                        </tr>
                                        <tr>
                                            <td>Coherence and Cohesion</td>
                                            <td><?php echo number_format($reviews[$task2['id']]['ai_coherence_cohesion'], 1); ?></td>
                                            <td><?php echo $reviews[$task2['id']]['human_coherence_cohesion'] ? number_format($reviews[$task2['id']]['human_coherence_cohesion'], 1) : 'Chưa có'; ?></td>
                                            <td><?php echo calculateFinalScore($reviews[$task2['id']]['ai_coherence_cohesion'], $reviews[$task2['id']]['human_coherence_cohesion']); ?></td>
                                        </tr>
                                        <tr>
                                            <td>Lexical Resource</td>
                                            <td><?php echo number_format($reviews[$task2['id']]['ai_lexical_resource'], 1); ?></td>
                                            <td><?php echo $reviews[$task2['id']]['human_lexical_resource'] ? number_format($reviews[$task2['id']]['human_lexical_resource'], 1) : 'Chưa có'; ?></td>
                                            <td><?php echo calculateFinalScore($reviews[$task2['id']]['ai_lexical_resource'], $reviews[$task2['id']]['human_lexical_resource']); ?></td>
                                        </tr>
                                        <tr>
                                            <td>Grammatical Range and Accuracy</td>
                                            <td><?php echo number_format($reviews[$task2['id']]['ai_grammatical_range'], 1); ?></td>
                                            <td><?php echo $reviews[$task2['id']]['human_grammatical_range'] ? number_format($reviews[$task2['id']]['human_grammatical_range'], 1) : 'Chưa có'; ?></td>
                                            <td><?php echo calculateFinalScore($reviews[$task2['id']]['ai_grammatical_range'], $reviews[$task2['id']]['human_grammatical_range']); ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                                <?php if ($reviews[$task2['id']]['ai_dependency_comments']): ?>
                                    <div class="ai-dependency-section">
                                        <h6><strong>Phân tích mức độ phụ thuộc vào AI từ Gemini:</strong></h6>
                                        <p><strong>Mức độ phụ thuộc:</strong> <?php echo htmlspecialchars($task2['ai_assistance_level']); ?></p>
                                        <p><strong>Điểm AI Detection:</strong> <?php echo number_format($task2['ai_detection_score'], 1); ?>%</p>
                                        <p><strong>Nhận xét:</strong> <?php echo htmlspecialchars($reviews[$task2['id']]['ai_dependency_comments']); ?></p>
                                        <p><strong>Cần xem xét thêm:</strong> <?php echo htmlspecialchars($task2['ai_detection_score'] > 50 ? 'Yes' : 'No'); ?></p>
                                    </div>
                                <?php endif; ?>
                                <?php if ($reviews[$task2['id']]['feedback']): ?>
                                    <div class="feedback-section">
                                        <h6><strong>Đánh giá tổng quan từ Gemini:</strong></h6>
                                        <p><?php echo htmlspecialchars($reviews[$task2['id']]['feedback']); ?></p>
                                    </div>
                                <?php endif; ?>
                                <?php if ($reviews[$task2['id']]['advice']): ?>
                                    <div class="feedback-section">
                                        <h6><strong>Lời khuyên từ Gemini:</strong></h6>
                                        <p><?php echo htmlspecialchars($reviews[$task2['id']]['advice']); ?></p>
                                    </div>
                                <?php endif; ?>
                                <?php if ($reviews[$task2['id']]['task_achievement_explanation'] || $reviews[$task2['id']]['coherence_cohesion_explanation'] || $reviews[$task2['id']]['lexical_resource_explanation'] || $reviews[$task2['id']]['grammatical_range_explanation']): ?>
                                    <div class="explanation-section">
                                        <h6><strong>Giải thích chi tiết điểm số (bằng tiếng Anh):</strong></h6>
                                        <?php 
                                        $task_key = isset($errors_and_suggestions[$task2['id']]['task_achievement']) && !empty($errors_and_suggestions[$task2['id']]['task_achievement']['errors']) ? 'task_achievement' : 'task_response';
                                        if ($reviews[$task2['id']]['task_achievement_explanation']): ?>
                                            <p><strong>Task Achievement/Task Response:</strong> <?php echo htmlspecialchars($reviews[$task2['id']]['task_achievement_explanation']); ?></p>
                                            <p><strong>Lỗi:</strong> <?php echo htmlspecialchars($errors_and_suggestions[$task2['id']][$task_key]['errors']); ?></p>
                                            <p><strong>Gợi ý cải thiện:</strong> <?php echo htmlspecialchars($errors_and_suggestions[$task2['id']][$task_key]['suggestions']); ?></p>
                                            <?php if ($reviews[$task2['id']]['task_achievement_examples']): ?>
                                                <p><strong>Ví dụ từ bài viết:</strong> <?php echo htmlspecialchars(implode(', ', json_decode($reviews[$task2['id']]['task_achievement_examples'], true))); ?></p>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        <?php if ($reviews[$task2['id']]['coherence_cohesion_explanation']): ?>
                                            <p><strong>Coherence and Cohesion:</strong> <?php echo htmlspecialchars($reviews[$task2['id']]['coherence_cohesion_explanation']); ?></p>
                                            <p><strong>Lỗi:</strong> <?php echo htmlspecialchars($errors_and_suggestions[$task2['id']]['coherence_cohesion']['errors']); ?></p>
                                            <p><strong>Gợi ý cải thiện:</strong> <?php echo htmlspecialchars($errors_and_suggestions[$task2['id']]['coherence_cohesion']['suggestions']); ?></p>
                                            <?php if ($reviews[$task2['id']]['coherence_cohesion_examples']): ?>
                                                <p><strong>Ví dụ từ bài viết:</strong> <?php echo htmlspecialchars(implode(', ', json_decode($reviews[$task2['id']]['coherence_cohesion_examples'], true))); ?></p>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        <?php if ($reviews[$task2['id']]['lexical_resource_explanation']): ?>
                                            <p><strong>Lexical Resource:</strong> <?php echo htmlspecialchars($reviews[$task2['id']]['lexical_resource_explanation']); ?></p>
                                            <p><strong>Lỗi:</strong> <?php echo htmlspecialchars($errors_and_suggestions[$task2['id']]['lexical_resource']['errors']); ?></p>
                                            <p><strong>Gợi ý cải thiện:</strong> <?php echo htmlspecialchars($errors_and_suggestions[$task2['id']]['lexical_resource']['suggestions']); ?></p>
                                            <?php if ($reviews[$task2['id']]['lexical_resource_examples']): ?>
                                                <p><strong>Ví dụ từ bài viết:</strong> <?php echo htmlspecialchars(implode(', ', json_decode($reviews[$task2['id']]['lexical_resource_examples'], true))); ?></p>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        <?php if ($reviews[$task2['id']]['grammatical_range_explanation']): ?>
                                            <p><strong>Grammatical Range and Accuracy:</strong> <?php echo htmlspecialchars($reviews[$task2['id']]['grammatical_range_explanation']); ?></p>
                                            <p><strong>Lỗi:</strong> <?php echo htmlspecialchars($errors_and_suggestions[$task2['id']]['grammatical_range']['errors']); ?></p>
                                            <p><strong>Gợi ý cải thiện:</strong> <?php echo htmlspecialchars($errors_and_suggestions[$task2['id']]['grammatical_range']['suggestions']); ?></p>
                                            <?php if ($reviews[$task2['id']]['grammatical_range_examples']): ?>
                                                <p><strong>Ví dụ từ bài viết:</strong> <?php echo htmlspecialchars(implode(', ', json_decode($reviews[$task2['id']]['grammatical_range_examples'], true))); ?></p>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info" id="scoringAlertTask2">
                            Bài viết Task 2 đang được chấm điểm bởi Gemini. Vui lòng chờ hoặc làm mới trang.
                            <div class="loading-spinner">
                                <i class="fas fa-spinner fa-spin"></i> Đang xử lý...
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

                <!-- Progress Section -->
                <?php if (!empty($progressData)): ?>
                    <div class="card mb-4">
                        <div class="card-body progress-section">
                            <h5 class="card-title">Tiến bộ của bạn (Full Test)</h5>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Loại</th>
                                        <th>Nhiệm vụ</th>
                                        <th>Task Achievement/Response</th>
                                        <th>Coherence</th>
                                        <th>Lexical</th>
                                        <th>Grammar</th>
                                        <th>Overall</th>
                                        <th>Thời gian</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($progressData as $row): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($row['task_type']); ?></td>
                                            <td><?php echo htmlspecialchars($row['task_category']); ?></td>
                                            <td><?php echo number_format($row['task_achievement_response'], 1); ?></td>
                                            <td><?php echo number_format($row['coherence_cohesion'], 1); ?></td>
                                            <td><?php echo number_format($row['lexical_resource'], 1); ?></td>
                                            <td><?php echo number_format($row['grammatical_range_accuracy'], 1); ?></td>
                                            <td><?php echo number_format($row['overall_band'], 1); ?></td>
                                            <td><?php echo date('d/m/Y H:i', strtotime($row['completed_at'])); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            <canvas id="progressChart"></canvas>
                            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                            <script>
                                const ctx = document.getElementById('progressChart').getContext('2d');
                                new Chart(ctx, {
                                    type: 'line',
                                    data: {
                                        labels: <?php echo json_encode($submissionTimes); ?>,
                                        datasets: [{
                                            label: 'Overall Band',
                                            data: <?php echo json_encode($overallScores); ?>,
                                            borderColor: '#007bff',
                                            backgroundColor: 'rgba(0, 123, 255, 0.1)',
                                            fill: true
                                        }]
                                    },
                                    options: {
                                        responsive: true,
                                        scales: {
                                            y: { beginAtZero: true, max: 9, title: { display: true, text: 'Band Score' } },
                                            x: { title: { display: true, text: 'Submission Time' } }
                                        },
                                        plugins: { title: { display: true, text: 'IELTS Writing Progress (Full Test)' } }
                                    }
                                });
                            </script>
                            <?php if ($progressFeedback): ?>
                                <div class="progress-feedback-section">
                                    <?php echo $progressFeedback; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container text-center">
            <p>&copy; <?php echo date('Y'); ?> IELTS Writing Pro. All rights reserved.</p>
            <p>
                <a href="about.php" class="text-white">About Us</a> | 
                <a href="contact.php" class="text-white">Contact</a> | 
                <a href="privacy.php" class="text-white">Privacy Policy</a>
            </p>
        </div>
    </footer>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            // Check if question images exist
            $('.question-image').each(function() {
                var img = $(this);
                var path = img.data('path');
                $.ajax({
                    url: path,
                    type: 'HEAD',
                    success: function() {
                        img.show();
                    },
                    error: function() {
                        img.hide();
                        img.siblings('.image-error').show();
                    }
                });
            });

            // Handle dropdown submenu on hover for large screens
            $('.dropdown-submenu').hover(function() {
                if ($(window).width() > 991) {
                    $(this).find('.dropdown-menu').stop(true, true).fadeIn(200);
                }
            }, function() {
                if ($(window).width() > 991) {
                    $(this).find('.dropdown-menu').stop(true, true).fadeOut(200);
                }
            });

            // Handle click for dropdown submenu on mobile
            $('.dropdown-submenu > a').click(function(e) {
                if ($(window).width() <= 991) {
                    e.preventDefault();
                    $(this).next('.dropdown-menu').slideToggle(200);
                }
            });

            // Auto-refresh for scoring status
            if ($('#scoringAlertTask1').length || $('#scoringAlertTask2').length) {
                $('.loading-spinner').show();
                setTimeout(function() {
                    location.reload();
                }, 30000); // Refresh after 30 seconds if scoring is pending
            }
        });
    </script>
</body>
</html>