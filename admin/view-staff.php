<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("location: admin.php"); 
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

// Handle Delete Staff
if (isset($_GET['delete_id'])) {
    $id = $con->real_escape_string($_GET['delete_id']);
    $con->query("DELETE FROM staff WHERE staff_id='$id'");
    header("Location: dashboard.php");
    exit();
}

// Fetch all staff
$query = "SELECT * FROM staff ORDER BY created_at DESC";
$staffResult = $con->query($query);
if (!$staffResult) {
    die("Query failed: " . $con->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Staff | Admin Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<link href="admin.css" rel="stylesheet">
</head>
<body>

<!-- Sidebar & Header -->
<?php include("sidebar.php"); ?>
<?php include("header.php"); ?>

<div class="container my-4">
    <h4 class="mb-4">Manage Staff</h4>
    <div class="card">
        <div class="card-body">
            <!-- ✅ Table Responsive Wrapper -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>S.No</th>
                            <th>Staff Id</th>
                            <th>Staff Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($staffResult->num_rows > 0) {
                            $sno = 1;
                            while ($staff = $staffResult->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>{$sno}</td>";
                                echo "<td>{$staff['staff_id']}</td>"; 
                                echo "<td>" . ucwords(strtolower($staff['name'])) . "</td>";
                                echo "<td>{$staff['email']}</td>";
                                echo "<td>{$staff['phone']}</td>";
                                echo "<td>
                                        <a href='view-staff.php?delete_id={$staff['staff_id']}' 
                                           class='btn btn-sm btn-danger text-white' 
                                           onclick='return confirm(\"Are you sure you want to delete this staff?\")'>
                                           Delete
                                        </a>
                                      </td>";
                                echo "</tr>";
                                $sno++;
                            }
                        } else {
                            echo "<tr><td colspan='6' class='text-center'>No staff found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <!-- ✅ End Table Responsive -->
        </div>
    </div>
</div>


<?php include("dashboardjs.php"); ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
