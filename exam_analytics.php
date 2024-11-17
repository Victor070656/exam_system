<?php
// Include necessary files
require_once 'config/config.php';

// Initialize ResultProcessor
$resultProcessor = new ResultProcessor($conn);
$examManager = new ExamManager($conn);

// Get the exam_id from the URL
$examId = $_GET['exam_id'] ?? null;

// Fetch exam details
$exam = $examManager->getExam($examId);
$examDetails = $exam[0] ?? null; // Assuming getExam returns an array with one exam

// If no exam found, redirect to the exam list page
if (!$examDetails) {
    header('Location: exam_list.php');
    exit;
}

// Fetch analytics for the exam
$analytics = $resultProcessor->getAnalytics($examId);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Analytics - <?= htmlspecialchars($examDetails['title']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <div class="container mt-5">
        <h1>Exam Analytics: <?= htmlspecialchars($examDetails['title']) ?></h1>

        <h3>Total Students: <?= $analytics['total_students'] ?></h3>
        <h4>Average Score: <?= round($analytics['average_score'], 2) ?> / <?= $examDetails['total_marks'] ?></h4>
        <h4>Lowest Score: <?= $analytics['lowest_score'] ?> / <?= $examDetails['total_marks'] ?></h4>
        <h4>Highest Score: <?= $analytics['highest_score'] ?> / <?= $examDetails['total_marks'] ?></h4>
        <h4>Pass Rate: <?= round(($analytics['passed_count'] / $analytics['total_students']) * 100, 2) ?>%</h4>

        <a href="exam_list.php" class="btn btn-secondary mt-4">Back to Exams</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>