<?php
session_start();
require_once 'config/database.php';
require_once 'models/Performance.php';
require_once 'models/Student.php';

$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
$performance = new Performance($db);
$student = new Student($db);

if (!isset($_GET['id'])) {
    header("Location: students.php");
    exit;
}

$studentId = $_GET['id'];
$studentData = $student->getStudentById($studentId);

if (!$studentData) {
    header("Location: students.php");
    exit;
}

$page_title = $studentData['full_name'] . " - Performance - Student Performance";

// Get all performance records
$perfResult = $performance->getStudentPerformance($studentId);

// Calculate CGPA
$cgpa = $performance->calculateCGPA($studentId);

// Get grade breakdown
$gradeQuery = "
    SELECT grade, COUNT(*) as count, AVG(total_marks) as avg_marks
    FROM performance
    WHERE student_id = ?
    GROUP BY grade
";
$stmt = $db->prepare($gradeQuery);
$stmt->bind_param('i', $studentId);
$stmt->execute();
$gradeResult = $stmt->get_result();
$gradeBreakdown = [];
while ($row = $gradeResult->fetch_assoc()) {
    $gradeBreakdown[$row['grade']] = $row;
}
?>

<?php include 'includes/header.php'; ?>

<div class="row mb-4">
    <div class="col-12">
        <div class="page-header">
            <h1><i class="fas fa-user me-2"></i> <?php echo htmlspecialchars($studentData['full_name']); ?>'s Performance</h1>
            <p>Reg. No: <?php echo htmlspecialchars($studentData['registration_number']); ?> | Class: <?php echo htmlspecialchars($studentData['class_section'] ?? 'N/A'); ?></p>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="stat-card primary">
            <div class="stat-icon"><i class="fas fa-book"></i></div>
            <h5>Total Subjects</h5>
            <div class="stat-value"><?php echo $perfResult->num_rows; ?></div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card success">
            <div class="stat-icon"><i class="fas fa-star"></i></div>
            <h5>CGPA</h5>
            <div class="stat-value"><?php echo number_format($cgpa, 2); ?></div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card warning">
            <div class="stat-icon"><i class="fas fa-trophy"></i></div>
            <h5>Best Grade</h5>
            <div class="stat-value"><?php echo !empty($gradeBreakdown) ? max(array_keys($gradeBreakdown)) : 'N/A'; ?></div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card danger">
            <div class="stat-icon"><i class="fas fa-user-graduate"></i></div>
            <h5>Performance</h5>
            <div class="stat-value"><?php echo $cgpa >= 3.5 ? 'Excellent' : ($cgpa >= 3.0 ? 'Good' : 'Average'); ?></div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-table me-2"></i> Subject Performance</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover datatable mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Subject</th>
                            <th>Semester</th>
                            <th>Year</th>
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
                        $perfResult->data_seek(0);
                        if ($perfResult->num_rows > 0) {
                            while ($row = $perfResult->fetch_assoc()): 
                        ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($row['subject_name']); ?></strong></td>
                            <td><?php echo $row['semester']; ?></td>
                            <td><?php echo htmlspecialchars($row['academic_year']); ?></td>
                            <td><?php echo number_format($row['assignment_marks'], 1); ?>/10</td>
                            <td><?php echo number_format($row['midterm_marks'], 1); ?>/20</td>
                            <td><?php echo number_format($row['practical_marks'], 1); ?>/20</td>
                            <td><?php echo number_format($row['final_marks'], 1); ?>/50</td>
                            <td><strong><?php echo number_format($row['total_marks'], 2); ?>/100</strong></td>
                            <td><span class="grade-badge grade-<?php echo strtolower(str_replace('+', '-plus', $row['grade'])); ?>"><?php echo htmlspecialchars($row['grade']); ?></span></td>
                            <td><?php echo number_format($row['grade_point'], 2); ?></td>
                        </tr>
                        <?php 
                            endwhile;
                        } else {
                            echo '<tr><td colspan="10" class="empty-state">No performance records found.</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i> Grade Distribution</h5>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="gradeDistChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-clipboard-list me-2"></i> Performance Summary</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm mb-0">
                    <tr>
                        <td><strong>Total Evaluations:</strong></td>
                        <td><?php echo $perfResult->num_rows; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Overall CGPA:</strong></td>
                        <td><?php echo number_format($cgpa, 2); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Highest Grade:</strong></td>
                        <td><?php 
                            $perfResult->data_seek(0);
                            $grades = [];
                            while ($row = $perfResult->fetch_assoc()) {
                                $grades[] = $row['grade'];
                            }
                            echo !empty($grades) ? max($grades) : 'N/A';
                        ?></td>
                    </tr>
                    <tr>
                        <td><strong>Performance Level:</strong></td>
                        <td>
                            <?php 
                            if ($cgpa >= 3.8) echo '<span class="badge bg-success">Excellent (A+)</span>';
                            elseif ($cgpa >= 3.5) echo '<span class="badge bg-success">Very Good (A)</span>';
                            elseif ($cgpa >= 3.0) echo '<span class="badge bg-info">Good (B)</span>';
                            elseif ($cgpa >= 2.5) echo '<span class="badge bg-warning">Average (C)</span>';
                            else echo '<span class="badge bg-danger">Below Average (D/F)</span>';
                            ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <a href="students.php" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i> Back to Students</a>
        <button onclick="printTable()" class="btn btn-info"><i class="fas fa-print me-2"></i> Print</button>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<script>
const gradeBreakdown = <?php echo json_encode($gradeBreakdown); ?>;
const grades = Object.keys(gradeBreakdown);
const counts = grades.map(g => gradeBreakdown[g].count);

const ctx = document.getElementById('gradeDistChart').getContext('2d');
new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: grades,
        datasets: [{
            data: counts,
            backgroundColor: ['#10b981', '#06b6d4', '#f59e0b', '#ef4444', '#8b5cf6'],
            borderColor: '#ffffff',
            borderWidth: 2,
            hoverOffset: 8
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: { legend: { position: 'bottom' } }
    }
});
</script>
