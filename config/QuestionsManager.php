<?php
class QuestionManager
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    // Add a question to an exam
    public function addQuestion($examId, $questionText, $options, $correctAnswer)
    {
        $stmt = $this->conn->prepare("
            INSERT INTO questions (exam_id, question_text, options, correct_answer)
            VALUES (?, ?, ?, ?)
        ");
        $optionsJson = json_encode($options);
        $stmt->bind_param("isss", $examId, $questionText, $optionsJson, $correctAnswer);
        return $stmt->execute();
    }

    // Get all questions for a specific exam
    public function getQuestionsForExam($examId)
    {
        $stmt = $this->conn->prepare("SELECT * FROM questions WHERE exam_id = ?");
        $stmt->bind_param("i", $examId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // public function getQuestionsByExamId($examId)
    // {
    //     $stmt = $this->conn->prepare("SELECT * FROM questions WHERE exam_id = ?");
    //     $stmt->bind_param("i", $examId);
    //     $stmt->execute();
    //     return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    // }

    // Get a single question by its ID
    public function getQuestion($questionId)
    {
        $stmt = $this->conn->prepare("SELECT * FROM questions WHERE id = ?");
        $stmt->bind_param("i", $questionId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Update a question
    public function updateQuestion($questionId, $questionText, $options, $correctAnswer)
    {
        $stmt = $this->conn->prepare("
            UPDATE questions 
            SET question_text = ?, options = ?, correct_answer = ?
            WHERE id = ?
        ");
        $optionsJson = json_encode($options);
        $stmt->bind_param("sssi", $questionText, $optionsJson, $correctAnswer, $questionId);
        return $stmt->execute();
    }

    // Delete a question
    public function deleteQuestion($questionId)
    {
        $stmt = $this->conn->prepare("DELETE FROM questions WHERE id = ?");
        $stmt->bind_param("i", $questionId);
        return $stmt->execute();
    }
}
