<?php
session_start();
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "studentms1";

$con = new mysqli($host, $user, $pass, $dbname);
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

if(isset($_POST['login'])){
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM staff WHERE username='$username'";
    $result = $con->query($sql);

    if($result->num_rows > 0){
        $row = $result->fetch_assoc();
        if(password_verify($password, $row['password'])){
            $_SESSION['staff_id'] = $row['staff_id'];
            $_SESSION['staff_name'] = $row['name'];
            header("Location: staff-dashboard.php");
            exit();
        } else {
            echo "<script>alert('❌ Invalid Password');</script>";
        }
    } else {
        echo "<script>alert('❌ Username not found');</script>";
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
  <style>
    body {
      background-color: #f8f9fa;
    }
    .auth-form {
      background-color: #ffffff;
      border-radius: 0.5rem;
      padding: 2rem;
      box-shadow: 0 0 15px rgba(0,0,0,0.1);
    }
    .brand-logo {
      font-weight: bold;
      font-size: 1.3rem;
      margin-bottom: 1rem;
      text-align: center;
    }
    .btn-login {
      background-color: #198754; /* Bootstrap success color */
      color: #fff;
    }
    .btn-login:hover {
      background-color: #157347;
      color: #fff;
    }
      input::placeholder {
    font-size: 1rem;
    color: #6c757d;
  }
  </style>
</head>
<body>
  <div class="container vh-100 d-flex justify-content-center align-items-center">
    <div class="col-12 col-sm-10 col-md-6 col-lg-4">
      <div class="auth-form">
        <div class="brand-logo">Student Management System</div>
        <h6 class="text-center text-muted mb-4">Sign in using Staff ID</h6>
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
