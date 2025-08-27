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

// Fetch unique classes for dropdown (numeric order)
$class_options = [];
$class_query = $con->query("SELECT DISTINCT class_name FROM addclass ORDER BY CAST(class_name AS UNSIGNED), class_name");
if ($class_query->num_rows > 0) {
    while ($row = $class_query->fetch_assoc()) {
        $class_options[] = $row['class_name'];
    }
}

// Handle form submission
if (isset($_POST['register'])) {
    $first_name = $con->real_escape_string($_POST['firstName']);
    $middle_name = $con->real_escape_string($_POST['middleName']);
    $last_name = $con->real_escape_string($_POST['lastName']);
    $dob = $_POST['dob'];
    $age = intval($_POST['age']);
    $street = $con->real_escape_string($_POST['street']);
    $city = $con->real_escape_string($_POST['city']);
    $state = $con->real_escape_string($_POST['state']);
    $country = $con->real_escape_string($_POST['country']);
    $pincode = $con->real_escape_string($_POST['pincode']);
    $username = $con->real_escape_string($_POST['studentId']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $class = $_POST['class'];
    $division = $_POST['division'];
    $phone = $con->real_escape_string($_POST['phone']);
    $parent_phone = $con->real_escape_string($_POST['parent_phone']);

    // Check if username exists
    $stmt = $con->prepare("SELECT * FROM students WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $error = "Username already taken!";
    } else {
        $stmt = $con->prepare("INSERT INTO students (first_name, middle_name, last_name, dob, age, street, city, state, country, pincode, username, password, class, division, phone, parent_phone) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssisssssssssss", $first_name, $middle_name, $last_name, $dob, $age, $street, $city, $state, $country, $pincode, $username, $password, $class, $division, $phone, $parent_phone);

        if ($stmt->execute()) {
            $success = "Registration Successful! You can now login.";
        } else {
            $error = "Error: " . $con->error;
        }
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Student Registration | SMS</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    body { background-color: #f8f9fa; }
    .reg-form { background: #fff; padding: 2rem; border-radius: 0.5rem; box-shadow: 0 0 15px rgba(0,0,0,0.1); }
    .form-title { text-align:center; margin-bottom:1.5rem; font-weight:bold; color:#198754; }
</style>
</head>
<body>
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6">
            <div class="reg-form">
                <h3 class="form-title">🎓 Student Registration</h3>

                <?php if(isset($error)){ ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php } ?>
                <?php if(isset($success)){ ?>
                    <div class="alert alert-success"><?= $success ?></div>
                <?php } ?>

                <form method="POST" id="registrationForm">
                    <!-- Name -->
                    <div class="row mb-3">
                        <div class="col"><label class="form-label">First Name</label><input type="text" class="form-control" name="firstName" required></div>
                        <div class="col"><label class="form-label">Middle Name</label><input type="text" class="form-control" name="middleName"></div>
                        <div class="col"><label class="form-label">Last Name</label><input type="text" class="form-control" name="lastName" required></div>
                    </div>

                    <!-- DOB & Age -->
                    <div class="row mb-3">
                        <div class="col"><label class="form-label">Date of Birth</label><input type="date" class="form-control" name="dob" id="dob" required></div>
                        <div class="col"><label class="form-label">Age</label><input type="number" class="form-control" name="age" id="age" readonly></div>
                    </div>

                    <!-- Address -->
                    <div class="mb-3"><label class="form-label">Street Address</label><input type="text" class="form-control" name="street" required></div>
                    <div class="row mb-3">
                        <div class="col-md-6"><label class="form-label">City</label><input type="text" class="form-control" name="city" required></div>
                        <div class="col-md-6"><label class="form-label">State</label><input type="text" class="form-control" name="state" required></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6"><label class="form-label">Country</label><input type="text" class="form-control" name="country" value="India" required></div>
                        <div class="col-md-6"><label class="form-label">Pincode</label><input type="text" class="form-control" name="pincode" maxlength="6" required></div>
                    </div>

                    <!-- Mobile Numbers -->
                    <div class="row mb-3">
                        <div class="col-md-6"><label class="form-label">Mobile Number</label><input type="text" class="form-control" name="phone" maxlength="15" required></div>
                        <div class="col-md-6"><label class="form-label">Parent Mobile Number</label><input type="text" class="form-control" name="parent_phone" maxlength="15" required></div>
                    </div>

                    <!-- Username & Password -->
                    <div class="mb-3"><label class="form-label">Username</label><input type="text" class="form-control" name="studentId" required></div>
                    <div class="row mb-3">
                        <div class="col-md-6"><label class="form-label">Password</label><input type="password" class="form-control" name="password" id="password" required></div>
                        <div class="col-md-6"><label class="form-label">Confirm Password</label><input type="password" class="form-control" name="confirmPassword" id="confirmPassword" required></div>
                    </div>

                    <!-- Class & Division -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Class</label>
                            <select name="class" class="form-select" required>
                                <option value="">Select Class</option>
                                <?php foreach($class_options as $cls){ ?>
                                    <option value="<?= $cls ?>"><?= $cls ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Division</label>
                            <select name="division" class="form-select" required>
                                <option value="">Select Division</option>
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
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" name="register" class="btn btn-success btn-lg">Register</button>
                        <a href="student.php" class="btn btn-outline-primary">🏠 Back to login</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Auto calculate Age
document.getElementById("dob").addEventListener("change", function(){
    let dob = new Date(this.value),
        today = new Date();
    let age = today.getFullYear() - dob.getFullYear();
    let m = today.getMonth() - dob.getMonth();
    if(m<0 || (m===0 && today.getDate()<dob.getDate())) age--;
    document.getElementById("age").value = age;
});

// Password Match
document.getElementById("registrationForm").addEventListener("submit", function(e){
    let pass = document.getElementById("password").value;
    let cpass = document.getElementById("confirmPassword").value;
    if(pass !== cpass){
        e.preventDefault();
        alert("Passwords do not match!");
    }
});
</script>
</body>
</html>
