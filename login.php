<?php
session_start();
require_once 'functions.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    
    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password';
    } else {
        $user = authenticateUser($username, $password);
        
        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header('Location: index.php');
            exit();
        } else {
            $error = 'Invalid username or password';
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login - Classroom Attendance System</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-image: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }

        .login-container {
            width: 100%;
            max-width: 400px;
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transform: translateY(0);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .login-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        }

        h2 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 30px;
            font-weight: 600;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #34495e;
            font-size: 14px;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 16px;
            transition: border-color 0.3s;
            box-sizing: border-box;
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            border-color: #3498db;
            outline: none;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }

        button {
            width: 100%;
            padding: 14px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s;
            margin-top: 10px;
        }

        button:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
        }

        button:active {
            transform: translateY(0);
        }

        .error {
            color: #e74c3c;
            background-color: #fdecea;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            text-align: center;
            font-size: 14px;
            border-left: 4px solid #e74c3c;
        }

        .logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo img {
            height: 50px;
        }

        .forgot-password {
            text-align: center;
            margin-top: 15px;
        }

        .forgot-password a {
            color: #7f8c8d;
            text-decoration: none;
            font-size: 13px;
            transition: color 0.3s;
        }

        .forgot-password a:hover {
            color: #3498db;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <!-- You can add your logo here -->
            <!-- <img src="logo.png" alt="School Logo"> -->
            <h2>Classroom Attendance System</h2>
        </div>
        
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required autofocus placeholder="Enter your username">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required placeholder="Enter your password">
            </div>
            <button type="submit">Login</button>
        </form>
        
        <div class="forgot-password">
            <a href="forgot-password.php">Forgot password?</a>
        </div>
    </div>
</body>
</html>