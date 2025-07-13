<?php
require_once 'functions.php';

session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $studentId = $_POST['student_id'];
    $firstName = $_POST['first_name'];
    $lastName = $_POST['last_name'];
    $lineUserId = $_POST['line_user_id'] ?: null;
    
    if (registerStudent($studentId, $firstName, $lastName, $lineUserId)) {
        $_SESSION['success_message'] = "Student $studentId added successfully";
        header('Location: admin.php');
        exit;
    } else {
        $error = "Failed to add student";
    }
}
?>