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

// Auto-generate Staff ID
$result = $con->query("SELECT staff_id FROM staff ORDER BY staff_id DESC LIMIT 1");
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $lastId = intval(substr($row['staff_id'], 2)); 
    $newId = "ST" . str_pad($lastId + 1, 3, "0", STR_PAD_LEFT);
} else {
    $newId = "ST001";
}

// When form submitted
if (isset($_POST['submit'])) {
    $staff_id    = $newId;
    $name        = $_POST['name'];
    $email       = $_POST['email'];
    $phone       = $_POST['phone'];
    $username    = $_POST['username'];
    $password    = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO staff (staff_id, name, email, phone,  username, password) 
            VALUES ('$staff_id', '$name', '$email', '$phone','$username', '$password')";
    
    if ($con->query($sql) === TRUE) {
        echo "<script>alert('✅ Staff Added Successfully with ID: $staff_id');</script>";
    } else {
        echo "❌ Error: " . $con->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Staff</title>
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link href="admin.css" rel="stylesheet">
</head>
<body class="bg-light">
  <?php include("sidebar.php"); ?>
  <?php include("header.php"); ?>
  
<div class="container mt-5">
    <div class="card shadow p-4">
        <h3 class="mb-3 text-center">Add New Staff</h3>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Staff ID</label>
                <input type="text" class="form-control" value="<?php echo $newId; ?>" readonly>
            </div>
            <div class="mb-3">
                <label class="form-label">Full Name *</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email *</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Phone *</label>
                <input type="tel" name="phone" class="form-control" pattern="[0-9]{10}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Username *</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password *</label>
                <input type="password" name="password" class="form-control" minlength="6" required>
            </div>
            
            <button type="submit" name="submit" class="btn btn-primary w-100">Add Staff</button>
        </form>
    </div>
</div>
</body>
<?php include("dashboardjs.php"); ?>
</html>
