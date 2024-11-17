<?php
session_start();
include 'config/config.php';  // Assuming the ExamManager class is included

$examManager = new ExamManager($conn);

// If editing an existing exam, fetch the exam details
$exam = null;
if (isset($_GET['exam_id'])) {
    $examId = $_GET['exam_id'];
    $exam = $examManager->getExam($examId); // Fetch exam details using the getExam method
}

// Handle form submission for creating or updating the exam
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $duration = $_POST['duration'];
    $totalMarks = $_POST['total_marks'];
    $startTime = $_POST['start_time'];
    $endTime = $_POST['end_time'];
    $passingScore = $_POST['passing_score'];

    if ($exam) {
        // Update the existing exam
        $examManager->updateExam($examId, $title, $duration, $totalMarks, $startTime, $endTime, $passingScore);
    } else {
        // Create a new exam
        $examManager->createExam($title, $duration, $totalMarks, $startTime, $endTime, $passingScore);
    }
    header('Location: exams.php');  // Redirect to the exams list page after submission
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create/Edit Exam</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <div class="container mt-5">
        <h2><?= $exam ? 'Edit Exam' : 'Create New Exam' ?></h2>
        <form method="POST">
            <div class="mb-3">
                <label for="title" class="form-label">Exam Title</label>
                <input type="text" class="form-control" id="title" name="title" value="<?= $exam ? htmlspecialchars($exam['title']) : '' ?>" required>
            </div>
            <div class="mb-3">
                <label for="duration" class="form-label">Duration (in minutes)</label>
                <input type="number" class="form-control" id="duration" name="duration" value="<?= $exam ? htmlspecialchars($exam['duration']) : '' ?>" required>
            </div>
            <div class="mb-3">
                <label for="total_marks" class="form-label">Total Marks</label>
                <input type="number" class="form-control" id="total_marks" name="total_marks" value="<?= $exam ? htmlspecialchars($exam['total_marks']) : '' ?>" required>
            </div>
            <div class="mb-3">
                <label for="start_time" class="form-label">Start Time</label>
                <input type="datetime-local" class="form-control" id="start_time" name="start_time" value="<?= $exam ? htmlspecialchars($exam['start_time']) : '' ?>" required>
            </div>
            <div class="mb-3">
                <label for="end_time" class="form-label">End Time</label>
                <input type="datetime-local" class="form-control" id="end_time" name="end_time" value="<?= $exam ? htmlspecialchars($exam['end_time']) : '' ?>" required>
            </div>
            <div class="mb-3">
                <label for="passing_score" class="form-label">Passing Score</label>
                <input type="number" class="form-control" id="passing_score" name="passing_score" value="<?= $exam ? htmlspecialchars($exam['passing_score']) : '' ?>" required>
            </div>
            <button type="submit" class="btn btn-primary"><?= $exam ? 'Update Exam' : 'Create Exam' ?></button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>