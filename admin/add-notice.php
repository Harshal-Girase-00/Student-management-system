<?php
// Database connection
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "studentms1";

$con = new mysqli($host, $user, $pass, $dbname);

// Connection check
if ($con->connect_error) {
  die("Connection failed: " . $con->connect_error);
}

// Fetch Classes from DB (from addclass table)
$classQuery = "SELECT id, class_name FROM addclass"; 
$classResult = $con->query($classQuery);

// Insert Notice
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $title = $con->real_escape_string($_POST['noticeTitle']);
  $class = $con->real_escape_string($_POST['class']);
  $division = $con->real_escape_string($_POST['division']);
  $message = $con->real_escape_string($_POST['noticeMessage']);

  $fileName = "";
  if (isset($_FILES['noticeFile']) && $_FILES['noticeFile']['error'] == 0) {
      $fileTmp = $_FILES['noticeFile']['tmp_name'];
      $fileName = time() . "_" . basename($_FILES['noticeFile']['name']);
      $uploadDir = "../staff/uploads/notice/";

      // Folder create if not exists
      if (!is_dir($uploadDir)) {
          mkdir($uploadDir, 0777, true);
      }

      $uploadPath = $uploadDir . $fileName;
      if (!move_uploaded_file($fileTmp, $uploadPath)) {
          $error = "❌ File upload failed!";
      }
  }

  if (!isset($error)) {
    $sql = "INSERT INTO notices (title, class, division, message, file_path, created_at) 
            VALUES ('$title', '$class', '$division', '$message', '$fileName', NOW())";

    if ($con->query($sql) === TRUE) {
      $success = "✅ Notice added successfully!";
    } else {
      $error = "❌ Error: " . $con->error;
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Notice</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link href="admin.css" rel="stylesheet">
  <style>
    .form-container {
      background-color: #fff;
      padding: 20px;
      border-radius: 5px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
      margin: auto;
    }

    .btn-add {
      background-color: #17a2b8;
      color: #fff;
    }

    .btn-add:hover {
      background-color: #138496;
    }
  </style>
</head>

<body>
  <?php include("sidebar.php"); ?>
  <?php include("header.php"); ?>

  <div class="form-container mt-4">
    <h4>Add Notice</h4>

    <!-- Success/Error Message -->
    <?php if (isset($success)) { ?>
      <div class="alert alert-success"><?= $success; ?></div>
    <?php } elseif (isset($error)) { ?>
      <div class="alert alert-danger"><?= $error; ?></div>
    <?php } ?>

    <form method="POST" action="" enctype="multipart/form-data">
      <div class="mb-3">
        <label for="noticeTitle" class="form-label">Notice Title</label>
        <input type="text" class="form-control" id="noticeTitle" name="noticeTitle" required>
      </div>

      <!-- Class Dropdown -->
      <div class="mb-3">
        <label for="class" class="form-label">Class</label>
        <select class="form-select" id="class" name="class" required>
          <option value="">Select Class</option>
          <?php
          if ($classResult && $classResult->num_rows > 0) {
            while ($row = $classResult->fetch_assoc()) {
              echo "<option value='" . $row['class_name'] . "'>" . $row['class_name'] . "</option>";
            }
          }
          ?>
        </select>
      </div>

      <!-- Division Dropdown -->
      <div class="mb-3">
        <label for="division" class="form-label">Division</label>
        <select class="form-select" id="division" name="division" required>
          <option value="">Select Division</option>
          <?php
          foreach (range('A', 'J') as $div) {
            echo "<option value='$div'>$div</option>";
          }
          ?>
        </select>
      </div>

      <div class="mb-3">
        <label for="noticeMessage" class="form-label">Notice Message</label>
        <textarea class="form-control" id="noticeMessage" name="noticeMessage" rows="4" required></textarea>
      </div>

      <!-- File Upload -->
      <div class="mb-3">
        <label for="noticeFile" class="form-label">Upload File</label>
        <input type="file" class="form-control" id="noticeFile" name="noticeFile">
        <small class="text-muted">Allowed: PDF, DOC, DOCX, JPG, PNG</small>
      </div>

      <button type="submit" class="btn btn-add">Add</button>
    </form>
  </div>

  <p class="text-center text-muted mt-4">Student Management System</p>

  <?php include("dashboardjs.php"); ?>
</body>
</html>
