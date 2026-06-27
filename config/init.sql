-- Create Database
CREATE DATABASE IF NOT EXISTS student_performance_db;
USE student_performance_db;

-- Users Table (Admin/Teachers)
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('admin', 'teacher') DEFAULT 'teacher',
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Students Table
CREATE TABLE IF NOT EXISTS students (
    id INT PRIMARY KEY AUTO_INCREMENT,
    registration_number VARCHAR(50) UNIQUE NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    phone VARCHAR(20),
    date_of_birth DATE,
    gender ENUM('Male', 'Female', 'Other') DEFAULT 'Male',
    address TEXT,
    class_section VARCHAR(50),
    mother_name VARCHAR(100),
    father_name VARCHAR(100),
    guardian_contact VARCHAR(20),
    admission_date DATE,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- Subjects Table
CREATE TABLE IF NOT EXISTS subjects (
    id INT PRIMARY KEY AUTO_INCREMENT,
    subject_code VARCHAR(20) UNIQUE NOT NULL,
    subject_name VARCHAR(100) NOT NULL,
    credits INT,
    semester INT,
    description TEXT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- Performance/Marks Table
CREATE TABLE IF NOT EXISTS performance (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    subject_id INT NOT NULL,
    semester INT,
    academic_year VARCHAR(20),
    assignment_marks DECIMAL(5,2),
    midterm_marks DECIMAL(5,2),
    practical_marks DECIMAL(5,2),
    final_marks DECIMAL(5,2),
    total_marks DECIMAL(5,2),
    grade VARCHAR(2),
    grade_point DECIMAL(3,2),
    remarks TEXT,
    evaluation_date DATE,
    evaluated_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subjects(id),
    FOREIGN KEY (evaluated_by) REFERENCES users(id)
);

-- Attendance Table
CREATE TABLE IF NOT EXISTS attendance (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    subject_id INT NOT NULL,
    attendance_date DATE,
    status ENUM('Present', 'Absent', 'Leave') DEFAULT 'Present',
    marked_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subjects(id),
    FOREIGN KEY (marked_by) REFERENCES users(id)
);

-- Reports Table
CREATE TABLE IF NOT EXISTS reports (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    report_type ENUM('semester', 'annual', 'progress') DEFAULT 'semester',
    academic_year VARCHAR(20),
    semester INT,
    cgpa DECIMAL(3,2),
    generated_by INT,
    generated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (generated_by) REFERENCES users(id)
);

-- Audit Log Table
CREATE TABLE IF NOT EXISTS audit_log (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    action VARCHAR(255),
    table_name VARCHAR(50),
    record_id INT,
    old_values JSON,
    new_values JSON,
    action_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Insert Default Admin
INSERT INTO users (username, email, password, full_name, role) 
VALUES ('admin', 'admin@school.com', '$2y$10$WyM8lXpTcZ.3q.KqKzR8/eO0fCQx9Vj4QN1mq1U8mK6Q0j9V.2Xe6', 'Administrator', 'admin');

-- Create Indexes for Performance
CREATE INDEX idx_student_id ON performance(student_id);
CREATE INDEX idx_subject_id ON performance(subject_id);
CREATE INDEX idx_semester ON performance(semester);
CREATE INDEX idx_academic_year ON performance(academic_year);
CREATE INDEX idx_grade ON performance(grade);

-- Create Indexes for Attendance
CREATE INDEX idx_attendance_student ON attendance(student_id);
CREATE INDEX idx_attendance_date ON attendance(attendance_date);

-- Create Indexes for Students
CREATE INDEX idx_student_status ON students(status);
CREATE INDEX idx_student_class ON students(class_section);
