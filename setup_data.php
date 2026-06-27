<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$db = new mysqli('localhost', 'root', '');
if ($db->connect_error) {
    die('Connection failed: ' . $db->connect_error);
}

$db->query('CREATE DATABASE IF NOT EXISTS student_performance_db');
$db->select_db('student_performance_db');
$db->set_charset('utf8');

$db->query("CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('admin','teacher') DEFAULT 'teacher',
    status ENUM('active','inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)");

$db->query("CREATE TABLE IF NOT EXISTS students (
    id INT PRIMARY KEY AUTO_INCREMENT,
    registration_number VARCHAR(50) UNIQUE NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    phone VARCHAR(20),
    date_of_birth DATE,
    gender ENUM('Male','Female','Other') DEFAULT 'Male',
    address TEXT,
    class_section VARCHAR(50),
    mother_name VARCHAR(100),
    father_name VARCHAR(100),
    guardian_contact VARCHAR(20),
    admission_date DATE,
    status ENUM('active','inactive') DEFAULT 'active',
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id)
)");

$db->query("CREATE TABLE IF NOT EXISTS subjects (
    id INT PRIMARY KEY AUTO_INCREMENT,
    subject_code VARCHAR(20) UNIQUE NOT NULL,
    subject_name VARCHAR(100) NOT NULL,
    credits INT,
    semester INT,
    description TEXT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id)
)");

$db->query("CREATE TABLE IF NOT EXISTS performance (
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
)");

$adminPassword = '$2y$10$WyM8lXpTcZ.3q.KqKzR8/eO0fCQx9Vj4QN1mq1U8mK6Q0j9V.2Xe6';
$insertUser = $db->prepare("INSERT IGNORE INTO users (id, username, email, password, full_name, role) VALUES (1, 'admin', 'admin@school.com', ?, 'Administrator', 'admin')");
$insertUser->bind_param('s', $adminPassword);
$insertUser->execute();
$insertUser->close();

$db->query('DELETE FROM performance');
$db->query('DELETE FROM students');
$db->query('DELETE FROM subjects');

$subjects = [
    ['MAT101', 'Mathematics', 3, 1, 'Core mathematics'],
    ['ENG101', 'English Literature', 3, 1, 'Language and writing'],
    ['SCI101', 'Physics', 3, 1, 'Fundamental physics'],
    ['CS101', 'Computer Science', 4, 2, 'Programming basics'],
    ['BIO101', 'Biology', 3, 2, 'Life sciences'],
    ['HIS101', 'History', 3, 2, 'World history'],
    ['CHE101', 'Chemistry', 3, 2, 'Applied chemistry'],
    ['ART101', 'Art & Design', 2, 1, 'Creative studies']
];

$insertSubject = $db->prepare('INSERT IGNORE INTO subjects (subject_code, subject_name, credits, semester, description, created_by) VALUES (?, ?, ?, ?, ?, 1)');
foreach ($subjects as $subject) {
    $insertSubject->bind_param('ssdis', $subject[0], $subject[1], $subject[2], $subject[3], $subject[4]);
    $insertSubject->execute();
}
$insertSubject->close();

