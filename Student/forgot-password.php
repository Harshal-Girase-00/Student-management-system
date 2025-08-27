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

$message = "";

// Handle form submission
if (isset($_POST['reset'])) {
    $username = $con->real_escape_string($_POST['username']);
    $phone = $con->real_escape_string($_POST['phone']); // new field
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        $message = "<div class='alert alert-danger'>Passwords do not match!</div>";
    } else {
        // Check if user exists with username + phone
        $stmt = $con->prepare("SELECT * FROM students WHERE username=? AND phone=?");
        $stmt->bind_param("ss", $username, $phone);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $message = "<div class='alert alert-danger'>Username or phone number not found!</div>";
        } else {
            // Hash new password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt_update = $con->prepare("UPDATE students SET password=? WHERE username=? AND phone=?");
            $stmt_update->bind_param("sss", $hashed_password, $username, $phone);

            if ($stmt_update->execute()) {
                $message = "<div class='alert alert-success'>Password reset successfully! You can now login.</div>";
            } else {
                $message = "<div class='alert alert-danger'>Error updating password: " . $con->error . "</div>";
            }
            $stmt_update->close();
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Forgot Password | SMS</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    body { background-color: #f8f9fa; }
    .forgot-form { background: #fff; padding: 2rem; border-radius: 0.5rem; box-shadow: 0 0 15px rgba(0,0,0,0.1); max-width: 450px; margin:auto; margin-top:50px; }
    .form-title { text-align:center; margin-bottom:1.5rem; font-weight:bold; color:#dc3545; }
</style>
</head>
<body>
<div class="forgot-form">
    <h3 class="form-title">🔑 Forgot Password</h3>

    <?php if(!empty($message)) echo $message; ?>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" class="form-control" name="username" placeholder="Enter your username" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Registered Phone Number</label>
            <input type="text" class="form-control" name="phone" placeholder="Enter your phone number" required>
        </div>
        <div class="mb-3">
            <label class="form-label">New Password</label>
            <input type="password" class="form-control" name="new_password" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Confirm Password</label>
            <input type="password" class="form-control" name="confirm_password" required>
        </div>
        <div class="d-grid gap-2">
            <button type="submit" name="reset" class="btn btn-danger">Reset Password</button>
            <a href="student.php" class="btn btn-outline-primary">Back to Login</a>
        </div>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
