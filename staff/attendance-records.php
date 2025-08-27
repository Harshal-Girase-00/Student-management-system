<?php
session_start();
/*  OPTIONAL AUTH CHECK
if (!isset($_SESSION['staff_id'])) {
    header("Location: login.php");
    exit();
}
*/

// ---------------- DB CONNECT ----------------
$host = "localhost";
$user = "root";
$pass = "";
$db   = "studentms1";
$con = new mysqli($host, $user, $pass, $db);
if ($con->connect_error) {
    die("DB Connection failed: " . $con->connect_error);
}
$con->set_charset("utf8mb4");

// --------- LOAD FILTER OPTIONS (from students) ----------
$classes   = [];
$divisions = [];

$resC = $con->query("SELECT DISTINCT class FROM students ORDER BY class");
while ($r = $resC->fetch_assoc()) { $classes[] = $r['class']; }
$resD = $con->query("SELECT DISTINCT division FROM students ORDER BY division");
while ($r = $resD->fetch_assoc()) { $divisions[] = $r['division']; }

// --------- READ FILTERS (GET) ----------
$selectedClass    = isset($_GET['class']) ? trim($_GET['class']) : "";
$selectedDivision = isset($_GET['division']) ? trim($_GET['division']) : "";
$selectedDate     = isset($_GET['date']) ? trim($_GET['date']) : "";

// --------- DATA + SUMMARY ----------
$rows = [];
$summary = ["Present"=>0, "Absent"=>0, "Late"=>0, "Not Marked"=>0];

if ($selectedClass !== "" && $selectedDivision !== "" && $selectedDate !== "") {
    // LEFT JOIN so that even if attendance entry missing for a student, we still show "Not Marked"
    $sql = "
        SELECT 
            s.student_id,
            CONCAT_WS(' ', s.first_name, s.middle_name, s.last_name) AS full_name,
            s.class, s.division,
            a.status
        FROM students s
        LEFT JOIN attendance a
            ON a.student_id = s.student_id
           AND a.date = ?
        WHERE s.class = ?
          AND s.division = ?
        ORDER BY s.first_name, s.last_name
    ";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("sss", $selectedDate, $selectedClass, $selectedDivision);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($r = $result->fetch_assoc()) {
        $status = $r['status'] ?? "Not Marked";
        $rows[] = [
            "student_id" => $r['student_id'],
            "full_name"  => $r['full_name'],
            "class"      => $r['class'],
            "division"   => $r['division'],
            "status"     => $status
        ];
        if (isset($summary[$status])) {
            $summary[$status]++;
        } else {
            $summary["Not Marked"]++;
        }
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Attendance Records (Class & Date Wise)</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="staff.css" rel="stylesheet">
</head>
<body class="bg-light">
<?php include 'staff-sidebar.php'; ?>

<?php include 'staff-header.php'; ?>
<div class="container py-4">

  <h3 class="mb-3">Attendance Records</h3>
  <div class="card shadow-sm mb-4">
    <div class="card-body">
      <form class="row g-3" method="get" action="">
        <div class="col-md-4">
          <label class="form-label">Class</label>
          <select name="class" class="form-select" required>
            <option value="">Select Class</option>
            <?php foreach($classes as $c): ?>
              <option value="<?= htmlspecialchars($c) ?>" <?= ($c===$selectedClass?'selected':'') ?>>
                <?= htmlspecialchars($c) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="col-md-4">
          <label class="form-label">Division</label>
          <select name="division" class="form-select" required>
            <option value="">Select Division</option>
            <?php foreach($divisions as $d): ?>
              <option value="<?= htmlspecialchars($d) ?>" <?= ($d===$selectedDivision?'selected':'') ?>>
                <?= htmlspecialchars($d) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="col-md-4">
          <label class="form-label">Date</label>
          <input type="date" name="date" class="form-control" value="<?= htmlspecialchars($selectedDate) ?>" required>
        </div>

        <div class="col-12 d-flex gap-2">
          <button type="submit" class="btn btn-primary">Load Records</button>
          <a class="btn btn-outline-secondary" href="?">Reset</a>
        </div>
      </form>
    </div>
  </div>

  <?php if ($selectedClass !== "" && $selectedDivision !== "" && $selectedDate !== ""): ?>

    <!-- Summary badges -->
    <div class="mb-3 d-flex flex-wrap gap-2">
      <span class="badge bg-success fs-6">Present: <?= (int)$summary["Present"] ?></span>
      <span class="badge bg-danger fs-6">Absent: <?= (int)$summary["Absent"] ?></span>
      <span class="badge bg-warning text-dark fs-6">Late: <?= (int)$summary["Late"] ?></span>
      <span class="badge bg-secondary fs-6">Not Marked: <?= (int)$summary["Not Marked"] ?></span>
    </div>

    <div class="card shadow-sm">
      <div class="card-body">
        <h5 class="card-title mb-3">
          Class: <span class="text-primary"><?= htmlspecialchars($selectedClass) ?> - <?= htmlspecialchars($selectedDivision) ?></span> |
          Date: <span class="text-primary"><?= htmlspecialchars($selectedDate) ?></span>
        </h5>

        <div class="table-responsive">
          <table class="table table-bordered table-striped align-middle text-center">
            <thead class="table-dark">
              <tr>
                <th>#</th>
                <th>Student ID</th>
                <th>Name</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
            <?php if (count($rows) > 0): ?>
              <?php $i=1; foreach($rows as $row): ?>
                <tr>
                  <td><?= $i++ ?></td>
                  <td><?= htmlspecialchars($row['student_id']) ?></td>
                  <td class="text-start"><?= htmlspecialchars($row['full_name']) ?></td>
                  <td>
                    <?php if ($row['status']==="Present"): ?>
                      <span class="badge bg-success">Present</span>
                    <?php elseif ($row['status']==="Absent"): ?>
                      <span class="badge bg-danger">Absent</span>
                    <?php elseif ($row['status']==="Late"): ?>
                      <span class="badge bg-warning text-dark">Late</span>
                    <?php else: ?>
                      <span class="badge bg-secondary">Not Marked</span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="4" class="text-center">No students found for this Class/Division.</td></tr>
            <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

  <?php else: ?>
    <div class="alert alert-info">Please select <strong>Class</strong>, <strong>Division</strong> and <strong>Date</strong> to view attendance.</div>
  <?php endif; ?>

</div>
<?php include 'staff-dashboard-js.php'; ?>
</body>
</html>
