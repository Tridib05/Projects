<?php
session_start();
require_once 'config/database.php';
require_once 'models/Student.php';

$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
$student = new Student($db);

$page_title = "Students - Student Performance";

// Search functionality
$search = $_GET['search'] ?? '';
$students = $search ? $student->searchStudents($search) : $student->getAllStudents();
?>

<?php include 'includes/header.php'; ?>

<div class="row mb-4">
    <div class="col-12">
        <div class="page-header d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
            <div>
                <h1><i class="fas fa-users me-2"></i> Student Management</h1>
                <p>Maintain student records and access performance details with ease.</p>
            </div>
            <a href="students_form.php" class="btn btn-light">
                <i class="fas fa-plus me-2"></i> Add New Student
            </a>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" class="row g-3 align-items-end">
                    <div class="col-lg-9">
                        <label class="form-label">Search Students</label>
                        <input type="text" name="search" class="form-control" 
                               placeholder="Search by name, email, or registration number..." 
                               value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <div class="col-lg-3">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-2"></i> Search
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
                <h5 class="mb-0"><i class="fas fa-id-card me-2"></i> Student Records</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover datatable mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Reg. No.</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Class</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if ($students->num_rows > 0) {
                            while ($row = $students->fetch_assoc()): 
                        ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($row['registration_number']); ?></strong></td>
                            <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['email'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($row['phone'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($row['class_section'] ?? 'N/A'); ?></td>
                            <td>
                                <span class="status-badge <?php echo 'status-' . strtolower($row['status'] ?? 'active'); ?>">
                                    <?php echo ucfirst($row['status'] ?? 'Active'); ?>
                                </span>
                            </td>
                            <td>
                                <a href="students_form.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-primary btn-small me-1">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a href="student_performance.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary btn-small me-1">
                                    <i class="fas fa-chart-bar"></i> Performance
                                </a>
                                <button onclick="deleteConfirm(<?php echo $row['id']; ?>, 'student')" class="btn btn-sm btn-danger btn-small">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </td>
                        </tr>
                        <?php 
                            endwhile;
                        } else {
                            echo '<tr><td colspan="7" class="empty-state">No students found. Add one to get started.</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
