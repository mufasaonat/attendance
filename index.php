<?php
require_once 'functions.php';
session_start();

//Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$error = '';
$success = '';
$currentStatus = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['student_id'])) {
    $studentId = $_POST['student_id'];
    $scanType = $_POST['scan_type'] ?? '';
    
    $result = recordScan($studentId, $scanType);
    
    if ($result['success']) {
        $success = "{$result['student_name']} successfully scanned {$result['scan_type']}!";
        $currentStatus = $result['new_status'];
    } else {
        $error = $result['message'];
    }
}

// Get current status if student ID exists in POST
if (isset($_POST['student_id'])) {
    $currentStatus = getStudentStatus($_POST['student_id']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Classroom Attendance System</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            position: relative;
        }
        
        .admin-link {
            position: absolute;
            top: 20px;
            right: 20px;
            color: #3498db;
            text-decoration: none;
            font-weight: bold;
        }
        
        .logout-link {
            position: absolute;
            top: 20px;
            left: 20px;
            color: #e74c3c;
            text-decoration: none;
            font-weight: bold;
        }
        
        .admin-link:hover, .logout-link:hover {
            text-decoration: underline;
        }
        
        h1 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 30px;
        }
        
        .status-indicator {
            text-align: center;
            margin: 15px 0;
            padding: 10px;
            border-radius: 5px;
            font-weight: bold;
            font-size: 1.2em;
        }
        
        .status-in {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-out {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .scan-form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        
        label {
            font-weight: bold;
            color: #2c3e50;
        }
        
        input[type="text"] {
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        
        .scan-buttons {
            display: flex;
            gap: 10px;
        }
        
        button {
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            flex: 1;
            transition: all 0.3s;
        }
        
        button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        .scan-in {
            background-color: #2ecc71;
            color: white;
        }
        
        .scan-in:hover:not(:disabled) {
            background-color: #27ae60;
        }
        
        .scan-out {
            background-color: #e74c3c;
            color: white;
        }
        
        .scan-out:hover:not(:disabled) {
            background-color: #c0392b;
        }
        
        .result-message {
            margin-top: 20px;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
            font-weight: bold;
        }
        
        .success {
            background-color: #d4edda;
            color: #155724;
        }
        
        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .qr-scanner {
            margin: 30px 0;
            text-align: center;
        }
        
        #qr-video {
            width: 100%;
            max-width: 400px;
            border: 2px solid #3498db;
            border-radius: 5px;
        }
        
        .welcome-message {
            text-align: center;
            margin-bottom: 20px;
            color: #3498db;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="logout.php" class="logout-link">Logout</a>
        <a href="admin_dashboard.php" class="admin-link">Settings/Admin</a>
        
        <h1>Classroom Attendance System</h1>
        <div class="welcome-message">
            Welcome, <?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?>!
        </div>
        
        <?php if ($currentStatus): ?>
            <div class="status-indicator status-<?php echo $currentStatus; ?>">
                CURRENT STATUS: <?php echo strtoupper($currentStatus); ?> THE CLASSROOM
            </div>
        <?php endif; ?>
        
        <div class="qr-scanner">
            <video id="qr-video"></video>
            <p>Scan your QR code or enter student ID manually</p>
        </div>
        
        <form class="scan-form" method="POST" action="">
            <div class="form-group">
                <label for="student_id">Student ID</label>
                <input type="text" id="student_id" name="student_id" 
                       placeholder="Enter your student ID" required
                       value="<?php echo $_POST['student_id'] ?? ''; ?>">
            </div>
            
            <div class="scan-buttons">
                <button type="submit" name="scan_type" value="in" 
                        class="scan-in" <?php echo ($currentStatus === 'in') ? 'disabled' : ''; ?>>
                    SCAN IN
                </button>
                <button type="submit" name="scan_type" value="out" 
                        class="scan-out" <?php echo ($currentStatus === 'out') ? 'disabled' : ''; ?>>
                    SCAN OUT
                </button>
            </div>
        </form>
        
        <?php if ($error): ?>
            <div class="result-message error">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="result-message success">
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>
    </div>
    
    <script src="https://rawgit.com/sitepoint-editors/jsqrcode/master/src/qr_packed.js"></script>
    <script>
        const video = document.getElementById('qr-video');
        const studentIdInput = document.getElementById('student_id');
        
        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } })
                .then(function(stream) {
                    video.srcObject = stream;
                    video.play();
                    
                    const canvasElement = document.createElement('canvas');
                    const canvas = canvasElement.getContext('2d');
                    
                    function scanQRCode() {
                        if (video.readyState === video.HAVE_ENOUGH_DATA) {
                            canvasElement.height = video.videoHeight;
                            canvasElement.width = video.videoWidth;
                            canvas.drawImage(video, 0, 0, canvasElement.width, canvasElement.height);
                            
                            try {
                                const imageData = canvas.getImageData(0, 0, canvasElement.width, canvasElement.height);
                                const code = jsqrcode.decode(imageData);
                                
                                if (code) {
                                    studentIdInput.value = code;
                                    video.srcObject.getTracks().forEach(track => track.stop());
                                }
                            } catch (e) {
                                // QR code not found, continue scanning
                            }
                        }
                        
                        requestAnimationFrame(scanQRCode);
                    }
                    
                    scanQRCode();
                })
                .catch(function(err) {
                    console.error('Error accessing camera:', err);
                });
        }
    </script>
</body>
</html>