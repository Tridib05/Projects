<?php
class Student {
    private $conn;
    private $table = 'students';
    
    public $id;
    public $registration_number;
    public $full_name;
    public $email;
    public $phone;
    public $date_of_birth;
    public $gender;
    public $address;
    public $class_section;
    public $mother_name;
    public $father_name;
    public $guardian_contact;
    public $status;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Get all students
    public function getAllStudents() {
        $sql = "SELECT * FROM {$this->table} ORDER BY full_name ASC";
        return $this->conn->query($sql);
    }
    
    // Get student by ID
    public function getStudentById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    // Add new student
    public function addStudent() {
        $sql = "INSERT INTO {$this->table} 
                (registration_number, full_name, email, phone, date_of_birth, 
                 gender, address, class_section, mother_name, father_name, 
                 guardian_contact, created_by) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        $user_id = $_SESSION['user_id'] ?? 1;
        
        $stmt->bind_param('sssssssssssi',
            $this->registration_number,
            $this->full_name,
            $this->email,
            $this->phone,
            $this->date_of_birth,
            $this->gender,
            $this->address,
            $this->class_section,
            $this->mother_name,
            $this->father_name,
            $this->guardian_contact,
            $user_id
        );
        
        return $stmt->execute();
    }
    
    // Update student
    public function updateStudent() {
        $sql = "UPDATE {$this->table} SET 
                full_name = ?, email = ?, phone = ?, date_of_birth = ?, 
                gender = ?, address = ?, class_section = ?, 
                mother_name = ?, father_name = ?, guardian_contact = ? 
                WHERE id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ssssssssssi',
            $this->full_name,
            $this->email,
            $this->phone,
            $this->date_of_birth,
            $this->gender,
            $this->address,
            $this->class_section,
            $this->mother_name,
            $this->father_name,
            $this->guardian_contact,
            $this->id
        );
        
        return $stmt->execute();
    }
    
    // Delete student
    public function deleteStudent($id) {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }
    
    // Search students
    public function searchStudents($searchTerm) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE full_name LIKE ? OR registration_number LIKE ? 
                OR email LIKE ? ORDER BY full_name ASC";
        $stmt = $this->conn->prepare($sql);
        $search = "%{$searchTerm}%";
        $stmt->bind_param('sss', $search, $search, $search);
        $stmt->execute();
        return $stmt->get_result();
    }
    
    // Get total students
    public function getTotalStudents() {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        $result = $this->conn->query($sql)->fetch_assoc();
        return $result['total'];
    }
}
?>
