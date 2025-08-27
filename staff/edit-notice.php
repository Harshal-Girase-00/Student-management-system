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

// Fetch notice data if id is provided
$notice = null;
$message = "";

// Fetch classes
$classQuery = "SELECT id, class_name FROM addclass ORDER BY class_name+0 ASC"; 
$classResult = $con->query($classQuery);

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $con->prepare("SELECT * FROM notices WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $notice = $result->fetch_assoc();
    $stmt->close();
    if (!$notice) {
        $message = "Notice not found.";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = (int)$_POST['id'];
    $title = $con->real_escape_string($_POST['title']);
    $class = $con->real_escape_string($_POST['class']);
    $division = $con->real_escape_string($_POST['division']);
    $message_content = $con->real_escape_string($_POST['message']);

    // by default old file rakho
    $fileName = $notice['file_path'];

    // if new file uploaded
    if (isset($_FILES['noticeFile']) && $_FILES['noticeFile']['error'] == 0) {
        $fileTmp = $_FILES['noticeFile']['tmp_name'];
        $fileName = time() . "_" . basename($_FILES['noticeFile']['name']);
        $uploadDir = "uploads/notice/";

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $uploadPath = $uploadDir . $fileName;
        if (!move_uploaded_file($fileTmp, $uploadPath)) {
            $message = "❌ File upload failed!";
        }
    }

    $stmt = $con->prepare("UPDATE notices SET title = ?, class = ?, division = ?, message = ?, file_path = ?, created_at = NOW() WHERE id = ?");
    $stmt->bind_param("sssssi", $title, $class, $division, $message_content, $fileName, $id);

    if ($stmt->execute()) {
        header("Location: view-notice.php?success=1");
        exit();
    } else {
        $message = "Error updating notice: " . $con->error;
    }
    $stmt->close();
}

$con->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Notice | Student Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="staff.css" rel="stylesheet">
    <style>
        body { background-color: #f0f4f8; font-family: Arial, sans-serif; }
        .main-content { background-color: #e9ecef; padding: 20px; border-radius: 8px; margin-top: 20px; min-height: calc(100vh - 100px); }
        .update-notice-form { padding: 20px; background-color: #fff; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .btn-update { background-color: #17a2b8; color: #fff; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; }
        .btn-update:hover { background-color: #138496; }
    </style>
</head>
<body>
    <?php include("staff-sidebar.php"); ?>
    <?php include("staff-header.php"); ?>

    <div class="main-content">
        <h4 class="text-center mb-4">Update Notice</h4>
        <?php if ($message): ?>
            <div class="alert alert-danger text-center"><?php echo $message; ?></div>
        <?php endif; ?>

        <?php if ($notice): ?>
            <div class="update-notice-form">
                <form method="post" action="" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?php echo $notice['id']; ?>">

                    <div class="form-group mb-3">
                        <input type="text" class="form-control" name="title" value="<?php echo htmlspecialchars($notice['title']); ?>" placeholder="Notice Title" required>
                    </div>

                    <!-- Class Dropdown -->
                    <div class="form-group mb-3">
                        <label>Class</label>
                        <select class="form-control" name="class" required>
                            <option value="">Select Class</option>
                            <?php
                            if ($classResult && $classResult->num_rows > 0) {
                                while ($row = $classResult->fetch_assoc()) {
                                    $selected = ($notice['class'] == $row['class_name']) ? "selected" : "";
                                    echo "<option value='" . $row['class_name'] . "' $selected>" . $row['class_name'] . "</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>

                    <!-- Division Dropdown -->
                    <div class="form-group mb-3">
                        <label>Division</label>
                        <select class="form-control" name="division" required>
                            <option value="">Select Division</option>
                            <?php
                            foreach (range('A', 'J') as $div) {
                                $selected = ($notice['division'] == $div) ? "selected" : "";
                                echo "<option value='$div' $selected>$div</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <textarea class="form-control" name="message" rows="8" placeholder="Notice Message" required><?php echo htmlspecialchars($notice['message']); ?></textarea>
                    </div>

                    <!-- File Upload -->
                    <div class="form-group mb-3">
                        <label>Upload New File (optional)</label>
                        <input type="file" class="form-control" name="noticeFile">
                        <?php if (!empty($notice['file_path'])): ?>
                            <p class="mt-2">📄 Current File:
                                <a href="uploads/notice/<?php echo htmlspecialchars($notice['file_path']); ?>" target="_blank">View File</a>
                            </p>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn-update">Update</button>
                    </div>
                </form>
            </div>
        <?php else: ?>
            <p class="text-center">Notice not found.</p>
        <?php endif; ?>
    </div>

    <footer class="footer p-4 text-center">
        <small>Student Management System</small>
    </footer>
    <?php include("staff-dashboard-js.php"); ?>
</body>
</html>
