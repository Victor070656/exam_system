<?php
// Exam management
class ExamManager
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function addQuestion($examId, $question, $options, $correctAnswer)
    {
        $stmt = $this->conn->prepare("INSERT INTO questions 
                                    (exam_id, question_text, options, correct_answer) 
                                    VALUES (?, ?, ?, ?)");
        $optionsJson = json_encode($options);
        $stmt->bind_param("isss", $examId, $question, $optionsJson, $correctAnswer);
        return $stmt->execute();
    }

    public function getExam($examId)
    {
        $stmt = $this->conn->prepare("SELECT e.*, q.* FROM exams e 
                                    LEFT JOIN questions q ON e.id = q.exam_id 
                                    WHERE e.id = ?");
        $stmt->bind_param("i", $examId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function createExam($title, $duration, $totalMarks, $startTime, $endTime, $passingScore)
    {
        $stmt = $this->conn->prepare("INSERT INTO exams (title, duration, total_marks, 
                                    start_time, end_time, passing_score) 
                                    VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param(
            "siissi",
            $title,
            $duration,
            $totalMarks,
            $startTime,
            $endTime,
            $passingScore
        );
        return $stmt->execute();
    }
    // Method to update an exam
    public function updateExam($examId, $title, $duration, $totalMarks, $startTime, $endTime, $passingScore)
    {
        $stmt = $this->conn->prepare("
            UPDATE exams 
            SET title = ?, duration = ?, total_marks = ?, start_time = ?, end_time = ?, passing_score = ?
            WHERE id = ?
        ");
        $stmt->bind_param("siissii", $title, $duration, $totalMarks, $startTime, $endTime, $passingScore, $examId);
        return $stmt->execute();
    }

    public function isExamAvailable($examId, $userId)
    {
        $stmt = $this->conn->prepare("
            SELECT 
                e.*,
                CASE 
                    WHEN NOW() < e.start_time THEN 'not_started'
                    WHEN NOW() > e.end_time THEN 'expired'
                    WHEN r.id IS NOT NULL THEN 'completed'
                    ELSE 'available'
                END as status
            FROM exams e
            LEFT JOIN results r ON e.id = r.exam_id AND r.user_id = ?
            WHERE e.id = ?
        ");
        $stmt->bind_param("ii", $userId, $examId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result;
    }

    public function startExam($userId, $examId)
    {
        $stmt = $this->conn->prepare("INSERT INTO exam_sessions 
                                    (user_id, exam_id, start_time, end_time) 
                                    VALUES (?, ?, NOW(), DATE_ADD(NOW(), 
                                    INTERVAL (SELECT duration FROM exams 
                                    WHERE id = ?) MINUTE))");
        $stmt->bind_param("iii", $userId, $examId, $examId);
        return $stmt->execute();
    }

    public function getRemainingTime($userId, $examId)
    {
        $stmt = $this->conn->prepare("SELECT 
                                    TIMESTAMPDIFF(SECOND, NOW(), end_time) 
                                    as remaining_seconds 
                                    FROM exam_sessions 
                                    WHERE user_id = ? AND exam_id = ? 
                                    AND end_time > NOW()");
        $stmt->bind_param("ii", $userId, $examId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result ? $result['remaining_seconds'] : 0;
    }

    public function getTeacherExams($teacherId)
    {
        $stmt = $this->conn->prepare("
            SELECT 
                e.*,
                COUNT(DISTINCT q.id) as question_count,
                COUNT(DISTINCT r.id) as submission_count
            FROM exams e
            LEFT JOIN questions q ON e.id = q.exam_id
            LEFT JOIN results r ON e.id = r.exam_id
            WHERE e.created_by = ?
            GROUP BY e.id
            ORDER BY e.created_at DESC
        ");

        $stmt->bind_param("i", $teacherId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getAvailableExams($userId)
    {
        $stmt = $this->conn->prepare("
            SELECT 
                e.*,
                CASE 
                    WHEN NOW() < e.start_time THEN 'upcoming'
                    WHEN NOW() > e.end_time THEN 'expired'
                    WHEN r.id IS NOT NULL THEN 'completed'
                    ELSE 'available'
                END as status
            FROM exams e
            LEFT JOIN results r ON e.id = r.exam_id AND r.user_id = ?
            WHERE (e.start_time <= NOW() AND e.end_time >= NOW())
                OR r.id IS NOT NULL
            ORDER BY e.start_time ASC
        ");

        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getUpcomingExams($userId)
    {
        $stmt = $this->conn->prepare("
        SELECT 
            e.*,
            CASE 
                WHEN NOW() < e.start_time THEN 'upcoming'
                WHEN NOW() > e.end_time THEN 'expired'
                WHEN r.id IS NOT NULL THEN 'completed'
                ELSE 'available'
            END as status
        FROM exams e
        LEFT JOIN results r ON e.id = r.exam_id AND r.user_id = ?
        WHERE e.start_time > NOW()
        ORDER BY e.start_time ASC
    ");

        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Fetch all exams (for teacher or all exams)
    public function getAllExams($teacherId = null)
    {
        if ($teacherId) {
            // Get exams for a specific teacher
            $stmt = $this->conn->prepare("
            SELECT e.* 
            FROM exams e 
            WHERE e.created_by = ? 
            ORDER BY e.created_at DESC
        ");
            $stmt->bind_param("i", $teacherId);
        } else {
            // Get all exams
            $stmt = $this->conn->prepare("SELECT e.* FROM exams e ORDER BY e.created_at DESC");
        }
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }


}