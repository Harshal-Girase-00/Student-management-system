<?php
session_start();
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "studentms1";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $class_name = $_POST['class_name'];
    $section = $_POST['section'];

    $sql = "INSERT INTO addclass (class_name, section) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $class_name, $section);

    if ($stmt->execute()) {
        $message = "Class added successfully!";
    } else {
        $message = "Error adding class: " . $conn->error;
    }
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Class | Student Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="admin.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f4f8;
            font-family: Arial, sans-serif;
        }

        .main-content {
            background-color: #e9ecef;
            padding: 20px;
            height: 50vh;
            border-radius: 8px;
            margin-top: 20px;
            transition: margin-left 0.3s ease;
        }

        .add-class-form {
            width: 100%;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-control {
            border-radius: 4px;
            border: 1px solid #ced4da;
        }

        .btn-add {
            background-color: #17a2b8;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn-add:hover {
            background-color: #138496;
        }

        .message {
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 4px;
            text-align: center;
        }

        .message.success {
            background-color: #d4edda;
            color: #155724;
        }

        .message.error {
            background-color: #f8d7da;
            color: #721c24;
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 10px;
            }
        }
    </style>
</head>

<body>
    <?php include 'sidebar.php'; ?>
    <?php include 'header.php'; ?>

    <div class="main-content">
        <h6>Add Class</h6>
        <?php if ($message): ?>
            <div class="message <?php echo strpos($message, 'successfully') !== false ? 'success' : 'error'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        <div class="add-class-form">
            <form method="post" action="">
                <div class="form-group">
                    <input type="text" class="form-control" name="class_name" placeholder="Add Standard" required>
                </div>
                <div class="form-group">
                    <select class="form-control" name="section" required>
                        <option value="" disabled selected>Choose Section</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                        <option value="D">D</option>
                        <option value="E">E</option>
                        <option value="F">F</option>
                        <option value="G">G</option>
                        <option value="H">H</option>
                        <option value="I">I</option>
                        <option value="J">J</option>
                    </select>
                </div>
                <div class="form-group text-center">
                    <button type="submit" class="btn-add">Add</button>
                </div>
            </form>
        </div>
    </div>

    <footer class="footer p-4">
        <small>Student Management System</small>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <?php include 'dashboardjs.php'; ?>
</body>

</html>