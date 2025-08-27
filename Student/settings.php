<?php
session_start();

// Database connection
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "studentms1";

$con = new mysqli($host, $user, $pass, $dbname);
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// Check student login
if (!isset($_SESSION['student_id'])) {
    header("location: login.php");
    exit();
}

$studentID = $_SESSION['student_id'];
$message = "";

// Handle password change
if (isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Fetch current password from database
    $stmt = $con->prepare("SELECT password FROM students WHERE student_id=?");
    $stmt->bind_param("s", $studentID);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();

    if ($student && password_verify($current_password, $student['password'])) {
        if ($new_password === $confirm_password) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update = $con->prepare("UPDATE students SET password=? WHERE student_id=?");
            $update->bind_param("ss", $hashed_password, $studentID);
            if ($update->execute()) {
                $message = '<div class="alert alert-success">Password changed successfully!</div>';
            } else {
                $message = '<div class="alert alert-danger">Failed to update password. Try again.</div>';
            }
            $update->close();
        } else {
            $message = '<div class="alert alert-warning">New password and confirm password do not match.</div>';
        }
    } else {
        $message = '<div class="alert alert-danger">Current password is incorrect.</div>';
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Change Password | Student Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<link href="student.css" rel="stylesheet">
</head>
<body>

<?php include("student-sidebar.php"); ?>
<?php include("student-header.php"); ?>

<div class="container-fluid my-4">
    <div class="card p-4 shadow-sm mx-auto" >
        <h3 class="mb-4 text-center text-primary">🔒 Change Password</h3>

        <?php if($message) echo $message; ?>

        <form method="post">
            <div class="mb-3">
                <label class="form-label">Current Password</label>
                <input type="password" class="form-control" name="current_password" required>
            </div>
            <div class="mb-3">
                <label class="form-label">New Password</label>
                <input type="password" class="form-control" name="new_password" required minlength="6">
            </div>
            <div class="mb-3">
                <label class="form-label">Confirm New Password</label>
                <input type="password" class="form-control" name="confirm_password" required minlength="6">
            </div>
            <div class="d-grid gap-2">
                <button type="submit" name="change_password" class="btn btn-success btn-lg">Change Password</button>
                <a href="student-dashboard.php" class="btn btn-outline-secondary btn-lg">🏠 Back to Dashboard</a>
            </div>
        </form>
    </div>
</div>
<?php include("student-dashboard-js.php"); ?>
</body>
</html>

<?php
$con->close();
?>
