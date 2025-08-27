<?php 
session_start(); 

// Check student login
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
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

$studentID = $_SESSION['student_id'];

// Fetch student's class and division
$stmt = $con->prepare("SELECT class, division FROM students WHERE student_id = ?");
$stmt->bind_param("s", $studentID);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
$stmt->close();

$class = $student['class'];
$division = $student['division'];

// Fetch notices only for student's class and division
$stmt = $con->prepare("SELECT * FROM notices WHERE class=? AND division=? ORDER BY created_at DESC");
$stmt->bind_param("ss", $class, $division);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>View Notices | Student Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<link href="student.css" rel="stylesheet">
<style>
    td.message-col {
        width: 45%;
        word-wrap: break-word;
        white-space: pre-wrap;
    }
</style>
</head>
<body>
<?php include("student-sidebar.php"); ?>
<?php include("student-header.php"); ?>

<div class="container-fluid my-4">
    <div class="card shadow-sm p-4">
        <h3 class="mb-4 text-warning">
            <i class="bi bi-megaphone me-2"></i>
            Notices for Class <?= htmlentities($class) ?> Division <?= htmlentities($division) ?>
        </h3>

        <?php if ($result && $result->num_rows > 0) { ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark text-center">
                    <tr>
                        <th style="width:5%;">S.NO</th>
                        <th style="width:15%;">Title</th>
                        <th style="width:45%;">Message</th>
                        <th style="width:15%;">File</th>
                        <th style="width:20%;">Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $count = 1; while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td class="text-center"><?= $count++; ?></td>
                        <td class="fw-bold"><?= htmlentities($row['title']); ?></td>
                        <td class="message-col"><?= nl2br(htmlentities($row['message'])); ?></td>
                        <td class="text-center">
                            <?php if (!empty($row['file_path'])) { 
                                // staff/uploads/notice/ ke andar file hai
                                $filePath = "../staff/uploads/notice/" . $row['file_path'];
                            ?>
                                <a href="<?= $filePath; ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i> View File
                                </a>
                            <?php } else { ?>
                                <span class="text-muted">No File</span>
                            <?php } ?>
                        </td>
                        <td class="text-center"><?= htmlentities(date("d-m-Y H:i", strtotime($row['created_at']))); ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <?php } else { ?>
            <div class="alert alert-info text-center">
                No notices available for your class and division.
            </div>
        <?php } ?>
    </div>
</div>


<?php include("student-dashboard-js.php"); ?>
</body>
</html>

<?php
$stmt->close(); 
$con->close(); 
?>
