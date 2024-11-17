<?php
// Include necessary files
require_once 'config/config.php';

// Initialize ExamManager
$examManager = new ExamManager($conn);

// Fetch exam details using the exam_id from URL
$examId = $_GET['exam_id'] ?? null;
$exam = $examManager->getExam($examId);
$examDetails = $exam[0] ?? null; // Assuming getExam returns an array of one exam's details

// If no exam found, redirect to exam list page
if (!$examDetails) {
    header('Location: exam_list.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Instructions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEJaoJvY13HnUdi2j67UtvXsZXdFlcx2x3pVu6c/2pNSkx5TkJmyA1h5u/1nA" crossorigin="anonymous">
</head>

<body>

    <div class="container mt-5">
        <h1 class="mb-4">Exam Instructions</h1>

        <div class="card">
            <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($examDetails['title']) ?></h5>
                <p><strong>Duration:</strong> <?= htmlspecialchars($examDetails['duration']) ?> minutes</p>
                <p><strong>Total Marks:</strong> <?= htmlspecialchars($examDetails['total_marks']) ?></p>
                <p><strong>Passing Score:</strong> <?= htmlspecialchars($examDetails['passing_score']) ?>%</p>
                <p><strong>Start Time:</strong> <?= htmlspecialchars($examDetails['start_time']) ?></p>
                <p><strong>End Time:</strong> <?= htmlspecialchars($examDetails['end_time']) ?></p>

                <h6 class="mt-4">Instructions:</h6>
                <ul>
                    <li>Make sure to complete the exam within the allotted time.</li>
                    <li>There are <?= htmlspecialchars($examDetails['total_marks']) ?> total marks.</li>
                    <li>Please review your answers before submitting the exam.</li>
                    <li>Once you start the exam, you cannot pause it.</li>
                </ul>

                <a href="take_exam.php?exam_id=<?= htmlspecialchars($examDetails['id']) ?>" class="btn btn-primary">Start Exam</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pzjw8f+ua7Kw1TIq0gVR8JIT0rC5D5dgy7AOzOMk5RfnDfe8z5Vohb3ke6+Zj+4L" crossorigin="anonymous"></script>
</body>

</html>