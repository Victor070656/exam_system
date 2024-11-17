<?php
// Include necessary files
require_once 'config/config.php';

// Initialize ResultProcessor
$resultProcessor = new ResultProcessor($conn);

// Get user_id and exam_id from the URL
$userId = 1; // Assuming user is logged in and has ID = 1
$examId = $_GET['exam_id'] ?? null;

// Fetch certificate details for the user and the specific exam
$certificateDetails = $resultProcessor->generateCertificate($userId, $examId);

// If no certificate found (i.e., user didn't pass), redirect to the exam list page
if (!$certificateDetails) {
    header('Location: exam_list.php');
    exit;
}

// Get user and exam details
$userEmail = $certificateDetails['email'];
$examTitle = $certificateDetails['title'];
$score = $certificateDetails['score'];
$passingScore = $certificateDetails['passing_score'];
$certificateNumber = 'CERT-' . strtoupper(uniqid()); // Unique certificate number

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate - <?= htmlspecialchars($examTitle) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .certificate-container {
            margin-top: 50px;
            text-align: center;
            border: 2px solid #000;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
        }

        .certificate-header {
            font-size: 2rem;
            font-weight: bold;
        }

        .certificate-body {
            font-size: 1.25rem;
            margin: 20px 0;
        }

        .certificate-footer {
            font-size: 1rem;
            margin-top: 30px;
        }

        .btn-download {
            margin-top: 20px;
        }
    </style>
</head>

<body>

    <div class="container certificate-container">
        <div class="certificate-header">
            Certificate of Completion
        </div>
        <div class="certificate-body">
            <p>Congratulations, <strong><?= htmlspecialchars($certificateDetails['email']) ?></strong>!</p>
            <p>You have successfully completed the exam titled: <strong><?= htmlspecialchars($examTitle) ?></strong>.</p>
            <p>Your Score: <strong><?= $score ?> / <?= $certificateDetails['total_marks'] ?></strong></p>
            <p>Passing Score: <strong><?= $passingScore ?> / <?= $certificateDetails['total_marks'] ?></strong></p>
            <p>Certificate Number: <strong><?= $certificateNumber ?></strong></p>
        </div>
        <div class="certificate-footer">
            <p>Issued by: <strong>Exam System</strong></p>
            <p>Date of Issue: <strong><?= date('F j, Y') ?></strong></p>
        </div>
        <a href="download_certificate.php?certificate_id=<?= urlencode($certificateNumber) ?>" class="btn btn-primary btn-download">Download Certificate</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>