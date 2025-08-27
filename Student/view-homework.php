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

// Fetch homework only for student's class and division
$stmt = $con->prepare("SELECT * FROM homework WHERE class=? AND division=? ORDER BY due_date DESC");
$stmt->bind_param("ss", $class, $division);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>View Homework | Student Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<link href="student.css" rel="stylesheet">
</head>
<body>
<?php include("student-sidebar.php"); ?>
<?php include("student-header.php"); ?>

<div class="container-fluid my-4">
    <div class="card shadow-sm p-4">
        <h3 class="mb-4 text-primary"><i class="bi bi-journal-text me-2"></i>Homework for Class <?= htmlentities($class) ?> Division <?= htmlentities($division) ?></h3>

        <?php if ($result && $result->num_rows > 0) { ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark text-center">
                    <tr>
                        <th>S.NO</th>
                        <th>Subject</th>
                        <th>Homework</th>
                        <th>Due Date</th>
                        <th>Attachment</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $count = 1; while ($row = $result->fetch_assoc()) { ?>
                    <tr class="text-center">
                        <td><?= $count++; ?></td>
                        <td><?= htmlentities($row['subject']); ?></td>
                        <td class="text-start"><?= nl2br(htmlentities($row['homework_text'])); ?></td>
                        <td><?= htmlentities(date("d-m-Y", strtotime($row['due_date']))); ?></td>
                        <td>
                            <?php 
                            if (!empty($row['file_path'])) { 
                                // Correct file path: database me already "uploads/homework/filename.pdf" saved
                                $filePath = "../staff/" . $row['file_path'];
                            ?>
                                <a href="<?= $filePath; ?>" class="btn btn-sm btn-outline-primary" target="_blank">
                                    <i class="bi bi-eye"></i> View
                                </a>
                            <?php 
                            } else { 
                                echo "<span class='text-muted'>No File</span>"; 
                            } 
                            ?>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <?php } else { ?>
            <div class="alert alert-info text-center">
                No homework assigned for your class and division.
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
