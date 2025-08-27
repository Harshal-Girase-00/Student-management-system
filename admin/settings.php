<?php
session_start();

// Admin login check
if (!isset($_SESSION['admin_id'])) {
  header("location: ../index.php");
  exit();
}

// Database connection
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "studentms1";

$con = new mysqli($host, $user, $pass, $dbname);
if ($con->connect_error) {
  die("Connection failed: " . $con->connect_error);
}

// Fetch admin info
$adminID = $_SESSION['admin_id'];
$sql = "SELECT * FROM tbladmin WHERE ID = '$adminID'";
$result = $con->query($sql);
$admin = $result->fetch_assoc();

// Handle password change
if (isset($_POST['changepass'])) {
  $current = trim($_POST['currentpassword']);
  $newpass = trim($_POST['newpassword']);
  $confirmpass = trim($_POST['confirmpassword']);

  if ($newpass !== $confirmpass) {
    $_SESSION['error'] = "New password and confirm password do not match!";
  } else {
    // Verify current password
    if (md5($current) === $admin['Password']) { // keeping md5 for backward compatibility
      $hashedNewPass = md5($newpass);
      $update = "UPDATE tbladmin SET Password='$hashedNewPass' WHERE ID='$adminID'";
      if ($con->query($update)) {
        $_SESSION['success'] = "Password changed successfully!";
      } else {
        $_SESSION['error'] = "Error updating password!";
      }
    } else {
      $_SESSION['error'] = "Current password is incorrect!";
    }
  }
  header("location: settings.php");
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Change Password | Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link href="admin.css" rel="stylesheet">
  
</head>

<body>

  <?php include("sidebar.php"); ?>
  <?php include("header.php"); ?>

  <div class="container">
    <div class="card shadow-lg p-4">
      <h3 class="mb-4">Change Password</h3>

      <?php
      if (isset($_SESSION['success'])) {
        echo "<div class='alert alert-success'>" . $_SESSION['success'] . "</div>";
        unset($_SESSION['success']);
      }
      if (isset($_SESSION['error'])) {
        echo "<div class='alert alert-danger'>" . $_SESSION['error'] . "</div>";
        unset($_SESSION['error']);
      }
      ?>

      <form method="POST">
        <div class="mb-3">
          <label class="form-label">Current Password</label>
          <input type="password" class="form-control" name="currentpassword" required>
        </div>
        <div class="mb-3">
          <label class="form-label">New Password</label>
          <input type="password" class="form-control" name="newpassword" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Confirm New Password</label>
          <input type="password" class="form-control" name="confirmpassword" required>
        </div>
        <button type="submit" name="changepass" class="btn btn-success">Change Password</button>
        <a href="dashboard.php" class="btn btn-secondary">Back</a>
      </form>
    </div>
  </div>

<?php include("dashboardjs.php"); ?>
</body>

</html>