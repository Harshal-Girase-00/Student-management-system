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

// Check student login
if (!isset($_SESSION['student_id'])) {
    header("location: login.php");
    exit();
}

$studentID = $_SESSION['student_id'];

// Fetch student details
$sql = "SELECT * FROM students WHERE student_id = ?";
$stmt = $con->prepare($sql);
if ($stmt === false) {
    die("Prepare failed: " . $con->error);
}
$stmt->bind_param("s", $studentID);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_object();

if (!$student) {
    echo "<script>alert('Student not found for student_id: $studentID.'); window.location='student-dashboard.php';</script>";
    exit();
}

// Update student details (except class & division)
if (isset($_POST['update'])) {
    $first_name = trim($_POST['first_name']);
    $middle_name = trim($_POST['middle_name']);
    $last_name = trim($_POST['last_name']);
    $dob = $_POST['dob'];
    $age = intval($_POST['age']);
    $street = trim($_POST['street']);
    $city = trim($_POST['city']);
    $state = trim($_POST['state']);
    $country = trim($_POST['country']);
    $pincode = trim($_POST['pincode']);
    $phone = trim($_POST['phone']);
    $parent_phone = trim($_POST['parent_phone']);

    $update_sql = "UPDATE students SET first_name=?, middle_name=?, last_name=?, dob=?, age=?, street=?, city=?, state=?, country=?, pincode=?, phone=?, parent_phone=? WHERE student_id=?";
    $stmt = $con->prepare($update_sql);
    if ($stmt === false) {
        die("Prepare failed: " . $con->error);
    }
    $stmt->bind_param("sssisssssssss", $first_name, $middle_name, $last_name, $dob, $age, $street, $city, $state, $country, $pincode, $phone, $parent_phone, $studentID);

    if ($stmt->execute()) {
        echo "<script>alert('Details successfully updated'); window.location='student-dashboard.php';</script>";
    } else {
        echo "<script>alert('Update failed: " . $con->error . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Student Profile | Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="student.css" rel="stylesheet">
</head>
<body>

<?php include("student-sidebar.php"); ?>
<?php include("student-header.php"); ?>

<div class="container-fluid my-4">
    <div class="card p-4 shadow-sm">
        <h3 class="mb-4 text-center text-primary">👤 View / Edit Profile</h3>
        <form method="post">
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">First Name</label>
                    <input type="text" class="form-control" name="first_name" value="<?= htmlentities($student->first_name) ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Middle Name</label>
                    <input type="text" class="form-control" name="middle_name" value="<?= htmlentities($student->middle_name) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Last Name</label>
                    <input type="text" class="form-control" name="last_name" value="<?= htmlentities($student->last_name) ?>" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Date of Birth</label>
                    <input type="date" class="form-control" name="dob" id="dob" value="<?= htmlentities($student->dob) ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Age</label>
                    <input type="number" class="form-control" name="age" id="age" value="<?= htmlentities($student->age) ?>" readonly>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Street Address</label>
                <input type="text" class="form-control" name="street" value="<?= htmlentities($student->street) ?>" required>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">City</label>
                    <input type="text" class="form-control" name="city" value="<?= htmlentities($student->city) ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">State</label>
                    <input type="text" class="form-control" name="state" value="<?= htmlentities($student->state) ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Country</label>
                    <input type="text" class="form-control" name="country" value="<?= htmlentities($student->country) ?>" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Pincode</label>
                    <input type="text" class="form-control" name="pincode" value="<?= htmlentities($student->pincode) ?>" maxlength="6" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Class</label>
                    <input type="text" class="form-control" name="class" value="<?= htmlentities($student->class) ?>" readonly>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Division</label>
                    <input type="text" class="form-control" name="division" value="<?= htmlentities($student->division) ?>" readonly>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Phone</label>
                    <input type="text" class="form-control" name="phone" value="<?= htmlentities($student->phone) ?>" required pattern="[0-9]{10}" title="Enter 10-digit number">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Parent Phone</label>
                <input type="text" class="form-control" name="parent_phone" value="<?= htmlentities($student->parent_phone) ?>" required pattern="[0-9]{10}" title="Enter 10-digit number">
            </div>

            <div class="d-grid gap-2">
                <button type="submit" name="update" class="btn btn-success btn-lg">Update Profile</button>
                <a href="student-dashboard.php" class="btn btn-outline-secondary btn-lg">🏠 Back to Dashboard</a>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Auto-calculate age
document.getElementById("dob").addEventListener("change", function(){
    let dob = new Date(this.value);
    let today = new Date();
    let age = today.getFullYear() - dob.getFullYear();
    let m = today.getMonth() - dob.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) age--;
    document.getElementById("age").value = age;
});
</script>

</body>
</html>

<?php
$con->close();
?>
