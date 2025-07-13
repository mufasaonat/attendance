<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'classroom_attendance');
define('DB_USER', 'root');
define('DB_PASS', '');
define('PASSWORD_ALGO', PASSWORD_BCRYPT);
define('PASSWORD_OPTIONS', ['cost' => 12]);

// LINE API configuration
define('LINE_CHANNEL_ACCESS_TOKEN', 'YOUR_LINE_CHANNEL_ACCESS_TOKEN');
define('LINE_CHANNEL_SECRET', 'YOUR_LINE_CHANNEL_SECRET');

// Establish database connection
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>