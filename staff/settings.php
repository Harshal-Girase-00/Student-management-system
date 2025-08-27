<?php
session_start();
include('../includes/dbconnection.php'); // PDO connection

if (!isset($_SESSION['staff_id'])) {
  header("location: staff-login.php");
  exit();
}

$staffID = $_SESSION['staff_id'];

// Fetch staff info (optional, for display)
$stmt = $dbh->prepare("SELECT name FROM staff WHERE staff_id = :id");
$stmt->bindParam(':id', $staffID, PDO::PARAM_STR);
$stmt->execute();
$staff = $stmt->fetch(PDO::FETCH_OBJ);

// Handle password change
if (isset($_POST['changepass'])) {
  $current = trim($_POST['currentpassword']);
  $newpass = trim($_POST['newpassword']);
  $confirmpass = trim($_POST['confirmpassword']);

  if ($newpass !== $confirmpass) {
    echo "<script>alert('New password and confirm password do not match');</script>";
  } else {
    // Fetch current hashed password from DB
    $check = $dbh->prepare("SELECT password FROM staff WHERE staff_id = :id");
    $check->bindParam(':id', $staffID, PDO::PARAM_STR);
    $check->execute();
    $row = $check->fetch(PDO::FETCH_ASSOC);

    if ($row && password_verify($current, $row['password'])) {
      // Hash new password securely
      $hashedNewPass = password_hash($newpass, PASSWORD_BCRYPT);

      // Update password
      $update = $dbh->prepare("UPDATE staff SET password = :newpass WHERE staff_id = :id");
      $update->bindParam(':newpass', $hashedNewPass, PDO::PARAM_STR);
      $update->bindParam(':id', $staffID, PDO::PARAM_STR);
      $update->execute();

      echo "<script>alert('✅ Password successfully changed');</script>";
    } else {
      echo "<script>alert('❌ Current password is incorrect');</script>";
    }
  }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Settings | Staff</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link href="staff.css" rel="stylesheet">
  
</head>

<body>

  <?php include("staff-sidebar.php"); ?>

  <?php include("staff-header.php"); ?>

  <!-- Change Password Form -->
  <div class="container-fluid">
    <div class="card p-4 shadow-sm">
      <form method="post">
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
      </form>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    const toggleBtn = document.getElementById("toggleBtn");
    const sidebar = document.getElementById("sidebar");
    const overlay = document.getElementById("overlay");

    toggleBtn.addEventListener("click", () => {
      sidebar.classList.toggle("active");
      overlay.classList.toggle("active");
    });

    overlay.addEventListener("click", () => {
      sidebar.classList.remove("active");
      overlay.classList.remove("active");
    });
  </script>
</body>

</html>