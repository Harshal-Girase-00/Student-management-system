<?php
session_start();
if (!isset($_SESSION['staff_id'])) {
    header("location: staff-login.php");
    exit();
}

include('../includes/dbconnection.php'); // PDO connection

// Total Students
$stmt = $dbh->prepare("SELECT COUNT(student_id) as total FROM students");
$stmt->execute();
$totalStudents = $stmt->fetch(PDO::FETCH_OBJ)->total;

// Total Notices
$stmt = $dbh->prepare("SELECT COUNT(id) as total FROM notices");
$stmt->execute();
$totalNotices = $stmt->fetch(PDO::FETCH_OBJ)->total;

// Total Notes
$stmt = $dbh->prepare("SELECT COUNT(id) as total FROM notes");
$stmt->execute();
$totalNotes = $stmt->fetch(PDO::FETCH_OBJ)->total;

// Total Homeworks
$stmt = $dbh->prepare("SELECT COUNT(id) as total FROM homework");
$stmt->execute();
$totalHomeworks = $stmt->fetch(PDO::FETCH_OBJ)->total;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMS Staff Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="staff.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f4f8;
            font-family: Arial, sans-serif;
        }

        .main-content {
            background-color: #e9ecef;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
        }
        .card {
            border: none;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            padding: 15px;
            text-align: center;
        }
        .card-icon {
            width: 60px;
            height: 60px;
            margin: 0 auto 10px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card-title {
            font-size: 1.1rem;
            color: #6c757d;
            margin-bottom: 5px;
        }
        .card-text {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .card-link {
            color: #6f42c1;
            text-decoration: none;
            font-size: 0.9rem;
        }
        .card-link:hover {
            text-decoration: underline;
        }
        .green-bg { background-color: #28a745; }
        .red-bg { background-color: #dc3545; }
        .yellow-bg { background-color: #ffc107; }
        .teal-bg { background-color: #17a2b8; }
        .icon-white { color: #fff; font-size: 1.5rem; }
        .footer {
            margin-top: 50px;
            text-align: left;
            color: #6c757d;
            font-size: 0.8rem;
        }
    </style>
</head>
<body>

            <!-- Main Content -->
             <?php include 'staff-sidebar.php'; ?>
            <?php include 'staff-header.php'; ?>
            
                <div class="main-content">
                    <h6>Report Summary</h6>
                    <div class="row g-4">
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-icon red-bg"><i class="bi bi-person icon-white"></i></div>
                                <div class="card-title">Total Students</div>
                                <div class="card-text"><?php echo $totalStudents; ?></div>
                                <a href="view-student.php" class="card-link">View Students</a>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-icon yellow-bg"><i class="bi bi-file-earmark-text icon-white"></i></div>
                                <div class="card-title">Total Notices</div>
                                <div class="card-text"><?php echo $totalNotices; ?></div>
                                <a href="view-notice.php" class="card-link"> Notices</a>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-icon teal-bg"><i class="bi bi-file-earmark-text icon-white"></i></div>
                                <div class="card-title">Total Notes </div>
                                <div class="card-text"><?php echo $totalNotes; ?></div>
                                <a href="manage-notes.php" class="card-link">Manage notes</a>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-icon green-bg"><i class="bi bi-file-earmark-text icon-white"></i></div>
                                <div class="card-title">Total Homeworks</div>
                                <div class="card-text"><?php echo $totalHomeworks; ?></div>
                                <a href="manage-homework.php" class="card-link">Manage Homework</a>
                            </div>
                        </div>
                    </div>
                </div>
                <footer class="footer p-4">
                    <small>Student Management System</small>
                </footer>
            </main>
        </div>
    </div>

<?php include 'staff-dashboard-js.php'; ?>
</body>
</html>

