<?php
session_start();

// Agar admin login na ho to
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

// Admin details fetch
$adminID = $_SESSION['admin_id'];
$sql = "SELECT * FROM tbladmin WHERE ID = '$adminID'";
$result = $con->query($sql);
$admin = $result->fetch_assoc();

// Profile Update
if (isset($_POST['update'])) {
    $AdminName = $_POST['AdminName'];
    $UserName = $_POST['UserName'];
    $MobileNumber = $_POST['MobileNumber'];
    $Email = $_POST['Email'];

    $update = "UPDATE tbladmin SET 
                AdminName='$AdminName',
                UserName='$UserName',
                MobileNumber='$MobileNumber',
                Email='$Email'
               WHERE ID='$adminID'";

    if ($con->query($update)) {
        $_SESSION['success'] = "Profile updated successfully!";
        header("location: profile.php");
        exit();
    } else {
        $_SESSION['error'] = "Error updating profile!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Profile</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<link href="admin.css" rel="stylesheet">
</head>
<body class="bg-light">
    <?php include_once('sidebar.php');?>
    <?php include_once('header.php');?>

<div class="container mt-5">
  <div class="card shadow-lg p-4">
    <h3 class="mb-4">Admin Profile</h3>
    
    <?php
      if (isset($_SESSION['success'])) {
          echo "<div class='alert alert-success'>".$_SESSION['success']."</div>";
          unset($_SESSION['success']);
      }
      if (isset($_SESSION['error'])) {
          echo "<div class='alert alert-danger'>".$_SESSION['error']."</div>";
          unset($_SESSION['error']);
      }
    ?>

    <form method="POST">
      <div class="mb-3">
        <label class="form-label">Admin Name</label>
        <input type="text" name="AdminName" class="form-control" 
               value="<?php echo $admin['AdminName']; ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label">User Name</label>
        <input type="text" name="UserName" class="form-control" 
               value="<?php echo $admin['UserName']; ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Mobile Number</label>
        <input type="text" name="MobileNumber" class="form-control" 
               value="<?php echo $admin['MobileNumber']; ?>">
      </div>
      <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="Email" class="form-control" 
               value="<?php echo $admin['Email']; ?>" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Registered Date</label>
        <input type="text" class="form-control" 
               value="<?php echo $admin['AdminRegdate']; ?>" readonly>
      </div>

      <button type="submit" name="update" class="btn btn-primary">Update Profile</button>
      <a href="dashboard.php" class="btn btn-secondary">Back</a>
    </form>
  </div>
</div>
<?php include("dashboardjs.php"); ?>
</body>
</html>
