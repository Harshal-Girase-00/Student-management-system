  <!-- Toggle Button -->
  <button class="toggle-btn" id="toggleBtn"><i class="bi bi-list"></i></button>
  <div class="overlay" id="overlay"></div>
  <div class="sidebar" id="sidebar">
    <h3><i class="bi bi-mortarboard-fill"></i> SMS</h3>

    <a href="staff-dashboard.php"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>

    <!-- Staff Menu with Submenu -->
    <a class="d-flex justify-content-between align-items-center"
      data-bs-toggle="collapse" href="#attendanceMenu" role="button" aria-expanded="false" aria-controls="attendanceMenu">
      <span><i class="bi bi-calendar-check me-2"></i> Attendance</span>

    </a>
    <div class="collapse ps-4" id="attendanceMenu">
      <a href="mark-attendance.php"><i class="bi bi-person-plus me-2"></i> Mark Attendance</a>
      <a href="attendance-records.php"><i class="bi bi-eye me-2"></i> Attendance Records</a>
      <a href="attendance-summary.php"><i class="bi bi-eye me-2"></i> Attendance Summary</a>
    </div>

    <!-- Students Menu -->
    <a href="view-student.php" class="d-flex justify-content-between align-items-center">
      <span><i class="bi bi-people me-2"></i> View Students</span> 🎓
    </a>


    <!-- Homework -->
    <a class="d-flex justify-content-between align-items-center"
      data-bs-toggle="collapse" href="#HomeworkMenu" role="button" aria-expanded="false" aria-controls="HomeworkMenu">
      <span><i class="bi bi-file-earmark-text"></i> Homework</span>
      <i class="bi bi-chevron-down small"></i>
    </a>
    <div class="collapse ps-4" id="HomeworkMenu">
      <a href="add-homework.php"><i class="bi bi-plus-square me-2"></i> Add Homework</a>
      <a href="manage-homework.php"><i class="bi bi-eye me-2"></i> Manage Homework</a>
    </div>

     <a class="d-flex justify-content-between align-items-center"
      data-bs-toggle="collapse" href="#NoteskMenu" role="button" aria-expanded="false" aria-controls="NoteskMenu">
      <span><i class="bi bi-file-earmark-text"></i> Notes</span>
      <i class="bi bi-chevron-down small"></i>
    </a>
    <div class="collapse ps-4" id="NoteskMenu">
      <a href="upload-notes.php"><i class="bi bi-plus-square me-2"></i> Upload Notes</a>
      <a href="manage-notes.php"><i class="bi bi-eye me-2"></i> Manage Notes</a>
    </div>
    

    <!-- Notice -->
    <a class="d-flex justify-content-between align-items-center"
      data-bs-toggle="collapse" href="#noticeMenu" role="button" aria-expanded="false" aria-controls="noticeMenu">
      <span><i class="bi bi-megaphone me-2"></i> Notice</span>
      <i class="bi bi-chevron-down small"></i>
    </a>
    <div class="collapse ps-4" id="noticeMenu">
      <a href="add-notice.php"><i class="bi bi-plus-square me-2"></i> Add Notice</a>
      <a href="view-notice.php"><i class="bi bi-eye me-2"></i> Manage Notices</a>
    </div>
  </div>