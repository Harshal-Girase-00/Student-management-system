<?php
session_start();

// Staff login check
if (!isset($_SESSION['staff_id'])) {
    header("location: login.php");
    exit();
}

// Increase PHP limits for large files (1GB)
ini_set('upload_max_filesize', '1024M');
ini_set('post_max_size', '1024M');
ini_set('max_execution_time', 3000);
ini_set('max_input_time', 3000);
ini_set('memory_limit', '2048M');

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

// Fetch classes for dropdown
$classes = [];
$classResult = $con->query("SELECT DISTINCT class_name FROM addclass ORDER BY CAST(class_name AS UNSIGNED)");
if ($classResult && $classResult->num_rows > 0) {
    while ($row = $classResult->fetch_assoc()) {
        $classes[] = $row['class_name'];
    }
}

// Handle form submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $class = $_POST['class'];
    $division = $_POST['division'];
    $subject = $_POST['subject'];
    $file_path = NULL;

    if (isset($_FILES['file_upload']) && $_FILES['file_upload']['error'] == 0) {
        $targetDir = "uploads/notes/";
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        // Sanitize filename
        $fileName = time() . "_" . preg_replace("/[^a-zA-Z0-9\.\-_]/", "_", basename($_FILES["file_upload"]["name"]));
        $targetFilePath = $targetDir . $fileName;

        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
        $allowedTypes = ['pdf', 'jpg', 'jpeg', 'png'];

        if (in_array($fileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES["file_upload"]["tmp_name"], $targetFilePath)) {
                $file_path = $targetFilePath;

                // Insert path into database
                $sql = "INSERT INTO notes (class, division, subject, file_path, uploaded_on) 
                        VALUES (?, ?, ?, ?, NOW())";
                $stmt = $con->prepare($sql);
                $stmt->bind_param("ssss", $class, $division, $subject, $file_path);

                if ($stmt->execute()) {
                    $message = "<div class='alert alert-success'>Notes uploaded successfully!</div>";
                } else {
                    $message = "<div class='alert alert-danger'>Database error: " . $con->error . "</div>";
                    // Delete the uploaded file if DB insert fails
                    if(file_exists($file_path)) unlink($file_path);
                }
                $stmt->close();
            } else {
                $message = "<div class='alert alert-danger'>File upload failed! Check server permissions.</div>";
            }
        } else {
            $message = "<div class='alert alert-danger'>Only PDF, JPG, JPEG, PNG files are allowed!</div>";
        }
    } else {
        $message = "<div class='alert alert-danger'>Please select a file to upload!</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Upload Notes</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<link href="staff.css" rel="stylesheet">
</head>
<body class="bg-light">

<?php include("staff-sidebar.php"); ?>
<?php include("staff-header.php"); ?>

<div class="container mt-5">
    <div class="card shadow-lg p-4">
        <h3><i class="bi bi-upload me-2"></i> Upload Notes</h3>
        <hr>
        <?php echo $message; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Class</label>
                    <select name="class" class="form-control" required>
                        <option value="">-- Select Class --</option>
                        <?php foreach ($classes as $cls) { ?>
                            <option value="<?php echo $cls; ?>"><?php echo $cls; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Division</label>
                    <select name="division" class="form-control" required>
                        <option value="">-- Select Division --</option>
                        <?php 
                        for ($i = 'A'; $i <= 'J'; $i++) {
                            echo "<option value='$i'>$i</option>";
                            if ($i === 'J') break;
                        }
                        ?>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Subject</label>
                <input type="text" name="subject" class="form-control" placeholder="Enter Subject" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Select File (PDF / Image)</label>
                <input type="file" name="file_upload" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="bi bi-upload"></i> Upload Notes
            </button>
        </form>
    </div>
</div>

<?php include("staff-dashboard-js.php"); ?>
</body>
</html>
