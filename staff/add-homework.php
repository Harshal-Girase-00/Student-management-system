<?php
session_start();

// Staff/Admin login check
if (!isset($_SESSION['staff_id']) && !isset($_SESSION['aid'])) {
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

$message = "";

// Fetch all classes
$classes = [];
$classResult = $con->query("SELECT DISTINCT class_name FROM addclass ORDER BY CAST(class_name AS UNSIGNED)");
if ($classResult && $classResult->num_rows > 0) {
    while ($row = $classResult->fetch_assoc()) {
        $classes[] = $row['class_name'];
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $class = $_POST['class'];
    $division = $_POST['division'];
    $subject = $_POST['subject'];
    $homework_text = $_POST['homework_text'];
    $due_date = $_POST['due_date'];
    $file_path = NULL;

    // File Upload Handling
    if (isset($_FILES['file_upload']) && $_FILES['file_upload']['error'] == 0) {
        $targetDir = "../staff/uploads/homework/"; // relative to staff folder
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        $fileName = time() . "_" . basename($_FILES["file_upload"]["name"]);
        $targetFilePath = $targetDir . $fileName;

        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
        $allowedTypes = ['pdf', 'jpg', 'jpeg', 'png'];

        if (in_array($fileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES["file_upload"]["tmp_name"], $targetFilePath)) {
                // Store relative path for student access
                $file_path = "uploads/homework/" . $fileName;
            } else {
                $message = "<div class='alert alert-danger'>File upload failed!</div>";
            }
        } else {
            $message = "<div class='alert alert-danger'>Only PDF, JPG, JPEG, PNG files are allowed!</div>";
        }
    }

    // Insert homework
    $sql = "INSERT INTO homework (class, division, subject, homework_text, due_date, file_path) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("ssssss", $class, $division, $subject, $homework_text, $due_date, $file_path);

    if ($stmt->execute()) {
        $message = "<div class='alert alert-success'>Homework added successfully!</div>";
    } else {
        $message = "<div class='alert alert-danger'>Error: " . $con->error . "</div>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Homework</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link href="staff.css" rel="stylesheet">
</head>
<body class="bg-light">

<?php include("staff-sidebar.php"); ?>
<?php include("staff-header.php"); ?>

<div class="container mt-5">
  <div class="card shadow-lg p-4">
    <h3><i class="bi bi-journal-text me-2"></i> Add Homework</h3>
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
        <input type="text" name="subject" class="form-control" placeholder="Enter subject" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Homework</label>
        <textarea name="homework_text" class="form-control" rows="4" placeholder="Enter homework details" required></textarea>
      </div>

      <div class="mb-3">
        <label class="form-label">Due Date</label>
        <input type="date" name="due_date" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Upload File (PDF / Image)</label>
        <input type="file" name="file_upload" class="form-control">
      </div>

      <button type="submit" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Add Homework
      </button>
    </form>
  </div>
</div>

<?php include("staff-dashboard-js.php"); ?>
</body>
</html>
