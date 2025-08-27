<?php
session_start();
if (!isset($_SESSION['staff_id'])) {
    header("location: login.php");
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

$message = "";

// ✅ Delete Homework
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);

    // agar file bhi delete karni hai to uska path nikal lo
    $res = $con->query("SELECT file_path FROM homework WHERE id=$delete_id");
    if ($res && $res->num_rows > 0) {
        $row = $res->fetch_assoc();
        if (!empty($row['file_path']) && file_exists($row['file_path'])) {
            unlink($row['file_path']); // server se file bhi delete karega
        }
    }

    $sql = "DELETE FROM homework WHERE id=?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $delete_id);

    if ($stmt->execute()) {
        $message = "<div class='alert alert-success'>Homework deleted successfully!</div>";
    } else {
        $message = "<div class='alert alert-danger'>Error deleting: " . $con->error . "</div>";
    }
}

// ✅ Filters
$classFilter = isset($_GET['class_filter']) ? $_GET['class_filter'] : '';
$divisionFilter = isset($_GET['division_filter']) ? $_GET['division_filter'] : '';

// Build query dynamically
$query = "SELECT id, subject, class, division FROM homework WHERE 1=1";
$params = [];
$types = "";

if ($classFilter != '') {
    $query .= " AND class = ?";
    $params[] = $classFilter;
    $types .= "s";
}

if ($divisionFilter != '') {
    $query .= " AND division = ?";
    $params[] = $divisionFilter;
    $types .= "s";
}

$query .= " ORDER BY id DESC";

$stmt = $con->prepare($query);
if(!empty($params)){
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Homework</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="staff.css" rel="stylesheet">
    <style>
        .btn-purple {
            background-color: #6f42c1;
            color: #fff;
        }
        .btn-purple:hover {
            background-color: #5a32a3;
            color: #fff;
        }
    </style>
</head>
<body class="p-4">
<?php include("staff-sidebar.php"); ?>
<?php include("staff-header.php"); ?>
<div class="container">
    <h2 class="mb-4">Manage Homework</h2>

    <?php echo $message; ?>

    <!-- ✅ Filter Form -->
    <form method="GET" class="row g-3 mb-3">
        <div class="col-md-4">
            <select name="class_filter" class="form-control">
                <option value="">-- Select Class --</option>
                <?php
                $classResult = $con->query("SELECT DISTINCT class FROM homework ORDER BY CAST(class AS UNSIGNED)");
                if($classResult && $classResult->num_rows > 0){
                    while($cls = $classResult->fetch_assoc()){
                        $selected = ($cls['class'] == $classFilter) ? 'selected' : '';
                        echo "<option value='{$cls['class']}' $selected>{$cls['class']}</option>";
                    }
                }
                ?>
            </select>
        </div>
        <div class="col-md-4">
            <select name="division_filter" class="form-control">
                <option value="">-- Select Division --</option>
                <?php
                for($i='A'; $i<='J'; $i++){
                    $selected = ($i == $divisionFilter) ? 'selected' : '';
                    echo "<option value='$i' $selected>$i</option>";
                    if($i=='J') break;
                }
                ?>
            </select>
        </div>
        <div class="col-md-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary w-100"><i class="bi bi-funnel"></i> Filter</button>
            <a href="manage-homework.php" class="btn btn-secondary w-100"><i class="bi bi-x-circle"></i> Reset</a>
        </div>
    </form>

    <!-- ✅ Homework Table -->
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>S.No</th>
                <th>Subject</th>
                <th>Class</th>
                <th>Division</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            if ($result->num_rows > 0) {
                $sn = 1;
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                        <td>{$sn}</td>
                        <td>{$row['subject']}</td>
                        <td>{$row['class']}</td>
                        <td>{$row['division']}</td>
                        <td>
                            <a href='edit-homework.php?id={$row['id']}' class='btn btn-purple btn-sm'>
                                <i class='bi bi-pencil'></i> Edit
                            </a>
                            <a href='manage-homework.php?delete_id={$row['id']}' 
                               class='btn btn-danger btn-sm'
                               onclick=\"return confirm('Are you sure you want to delete this homework?');\">
                                <i class='bi bi-trash'></i> Delete
                            </a>
                        </td>
                    </tr>";
                    $sn++;
                }
            } else {
                echo "<tr><td colspan='5' class='text-center'>No homework found</td></tr>";
            }
            ?>
        </tbody>
    </table>
    
</div>
<?php include("staff-dashboard-js.php"); ?>
</body>
</html>
