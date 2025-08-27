<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('../includes/dbconnection.php');

if(isset($_POST['submit']))
  {
    $email=$_POST['email'];
$mobile=$_POST['mobile'];
$newpassword=md5($_POST['newpassword']);
  $sql ="SELECT Email FROM tbladmin WHERE Email=:email and MobileNumber=:mobile";
$query= $dbh -> prepare($sql);
$query-> bindParam(':email', $email, PDO::PARAM_STR);
$query-> bindParam(':mobile', $mobile, PDO::PARAM_STR);
$query-> execute();
$results = $query -> fetchAll(PDO::FETCH_OBJ);
if($query -> rowCount() > 0)
{
$con="update tbladmin set Password=:newpassword where Email=:email and MobileNumber=:mobile";
$chngpwd1 = $dbh->prepare($con);
$chngpwd1-> bindParam(':email', $email, PDO::PARAM_STR);
$chngpwd1-> bindParam(':mobile', $mobile, PDO::PARAM_STR);
$chngpwd1-> bindParam(':newpassword', $newpassword, PDO::PARAM_STR);
$chngpwd1->execute();
echo "<script>alert('Your Password succesfully changed');</script>";
}
else {
echo "<script>alert('Email id or Mobile no is invalid');</script>"; 
}
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Forgot Password | Student Management System</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f0f2f5;
    }
    .auth-card {
      max-width: 400px;
      background: #fff;
      padding: 2rem;
      border-radius: 0.5rem;
      box-shadow: 0 0 15px rgba(0,0,0,0.1);
    }
    .btn-reset {
      background-color: #28a745;
      color: white;
    }
    .btn-reset:hover {
      background-color: #218838;
      color: white;
    }
    .btn-home {
      background-color: #3b5998;
      color: white;
    }
    .btn-home:hover {
      background-color: #2d4373;
      color: white;
    }
  </style>
</head>
<body>
  <div class="container vh-100 d-flex justify-content-center align-items-center">
    <div class="auth-card">
      <h5 class="text-center fw-bold">Student Management System</h5>
      <h6 class="text-center text-muted mb-4">RECOVER PASSWORD</h6>
      <p class="text-center small text-secondary">Enter your email address and mobile number to reset password!</p>
      <form method="post">
        <div class="mb-3">
          <input type="email" class="form-control" placeholder="Email Address" name="email" required>
        </div>
        <div class="mb-3">
          <input type="text" class="form-control" placeholder="Mobile Number" name="mobile" required>
        </div>
        <div class="mb-3">
          <input type="password" class="form-control" placeholder="New Password" name="newpassword" required>
        </div>
        <div class="mb-3">
          <input type="password" class="form-control" placeholder="Confirm Password" name="confirmpassword" required>
        </div>
        <div class="d-grid mb-2">
          <button type="submit" name="submit" class="btn btn-reset">Reset</button>
        </div>
        <div class="d-grid">
          <a href="admin.php" class="btn btn-home">Back Login page</a>
        </div>
      </form>
    </div>
  </div>
  </body>
</html>
