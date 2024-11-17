<?php
// Result processing
class ResultProcessor
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function submitExam($userId, $examId, $answers)
    {
        $score = $this->calculateScore($examId, $answers);

        $stmt = $this->conn->prepare("INSERT INTO results 
                                    (user_id, exam_id, score, answers) 
                                    VALUES (?, ?, ?, ?)");
        $answersJson = json_encode($answers);
        $stmt->bind_param("iiis", $userId, $examId, $score, $answersJson);
        return $stmt->execute();
    }

    private function calculateScore($examId, $userAnswers)
    {
        $score = 0;
        $stmt = $this->conn->prepare("SELECT question_text, correct_answer 
                                    FROM questions WHERE exam_id = ?");
        $stmt->bind_param("i", $examId);
        $stmt->execute();
        $results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        foreach ($results as $index => $question) {
            if (
                isset($userAnswers[$index]) &&
                $userAnswers[$index] === $question['correct_answer']
            ) {
                $score++;
            }
        }
        return $score;
    }

    public function getResults($userId)
    {
        $stmt = $this->conn->prepare("SELECT r.*, e.title FROM results r 
                                    JOIN exams e ON r.exam_id = e.id 
                                    WHERE r.user_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getAnalytics($examId)
    {
        $stmt = $this->conn->prepare("
            SELECT 
                COUNT(*) as total_students,
                AVG(score) as average_score,
                MIN(score) as lowest_score,
                MAX(score) as highest_score,
                SUM(CASE WHEN score >= e.passing_score THEN 1 ELSE 0 END) as passed_count
            FROM results r
            JOIN exams e ON r.exam_id = e.id
            WHERE exam_id = ?
        ");
        $stmt->bind_param("i", $examId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function generateCertificate($userId, $examId)
    {
        $stmt = $this->conn->prepare("
            SELECT r.*, e.title, e.passing_score, u.email
            FROM results r
            JOIN exams e ON r.exam_id = e.id
            JOIN users u ON r.user_id = u.id
            WHERE r.user_id = ? AND r.exam_id = ? 
            AND r.score >= e.passing_score
        ");
        $stmt->bind_param("ii", $userId, $examId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    // Method to fetch certificate details by certificate number
    public function getCertificateDetailsByNumber($certificateNumber)
    {
        // Assuming certificate number is linked to the user and exam details
        // You may want to store the certificate number in the results table or have a mapping table
        $stmt = $this->conn->prepare("
            SELECT r.*, e.title, e.passing_score, u.email
            FROM results r
            JOIN exams e ON r.exam_id = e.id
            JOIN users u ON r.user_id = u.id
            WHERE CONCAT('CERT-', LPAD(r.id, 5, '0')) = ?"); // Using padded result ID as certificate number
        $stmt->bind_param("s", $certificateNumber); // Binding the certificate number
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        } else {
            return null; // No certificate found
        }
    }
}