$students = [
    ['2024001', 'Rafiq Rahman', 'rafiq@example.com', '01711111111', '2005-04-12', 'Male', 'House 12, Dhaka', '10A', 'Mina Rahman', 'Abdur Rahman', '01722222222'],
    ['2024002', 'Nadia Islam', 'nadia@example.com', '01733333333', '2005-06-08', 'Female', 'Road 7, Chittagong', '10B', 'Farida Islam', 'Kamal Islam', '01744444444'],
    ['2024003', 'Samiul Hasan', 'samiul@example.com', '01755555555', '2005-09-20', 'Male', 'Block C, Sylhet', '10A', 'Salma Hasan', 'Hasan Ali', '01766666666'],
    ['2024004', 'Farhana Akter', 'farhana@example.com', '01777777777', '2005-01-15', 'Female', 'Garden Road, Khulna', '10B', 'Laila Akter', 'Bakul Akter', '01788888888'],
    ['2024005', 'Tanvir Ahmed', 'tanvir@example.com', '01799999999', '2005-07-22', 'Male', 'Bashundhara, Dhaka', '10A', 'Shila Ahmed', 'Masud Ahmed', '01811111111'],
    ['2024006', 'Meherin Chowdhury', 'meherin@example.com', '01822222222', '2005-03-10', 'Female', 'Uttara, Dhaka', '10C', 'Rina Chowdhury', 'Nabil Chowdhury', '01833333333'],
    ['2024007', 'Arman Hossain', 'arman@example.com', '01844444444', '2005-11-05', 'Male', 'Joypurhat Road, Rajshahi', '10B', 'Nazma Hossain', 'Jahangir Hossain', '01855555555'],
    ['2024008', 'Sumaiya Begum', 'sumaiya@example.com', '01866666666', '2005-08-18', 'Female', 'Lake City, Barishal', '10A', 'Ayesha Begum', 'Mofiz Begum', '01877777777'],
    ['2024009', 'Jamil Rahman', 'jamil@example.com', '01888888888', '2005-10-02', 'Male', 'Old Town, Rangpur', '10C', 'Kamrun Nahar', 'Rahim Rahman', '01899999999'],
    ['2024010', 'Mitu Akter', 'mitu@example.com', '01900000000', '2005-12-25', 'Female', 'North Avenue, Cumilla', '10B', 'Anjuman Akter', 'Abu Akter', '01911111111'],
    ['2024011', 'Nafisa Rahman', 'nafisa@example.com', '01922222222', '2005-05-17', 'Female', 'Banani, Dhaka', '10A', 'Sadia Rahman', 'Hafiz Rahman', '01933333333'],
    ['2024012', 'Khalid Hasan', 'khalid@example.com', '01944444444', '2005-02-09', 'Male', 'Motijheel, Dhaka', '10C', 'Sultana Hasan', 'Mizan Hasan', '01955555555'],
    ['2024013', 'Rima Sultana', 'rima@example.com', '01966666666', '2005-07-14', 'Female', 'Pabna Road, Bogura', '10B', 'Rokeya Sultana', 'Hossain Sultana', '01977777777'],
    ['2024014', 'Omar Faruk', 'omar@example.com', '01988888888', '2005-09-28', 'Male', 'Gulshan, Dhaka', '10A', 'Mina Faruk', 'Azad Faruk', '01999999999'],
    ['2024015', 'Shimu Akter', 'shimu@example.com', '02000000000', '2005-01-21', 'Female', 'Narayanganj', '10C', 'Selina Akter', 'Rafiq Akter', '02011111111'],
    ['2024016', 'Mahin Islam', 'mahin@example.com', '02022222222', '2005-10-19', 'Male', 'Satkhira', '10B', 'Amina Islam', 'Zahir Islam', '02033333333'],
    ['2024017', 'Tasnim Jahan', 'tasnim@example.com', '02044444444', '2005-04-30', 'Female', 'Mymensingh', '10A', 'Parvin Jahan', 'Abdul Jahan', '02055555555'],
    ['2024018', 'Ahsan Kabir', 'ahsan@example.com', '02066666666', '2005-06-16', 'Male', 'Comilla', '10C', 'Nusrat Kabir', 'Shahid Kabir', '02077777777'],
    ['2024019', 'Mowmita Roy', 'mowmita@example.com', '02088888888', '2005-11-12', 'Female', 'Jessore', '10B', 'Mina Roy', 'Biplob Roy', '02099999999'],
    ['2024020', 'Ibrahim Chowdhury', 'ibrahim@example.com', '02100000000', '2005-08-03', 'Male', 'Rangpur', '10A', 'Laila Chowdhury', 'Bashir Chowdhury', '02111111111']
];

