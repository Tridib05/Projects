<?php
session_start();
require_once 'config/database.php';
require_once 'models/Database.php';
require_once 'models/Student.php';
require_once 'models/Performance.php';

// Initialize database and models
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
$student = new Student($db);
$performance = new Performance($db);

// Get statistics
$totalStudents = $student->getTotalStudents();

$perfResult = $db->query("SELECT COUNT(*) as total FROM performance");
$totalRecords = $perfResult->fetch_assoc()['total'];

$avgResult = $db->query("SELECT AVG(grade_point) as avg FROM performance");
$avgGPA = $avgResult->fetch_assoc()['avg'] ?? 0;

$avgMarksResult = $db->query("SELECT AVG(total_marks) as avg_marks FROM performance");
$avgMarks = $avgMarksResult->fetch_assoc()['avg_marks'] ?? 0;

$topStudentResult = $db->query("SELECT s.full_name, AVG(p.grade_point) as avg_gpa FROM performance p JOIN students s ON p.student_id = s.id GROUP BY s.id ORDER BY avg_gpa DESC LIMIT 1");
$topStudent = $topStudentResult->fetch_assoc();

$gradeResult = $db->query("SELECT grade, COUNT(*) as count FROM performance GROUP BY grade");
$gradeDistribution = [];
while ($row = $gradeResult->fetch_assoc()) {
    $gradeDistribution[$row['grade']] = $row['count'];
}

$subjectTrend = $db->query("SELECT sub.subject_name, AVG(p.total_marks) as avg_marks FROM performance p JOIN subjects sub ON p.subject_id = sub.id GROUP BY sub.id ORDER BY avg_marks DESC LIMIT 5");
$subjectTrendData = [];
while ($row = $subjectTrend->fetch_assoc()) {
    $subjectTrendData[] = $row;
}

// Get recent performance records
$recentResult = $db->query("
    SELECT p.*, s.full_name, sub.subject_name 
    FROM performance p
    JOIN students s ON p.student_id = s.id
    JOIN subjects sub ON p.subject_id = sub.id
    ORDER BY p.created_at DESC 
    LIMIT 5
");

$page_title = "Dashboard - Student Performance";
?>

<?php include 'includes/header.php'; ?>

<div class="row mb-4">
    <div class="col-12">
        <div class="page-header d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
            <div>
                <h1><i class="fas fa-chart-line me-2"></i> Dashboard</h1>
                <p>Welcome back! Track students, performances, and results from one place.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="students.php" class="btn btn-light"><i class="fas fa-users me-2"></i> View Students</a>
                <a href="performance.php" class="btn btn-outline-light"><i class="fas fa-plus me-2"></i> Add Evaluation</a>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="hero-panel">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h4 class="mb-2">Everything is organized for quick academic review</h4>
                    <p class="text-muted mb-0">Use the dashboard to monitor student growth, add new records, and stay on top of performance trends.</p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <span class="badge bg-primary-subtle text-primary px-3 py-2"><i class="fas fa-check-circle me-2"></i> Data entry is ready</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="stat-card primary">
            <div class="stat-icon"><i class="fas fa-users"></i></div>
            <h5>Total Students</h5>
            <div class="stat-value"><?php echo $totalStudents; ?></div>
            <small class="text-muted">Active learners in the system</small>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card success">
            <div class="stat-icon"><i class="fas fa-file-alt"></i></div>
            <h5>Evaluations</h5>
            <div class="stat-value"><?php echo $totalRecords; ?></div>
            <small class="text-muted">Completed assessments</small>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card warning">
            <div class="stat-icon"><i class="fas fa-star"></i></div>
            <h5>Average GPA</h5>
            <div class="stat-value"><?php echo number_format($avgGPA, 2); ?></div>
            <small class="text-muted">Overall academic standing</small>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card danger">
            <div class="stat-icon"><i class="fas fa-book"></i></div>
            <h5>Subjects</h5>
            <div class="stat-value"><?php 
                $subCount = $db->query("SELECT COUNT(*) as total FROM subjects")->fetch_assoc()['total'];
                echo $subCount;
            ?></div>
            <small class="text-muted">Courses currently tracked</small>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i> Academic Performance Overview</h5>
                <span class="badge bg-white text-primary">Avg Marks: <?php echo number_format($avgMarks, 1); ?>%</span>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="performanceChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-trophy me-2"></i> Top Performer</h5>
            </div>
            <div class="card-body">
                <div class="text-center py-3">
                    <div class="stat-icon mx-auto mb-3"><i class="fas fa-medal"></i></div>
                    <h4 class="mb-1"><?php echo htmlspecialchars($topStudent['full_name'] ?? 'N/A'); ?></h4>
                    <p class="text-muted mb-3">Highest average GPA</p>
                    <div class="display-6 text-primary"><?php echo number_format($topStudent['avg_gpa'] ?? 0, 2); ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i> Grade Distribution</h5>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="gradeChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-award me-2"></i> Best Subjects</h5>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="subjectChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-history me-2"></i> Recent Evaluations</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Student</th>
                            <th>Subject</th>
                            <th>Total Marks</th>
                            <th>Grade</th>
                            <th>GPA</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $recentResult->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['subject_name']); ?></td>
                            <td><?php echo number_format($row['total_marks'], 2); ?></td>
                            <td><span class="grade-badge grade-<?php echo strtolower(str_replace('+', '-plus', $row['grade'])); ?>"><?php echo htmlspecialchars($row['grade']); ?></span></td>
                            <td><?php echo number_format($row['grade_point'], 2); ?></td>
                            <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<script>
const gradeCtx = document.getElementById('gradeChart').getContext('2d');
new Chart(gradeCtx, {
    type: 'doughnut',
    data: {
        labels: <?php echo json_encode(array_keys($gradeDistribution)); ?>,
        datasets: [{
            label: 'Number of Evaluations',
            data: <?php echo json_encode(array_values($gradeDistribution)); ?>,
            backgroundColor: ['#10b981', '#06b6d4', '#f59e0b', '#ef4444', '#8b5cf6'],
            borderColor: '#ffffff',
            borderWidth: 2,
            hoverOffset: 8
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: { position: 'bottom' }
        }
    }
});

const perfCtx = document.getElementById('performanceChart').getContext('2d');
new Chart(perfCtx, {
    type: 'line',
    data: {
        labels: ['Assignment', 'Midterm', 'Practical', 'Final'],
        datasets: [{
            label: 'Average Score',
            data: [<?php echo number_format($avgMarks * 0.1, 1); ?>, <?php echo number_format($avgMarks * 0.2, 1); ?>, <?php echo number_format($avgMarks * 0.2, 1); ?>, <?php echo number_format($avgMarks * 0.5, 1); ?>],
            backgroundColor: 'rgba(37, 99, 235, 0.2)',
            borderColor: '#2563eb',
            borderWidth: 3,
            fill: true,
            tension: 0.35,
            pointBackgroundColor: '#2563eb',
            pointRadius: 5
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, max: 100 }
        }
    }
});

const subjectCtx = document.getElementById('subjectChart').getContext('2d');
new Chart(subjectCtx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode(array_column($subjectTrendData, 'subject_name')); ?>,
        datasets: [{
            label: 'Average Marks',
            data: <?php echo json_encode(array_map(function($item) { return round($item['avg_marks'], 1); }, $subjectTrendData)); ?>,
            backgroundColor: ['#2563eb', '#0ea5e9', '#10b981', '#f59e0b', '#ef4444'],
            borderRadius: 8
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, max: 100 } }
    }
});
</script>
