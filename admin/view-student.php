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
$con->set_charset("utf8mb4");

// ✅ Fetch distinct classes & divisions for filters
$classes = [];
$divisions = [];
$resC = $con->query("SELECT DISTINCT class FROM students ORDER BY class");
while ($r = $resC->fetch_assoc()) {
  $classes[] = $r['class'];
}
$resD = $con->query("SELECT DISTINCT division FROM students ORDER BY division");
while ($r = $resD->fetch_assoc()) {
  $divisions[] = $r['division'];
}

// ✅ Read selected filter values
$selectedClass    = isset($_GET['class']) ? trim($_GET['class']) : "";
$selectedDivision = isset($_GET['division']) ? trim($_GET['division']) : "";

// ✅ Base query
$sql = "SELECT * FROM students WHERE 1=1";

// Apply filters
$params = [];
$types  = "";

if ($selectedClass !== "") {
  $sql .= " AND class = ?";
  $params[] = $selectedClass;
  $types .= "s";
}
if ($selectedDivision !== "") {
  $sql .= " AND division = ?";
  $params[] = $selectedDivision;
  $types .= "s";
}

$sql .= " ORDER BY username DESC";

// ✅ Prepare & execute
$stmt = $con->prepare($sql);
if (!empty($params)) {
  $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>View Students</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link href="admin.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
    }

    .table-container {
      background: #fff;
      padding: 2rem;
      border-radius: 0.5rem;
      box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
    }

    .table-responsive {
      overflow-x: auto;
    }
  </style>
</head>

<body class="p-4">
  <?php include("sidebar.php"); ?>
  <?php include("header.php"); ?>

  <div class="container">
    <h3 class="mb-4 text-center text-primary">👨‍🎓 Manage Students</h3>

    <!-- ✅ Filter Form -->
    <div class="card shadow-sm mb-4">
      <div class="card-body">
        <form class="row g-3" method="get" action="">
          <div class="col-md-4">
            <label class="form-label"> Select Class</label>
            <select name="class" class="form-select">
              <option value="">Select Class</option>
              <?php foreach ($classes as $c): ?>
                <option value="<?= htmlspecialchars($c) ?>" <?= ($c === $selectedClass ? 'selected' : '') ?>>
                  <?= htmlspecialchars($c) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label">Select division</label>
            <select name="division" class="form-select">
              <option value="">Select Division</option>
              <?php foreach ($divisions as $d): ?>
                <option value="<?= htmlspecialchars($d) ?>" <?= ($d === $selectedDivision ? 'selected' : '') ?>>
                  <?= htmlspecialchars($d) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-4 d-flex align-items-end gap-2">
            <button type="submit" class="btn btn-primary"><i class="bi bi-funnel"></i> Filter</button>
            <a href="view-student.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-counterclockwise"></i> Reset</a>
          </div>
        </form>
      </div>
    </div>

    <!-- ✅ Students Table -->
    <div class="table-container">
      <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
          <thead class="table-dark">
            <tr>
              <th>S.No</th>
              <th>Full Name</th>
              <th>Class</th>
              <th>Division</th>
              <th>DOB</th>
              <th>Age</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php
            if ($result->num_rows > 0) {
              $sno = 1;
              while ($row = $result->fetch_assoc()) {
                $fullname = $row['first_name'] . " " . $row['middle_name'] . " " . $row['last_name'];
                echo "<tr>";
                echo "<td>" . $sno++ . "</td>";

                echo "<td>" . htmlspecialchars($fullname) . "</td>";
                echo "<td>" . htmlspecialchars($row['class']) . "</td>";
                echo "<td>" . htmlspecialchars($row['division']) . "</td>";
                echo "<td>" . htmlspecialchars($row['dob']) . "</td>";
                echo "<td>" . htmlspecialchars($row['age']) . "</td>";

                echo "<td>
                          <a href='view-student-details.php?username=" . $row['username'] . "' class='btn btn-sm btn-primary'>View</a>
                        </td>";
                echo "</tr>";
              }
            } else {
              echo "<tr><td colspan='9' class='text-center'>No students found</td></tr>";
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <?php include("dashboardjs.php"); ?>
</body>

</html>
<?php $con->close(); ?>