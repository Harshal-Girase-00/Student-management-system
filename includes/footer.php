<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Management System Footer</title>

  <!-- ✅ Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body id="top">

  <!-- Footer Start -->
  <footer class="text-light" style="background: #292929;">
    <!-- Upper Area -->
    <div class="container py-5">
      <div class="row text-sm">
        
        <!-- Left: Menu -->
        <div class="col-md-4 mb-4 mb-md-0">
          <ul class="list-inline">
            <li class="list-inline-item"><a href="/" class="text-white text-decoration-none">Home</a></li>
            <li class="list-inline-item"><a href="/contact" class="text-white text-decoration-none">Contact</a></li>
            <li class="list-inline-item"><a href="/admin" class="text-white text-decoration-none">Admin</a></li>
            <li class="list-inline-item"><a href="/staff" class="text-white text-decoration-none">Staff</a></li>
            <li class="list-inline-item"><a href="/student" class="text-white text-decoration-none">Student</a></li>
          </ul>
        </div>

        <!-- Middle: Address -->
        <div class="col-md-4 mb-4 mb-md-0">
          <h5 class="text-white fw-semibold mb-2">ADDRESS</h5>
          <p class="mb-0">
            D.C. Patel Navnirman Educational Campus,<br>
            New City Light Road,<br>
            Near Ashirwad Check Post,<br>
            Bharthana (Vesu), Surat – 395017<br>
            <strong>Phone: 9825451048</strong>
          </p>
        </div>

        <!-- Right: SMS -->
        <div class="col-md-4">
          <h5 class="text-white fw-semibold mb-2">SMS</h5>
          <p class="mb-0">Student Management System</p>
        </div>

      </div>
    </div>

    <!-- Bottom Bar -->
    <div style="background: #4D4D4D;">
      <div class="container d-flex justify-content-between align-items-center py-2 text-sm">
        <span class="text-light">Student Management System</span>

        <!-- Back to Top Button -->
        <button type="button" class="btn btn-pink btn-sm rounded-circle d-flex align-items-center justify-content-center"
                style="background-color: rgba(255,45,209,0.9); width: 36px; height: 36px;"
                onclick="window.scrollTo({ top: 0, behavior: 'smooth' })">
          <svg xmlns="http://www.w3.org/2000/svg" class="bi bi-arrow-up" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M8 12a.5.5 0 0 0 .5-.5V3.707l3.146 3.147a.5.5 0 0 0 .708-.708l-4-4-.007-.007a.5.5 0 0 0-.7.007l-4 4a.5.5 0 1 0 .708.708L7.5 3.707V11.5A.5.5 0 0 0 8 12z"/>
          </svg>
        </button>
      </div>
    </div>
  </footer>

  <!-- ✅ Bootstrap 5 JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
