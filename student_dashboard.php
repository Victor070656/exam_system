<?php
// Include necessary files and classes
require_once 'config/config.php';

session_start();

// Initialize classes
$examManager = new ExamManager($conn);
$resultProcessor = new ResultProcessor($conn);

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];

// Fetch available, completed, and upcoming exams
$availableExams = $examManager->getAvailableExams($userId);
$completedExams = $resultProcessor->getResults($userId);
$upcomingExams = $examManager->getUpcomingExams($userId);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
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
                        <a class="nav-link" href="student_dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="view_result.php">Results</a>
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
        <p class="lead">Manage your exams, view your results, and more!</p>
    </header>

    <!-- Available Exams -->
    <div class="container mt-5">
        <h2>Available Exams</h2>
        <div class="row">
            <?php if (!empty($availableExams)): ?>
                <?php foreach ($availableExams as $exam): ?>
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $exam['title']; ?></h5>
                                <p class="card-text">Start Time: <?php echo date('Y-m-d H:i', strtotime($exam['start_time'])); ?></p>
                                <p class="card-text">End Time: <?php echo date('Y-m-d H:i', strtotime($exam['end_time'])); ?></p>
                                <a href="take_exam.php?exam_id=<?php echo $exam['id']; ?>" class="btn btn-primary">Start Exam</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No available exams at the moment.</p>
            <?php endif; ?>
        </div>

        <h2 class="mt-5">Upcoming Exams</h2>
        <div class="row">
            <?php if (!empty($upcomingExams)): ?>
                <?php foreach ($upcomingExams as $exam): ?>
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $exam['title']; ?></h5>
                                <p class="card-text">Start Time: <?php echo date('Y-m-d H:i', strtotime($exam['start_time'])); ?></p>
                                <p class="card-text">End Time: <?php echo date('Y-m-d H:i', strtotime($exam['end_time'])); ?></p>
                                <a href="exam_details.php?exam_id=<?php echo $exam['id']; ?>" class="btn btn-info">View Details</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No upcoming exams at the moment.</p>
            <?php endif; ?>
        </div>

        <h2 class="mt-5">Completed Exams</h2>
        <div class="row">
            <?php if (!empty($completedExams)): ?>
                <?php foreach ($completedExams as $exam): ?>
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $exam['title']; ?></h5>
                                <p class="card-text">Score: <?php echo $exam['score']; ?></p>
                                <a href="view_results.php?exam_id=<?php echo $exam['exam_id']; ?>" class="btn btn-info">View Results</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>You haven't completed any exams yet.</p>
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