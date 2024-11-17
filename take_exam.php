<?php
// Include necessary files
require_once 'config/config.php';

// Initialize managers
$examManager = new ExamManager($conn);
$questionManager = new QuestionManager($conn);

// Fetch exam details using exam_id from URL
$examId = $_GET['exam_id'] ?? null;
$exam = $examManager->getExam($examId);
$examDetails = $exam[0] ?? null; // Assuming getExam returns an array of one exam's details

// Fetch questions for the exam
$questions = $questionManager->getQuestionsForExam($examId);

// If no exam or questions found, redirect to exam list page
if (!$examDetails || !$questions) {
    header('Location: exam_list.php');
    exit;
}

// Handle form submission to save the answers
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = 1; // Assume user is logged in and has ID = 1
    $answers = $_POST['answers'] ?? [];
    $resultProcessor = new ResultProcessor($conn);
    $resultProcessor->submitExam($userId, $examId, $answers);
    header("Location: exam_results.php?exam_id=$examId");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Take Exam - <?= htmlspecialchars($examDetails['title']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEJaoJvY13HnUdi2j67UtvXsZXdFlcx2x3pVu6c/2pNSkx5TkJmyA1h5u/1nA" crossorigin="anonymous">
</head>

<body>

    <div class="container mt-5">
        <h1 class="mb-4"><?= htmlspecialchars($examDetails['title']) ?> - Exam</h1>

        <!-- <form action="take_exam.php?exam_id=<?= htmlspecialchars($examDetails['id']) ?>" method="POST"> -->
        <form method="POST">
            <div id="exam-questions">
                <?php foreach ($questions as $index => $question): ?>
                    <div class="mb-4">
                        <h5><?= $index + 1 ?>. <?= htmlspecialchars($question['question_text']) ?></h5>
                        <?php
                        $options = json_decode($question['options'], true);
                        foreach ($options as $key => $option):
                        ?>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="answers[<?= $index ?>]" value="<?= $key ?>" id="q<?= $index ?>_option<?= $key ?>">
                                <label class="form-check-label" for="q<?= $index ?>_option<?= $key ?>">
                                    <?= htmlspecialchars($option) ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <button type="submit" class="btn btn-primary">Submit Exam</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pzjw8f+ua7Kw1TIq0gVR8JIT0rC5D5dgy7AOzOMk5RfnDfe8z5Vohb3ke6+Zj+4L" crossorigin="anonymous"></script>
</body>

</html>