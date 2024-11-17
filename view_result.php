<?php
// Include necessary files
require_once 'config/config.php';

// Initialize ResultProcessor
$resultProcessor = new ResultProcessor($conn);

// Get user_id and exam_id from the URL
$userId = 1; // Assuming user is logged in and has ID = 1
$examId = $_GET['exam_id'] ?? null;

// Fetch results for the user and the specific exam
$results = $resultProcessor->getResults($userId);
$resultDetails = null;

foreach ($results as $result) {
    if ($result['exam_id'] == $examId) {
        $resultDetails = $result;
        break;
    }
}

// If no result found, redirect to the exam list page
if (!$resultDetails) {
    header('Location: exam_list.php');
    exit;
}

// Decode the stored answers
$answers = json_decode($resultDetails['answers'], true);

// Fetch questions for the exam
$questionManager = new QuestionManager($conn);
$questions = $questionManager->getQuestionsForExam($examId);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Result - <?= htmlspecialchars($resultDetails['title']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <div class="container mt-5">
        <h1>Exam Result: <?= htmlspecialchars($resultDetails['title']) ?></h1>

        <h3>Your Score: <?= $resultDetails['score'] ?> / <?= count($questions) ?></h3>
        <h5>Passed: <?= $resultDetails['score'] >= $resultDetails['passing_score'] ? 'Yes' : 'No' ?></h5>

        <h4>Questions and Answers</h4>
        <ul class="list-group">
            <?php foreach ($questions as $index => $question): ?>
                <li class="list-group-item">
                    <strong><?= $index + 1 ?>. <?= htmlspecialchars($question['question_text']) ?></strong>
                    <div>
                        <strong>Your Answer: </strong>
                        <?= htmlspecialchars($question['options'][$answers[$index]]) ?>
                    </div>
                    <div>
                        <strong>Correct Answer: </strong>
                        <?= htmlspecialchars($question['options'][$question['correct_answer']]) ?>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>

        <a href="exam_list.php" class="btn btn-secondary mt-4">Back to Exams</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>