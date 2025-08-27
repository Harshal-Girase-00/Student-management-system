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

// Delete notice
if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    $stmt = $con->prepare("DELETE FROM notices WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();
    header("Location: view-notice.php");
    exit();
}

// Pagination
$limit = 5; // Number of records per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Fetch notices with pagination
$sql = "SELECT * FROM notices ORDER BY created_at DESC LIMIT ?, ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("ii", $start, $limit);
$stmt->execute();
$result = $stmt->get_result();

// Get total number of notices for pagination
$total_sql = "SELECT COUNT(*) FROM notices";
$total_result = $con->query($total_sql);
$total_rows = $total_result->fetch_array()[0];
$total_pages = ceil($total_rows / $limit);

$stmt->close();
$con->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Notice</title> 
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link href="staff.css" rel="stylesheet">
  <style>
    .content { padding: 20px; }
    .manage-notice-container {
      background-color: #fff;
      padding: 20px;
      border-radius: 5px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    .btn-purple {
      background-color: #6f42c1;
      color: #fff;
      border: none;
    }
    .btn-purple:hover { background-color: #5a2d9c; }
  </style>
</head>
<body>

<?php include("staff-sidebar.php"); ?>
<?php include("staff-header.php"); ?>

<div class="content" style="margin:0">
  <div class="manage-notice-container">
    <h4 class="mb-4">Manage Notice</h4>
    <table class="table table-bordered table-hover">
      <thead>
        <tr>
          <th style="width: 5%;">S.No</th>
          <th style="width: 20%;">Notice Title</th>
          <th style="width: 10%;">Class</th>
          <th style="width: 10%;">Division</th>
          <th style="width: 20%;">Notice Date</th>
          <th style="width: 20%;">Action</th>
        </tr>
      </thead>
      <tbody>
        <?php
        if ($result->num_rows > 0) {
            $sno = $start + 1;
            while ($row = $result->fetch_assoc()) {
                ?>
                <tr>
                  <td><?= $sno++; ?></td>
                  <td><?= htmlspecialchars($row['title']); ?></td>
                  <td><?= htmlspecialchars($row['class'] ?? ''); ?></td>
                  <td><?= htmlspecialchars($row['division'] ?? ''); ?></td>
                  <td><?= htmlspecialchars($row['created_at']); ?></td>
                  <td>
                    <a href="edit-notice.php?id=<?= $row['id']; ?>" class="btn btn-purple btn-sm me-2">Edit</a>
                    <a href="?delete_id=<?= $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this notice?');">Delete</a>
                  </td>
                </tr>
                <?php
            }
        } else {
            echo "<tr><td colspan='6' class='text-center'>No notices found</td></tr>";
        }
        ?>
      </tbody>
    </table>

    <!-- Pagination -->
    <nav aria-label="Page navigation" class="mt-3">
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

    <p class="text-muted text-center mt-2">View all Notice</p>
  </div>
  <p class="text-center text-muted mt-4">Student Management System</p>
</div>

<?php include("staff-dashboard-js.php"); ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
