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
$con->set_charset("utf8mb4");

// Check if username is provided
if (!isset($_GET['username'])) {
    echo "No student selected!";
    exit();
}

$username = $_GET['username'];

// Fetch student data
$stmt = $con->prepare("SELECT * FROM students WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows == 0) {
    echo "Student not found!";
    exit();
}
$student = $result->fetch_assoc();
$stmt->close();

// Function to capitalize first letter safely
function capitalizeName($name) {
    return $name ? ucfirst(strtolower($name)) : '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>View Student Profile</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<link href="admin.css" rel="stylesheet">
<style>
.content { padding: 20px; }
.profile-table td { padding: 12px; border: 1px solid #ddd; }
.profile-table tr:nth-child(odd) td { background-color: #e6e6fa; }
.profile-table tr:nth-child(even) td { background-color: #f0e68c; }
/* Mobile adjustments */
@media (max-width: 768px) {
  .content { margin-left: 0; padding: 10px; }
  .profile-table td { padding: 8px; font-size: 14px; }
}
</style>
</head>
<body>
<?php include("sidebar.php"); ?>
<?php include("header.php"); ?>

<div class="content" style="margin:0">
    <h3 class="text-center text-primary">Student Full Details</h3>

    <div class="table-responsive">
      <table class="table profile-table">
        <tr>
          <td>Student Name</td>
          <td>
            <?= htmlspecialchars(
                capitalizeName($student['first_name']).' '.
                capitalizeName($student['middle_name']).' '.
                capitalizeName($student['last_name'])
            ) ?>
          </td>
          <td>Student ID</td>
          <td><?= htmlspecialchars($student['username']) ?></td>
        </tr>
        <tr>
          <td>Class</td>
          <td><?= htmlspecialchars($student['class']) ?></td>
          <td>Division</td>
          <td><?= htmlspecialchars($student['division']) ?></td>
        </tr>
        <tr>
          <td>Date of Birth</td>
          <td><?= htmlspecialchars($student['dob']) ?></td>
          <td>Age</td>
          <td><?= htmlspecialchars($student['age']) ?></td>
        </tr>
        <tr>
          <td>Mobile Number</td>
          <td><?= htmlspecialchars($student['phone'] ?? 'N/A') ?></td>
          <td>Parent Mobile</td>
          <td><?= htmlspecialchars($student['parent_phone'] ?? 'N/A') ?></td>
        </tr>
        <tr>
          <td>Gender</td>
          <td><?= htmlspecialchars($student['gender'] ?? 'N/A') ?></td>
          <td>Date of Admission</td>
          <td><?= htmlspecialchars($student['created_at'] ?? 'N/A') ?></td>
        </tr>
        <tr>
          <td>Address</td>
          <td colspan="3">
            <?= htmlspecialchars($student['street'].', '.$student['city'].', '.$student['state'].', '.$student['country'].' - '.$student['pincode']) ?>
          </td>
        </tr>
      </table>
    </div>

    <p class="text-center text-muted">Student Management System</p>
</div>

<?php include("dashboardjs.php"); ?>
</body>
</html>
<?php $con->close(); ?>
