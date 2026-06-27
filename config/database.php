<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'student_performance_db');

// Create Connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// Check Connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset
$conn->set_charset("utf8");

// Define base URL
define('BASE_URL', 'http://localhost/Php/');

// Session start
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
