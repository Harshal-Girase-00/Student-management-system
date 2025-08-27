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

// Handle login
if (isset($_POST['login'])) {
    $username = $con->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    // Fetch student by username
    $stmt = $con->prepare("SELECT * FROM students WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $student = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $student['password'])) {
            // ✅ Sessions set
            $_SESSION['student_id'] = $student['student_id'];
            $_SESSION['student_name'] = $student['first_name'] . " " . $student['last_name'];
            $_SESSION['class'] = $student['class'];        // <-- Add kiya
            $_SESSION['division'] = $student['division'];  // <-- Add kiya

            header("Location: student-dashboard.php"); // redirect to dashboard
            exit;
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "Username not found!";
    }
    $stmt->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login | Student Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .auth-form {
            background-color: #ffffff;
            border-radius: 0.5rem;
            padding: 2rem;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        .brand-logo {
            font-weight: bold;
            font-size: 1.3rem;
            margin-bottom: 1rem;
            text-align: center;
        }
        .btn-login {
            background-color: #198754;
            color: #fff;
        }
        .btn-login:hover {
            background-color: #157347;
            color: #fff;
        }
        input::placeholder {
            font-size: 1rem;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="container vh-100 d-flex justify-content-center align-items-center">
        <div class="col-12 col-sm-10 col-md-6 col-lg-4">
            <div class="auth-form">
                <div class="brand-logo">Student Management System</div>
                <h6 class="text-center text-muted mb-4">Sign in using Student Id</h6>

                <?php if (isset($error)) { ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php } ?>

                <form id="login" method="post" name="login">
                    <div class="mb-3">
                        <input type="text" class="form-control form-control-lg" placeholder="Enter your username" required name="username">
                    </div>
                    <div class="mb-3">
                        <input type="password" class="form-control form-control-lg" placeholder="Enter your password" required name="password">
                    </div>
                    <div class="d-grid mb-3">
                        <button class="btn btn-login btn-lg" type="submit" name="login">Login</button>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="remember" name="remember">
                            <label class="form-check-label" for="remember">Keep me signed in</label>
                        </div>
                        <a href="forgot-password.php" class="text-decoration-none">Forgot password?</a>
                    </div>

                    <div class="d-grid mb-3">
                        <a href="../index.php" class="btn btn-outline-primary">🏠 Back Home</a>
                    </div>

                    <p class="text-center mt-3 mb-0">
                        Don’t have an account? 
                        <a href="register.php" class="fw-bold text-decoration-none">Register here</a>
                    </p>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
