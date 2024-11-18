<?php
session_start();
require_once 'config/config.php';

// Check if the user is authenticated
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Instantiate the ExamManager
$examManager = new ExamManager($conn);
$userId = $_SESSION['user_id'];

// Fetch exams for the logged-in user
$exams = $examManager->getAvailableExams($userId);
$examsUp = $examManager->getUpcomingExams($userId);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Exams</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h2 class="mb-4">Upcoming Exams</h2>
        <?php if (!empty($examsUp)) : ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Title</th>
                            <th>Start Time</th>
                            <th>End Time</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($examsUp as $exam) : ?>
                            <tr>
                                <td><?= htmlspecialchars($exam['title']) ?></td>
                                <td><?= htmlspecialchars($exam['start_time']) ?></td>
                                <td><?= htmlspecialchars($exam['end_time']) ?></td>
                                <td>
                                    <?php
                                    // Display the status based on availability
                                    switch ($exam['status']) {
                                        case 'upcoming':
                                            echo '<span class="badge bg-warning">Upcoming</span>';
                                            break;
                                        case 'available':
                                            echo '<span class="badge bg-success">Available</span>';
                                            break;
                                        case 'completed':
                                            echo '<span class="badge bg-primary">Completed</span>';
                                            break;
                                        case 'expired':
                                            echo '<span class="badge bg-danger">Expired</span>';
                                            break;
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php if ($exam['status'] === 'available') : ?>
                                        <a href="exam_start.php?exam_id=<?= $exam['id'] ?>" class="btn btn-primary btn-sm">Start Exam</a>
                                    <?php elseif ($exam['status'] === 'completed') : ?>
                                        <a href="view_results.php?exam_id=<?= $exam['id'] ?>" class="btn btn-info btn-sm">View Results</a>
                                    <?php else : ?>
                                        <span class="text-muted">No Action</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else : ?>
            <div class="alert alert-info">No exams available at the moment.</div>
        <?php endif; ?>
    </div>
    <div class="container mt-5">
        <h2 class="mb-4">Available Exams</h2>
        <?php if (!empty($exams)) : ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Title</th>
                            <th>Start Time</th>
                            <th>End Time</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($exams as $exam) : ?>
                            <tr>
                                <td><?= htmlspecialchars($exam['title']) ?></td>
                                <td><?= htmlspecialchars($exam['start_time']) ?></td>
                                <td><?= htmlspecialchars($exam['end_time']) ?></td>
                                <td>
                                    <?php
                                    // Display the status based on availability
                                    switch ($exam['status']) {
                                        case 'upcoming':
                                            echo '<span class="badge bg-warning">Upcoming</span>';
                                            break;
                                        case 'available':
                                            echo '<span class="badge bg-success">Available</span>';
                                            break;
                                        case 'completed':
                                            echo '<span class="badge bg-primary">Completed</span>';
                                            break;
                                        case 'expired':
                                            echo '<span class="badge bg-danger">Expired</span>';
                                            break;
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php if ($exam['status'] === 'available') : ?>
                                        <a href="exam_start.php?exam_id=<?= $exam['id'] ?>" class="btn btn-primary btn-sm">Start Exam</a>
                                    <?php elseif ($exam['status'] === 'completed') : ?>
                                        <a href="view_results.php?exam_id=<?= $exam['id'] ?>" class="btn btn-info btn-sm">View Results</a>
                                    <?php else : ?>
                                        <span class="text-muted">No Action</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else : ?>
            <div class="alert alert-info">No exams available at the moment.</div>
        <?php endif; ?>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>

</html>