<?php
session_start();
include 'config/config.php'; // Assuming the QuestionManager class is included

$questionManager = new QuestionManager($conn);

// If editing an existing question, fetch the question details
$question = null;
if (isset($_GET['question_id'])) {
    $questionId = $_GET['question_id'];
    $question = $questionManager->getQuestion($questionId); // Fetch question details using the getQuestion method
}

// Handle form submission for adding or editing the question
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $examId = $_POST['exam_id'];
    $questionText = $_POST['question_text'];
    $options = $_POST['options'];  // This will be an array of options
    $correctAnswer = $_POST['correct_answer'];

    if ($question) {
        // Update the existing question
        $questionManager->updateQuestion($questionId, $questionText, $options, $correctAnswer);
    } else {
        // Add a new question to the exam
        $questionManager->addQuestion($examId, $questionText, $options, $correctAnswer);
    }
    header('Location: exam_details.php?exam_id=' . $examId);  // Redirect to the exam details page after submission
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add/Edit Question</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <div class="container mt-5">
        <h2><?= $question ? 'Edit Question' : 'Add New Question' ?></h2>
        <form method="POST">
            <div class="mb-3">
                <label for="exam_id" class="form-label">Exam</label>
                <select class="form-select" id="exam_id" name="exam_id" required>
                    <!-- Populate exams here -->
                    <option value="1" <?= $question && $question['exam_id'] == 1 ? 'selected' : '' ?>>Exam 1</option>
                    <option value="2" <?= $question && $question['exam_id'] == 2 ? 'selected' : '' ?>>Exam 2</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="question_text" class="form-label">Question Text</label>
                <textarea class="form-control" id="question_text" name="question_text" required><?= $question ? htmlspecialchars($question['question_text']) : '' ?></textarea>
            </div>
            <div class="mb-3">
                <label for="options" class="form-label">Answer Options (comma separated)</label>
                <input type="text" class="form-control" id="options" name="options" value="<?= $question ? htmlspecialchars(implode(',', json_decode($question['options']))) : '' ?>" required>
            </div>
            <div class="mb-3">
                <label for="correct_answer" class="form-label">Correct Answer</label>
                <input type="text" class="form-control" id="correct_answer" name="correct_answer" value="<?= $question ? htmlspecialchars($question['correct_answer']) : '' ?>" required>
            </div>
            <button type="submit" class="btn btn-primary"><?= $question ? 'Update Question' : 'Add Question' ?></button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>