<?php
session_start();

// Agar staff/admin login check karna ho to
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

// Fetch all classes for dropdown
$classes = [];
$classResult = $con->query("SELECT DISTINCT class_name FROM addclass ORDER BY CAST(class_name AS UNSIGNED)");
if ($classResult && $classResult->num_rows > 0) {
    while ($row = $classResult->fetch_assoc()) {
        $classes[] = $row['class_name'];
    }
}

// Get homework id
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("location: manage-homework.php");
    exit();
}

$id = intval($_GET['id']);

// Fetch homework record
$sql = "SELECT * FROM homework WHERE id=?";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "Homework not found!";
    exit();
}

$row = $result->fetch_assoc();
$message = "";

// Update record
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $class = $_POST['class'];
    $division = $_POST['division'];
    $subject = $_POST['subject'];
    $homework_text = $_POST['homework_text'];
    $due_date = $_POST['due_date'];

    $file_path = $row['file_path']; // by default purana file

    // File upload check
    if (isset($_FILES['file_upload']) && $_FILES['file_upload']['error'] == 0) {
        $targetDir = "uploads/homework/";
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        $fileName = time() . "_" . basename($_FILES["file_upload"]["name"]);
        $targetFilePath = $targetDir . $fileName;

        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
        $allowedTypes = ['pdf', 'jpg', 'jpeg', 'png'];

        if (in_array($fileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES["file_upload"]["tmp_name"], $targetFilePath)) {
                $file_path = $targetFilePath;
            } else {
                $message = "<div class='alert alert-danger'>File upload failed!</div>";
            }
        } else {
            $message = "<div class='alert alert-danger'>Only PDF, JPG, JPEG, PNG files are allowed!</div>";
        }
    }

    // Update query
    $update = "UPDATE homework SET class=?, division=?, subject=?, homework_text=?, due_date=?, file_path=? WHERE id=?";
    $stmt2 = $con->prepare($update);
    $stmt2->bind_param("ssssssi", $class, $division, $subject, $homework_text, $due_date, $file_path, $id);

    if ($stmt2->execute()) {
        header("location: manage-homework.php?msg=updated");
        exit();
    } else {
        $message = "<div class='alert alert-danger'>Error updating record: " . $con->error . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Homework</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link href="staff.css" rel="stylesheet">
</head>
<body class="bg-light">

<?php include("staff-sidebar.php"); ?>
<?php include("staff-header.php"); ?>

<div class="container mt-5">
  <div class="card shadow-lg p-4">
    <h3><i class="bi bi-pencil-square me-2"></i> Edit Homework</h3>
    <hr>
    <?php echo $message; ?>

    <form method="POST" enctype="multipart/form-data">
      <div class="row mb-3">
        <div class="col-md-6">
          <label class="form-label">Class</label>
          <select name="class" class="form-control" required>
            <option value="">-- Select Class --</option>
            <?php foreach ($classes as $cls) { ?>
              <option value="<?php echo $cls; ?>" <?php if ($row['class'] == $cls) echo "selected"; ?>>
                <?php echo $cls; ?>
              </option>
            <?php } ?>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Division</label>
          <select name="division" class="form-control" required>
            <option value="">-- Select Division --</option>
            <?php 
              for ($i = 'A'; $i <= 'J'; $i++) {
                  $selected = ($row['division'] == $i) ? "selected" : "";
                  echo "<option value='$i' $selected>$i</option>";
                  if ($i === 'J') break;
              }
            ?>
          </select>
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label">Subject</label>
        <input type="text" name="subject" class="form-control" value="<?php echo htmlspecialchars($row['subject']); ?>" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Homework</label>
        <textarea name="homework_text" class="form-control" rows="4" required><?php echo htmlspecialchars($row['homework_text']); ?></textarea>
      </div>

      <div class="mb-3">
        <label class="form-label">Due Date</label>
        <input type="date" name="due_date" class="form-control" value="<?php echo $row['due_date']; ?>" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Upload File (PDF / Image)</label>
        <input type="file" name="file_upload" class="form-control">
        <?php if (!empty($row['file_path'])) { ?>
            <p class="mt-2">Current File: 
              <a href="<?php echo $row['file_path']; ?>" target="_blank">View File</a>
            </p>
        <?php } ?>
      </div>

      <button type="submit" class="btn btn-success">
        <i class="bi bi-save"></i> Update Homework
      </button>
      <a href="manage-homework.php" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Back
      </a>
    </form>
  </div>
</div>

<?php include("staff-dashboard-js.php"); ?>
</body>
</html>
