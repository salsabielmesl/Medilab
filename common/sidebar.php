<?php

$current_page = basename($_SERVER['PHP_SELF']);
$role = $_SESSION['role'] ?? '';
?>
<style>
  .sidebar {
    background-color: #c7d2fe;
    min-height: 100vh;
  }

  .sidebar img {
    width: 40px;
    height: auto;
  }

  .sidebar span {
    font-size: 1.25rem;
    font-weight: 600;
    color: #3730a3;
    margin-left: 10px;
    letter-spacing: 0.5px;
  }

  .sidebar .nav-link {
    color: #1e3a8a;
    transition: background-color 0.2s ease, color 0.2s ease;
  }

  .sidebar .nav-link:hover {
    background-color: #a5b4fc;
    color: #1e3a8a;
  }

  .sidebar .nav-link.active {
    background-color: #a5b4fc;
    color: #818cf8 !important;
    font-weight: 600;
  }

  .sidebar .nav-link i {
    color: #818cf8 !important;
  }
</style>

<div class="sidebar pe-4 pb-3">
  <nav class="navbar navbar-light">
    <a class="navbar d-flex align-items-center px-3 py-2">
      <img src="Medilab.png" alt="logo">
      <span class="fs-4 fw-semibold">Medilab</span>
    </a>

    <div class="navbar-nav w-100">
      <?php if ($role === 'admin'): ?>
        <a href="index.php" class="nav-item nav-link <?= ($current_page == 'index.php') ? 'active' : '' ?>">
          <i class="fa fa-tachometer-alt me-2"></i>Dashboard
        </a>
        <a href="deplist.php" class="nav-item nav-link <?= ($current_page == 'deplist.php') ? 'active' : '' ?>">
          <i class="fa fa-building me-2"></i>Department List
        </a>
        <a href="doclist.php" class="nav-item nav-link <?= ($current_page == 'doclist.php') ? 'active' : '' ?>">
          <i class="fa fa-user-md me-2"></i>Doctors List
        </a>
        <a href="stafflist.php" class="nav-item nav-link <?= ($current_page == 'stafflist.php') ? 'active' : '' ?>">
          <i class="fa fa-users me-2"></i>Receptionist List
        </a>
        
        <a href="adminschedule.php" class="nav-item nav-link <?= ($current_page == 'adminschedule.php') ? 'active' : '' ?>">
          <i class="fa fa-calendar-alt me-2"></i>Schedule
        </a>
        <a href="admininvoices.php" class="nav-item nav-link <?= ($current_page == 'admininvoices.php') ? 'active' : '' ?>">
          <i class="fa fa-file-invoice-dollar me-2"></i>Invoices
        </a>
        <a href="adminusers.php" class="nav-item nav-link <?= ($current_page == 'adminusers.php') ? 'active' : '' ?>">
          <i class="fa fa-file-invoice-dollar me-2"></i>Users
        </a>
        <a href="editcarousel.php" class="nav-item nav-link <?= ($current_page == 'editcarousel.php') ? 'active' : '' ?>">
          <i class="fa fa-info-circle me-2"></i>Carousel
        </a>
        <a href="edit_aboutus.php" class="nav-item nav-link <?= ($current_page == 'edit_aboutus.php') ? 'active' : '' ?>">
          <i class="fa fa-info-circle me-2"></i>About Us
        </a>
        <a href="editgallery.php" class="nav-item nav-link <?= ($current_page == 'editgallery.php') ? 'active' : '' ?>">
          <i class="fa fa-info-circle me-2"></i>Gallery
        </a>
        <a href="frontdesk.php" class="nav-item nav-link <?= ($current_page == 'frontdesk.php') ? 'active' : '' ?>">
          <i class="fa fa-calendar-alt me-2"></i>Appointments
        </a>
        <a href="patients.php" class="nav-item nav-link <?= ($current_page == 'patients.php') ? 'active' : '' ?>">
          <i class="fa fa-user-injured me-2"></i>Patients
        </a>
         <a href="doctordashboard.php" class="nav-item nav-link <?= ($current_page == 'doctordashboard.php') ? 'active' : '' ?>">
          <i class="fa fa-tachometer-alt me-2"></i>Doc Dashboard
        </a>
        <a href="doctorschedule.php" class="nav-item nav-link <?= ($current_page == 'doctorschedule.php') ? 'active' : '' ?>">
          <i class="fa fa-calendar-alt me-2"></i>Doc Schedule
        </a>
      <?php endif; ?>

      <?php if ($role === 'doctor'): ?>
        <a href="doctordashboard.php" class="nav-item nav-link <?= ($current_page == 'doctordashboard.php') ? 'active' : '' ?>">
          <i class="fa fa-tachometer-alt me-2"></i>Doc Dashboard
        </a>
        <a href="doctorschedule.php" class="nav-item nav-link <?= ($current_page == 'doctorschedule.php') ? 'active' : '' ?>">
          <i class="fa fa-calendar-alt me-2"></i>Doc Schedule
        </a>
      <?php endif; ?>

      <?php if ($role === 'receptionist'): ?>
        <a href="frontdesk.php" class="nav-item nav-link <?= ($current_page == 'frontdesk.php') ? 'active' : '' ?>">
          <i class="fa fa-calendar-alt me-2"></i>Appointments
        </a>
        <a href="patients.php" class="nav-item nav-link <?= ($current_page == 'patients.php') ? 'active' : '' ?>">
          <i class="fa fa-user-injured me-2"></i>Patients
        </a>
      <?php endif; ?>
    </div>
  </nav>
</div>
