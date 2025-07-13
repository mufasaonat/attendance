<?php
function authenticateUser($username, $password) {
    // Database connection parameters
    $host = 'localhost';
    $dbname = 'classroom_attendance';
    $user = 'root';
    $pass = '';
    
    try {
        // Create PDO connection
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Prepare SQL query
        $stmt = $pdo->prepare("SELECT id, username, password_hash FROM users WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        
        // Fetch user data
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Verify password
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        
        return false;
    } catch(PDOException $e) {
        // Handle database errors
        error_log("Database error: " . $e->getMessage());
        return false;
    }
}

// Database connection function (optional)
function getDBConnection() {
    $host = 'localhost';
    $dbname = 'classroom_attendance';
    $user = 'root';
    $pass = '';
    
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch(PDOException $e) {
        error_log("Database connection failed: " . $e->getMessage());
        die("Database connection error");
    }
}
?>