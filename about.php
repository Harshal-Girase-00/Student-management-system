<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management System | About Us</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <!-- Header -->
    <?php include_once('includes/header.php'); ?>

    <!-- Banner -->
    <div class="bg-primary text-white text-center py-5 mb-4">
        <div class="container">
            <h1 class="display-4">About Us</h1>
        </div>
    </div>

    <!-- About Section -->
    <section class="about py-5">
        <div class="container">
            <div class="row align-items-center">
                <!-- Image -->
                <div class="col-md-5 mb-4 mb-md-0">
                    <img src="images/abt.jpg" class="img-fluid rounded" alt="About Us">
                </div>

                <!-- Text -->
<div class="col-md-7">
    <h2 class="mb-3">Our Story</h2>
    <p>This Student Management System is developed as a college minor project using PHP and MySQL. The main aim of this project is to simplify the process of managing student information, attendance, and academic records in an organized way.</p>
    <p>The system allows administrators to add, update, and delete student data efficiently, ensuring smooth management of all academic activities.</p>
    <p>By implementing this project, we have gained practical experience in web development, database management, and PHP programming, enhancing our technical skills and understanding of real-world applications.</p>
</div>

            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include_once('includes/footer.php'); ?>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
