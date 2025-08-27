<!-- header.php -->

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Student Management System</title>

  <!-- ✅ Bootstrap 5 CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- ✅ Bootstrap 5 JS Bundle CDN -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>

<!-- ✅ Bootstrap Navbar -->
<nav class="navbar navbar-expand-lg" style="background-color: #FF2DD1; height: 80px">
  <div class="container-fluid justify-content-between">
    <a class="navbar-brand text-white fw-bold me-2" style="font-size: 2rem; " href="#">SMS</a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false"
            aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto d-flex gap-3">
        <li class="nav-item">
          <a class="nav-link btn btn-outline-light btn-sm ms-2" href="index.php">🏠 Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link btn btn-outline-light btn-sm ms-2" href="about.php">📖 About</a>
        </li>
      
        <li class="nav-item">
          <a class="nav-link btn btn-outline-light btn-sm ms-2" href="admin/admin.php">🔐 Admin login</a>
        </li>
        <li class="nav-item">
          <a class="nav-link btn btn-outline-light btn-sm ms-2" href="staff/staff.php">👨‍🏫 Staff login</a>
        </li>
        <li class="nav-item">
          <a class="nav-link btn btn-outline-light btn-sm ms-2" href="Student/login.php">🎓 Student login</a>
        </li>
        </li>
      </ul>
    </div>
  </div>
</nav>
