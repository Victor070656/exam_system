<?php
require_once './vendor/tecnickcom/tcpdf/tcpdf.php'; // Include TCPDF library

// Include necessary files
require_once 'ResultProcessor.php';
require_once 'Auth.php'; // Assuming user is logged in

// Initialize ResultProcessor
$resultProcessor = new ResultProcessor($conn);

// Get certificate ID from the URL
$certificateNumber = $_GET['certificate_id'] ?? null;

// Fetch certificate details for the given certificate number
// (You may need to query your database here to get the actual certificate data based on the ID)
$certificateDetails = $resultProcessor->getCertificateDetailsByNumber($certificateNumber);

// If no certificate found, redirect to the exam list page
if (!$certificateDetails) {
    header('Location: exam_list.php');
    exit;
}

// Create new PDF document
$pdf = new TCPDF();
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 12);

// Add certificate title
$pdf->SetFont('helvetica', 'B', 20);
$pdf->Cell(0, 15, 'Certificate of Completion', 0, 1, 'C');

// Add exam details
$pdf->SetFont('helvetica', '', 14);
$pdf->Ln(10);
$pdf->Cell(0, 10, 'Congratulations, ' . htmlspecialchars($certificateDetails['email']), 0, 1, 'C');
$pdf->Cell(0, 10, 'You have successfully completed the exam: ' . htmlspecialchars($certificateDetails['title']), 0, 1, 'C');
$pdf->Cell(0, 10, 'Score: ' . $certificateDetails['score'] . ' / ' . $certificateDetails['total_marks'], 0, 1, 'C');
$pdf->Cell(0, 10, 'Passing Score: ' . $certificateDetails['passing_score'] . ' / ' . $certificateDetails['total_marks'], 0, 1, 'C');
$pdf->Cell(0, 10, 'Certificate Number: ' . $certificateNumber, 0, 1, 'C');

// Output PDF
$pdf->Output('Certificate-' . $certificateNumber . '.pdf', 'D'); // D for download
