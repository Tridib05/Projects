<?php
session_start();
require_once 'config/database.php';
require_once 'models/Performance.php';

$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
$performance = new Performance($db);

$page_title = "Reports - Student Performance";

// Get semester filter
$semester = $_GET['semester'] ?? null;
$academic_year = $_GET['academic_year'] ?? null;

// Get all unique semesters and years
$semesterResult = $db->query("SELECT DISTINCT semester FROM performance ORDER BY semester");
$yearResult = $db->query("SELECT DISTINCT academic_year FROM performance ORDER BY academic_year DESC");

// Get performance data based on filters
$query = "
    SELECT p.*, s.full_name, s.registration_number, sub.subject_name, sub.credits
    FROM performance p
    JOIN students s ON p.student_id = s.id
    JOIN subjects sub ON p.subject_id = sub.id
    WHERE 1=1
";

if ($semester) {
    $query .= " AND p.semester = " . intval($semester);
}
if ($academic_year) {
    $query .= " AND p.academic_year = '" . $db->real_escape_string($academic_year) . "'";
}

$query .= " ORDER BY s.full_name, p.created_at DESC";
$perfResult = $db->query($query);
?>

<?php include 'includes/header.php'; ?>

<div class="row mb-4">
    <div class="col-12">
        <div class="page-header">
            <h1><i class="fas fa-file-pdf me-2"></i> Performance Reports</h1>
            <p>Advanced academic reporting with clean filters and polished analytics.</p>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Semester</label>
                        <select name="semester" class="form-select" onchange="this.form.submit()">
                            <option value="">All Semesters</option>
                            <?php 
                            $semesterResult->data_seek(0);
                            while ($row = $semesterResult->fetch_assoc()): 
                            ?>
                            <option value="<?php echo $row['semester']; ?>" <?php echo ($semester == $row['semester']) ? 'selected' : ''; ?>>
                                Semester <?php echo $row['semester']; ?>
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Academic Year</label>
                        <select name="academic_year" class="form-select" onchange="this.form.submit()">
                            <option value="">All Years</option>
                            <?php 
                            $yearResult->data_seek(0);
                            while ($row = $yearResult->fetch_assoc()): 
                            ?>
                            <option value="<?php echo $row['academic_year']; ?>" <?php echo ($academic_year == $row['academic_year']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($row['academic_year']); ?>
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="col-md-4 d-flex gap-2">
                        <button type="button" onclick="printTable()" class="btn btn-info flex-grow-1">
                            <i class="fas fa-print me-2"></i> Print
                        </button>
                        <button type="button" onclick="exportToCSV()" class="btn btn-success flex-grow-1">
                            <i class="fas fa-download me-2"></i> Export
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
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-table me-2"></i> Performance Report</h5>
                <span class="badge bg-white text-primary"><?php echo $perfResult->num_rows; ?> records</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover datatable mb-0" id="reportTable">
                    <thead class="table-light">
                        <tr>
                            <th>Reg. No.</th>
                            <th>Student Name</th>
                            <th>Subject</th>
                            <th>Semester</th>
                            <th>Assignment</th>
                            <th>Midterm</th>
                            <th>Practical</th>
                            <th>Final</th>
                            <th>Total</th>
                            <th>Grade</th>
                            <th>GPA</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if ($perfResult->num_rows > 0) {
                            while ($row = $perfResult->fetch_assoc()): 
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['registration_number']); ?></td>
                            <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['subject_name']); ?></td>
                            <td><?php echo $row['semester']; ?></td>
                            <td><?php echo number_format($row['assignment_marks'], 1); ?></td>
                            <td><?php echo number_format($row['midterm_marks'], 1); ?></td>
                            <td><?php echo number_format($row['practical_marks'], 1); ?></td>
                            <td><?php echo number_format($row['final_marks'], 1); ?></td>
                            <td><strong><?php echo number_format($row['total_marks'], 2); ?></strong></td>
                            <td><span class="grade-badge grade-<?php echo strtolower(str_replace('+', '-plus', $row['grade'])); ?>"><?php echo htmlspecialchars($row['grade']); ?></span></td>
                            <td><?php echo number_format($row['grade_point'], 2); ?></td>
                        </tr>
                        <?php 
                            endwhile;
                        } else {
                            echo '<tr><td colspan="11" class="empty-state">No performance records found for this filter.</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
