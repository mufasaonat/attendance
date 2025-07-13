<?php
require_once 'config.php';

function createAdmin($username, $password, $fullName) {
    global $pdo;
    
    $passwordHash = password_hash($password, PASSWORD_ALGO, PASSWORD_OPTIONS);
    
    $stmt = $pdo->prepare("INSERT INTO admins (username, password_hash, full_name) 
                          VALUES (?, ?, ?)");
    return $stmt->execute([$username, $passwordHash, $fullName]);
}

function verifyAdmin($username, $password) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT id, username, password_hash, full_name FROM admins 
                          WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();
    
    if ($admin && password_verify($password, $admin['password_hash'])) {
        return $admin;
    }
    
    return false;
}

function isLoggedIn() {
    return isset($_SESSION['admin_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: admin_login.php');
        exit;
    }
}

function logout() {
    session_unset();
    session_destroy();
    session_regenerate_id(true);
}
?>