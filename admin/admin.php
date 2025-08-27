<?php
session_start();
include('../includes/dbconnection.php'); // PDO connection

if (isset($_POST['login'])) {
    $uname = trim($_POST['username']);
    $password = md5(trim($_POST['password'])); // MD5 hash

    $stmt = $dbh->prepare("SELECT ID FROM tbladmin WHERE UserName=:uname AND Password=:password");
    $stmt->bindParam(':uname', $uname, PDO::PARAM_STR);
    $stmt->bindParam(':password', $password, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_OBJ);

    if ($result) {
        $_SESSION['admin_id'] = $result->ID;
        header("Location: dashboard.php");
        exit();
    } else {
        echo "<script>alert('Invalid Details');</script>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login | Student Management System</title>

  <!-- Bootstrap 5 CSS -->
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link href="admin.css" rel="stylesheet">
</head>
<body>
  <div class="container vh-100 d-flex justify-content-center align-items-center">
    <div class="col-12 col-sm-10 col-md-6 col-lg-4">
      <div class="auth-form">
        <div class="brand-logo">Student Management System</div>
        <h6 class="text-center text-muted mb-4">Sign in using Admin ID</h6>
        <form id="login" method="post" name="login">
          <div class="mb-3">
            <input type="text" class="form-control form-control-lg"  placeholder="Enter your username" required name="username">
          </div>
          <div class="mb-3">
            <input type="password" class="form-control form-control-lg" placeholder="Enter your password" required name="password">
          </div>
          <div class="d-grid mb-3">
            <button class="btn btn-login btn-lg" type="submit" name="login">Login</button>
          </div>
          <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" value="" id="remember" name="remember">
              <label class="form-check-label" for="remember">
                Keep me signed in
              </label>
            </div>
            <a href="forgot-password.php" class="text-decoration-none">Forgot password?</a>
          </div>
          <div class="d-grid">
            <a href="../index.php" class="btn btn-outline-primary">🏠 Back Home</a>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Bootstrap 5 JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
