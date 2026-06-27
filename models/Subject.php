<?php
class Subject {
    private $conn;
    private $table = 'subjects';
    
    public $id;
    public $subject_code;
    public $subject_name;
    public $credits;
    public $semester;
    public $description;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Get all subjects
    public function getAllSubjects() {
        $sql = "SELECT * FROM {$this->table} ORDER BY semester ASC, subject_name ASC";
        return $this->conn->query($sql);
    }
    
    // Get subjects by semester
    public function getSubjectsBySemester($semester) {
        $sql = "SELECT * FROM {$this->table} WHERE semester = ? ORDER BY subject_name ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $semester);
        $stmt->execute();
        return $stmt->get_result();
    }
    
    // Add subject
    public function addSubject() {
        $sql = "INSERT INTO {$this->table} 
                (subject_code, subject_name, credits, semester, description, created_by) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        $user_id = $_SESSION['user_id'] ?? 1;
        
        $stmt->bind_param('sssiss',
            $this->subject_code,
            $this->subject_name,
            $this->credits,
            $this->semester,
            $this->description,
            $user_id
        );
        
        return $stmt->execute();
    }
}
?>
