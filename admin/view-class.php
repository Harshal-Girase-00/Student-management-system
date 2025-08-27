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

// Pagination
$limit = 5; // Number of records per page
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$start = ($page - 1) * $limit;

$sql = "SELECT * FROM addclass LIMIT $start, $limit";
$result = $conn->query($sql);

$total_sql = "SELECT COUNT(*) FROM addclass";
$total_result = $conn->query($total_sql);
$total_rows = $total_result->fetch_array()[0];
$total_pages = ceil($total_rows / $limit);

$message = "";
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $delete_sql = "DELETE FROM addclass WHERE id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        $message = "Class deleted successfully!";
    } else {
        $message = "Error deleting class: " . $conn->error;
    }
    $stmt->close();
    header("Location: view-class.php");
    exit();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Class | Student Management System</title>
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
            border-radius: 8px;
            margin-top: 20px;
            min-height: calc(100vh - 100px); /* Adjust based on header/footer height */
            transition: margin-left 0.3s ease;
        }
        .table-responsive {
            margin-top: 20px;
        }
        .table {
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
        }
        .table th {
            background-color: #f8f9fa;
            color: #333;
        }
        .btn-edit {
            background-color: #6f42c1;
            color: #fff;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
        }
        .btn-edit:hover {
            background-color: #5a2d9c;
        }
        .btn-delete {
            background-color: #dc3545;
            color: #fff;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
        }
        .btn-delete:hover {
            background-color: #c82333;
        }
        .pagination {
            margin-top: 20px;
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
            .table-responsive {
                font-size: 14px;
            }
            .btn-edit, .btn-delete {
                padding: 3px 8px;
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <?php include 'header.php'; ?>

    <div class="main-content">
        <h6>Manage Class</h6>
        <?php if ($message): ?>
            <div class="message <?php echo strpos($message, 'successfully') !== false ? 'success' : 'error'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Class Name</th>
                        <th>Section</th>
                        <th>Creation Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        $sno = $start + 1;
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $sno++ . "</td>";
                            echo "<td>" . htmlspecialchars($row['class_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['section']) . "</td>";
                            echo "<td>" . date('Y-m-d H:i:s', strtotime($row['created_at'])) . "</td>";
                            echo "<td>";
                            echo "<a href='view-class.php?delete_id=" . $row['id'] . "' class='btn btn-delete' onclick='return confirm(\"Are you sure you want to delete?\");'>Delete</a>";
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5' class='text-center'>No classes found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?page=1">First</a>
                </li>
                <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $page - 1; ?>">Prev</a>
                </li>
                <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $page + 1; ?>">Next</a>
                </li>
                <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $total_pages; ?>">Last</a>
                </li>
            </ul>
        </nav>
        <p >studnet magment system</p>
    </div>

    <footer class="footer p-4">
        <small>Student Management System</small>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <?php include 'dashboardjs.php'; ?>
</body>
</html>