$insertStudent = $db->prepare('INSERT IGNORE INTO students (registration_number, full_name, email, phone, date_of_birth, gender, address, class_section, mother_name, father_name, guardian_contact, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)');
foreach ($students as $student) {
    $insertStudent->bind_param('sssssssssss', $student[0], $student[1], $student[2], $student[3], $student[4], $student[5], $student[6], $student[7], $student[8], $student[9], $student[10]);
    $insertStudent->execute();
}
$insertStudent->close();

$subjectMap = [];
$subjectResult = $db->query('SELECT id, subject_code FROM subjects');
while ($row = $subjectResult->fetch_assoc()) {
    $subjectMap[$row['subject_code']] = (int) $row['id'];
}

$studentMap = [];
$studentResult = $db->query('SELECT id, registration_number FROM students');
while ($row = $studentResult->fetch_assoc()) {
    $studentMap[$row['registration_number']] = (int) $row['id'];
}

$performanceRows = [];
$subjectCodes = ['MAT101', 'ENG101', 'SCI101', 'CS101', 'BIO101', 'HIS101', 'CHE101', 'ART101'];
$remarks = ['Excellent work', 'Strong performance', 'Great improvement', 'Very consistent', 'High potential', 'Reliable effort', 'Focused learning'];

foreach ($students as $index => $student) {
    $registrationNumber = $student[0];
    $firstSubject = $subjectCodes[$index % count($subjectCodes)];
    $secondSubject = $subjectCodes[($index + 3) % count($subjectCodes)];
    $semester = ($index % 2 === 0) ? 1 : 2;

    $baseAssignment = 9.0 + ($index % 3) * 0.6;
    $baseMidterm = 17.5 + ($index % 4) * 1.2;
    $basePractical = 18.0 + ($index % 3) * 0.7;
    $baseFinal = 45.0 + ($index % 4) * 2.3;

    $performanceRows[] = [$registrationNumber, $firstSubject, $semester, '2024-25', $baseAssignment, $baseMidterm, $basePractical, $baseFinal, $remarks[$index % count($remarks)]];
    $performanceRows[] = [$registrationNumber, $secondSubject, $semester, '2024-25', $baseAssignment + 0.6, $baseMidterm + 0.7, $basePractical + 0.8, $baseFinal + 2.5, 'Excellent progress'];
}

function assignGrade($totalMarks) {
    if ($totalMarks >= 90) return ['A+', 4.00];
    if ($totalMarks >= 80) return ['A', 3.75];
    if ($totalMarks >= 70) return ['B', 3.50];
    if ($totalMarks >= 60) return ['C', 3.00];
    if ($totalMarks >= 50) return ['D', 2.00];
    return ['F', 0.00];
}

$insertPerformance = $db->prepare('INSERT IGNORE INTO performance (student_id, subject_id, semester, academic_year, assignment_marks, midterm_marks, practical_marks, final_marks, total_marks, grade, grade_point, remarks, evaluated_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)');
foreach ($performanceRows as $row) {
    $studentId = $studentMap[$row[0]] ?? null;
    $subjectId = $subjectMap[$row[1]] ?? null;
    if (!$studentId || !$subjectId) {
        continue;
    }

    $totalMarks = $row[4] + $row[5] + $row[6] + $row[7];
    [$grade, $gradePoint] = assignGrade($totalMarks);

    $insertPerformance->bind_param('iiisddddddds', $studentId, $subjectId, $row[2], $row[3], $row[4], $row[5], $row[6], $row[7], $totalMarks, $grade, $gradePoint, $row[8]);
    $insertPerformance->execute();
}
$insertPerformance->close();

$studentCount = $db->query('SELECT COUNT(*) as c FROM students')->fetch_assoc()['c'];
$subjectCount = $db->query('SELECT COUNT(*) as c FROM subjects')->fetch_assoc()['c'];
$performanceCount = $db->query('SELECT COUNT(*) as c FROM performance')->fetch_assoc()['c'];

echo "Inserted: students=$studentCount, subjects=$subjectCount, performance=$performanceCount";
