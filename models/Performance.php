<?php
class Performance {
    private $conn;
    private $table = 'performance';
    
    public $id;
    public $student_id;
    public $subject_id;
    public $semester;
    public $academic_year;
    public $assignment_marks;
    public $midterm_marks;
    public $practical_marks;
    public $final_marks;
    public $total_marks;
    public $grade;
    public $grade_point;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Add performance record
    public function addPerformance() {
        $this->calculateMarks();
        $this->assignGrade();
        
        $sql = "INSERT INTO {$this->table} 
                (student_id, subject_id, semester, academic_year, 
                 assignment_marks, midterm_marks, practical_marks, final_marks,
                 total_marks, grade, grade_point, evaluated_by) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        $user_id = $_SESSION['user_id'] ?? 1;
        
        $stmt->bind_param('iiisdddddddi',
            $this->student_id,
            $this->subject_id,
            $this->semester,
            $this->academic_year,
            $this->assignment_marks,
            $this->midterm_marks,
            $this->practical_marks,
            $this->final_marks,
            $this->total_marks,
            $this->grade,
            $this->grade_point,
            $user_id
        );
        
        return $stmt->execute();
    }
    
    // Get student performance
    public function getStudentPerformance($student_id) {
        $sql = "SELECT p.*, s.subject_name, s.subject_code 
                FROM {$this->table} p
                JOIN subjects s ON p.subject_id = s.id
                WHERE p.student_id = ? 
                ORDER BY p.semester DESC, p.academic_year DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $student_id);
        $stmt->execute();
        return $stmt->get_result();
    }
    
    // Calculate total marks
    private function calculateMarks() {
        $this->total_marks = $this->assignment_marks +
                            $this->midterm_marks +
                            $this->practical_marks +
                            $this->final_marks;
    }
    
    // Assign grade
    private function assignGrade() {
        if ($this->total_marks >= 90) {
            $this->grade = 'A+';
            $this->grade_point = 4.0;
        } elseif ($this->total_marks >= 80) {
            $this->grade = 'A';
            $this->grade_point = 3.75;
        } elseif ($this->total_marks >= 70) {
            $this->grade = 'B';
            $this->grade_point = 3.5;
        } elseif ($this->total_marks >= 60) {
            $this->grade = 'C';
            $this->grade_point = 3.0;
        } elseif ($this->total_marks >= 50) {
            $this->grade = 'D';
            $this->grade_point = 2.0;
        } else {
            $this->grade = 'F';
            $this->grade_point = 0.0;
        }
    }
    
    // Get semester performance
    public function getSemesterPerformance($student_id, $semester, $academic_year) {
        $sql = "SELECT p.*, s.subject_name, s.subject_code, s.credits
                FROM {$this->table} p
                JOIN subjects s ON p.subject_id = s.id
                WHERE p.student_id = ? AND p.semester = ? AND p.academic_year = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('iis', $student_id, $semester, $academic_year);
        $stmt->execute();
        return $stmt->get_result();
    }
    
    // Calculate CGPA
    public function calculateCGPA($student_id, $semester = null) {
        if ($semester) {
            $sql = "SELECT AVG(grade_point) as cgpa FROM {$this->table} 
                    WHERE student_id = ? AND semester <= ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param('ii', $student_id, $semester);
        } else {
            $sql = "SELECT AVG(grade_point) as cgpa FROM {$this->table} 
                    WHERE student_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param('i', $student_id);
        }
        
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return round($result['cgpa'], 2);
    }
}
?>
