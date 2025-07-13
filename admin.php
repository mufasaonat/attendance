<?php
require_once 'functions.php';

// Authentication check (simple example - implement proper auth in production)
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit;
}

// Handle student deletion
if (isset($_GET['delete_student'])) {
    $studentId = $_GET['delete_student'];
    $stmt = $pdo->prepare("DELETE FROM students WHERE student_id = ?");
    $stmt->execute([$studentId]);
    $deleteMessage = "Student $studentId deleted successfully";
}

// Handle record deletion
if (isset($_GET['delete_record'])) {
    $recordId = $_GET['delete_record'];
    $stmt = $pdo->prepare("DELETE FROM attendance WHERE id = ?");
    $stmt->execute([$recordId]);
    $deleteMessage = "Record $recordId deleted successfully";
}

// Get all students
$stmt = $pdo->query("SELECT * FROM students ORDER BY last_name, first_name");
$students = $stmt->fetchAll();

// Get all attendance records (paginated)
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 50;
$offset = ($page - 1) * $limit;

$stmt = $pdo->prepare("
    SELECT a.*, s.first_name, s.last_name 
    FROM attendance a
    JOIN students s ON a.student_id = s.student_id
    ORDER BY a.scan_time DESC
    LIMIT ? OFFSET ?
");
$stmt->bindValue(1, $limit, PDO::PARAM_INT);
$stmt->bindValue(2, $offset, PDO::PARAM_INT);
$stmt->execute();
$records = $stmt->fetchAll();

// Get total records for pagination
$stmt = $pdo->query("SELECT COUNT(*) FROM attendance");
$totalRecords = $stmt->fetchColumn();
$totalPages = ceil($totalRecords / $limit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Classroom Attendance</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
            color: #333;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        header {
            background-color: #2c3e50;
            color: white;
            padding: 20px;
            margin-bottom: 30px;
            border-radius: 5px;
        }
        
        h1, h2 {
            margin: 0;
        }
        
        .logout-btn {
            float: right;
            color: white;
            text-decoration: none;
            background-color: #e74c3c;
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 14px;
        }
        
        .logout-btn:hover {
            background-color: #c0392b;
        }
        
        .tabs {
            display: flex;
            margin-bottom: 20px;
            border-bottom: 1px solid #ddd;
        }
        
        .tab {
            padding: 10px 20px;
            cursor: pointer;
            background-color: #f1f1f1;
            margin-right: 5px;
            border-radius: 5px 5px 0 0;
        }
        
        .tab.active {
            background-color: #3498db;
            color: white;
        }
        
        .tab-content {
            display: none;
            background-color: white;
            padding: 20px;
            border-radius: 0 5px 5px 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        
        .tab-content.active {
            display: block;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        th {
            background-color: #3498db;
            color: white;
        }
        
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        tr:hover {
            background-color: #f1f1f1;
        }
        
        .action-btn {
            padding: 5px 10px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            color: white;
            text-decoration: none;
            font-size: 12px;
        }
        
        .delete-btn {
            background-color: #e74c3c;
        }
        
        .delete-btn:hover {
            background-color: #c0392b;
        }
        
        .view-btn {
            background-color: #3498db;
        }
        
        .view-btn:hover {
            background-color: #2980b9;
        }
        
        .add-student-form {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        input[type="text"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 3px;
            box-sizing: border-box;
        }
        
        .submit-btn {
            background-color: #2ecc71;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 3px;
            cursor: pointer;
        }
        
        .submit-btn:hover {
            background-color: #27ae60;
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }
        
        .pagination a {
            color: #3498db;
            padding: 8px 16px;
            text-decoration: none;
            border: 1px solid #ddd;
            margin: 0 4px;
        }
        
        .pagination a.active {
            background-color: #3498db;
            color: white;
            border: 1px solid #3498db;
        }
        
        .pagination a:hover:not(.active) {
            background-color: #ddd;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        
        .alert-success {
            background-color: #dff0d8;
            color: #3c763d;
        }
        
        .stats-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            flex: 1;
            min-width: 200px;
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #3498db;
            margin: 10px 0;
        }
        
        .stat-label {
            color: #777;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Classroom Attendance System</h1>
            <h2>Admin Control Panel</h2>
            <a href="logout.php" class="logout-btn">Logout</a>
            <div class="clearfix"></div>
        </header>
        
        <?php if (isset($deleteMessage)): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($deleteMessage); ?>
            </div>
        <?php endif; ?>
        
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-label">Total Students</div>
                <div class="stat-value"><?php echo count($students); ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Total Records</div>
                <div class="stat-value"><?php echo $totalRecords; ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Currently In</div>
                <div class="stat-value">
                    <?php 
                    $stmt = $pdo->query("SELECT COUNT(*) FROM students WHERE current_status = 'in'");
                    echo $stmt->fetchColumn();
                    ?>
                </div>
            </div>
        </div>
        
        <div class="tabs">
            <div class="tab active" onclick="openTab('students')">Students</div>
            <div class="tab" onclick="openTab('attendance')">Attendance Records</div>
            <div class="tab" onclick="openTab('add-student')">Add Student</div>
        </div>
        
        <div id="students" class="tab-content active">
            <h3>Student Management</h3>
            <table>
                <thead>
                    <tr>
                        <th>Student ID</th>
                        <th>Name</th>
                        <th>Current Status</th>
                        <th>LINE ID</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $student): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                        <td><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></td>
                        <td>
                            <span style="color: <?php echo $student['current_status'] == 'in' ? 'green' : 'red'; ?>">
                                <?php echo strtoupper($student['current_status']); ?>
                            </span>
                        </td>
                        <td><?php echo $student['line_user_id'] ? 'Connected' : 'Not connected'; ?></td>
                        <td>
                            <a href="admin.php?delete_student=<?php echo $student['student_id']; ?>" 
                               class="action-btn delete-btn"
                               onclick="return confirm('Are you sure you want to delete this student?')">
                                Delete
                            </a>
                            <a href="student_details.php?id=<?php echo $student['student_id']; ?>" 
                               class="action-btn view-btn">
                                View Details
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <div id="attendance" class="tab-content">
            <h3>Attendance Records</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Student ID</th>
                        <th>Name</th>
                        <th>Scan Type</th>
                        <th>Scan Time</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($records as $record): ?>
                    <tr>
                        <td><?php echo $record['id']; ?></td>
                        <td><?php echo htmlspecialchars($record['student_id']); ?></td>
                        <td><?php echo htmlspecialchars($record['first_name'] . ' ' . $record['last_name']); ?></td>
                        <td>
                            <span style="color: <?php echo $record['scan_type'] == 'in' ? 'green' : 'red'; ?>">
                                <?php echo strtoupper($record['scan_type']); ?>
                            </span>
                        </td>
                        <td><?php echo $record['scan_time']; ?></td>
                        <td>
                            <a href="admin.php?delete_record=<?php echo $record['id']; ?>" 
                               class="action-btn delete-btn"
                               onclick="return confirm('Are you sure you want to delete this record?')">
                                Delete
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <div class="pagination">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="admin.php?page=<?php echo $i; ?>" <?php echo $i == $page ? 'class="active"' : ''; ?>>
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
            </div>
        </div>
        
        <div id="add-student" class="tab-content">
            <h3>Add New Student</h3>
            <div class="add-student-form">
                <form action="add_student.php" method="POST">
                    <div class="form-group">
                        <label for="student_id">Student ID</label>
                        <input type="text" id="student_id" name="student_id" required>
                    </div>
                    <div class="form-group">
                        <label for="first_name">First Name</label>
                        <input type="text" id="first_name" name="first_name" required>
                    </div>
                    <div class="form-group">
                        <label for="last_name">Last Name</label>
                        <input type="text" id="last_name" name="last_name" required>
                    </div>
                    <div class="form-group">
                        <label for="line_user_id">LINE User ID (optional)</label>
                        <input type="text" id="line_user_id" name="line_user_id">
                    </div>
                    <button type="submit" class="submit-btn">Add Student</button>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        function openTab(tabName) {
            // Hide all tab contents
            const tabContents = document.getElementsByClassName('tab-content');
            for (let i = 0; i < tabContents.length; i++) {
                tabContents[i].classList.remove('active');
            }
            
            // Remove active class from all tabs
            const tabs = document.getElementsByClassName('tab');
            for (let i = 0; i < tabs.length; i++) {
                tabs[i].classList.remove('active');
            }
            
            // Show the selected tab content and mark tab as active
            document.getElementById(tabName).classList.add('active');
            event.currentTarget.classList.add('active');
        }
        
        // Simple search functionality
        function searchTable(tableId, inputId) {
            const input = document.getElementById(inputId);
            const filter = input.value.toUpperCase();
            const table = document.getElementById(tableId);
            const rows = table.getElementsByTagName('tr');
            
            for (let i = 1; i < rows.length; i++) {
                let found = false;
                const cells = rows[i].getElementsByTagName('td');
                
                for (let j = 0; j < cells.length; j++) {
                    const cell = cells[j];
                    if (cell) {
                        if (cell.textContent.toUpperCase().indexOf(filter) > -1) {
                            found = true;
                            break;
                        }
                    }
                }
                
                rows[i].style.display = found ? '' : 'none';
            }
        }
    </script>
</body>
</html>