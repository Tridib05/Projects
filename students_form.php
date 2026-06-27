<?php
session_start();
require_once 'config/database.php';
require_once 'models/Student.php';

$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
$student = new Student($db);

$edit = false;
$studentData = [];

// Check if editing
if (isset($_GET['id'])) {
    $studentData = $student->getStudentById($_GET['id']);
    $edit = true;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student->registration_number = $_POST['registration_number'] ?? '';
    $student->full_name = $_POST['full_name'] ?? '';
    $student->email = $_POST['email'] ?? '';
    $student->phone = $_POST['phone'] ?? '';
    $student->date_of_birth = $_POST['date_of_birth'] ?? '';
    $student->gender = $_POST['gender'] ?? 'Male';
    $student->address = $_POST['address'] ?? '';
    $student->class_section = $_POST['class_section'] ?? '';
    $student->mother_name = $_POST['mother_name'] ?? '';
    $student->father_name = $_POST['father_name'] ?? '';
    $student->guardian_contact = $_POST['guardian_contact'] ?? '';
    
    if ($edit && isset($_GET['id'])) {
        $student->id = $_GET['id'];
        $result = $student->updateStudent();
        $message = "Student updated successfully!";
    } else {
        $result = $student->addStudent();
        $message = "Student added successfully!";
    }
    
    if ($result) {
        header("Location: students.php?success=1");
        exit;
    }
}

$page_title = ($edit ? "Edit" : "Add") . " Student - Student Performance";
?>

<?php include 'includes/header.php'; ?>

<div class="row mb-4">
    <div class="col-12">
        <div class="page-header">
            <h1><i class="fas fa-user-plus me-2"></i> <?php echo $edit ? 'Edit' : 'Add New'; ?> Student</h1>
            <p>Fill in the student details below. Every field is ready to save into the database.</p>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-9 mx-auto">
        <form method="POST" class="form-card">
            <div class="row mb-4">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Registration Number *</label>
                    <input type="text" name="registration_number" class="form-control" 
                           value="<?php echo htmlspecialchars($studentData['registration_number'] ?? ''); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Full Name *</label>
                    <input type="text" name="full_name" class="form-control" 
                           value="<?php echo htmlspecialchars($studentData['full_name'] ?? ''); ?>" required>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" 
                           value="<?php echo htmlspecialchars($studentData['email'] ?? ''); ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Phone</label>
                    <input type="tel" name="phone" class="form-control" 
                           value="<?php echo htmlspecialchars($studentData['phone'] ?? ''); ?>">
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Date of Birth</label>
                    <input type="date" name="date_of_birth" class="form-control" 
                           value="<?php echo htmlspecialchars($studentData['date_of_birth'] ?? ''); ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Gender</label>
                    <select name="gender" class="form-select">
                        <option value="Male" <?php echo ($studentData['gender'] ?? '') === 'Male' ? 'selected' : ''; ?>>Male</option>
                        <option value="Female" <?php echo ($studentData['gender'] ?? '') === 'Female' ? 'selected' : ''; ?>>Female</option>
                        <option value="Other" <?php echo ($studentData['gender'] ?? '') === 'Other' ? 'selected' : ''; ?>>Other</option>
                    </select>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">Address</label>
                <textarea name="address" class="form-control" rows="3"><?php echo htmlspecialchars($studentData['address'] ?? ''); ?></textarea>
            </div>

            <div class="row mb-4">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Class/Section</label>
                    <input type="text" name="class_section" class="form-control" 
                           value="<?php echo htmlspecialchars($studentData['class_section'] ?? ''); ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Guardian Contact</label>
                    <input type="tel" name="guardian_contact" class="form-control" 
                           value="<?php echo htmlspecialchars($studentData['guardian_contact'] ?? ''); ?>">
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Father's Name</label>
                    <input type="text" name="father_name" class="form-control" 
                           value="<?php echo htmlspecialchars($studentData['father_name'] ?? ''); ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Mother's Name</label>
                    <input type="text" name="mother_name" class="form-control" 
                           value="<?php echo htmlspecialchars($studentData['mother_name'] ?? ''); ?>">
                </div>
            </div>

            <div class="d-flex flex-wrap gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i> <?php echo $edit ? 'Update' : 'Add'; ?> Student
                </button>
                <a href="students.php" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-2"></i> Back to List
                </a>
            </div>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
