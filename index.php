<?php
// Include database connection and necessary classes
require_once 'config/config.php'; 

session_start();
if(!isset($_SESSION["user_id"])){
    header("location: login.php");
}else{
    $user_id = $_SESSION["user_id"];
}
// Initialize classes
$examManager = new ExamManager($conn);
$resultProcessor = new ResultProcessor($conn);

// Fetch available exams
$availableExams = $examManager->getAvailableExams($user_id);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam System - Home</title>
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
                        <a class="nav-link" href="exams.php">Exams</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="results.php">Results</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="analytics.php">Analytics</a>
                    </li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">Logout</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">Login</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="bg-primary text-white text-center py-5">
        <h1>Welcome to the Exam System</h1>
        <p class="lead">Manage and take your exams with ease.</p>
    </header>

    <!-- Available Exams Section -->
    <div class="container mt-5">
        <h2>Available Exams</h2>
        <div class="row">
            <?php
            // Display available exams if any
            if (!empty($availableExams)):
                foreach ($availableExams as $exam):
            ?>
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $exam['title']; ?></h5>
                                <p class="card-text">Duration: <?php echo $exam['duration']; ?> minutes</p>
                                <p class="card-text">Start Time: <?php echo date('Y-m-d H:i', strtotime($exam['start_time'])); ?></p>
                                <p class="card-text">End Time: <?php echo date('Y-m-d H:i', strtotime($exam['end_time'])); ?></p>
                                <a href="take_exam.php?exam_id=<?php echo $exam['id']; ?>" class="btn btn-primary">Take Exam</a>
                            </div>
                        </div>
                    </div>
            <?php
                endforeach;
            else:
                echo "<p>No exams available at the moment.</p>";
            endif;
            ?>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-light text-center py-3">
        <p>&copy; 2024 Exam System. All rights reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>