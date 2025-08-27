<?php
session_start();
/* OPTIONAL AUTH
if (!isset($_SESSION['staff_id'])) { header("Location: login.php"); exit(); }
}
*/

// ------ DB CONNECT ------
$host = "localhost";
$user = "root";
$pass = "";
$db   = "studentms1";
$con = new mysqli($host, $user, $pass, $db);
if ($con->connect_error) { die("DB Connection failed: " . $con->connect_error); }
$con->set_charset("utf8mb4");

// ------ LOAD FILTER OPTIONS ------
$classes   = [];
$divisions = [];
$resC = $con->query("SELECT DISTINCT class FROM students ORDER BY class");
while ($r = $resC->fetch_assoc()) { $classes[] = $r['class']; }
$resD = $con->query("SELECT DISTINCT division FROM students ORDER BY division");
while ($r = $resD->fetch_assoc()) { $divisions[] = $r['division']; }

// ------ READ FILTERS ------
$selectedClass    = isset($_GET['class']) ? trim($_GET['class']) : "";
$selectedDivision = isset($_GET['division']) ? trim($_GET['division']) : "";
$selectedMonth    = isset($_GET['month']) ? trim($_GET['month']) : ""; // format: YYYY-MM
$selectedStudent  = isset($_GET['student_id']) ? trim($_GET['student_id']) : ""; // optional

