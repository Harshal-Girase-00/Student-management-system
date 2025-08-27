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

if (!isset($_SESSION['staff_id'])) {
  header("location: staff-login.php");
  exit();
}

$staffID = $_SESSION['staff_id'];

// Fetch staff details with debugging
$sql = "SELECT name, email, phone, username FROM staff WHERE staff_id = ?";
$stmt = $con->prepare($sql);
if ($stmt === false) {
  die("Prepare failed: " . $con->error);
}
$stmt->bind_param("s", $staffID);
$stmt->execute();
$result = $stmt->get_result();
$staff = $result->fetch_object();

if (!$staff) {
  echo "<script>alert('Staff not found for staff_id: $staffID. Check database or session.'); window.location='staff-dashboard.php';</script>";
  exit();
}

// Update staff details
if (isset($_POST['update'])) {
  $name = trim($_POST['name']);
  $username = trim($_POST['username']);
  $email = trim($_POST['email']);
  $phone = trim($_POST['phone']);

  $update_sql = "UPDATE staff SET name = ?, username = ?, email = ?, phone = ? WHERE staff_id = ?";
  $stmt = $con->prepare($update_sql);
  if ($stmt === false) {
    die("Prepare failed: " . $con->error);
  }
  $stmt->bind_param("sssss", $name, $username, $email, $phone, $staffID);

  if ($stmt->execute()) {
    echo "<script>alert('Details successfully updated'); window.location='profile.php';</script>";
  } else {
    echo "<script>alert('Update failed: ' . $con->error);</script>";
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Staff Profile | Admin Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link href="staff.css" rel="stylesheet">
 
</head>

<body>
  <!-- Sidebar -->
  <?php include("staff-sidebar.php"); ?>
  <?php include("staff-header.php"); ?>

  <!-- Profile Form -->
  <div class="container-fluid">
    <div class="card p-4 shadow-sm">
      <form method="post">
        <div class="mb-3">
          <label class="form-label">Full Name</label>
          <input type="text" class="form-control" name="name" value="<?php echo htmlentities($staff->name); ?>" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Username</label>
          <input type="text" class="form-control" name="username" value="<?php echo htmlentities($staff->username); ?>" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Email</label>
          <input type="email" class="form-control" name="email" value="<?php echo htmlentities($staff->email); ?>" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Mobile Number</label>
          <input type="text" class="form-control" name="phone" value="<?php echo htmlentities($staff->phone); ?>" required pattern="[0-9]{10}" title="Please enter a 10-digit number">
        </div>
        <button type="submit" name="update" class="btn btn-success">Update Details</button>
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
<?php
$con->close();
?>