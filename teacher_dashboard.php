<?php
// Include necessary files and classes
require_once 'config/config.php';

session_start();

// Ensure teacher is logged in (check user role)
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header("Location: login.php");
    exit();
}

$examManager = new ExamManager($conn);
// $questionManager = new QuestionManager($conn);

// Fetch all exams for teacher
$exams = $examManager->getAllExams();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Exam System</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="teacher_dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Dashboard Header -->
    <header class="bg-primary text-white text-center py-5">
        <h1>Welcome to Your Dashboard</h1>
        <p class="lead">Manage your exams and questions.</p>
    </header>

    <!-- Exam Management -->
    <div class="container mt-5">
        <h2>Manage Exams</h2>
        <div class="row">
            <?php if (!empty($exams)): ?>
                <?php foreach ($exams as $exam): ?>
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $exam['title']; ?></h5>
                                <a href="edit_exam.php?exam_id=<?php echo $exam['id']; ?>" class="btn btn-warning">Edit Exam</a>
                                <a href="delete_exam.php?exam_id=<?php echo $exam['id']; ?>" class="btn btn-danger">Delete Exam</a>
                                <a href="add_questions.php?exam_id=<?php echo $exam['id']; ?>" class="btn btn-primary">Add Questions</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No exams available to manage.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-light text-center py-3">
        <p>&copy; 2024 Exam System. All rights reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>