// Load students for dropdown when class/div chosen
$studentsForFilter = [];
if ($selectedClass !== "" && $selectedDivision !== "") {
    $stmtS = $con->prepare("SELECT student_id, CONCAT_WS(' ', first_name, middle_name, last_name) AS full_name 
                            FROM students WHERE class=? AND division=? ORDER BY first_name, last_name");
    $stmtS->bind_param("ss", $selectedClass, $selectedDivision);
    $stmtS->execute();
    $rs = $stmtS->get_result();
    while($row = $rs->fetch_assoc()){ $studentsForFilter[] = $row; }
    $stmtS->close();
}

// ------ SUMMARY QUERY ------
$rows = []; // summary rows
$totalSummary = ["Present"=>0, "Absent"=>0, "Late"=>0, "Marked"=>0];

if ($selectedClass !== "" && $selectedDivision !== "" && $selectedMonth !== "") {
    // We will aggregate attendance for the month: DATE_FORMAT(a.date,'%Y-%m') = ? (YYYY-MM)
    // LEFT JOIN keeps students even if they have 0 entries in that month.
    $baseSql = "
        SELECT 
            s.student_id,
            CONCAT_WS(' ', s.first_name, s.middle_name, s.last_name) AS full_name,
            s.class,
            s.division,
            SUM(CASE WHEN a.status='Present' THEN 1 ELSE 0 END) AS present_cnt,
            SUM(CASE WHEN a.status='Absent'  THEN 1 ELSE 0 END) AS absent_cnt,
            SUM(CASE WHEN a.status='Late'    THEN 1 ELSE 0 END) AS late_cnt
        FROM students s
        LEFT JOIN attendance a
               ON a.student_id = s.student_id
              AND DATE_FORMAT(a.date, '%Y-%m') = ?
        WHERE s.class = ?
          AND s.division = ?
    ";
    $params = [$selectedMonth, $selectedClass, $selectedDivision];
    $types  = "sss";

    if ($selectedStudent !== "" && $selectedStudent !== "all") {
        $baseSql .= " AND s.student_id = ? ";
        $params[] = $selectedStudent;
        $types   .= "s";
    }

    $baseSql .= " GROUP BY s.student_id, full_name, s.class, s.division
                  ORDER BY s.first_name, s.last_name";

    $stmt = $con->prepare($baseSql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($r = $result->fetch_assoc()) {
        $present = (int)$r['present_cnt'];
        $absent  = (int)$r['absent_cnt'];
        $late    = (int)$r['late_cnt'];
        $marked  = $present + $absent + $late;
        $pct     = ($marked > 0) ? round(($present * 100.0) / $marked, 2) : 0.0;

        $rows[] = [
            "student_id" => $r['student_id'],
            "full_name"  => $r['full_name'],
            "class"      => $r['class'],
            "division"   => $r['division'],
            "present"    => $present,
            "absent"     => $absent,
            "late"       => $late,
            "marked"     => $marked,
            "percent"    => $pct
        ];

        $totalSummary["Present"] += $present;
        $totalSummary["Absent"]  += $absent;
        $totalSummary["Late"]    += $late;
        $totalSummary["Marked"]  += $marked;
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Attendance Summary (Monthly)</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- (optional) icons + your css -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
  <link href="staff.css" rel="stylesheet">
</head>
<body class="bg-light">
<?php include 'staff-sidebar.php'; ?>
<?php include 'staff-header.php'; ?>

<div class="container py-4">
  <h3 class="mb-3">Monthly Attendance Summary</h3>

  <div class="card shadow-sm mb-4">
    <div class="card-body">
      <form class="row g-3" method="get" action="">
        <div class="col-md-3">
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

        <div class="col-md-3">
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

        <div class="col-md-3">
          <label class="form-label">Month</label>
          <input type="month" name="month" class="form-control" value="<?= htmlspecialchars($selectedMonth) ?>" required>
        </div>

        <div class="col-md-3">
          <label class="form-label">Student (optional)</label>
          <select name="student_id" class="form-select" <?= ($selectedClass===""||$selectedDivision==="")?'disabled':''; ?>>
            <option value="all">All Students</option>
            <?php foreach($studentsForFilter as $stu): ?>
              <option value="<?= $stu['student_id']; ?>" <?= ($selectedStudent===$stu['student_id']?'selected':'') ?>>
                <?= htmlspecialchars($stu['full_name'])." (ID: ".$stu['student_id'].")" ?>
              </option>
            <?php endforeach; ?>
          </select>
          <?php if ($selectedClass==="" || $selectedDivision===""): ?>
            <div class="form-text">Select Class & Division to filter by student.</div>
          <?php endif; ?>
        </div>

        <div class="col-12 d-flex gap-2">
          <button type="submit" class="btn btn-primary"><i class="bi bi-funnel"></i> Show Summary</button>
          <a class="btn btn-outline-secondary" href="?"><i class="bi bi-arrow-counterclockwise"></i> Reset</a>
          <?php if(!empty($rows)): ?>
          <button type="button" id="exportCsvBtn" class="btn btn-success"><i class="bi bi-filetype-csv"></i> Export CSV</button>
          <button type="button" onclick="window.print()" class="btn btn-outline-dark"><i class="bi bi-printer"></i> Print</button>
          <?php endif; ?>
        </div>
      </form>
    </div>
  </div>

  <?php if ($selectedClass !== "" && $selectedDivision !== "" && $selectedMonth !== ""): ?>

    <div class="mb-3 d-flex flex-wrap gap-2">
      <span class="badge bg-success fs-6">Present: <?= (int)$totalSummary["Present"] ?></span>
      <span class="badge bg-danger  fs-6">Absent: <?= (int)$totalSummary["Absent"] ?></span>
      <span class="badge bg-warning text-dark fs-6">Late: <?= (int)$totalSummary["Late"] ?></span>
      <span class="badge bg-secondary fs-6">Marked Entries: <?= (int)$totalSummary["Marked"] ?></span>
    </div>

    <div class="card shadow-sm">
      <div class="card-body">
        <h5 class="card-title mb-3">
          Class: <span class="text-primary"><?= htmlspecialchars($selectedClass) ?> - <?= htmlspecialchars($selectedDivision) ?></span> |
          Month: <span class="text-primary"><?= htmlspecialchars($selectedMonth) ?></span>
          <?php if ($selectedStudent && $selectedStudent!=="all"): ?>
            | Student ID: <span class="text-primary"><?= htmlspecialchars($selectedStudent) ?></span>
          <?php endif; ?>
        </h5>

        <div class="table-responsive">
          <table id="summaryTable" class="table table-bordered table-striped align-middle text-center">
            <thead class="table-dark">
              <tr>
                <th>#</th>
                <th>Student ID</th>
                <th>Name</th>
                <th>Present</th>
                <th>Absent</th>
                <th>Late</th>
                <th>Total Marked</th>
                <th>% Present</th>
              </tr>
            </thead>
            <tbody>
              <?php if(count($rows)>0): $i=1; foreach($rows as $row): ?>
                <tr>
                  <td><?= $i++ ?></td>
                  <td><?= htmlspecialchars($row['student_id']) ?></td>
                  <td class="text-start"><?= htmlspecialchars($row['full_name']) ?></td>
                  <td><span class="badge bg-success"><?= (int)$row['present'] ?></span></td>
                  <td><span class="badge bg-danger"><?= (int)$row['absent'] ?></span></td>
                  <td><span class="badge bg-warning text-dark"><?= (int)$row['late'] ?></span></td>
                  <td><span class="badge bg-secondary"><?= (int)$row['marked'] ?></span></td>
                  <td><strong><?= number_format($row['percent'], 2) ?>%</strong></td>
                </tr>
              <?php endforeach; else: ?>
                <tr><td colspan="8" class="text-center">No data for this filter.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

  <?php else: ?>
    <div class="alert alert-info">Select <strong>Class</strong>, <strong>Division</strong> and <strong>Month</strong> to view summary.</div>
  <?php endif; ?>

</div>

<!-- Simple CSV Export (client-side) -->
<script>
document.getElementById('exportCsvBtn')?.addEventListener('click', function () {
  const table = document.getElementById('summaryTable');
  let csv = [];
  for (let r = 0; r < table.rows.length; r++) {
    let row = [], cols = table.rows[r].querySelectorAll('th, td');
    for (let c = 0; c < cols.length; c++) {
      // strip badges text
      let text = cols[c].innerText.trim().replace(/(\r\n|\n|\r)/gm," ");
      // escape quotes
      text = '"' + text.replace(/"/g, '""') + '"';
      row.push(text);
    }
    csv.push(row.join(','));
  }
  const blob = new Blob([csv.join('\n')], { type: 'text/csv;charset=utf-8;' });
  const url  = URL.createObjectURL(blob);
  const a    = document.createElement('a');
  a.href = url;
  a.download = 'attendance_summary_<?= htmlspecialchars($selectedClass) ?>_<?= htmlspecialchars($selectedDivision) ?>_<?= htmlspecialchars($selectedMonth) ?>.csv';
  a.click();
  URL.revokeObjectURL(url);
});
</script>
<?php include 'staff-dashboard-js.php'; ?>
</body>
</html>
