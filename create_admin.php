<?php
require_once 'auth_functions.php';

// Temporary bypass for initial setup - REMOVE OR COMMENT AFTER USE
$allowAccess = false;

// if (!$allowAccess && getenv('ENVIRONMENT') !== 'development') {
//     die("Access denied");
// }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $fullName = $_POST['full_name'] ?? '';
    
    if (createAdmin($username, $password, $fullName)) {
        echo "Admin account created successfully!";
        // Disable access after creation
        file_put_contents(__FILE__, str_replace(
            '$allowAccess = false;',
            '$allowAccess = false;',
            file_get_contents(__FILE__)
        ));
    } else {
        echo "Failed to create admin account";
    }
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Create Admin Account</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        form { max-width: 400px; margin: 0 auto; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input { width: 100%; padding: 8px; }
        button { padding: 10px 15px; background: #3498db; color: white; border: none; }
    </style>
</head>
<body>
    <h2>Create Admin Account</h2>
    <form method="POST">
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" required>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required>
        </div>
        <div class="form-group">
            <label>Full Name</label>
            <input type="text" name="full_name" required>
        </div>
        <button type="submit">Create Admin</button>
    </form>
</body>
</html>