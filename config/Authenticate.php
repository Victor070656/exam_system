<?php
// User authentication
class Authenticate
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    // User registration function
    public function register($email, $password, $role)
    {
        // Hash the password for security
        // $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Prepare SQL query to insert new user
        $stmt = $this->conn->prepare("INSERT INTO `users` (`email`, `password`, `role`) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $email, $password, $role);

        // Execute the query and check for success
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function login($email, $password)
    {
        $stmt = $this->conn->prepare("SELECT * FROM `users` 
                                    WHERE `email` = ? AND `password` = ?");
        $stmt->bind_param("ss", $email, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['role'] = $row['role'];
            return true;
        }
        return false;
    }
}
