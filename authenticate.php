<?php
session_start();
require_once 'config/database.php';

$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $db->real_escape_string($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    $result = $db->query("SELECT * FROM users WHERE username = '$username' LIMIT 1");
    
    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Verify password (using password_verify for hashed passwords)
        if (password_verify($password, $user['password']) || $password === $user['password']) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['full_name'] = $user['full_name'];
            
            header("Location: index.php");
            exit;
        }
    }
    
    header("Location: login.php?error=1");
    exit;
} else {
    header("Location: login.php");
    exit;
}
?>
