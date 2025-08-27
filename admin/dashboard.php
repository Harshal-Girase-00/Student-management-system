<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
  header("location: admin.php");
  exit();
}
include('../includes/dbconnection.php'); // PDO connection

// Fetch totals dynamically
// Total Staff
$stmt = $dbh->prepare("SELECT COUNT(staff_id) as total FROM staff");
$stmt->execute();
$totalStaff = $stmt->fetch(PDO::FETCH_OBJ)->total;

// Total Students
$stmt = $dbh->prepare("SELECT COUNT(student_id) as total FROM students");
$stmt->execute();
$totalStudent = $stmt->fetch(PDO::FETCH_OBJ)->total;

// Total Classes
$stmt = $dbh->prepare("SELECT COUNT(id) as total FROM addclass");
$stmt->execute();
$totalClass = $stmt->fetch(PDO::FETCH_OBJ)->total;

// Total Notices
$stmt = $dbh->prepare("SELECT COUNT(id) as total FROM notices");
$stmt->execute();
$totalNotice = $stmt->fetch(PDO::FETCH_OBJ)->total;
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link href="admin.css" rel="stylesheet">

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
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
      padding: 15px;
      text-align: center};

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

      .green-bg {
        background-color: #28a745;
      }

      .red-bg {
        background-color: #dc3545;
      }

      .yellow-bg {
        background-color: #ffc107;
      }

      .teal-bg {
        background-color: #17a2b8;
      }

      .icon-white {
        color: #fff;
        font-size: 1.5rem;
      }

      .footer {
        margin-top: 50px;
        text-align: left;
        color: #6c757d;
        font-size: 0.8rem;
      }
  </style>
</head>

<body>


  <!-- Sidebar -->

  <?php include("sidebar.php"); ?>
  <!-- Main Content -->
  <?php include("header.php"); ?>

  <!-- Report Summary -->
  <div class="main-content">
    <h6>Report Summary</h6>
    <div class="row g-4">
<!-- Total Staff -->
<div class="col-md-3">
  <div class="card">
    <div class="card-icon green-bg"><i class="bi-people icon-white"></i></div>
    <div class="card-title">Total Staff</div>
    <div class="card-text"><?php echo $totalStaff; ?></div>
    <a href="view-staff.php" class="card-link">View staff</a>
  </div>
</div>

<!-- Total Students -->
<div class="col-md-3">
  <div class="card">
    <div class="card-icon red-bg"><i class="bi-person-badge icon-white"></i></div>
    <div class="card-title">Total Students</div>
    <div class="card-text"><?php echo $totalStudent; ?></div>
    <a href="view-students.php" class="card-link">View Students</a>
  </div>
</div>

<!-- Total Classes -->
<div class="col-md-3">
  <div class="card">
    <div class="card-icon yellow-bg"><i class="bi-journal-bookmark icon-white"></i></div>
    <div class="card-title">Total Class </div>
    <div class="card-text"><?php echo $totalClass; ?></div>
    <a href="add-class.php" class="card-link">Add Class</a>
  </div>
</div>

<!-- Total Notices -->
<div class="col-md-3">
  <div class="card">
    <div class="card-icon teal-bg"><i class="bi-file-earmark-text icon-white"></i></div>
    <div class="card-title">Total Notice</div>
    <div class="card-text"><?php echo $totalNotice; ?></div>
    <a href="view-notice.php" class="card-link">View Notices</a>
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


  <?php include("dashboardjs.php"); ?>
</body>

</html>