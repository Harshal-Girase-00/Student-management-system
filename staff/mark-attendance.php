<?php
session_start();
if (!isset($_SESSION['staff_id'])) {
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

// ✅ Fetch Classes from addclass table
$class_options = [];
$class_query = $con->query("SELECT DISTINCT class_name FROM addclass ORDER BY CAST(class_name AS UNSIGNED), class_name");
if ($class_query->num_rows > 0) {
    while ($row = $class_query->fetch_assoc()) {
        $class_options[] = $row['class_name'];
    }
}

// Handle Load Students 
$students = [];
if (isset($_POST['load_students'])) {
    $class = $_POST['class_name'];
    $section = $_POST['section_name'];
    $date = $_POST['attendance_date'];

    // ✅ students table me class aur division ke columns alag hain
    $result = $con->query("SELECT * FROM students WHERE class='$class' AND division='$section'");
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $students[] = $row;
        }
    } else {
        $msg = "No students found in this class.";
    }
}

// Handle Save Attendance 
if (isset($_POST['submit_attendance'])) {
    $class = $_POST['class_name'];
    $section = $_POST['section_name'];
    $date = $_POST['attendance_date'];
    $attendance_data = $_POST['attendance'];

    foreach ($attendance_data as $student_id => $status) {
        // check if attendance already exists
        $check = $con->query("SELECT * FROM attendance WHERE student_id='$student_id' AND date='$date'");
        if ($check->num_rows > 0) {
            $con->query("UPDATE attendance SET status='$status' WHERE student_id='$student_id' AND date='$date'");
        } else {
            $con->query("INSERT INTO attendance (student_id, class, division, date, status) 
                         VALUES ('$student_id', '$class', '$section', '$date', '$status')");
        }
    }
    $msg = "Attendance marked successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="staff.css" rel="stylesheet">
    <title>SMS Mark Attendance</title>
</head>

<body>
    <?php include 'staff-sidebar.php'; ?>
    <?php include 'staff-header.php'; ?>

    <div class="container mt-4">
        <h2>Mark Attendance</h2>
        <?php if (isset($msg)) { ?>
            <div class="alert alert-info"><?= $msg ?></div>
        <?php } ?>

        <!-- Attendance Form -->
        <form method="POST" class="row g-3">
            <div class="col-md-4">
                <label>Class</label>
                <select name="class_name" class="form-control" required>
                    <option value="">Select Class</option>
                    <?php foreach($class_options as $cls){ ?>
                        <option value="<?= $cls ?>" <?= (isset($class) && $class == $cls) ? 'selected' : '' ?>><?= $cls ?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="col-md-4">
                <label>Section</label>
                <select name="section_name" class="form-control" required>
                    <option value="">Select Section</option>
                    <?php 
                    $sections = ["A","B","C","D","E","F","G","H","I","J"];
                    foreach ($sections as $sec) { ?>
                        <option value="<?= $sec ?>" <?= (isset($section) && $section == $sec) ? 'selected' : '' ?>><?= $sec ?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="col-md-4">
                <label>Date</label>
                <input type="date" name="attendance_date" class="form-control" value="<?= isset($date) ? $date : '' ?>" required>
            </div>

            <div class="col-md-12 d-flex justify-content-end">
                <button type="submit" name="load_students" class="btn btn-primary">Load Students</button>
            </div>
        </form>

        <?php if (!empty($students)) { ?>
            <form method="POST" class="mt-4">
                <input type="hidden" name="class_name" value="<?= $class ?>">
                <input type="hidden" name="section_name" value="<?= $section ?>">
                <input type="hidden" name="attendance_date" value="<?= $date ?>">

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Name</th>
                            <th>Attendance</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $stu) { 
                            $fullname = $stu['first_name'];
                            if (!empty($stu['middle_name'])) $fullname .= " " . $stu['middle_name'];
                            $fullname .= " " . $stu['last_name'];
                        ?>
                            <tr>
                                <td><?= $stu['student_id'] ?></td>
                                <td><?= $fullname ?></td>
                                <td>
                                    <select name="attendance[<?= $stu['student_id'] ?>]" class="form-control">
                                        <option value="Present">Present</option>
                                        <option value="Absent">Absent</option>
                                    </select>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <button type="submit" name="submit_attendance" class="btn btn-success">Save Attendance</button>
            </form>
        <?php } ?>
    </div>
    <?php include("staff-dashboard-js.php") ?>
</body>
</html>
