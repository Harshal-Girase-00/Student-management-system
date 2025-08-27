  <!-- Toggle Button -->
  <button class="toggle-btn" id="toggleBtn"><i class="bi bi-list"></i></button>
  <div class="overlay" id="overlay"></div>
<div class="sidebar" id="sidebar">
  <h3><i class="bi bi-mortarboard-fill"></i> SMS</h3>
  
  <a href="dashboard.php" class="active"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>

  <!-- Staff Menu with Submenu -->
<!-- Staff Menu -->
<a class="d-flex justify-content-between align-items-center" 
   data-bs-toggle="collapse" href="#staffMenu" role="button" aria-expanded="false" aria-controls="staffMenu">
  <span><i class="bi bi-layers me-2"></i> Staff</span>
  🧑‍🏫
</a>
<div class="collapse ps-4" id="staffMenu">
  <a href="addstaff.php"><i class="bi bi-person-plus me-2"></i> Add Staff</a>
  <a href="view-staff.php"><i class="bi bi-eye me-2"></i> View Staff</a>
</div>

<!-- Students Menu -->
<a href="view-student.php" class="d-flex justify-content-between align-items-center">
  <span><i class="bi bi-people me-2"></i> View Students</span> 🎓
</a>

<!-- Class Menu -->
<a class="d-flex justify-content-between align-items-center" 
   data-bs-toggle="collapse" href="#classMenu" role="button" aria-expanded="false" aria-controls="classMenu">
  <span><i class="bi bi-layers me-2"></i> Class</span>
  *
</a>
<div class="collapse ps-4" id="classMenu">
  <a href="add-class.php"><i class="bi bi-person-plus me-2"></i> Add Class</a>
  <a href="view-class.php"><i class="bi bi-eye me-2"></i> Manage Class</a>
</div>


  <!-- Homework -->
 

  <!-- Notice -->
  <a class="d-flex justify-content-between align-items-center" 
     data-bs-toggle="collapse" href="#noticeMenu" role="button" aria-expanded="false" aria-controls="noticeMenu">
    <span><i class="bi bi-megaphone me-2"></i> Notice</span>
    <i class="bi bi-chevron-down small"></i>
  </a>
  <div class="collapse ps-4" id="noticeMenu">
    <a href="add-notice.php"><i class="bi bi-plus-square me-2"></i> Add Notice</a>
    <a href="view-notice.php"><i class="bi bi-eye me-2"></i> Manage  Notices</a>
  </div>
</div>