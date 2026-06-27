<?php
session_start();
require_once 'config/database.php';
require_once 'models/Student.php';

$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
$student = new Student($db);

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    if ($student->deleteStudent($id)) {
        header("Location: students.php?deleted=1");
    } else {
        header("Location: students.php?error=1");
    }
} else {
    header("Location: students.php");
}
exit;
?>
