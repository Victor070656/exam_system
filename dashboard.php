<?php
session_start();
require_once 'config/config.php';

$examManager = new ExamManager($conn);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Examination System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <div class="container mt-4">
        <nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">Exam System</a>
                <div class="navbar-nav ms-auto">
                    <span class="nav-item nav-link">Welcome, <?php echo htmlspecialchars($_SESSION['email']); ?></span>
                    <a class="nav-link" href="logout.php">Logout</a>
                </div>
            </div>
        </nav>

        <?php if ($_SESSION['role'] === 'teacher'): ?>
            <!-- Teacher Dashboard -->
            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Create New Exam</h5>
                            <form action="create_exam.php" method="POST">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Exam Title</label>
                                    <input type="text" class="form-control" id="title" name="title" required>
                                </div>
                                <div class="mb-3">
                                    <label for="duration" class="form-label">Duration (minutes)</label>
                                    <input type="number" class="form-control" id="duration" name="duration" required>
                                </div>
                                <div class="mb-3">
                                    <label for="start_time" class="form-label">Start Time</label>
                                    <input type="datetime-local" class="form-control" id="start_time" name="start_time" required>
                                </div>
                                <div class="mb-3">
                                    <label for="end_time" class="form-label">End Time</label>
                                    <input type="datetime-local" class="form-control" id="end_time" name="end_time" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Create Exam</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Manage Exams</h5>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Start Time</th>
                                        <th>End Time</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $exams = $examManager->getTeacherExams($_SESSION['user_id']);
                                    foreach ($exams as $exam):
                                    ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($exam['title']); ?></td>
                                            <td><?php echo $exam['start_time']; ?></td>
                                            <td><?php echo $exam['end_time']; ?></td>
                                            <td>
                                                <a href="edit_exam.php?id=<?php echo $exam['id']; ?>"
                                                    class="btn btn-sm btn-primary">Edit</a>
                                                <a href="view_results.php?id=<?php echo $exam['id']; ?>"
                                                    class="btn btn-sm btn-info">Results</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        <?php else: ?>
            <!-- Student Dashboard -->
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Available Exams</h5>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Duration</th>
                                        <th>Start Time</th>
                                        <th>End Time</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $exams = $examManager->getAvailableExams($_SESSION['user_id']);
                                    foreach ($exams as $exam):
                                        $status = $examManager->isExamAvailable($exam['id'], $_SESSION['user_id']);
                                    ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($exam['title']); ?></td>
                                            <td><?php echo $exam['duration']; ?> mins</td>
                                            <td><?php echo $exam['start_time']; ?></td>
                                            <td><?php echo $exam['end_time']; ?></td>
                                            <td><?php echo $status['status']; ?></td>
                                            <td>
                                                <?php if ($status['status'] === 'available'): ?>
                                                    <a href="start_exam.php?id=<?php echo $exam['id']; ?>"
                                                        class="btn btn-sm btn-primary">Start Exam</a>
                                                <?php elseif ($status['status'] === 'completed'): ?>
                                                    <a href="view_result.php?id=<?php echo $exam['id']; ?>"
                                                        class="btn btn-sm btn-info">View Result</a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">My Results</h5>
                            <div class="list-group">
                                <?php
                                $results = $resultProcessor->getResults($_SESSION['user_id']);
                                foreach ($results as $result):
                                ?>
                                    <a href="view_result.php?id=<?php echo $result['exam_id']; ?>"
                                        class="list-group-item list-group-item-action">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1"><?php echo htmlspecialchars($result['title']); ?></h6>
                                            <small><?php echo $result['submitted_at']; ?></small>
                                        </div>
                                        <p class="mb-1">Score: <?php echo $result['score']; ?>%</p>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>