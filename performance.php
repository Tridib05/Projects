<?php
session_start();
require_once 'config/database.php';
require_once 'models/Performance.php';
require_once 'models/Student.php';

$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
$performance = new Performance($db);
$student = new Student($db);

$page_title = "Performance Management - Student Performance";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $performance->student_id = $_POST['student_id'] ?? 0;
    $performance->subject_id = $_POST['subject_id'] ?? 0;
    $performance->semester = $_POST['semester'] ?? 0;
    $performance->academic_year = $_POST['academic_year'] ?? '';
    $performance->assignment_marks = $_POST['assignment_marks'] ?? 0;
    $performance->midterm_marks = $_POST['midterm_marks'] ?? 0;
    $performance->practical_marks = $_POST['practical_marks'] ?? 0;
    $performance->final_marks = $_POST['final_marks'] ?? 0;
    
    if ($performance->addPerformance()) {
        header("Location: performance.php?success=1");
        exit;
    }
}

// Get all students and subjects
$students = $student->getAllStudents();
$subjectsResult = $db->query("SELECT * FROM subjects ORDER BY semester, subject_name");

// Get performance records
$perfResult = $db->query("
    SELECT p.*, s.full_name, sub.subject_name 
    FROM performance p
    JOIN students s ON p.student_id = s.id
    JOIN subjects sub ON p.subject_id = sub.id
    ORDER BY p.created_at DESC
");
?>

<?php include 'includes/header.php'; ?>

<div class="row mb-4">
    <div class="col-12">
        <div class="page-header">
            <h1><i class="fas fa-chart-bar me-2"></i> Performance Management</h1>
            <p>Add evaluations and review academic performance in a polished, organized form.</p>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-plus me-2"></i> Add Performance Record</h5>
            </div>
            <div class="card-body">
                <form method="POST" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Student *</label>
                        <select name="student_id" class="form-select" required>
                            <option value="">Select Student</option>
                            <?php while ($row = $students->fetch_assoc()): ?>
                            <option value="<?php echo $row['id']; ?>">
                                <?php echo htmlspecialchars($row['full_name']); ?>
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Subject *</label>
                        <select name="subject_id" class="form-select" required>
                            <option value="">Select Subject</option>
                            <?php 
                            $subjectsResult->data_seek(0);
                            while ($row = $subjectsResult->fetch_assoc()): 
                            ?>
                            <option value="<?php echo $row['id']; ?>">
                                <?php echo htmlspecialchars($row['subject_name']); ?>
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Semester *</label>
                        <select name="semester" class="form-select" required>
                            <option value="">Select</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Academic Year *</label>
                        <input type="text" name="academic_year" class="form-control" placeholder="2024-25" required>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Assignment (10) *</label>
                        <input type="number" name="assignment_marks" class="form-control" min="0" max="10" step="0.5" required>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Midterm (20) *</label>
                        <input type="number" name="midterm_marks" class="form-control" min="0" max="20" step="0.5" required>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Practical (20) *</label>
                        <input type="number" name="practical_marks" class="form-control" min="0" max="20" step="0.5" required>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Final (50) *</label>
                        <input type="number" name="final_marks" class="form-control" min="0" max="50" step="0.5" required>
                    </div>

                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i> Add Performance Record
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-table me-2"></i> Performance Records</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover datatable mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Student</th>
                            <th>Subject</th>
                            <th>Semester</th>
                            <th>Total Marks</th>
                            <th>Grade</th>
                            <th>GPA</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if ($perfResult->num_rows > 0) {
                            while ($row = $perfResult->fetch_assoc()): 
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['subject_name']); ?></td>
                            <td><?php echo $row['semester']; ?></td>
                            <td><?php echo number_format($row['total_marks'], 2); ?>/100</td>
                            <td><span class="grade-badge grade-<?php echo strtolower(str_replace('+', '-plus', $row['grade'])); ?>"><?php echo htmlspecialchars($row['grade']); ?></span></td>
                            <td><?php echo number_format($row['grade_point'], 2); ?></td>
                            <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                        </tr>
                        <?php 
                            endwhile;
                        } else {
                            echo '<tr><td colspan="7" class="empty-state">No performance records found yet.</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
