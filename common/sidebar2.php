<?php
  // Get the current page filename
  $current_page = basename($_SERVER['PHP_SELF']);
?>
<style>
  /* Sidebar container */
  .sidebar {
    background-color: #c7d2fe; /* indigo-200 */
    min-height: 100vh;
  }

  .sidebar img {
    width: 40px;
    height: auto;
  }

  .sidebar span {
    font-size: 1.25rem;
    font-weight: 600;
    color: #3730a3; /* indigo-700 */
    margin-left: 10px;
    letter-spacing: 0.5px;
  }

  .sidebar .nav-link {
    color: #1e3a8a; /* indigo-800 */
    transition: background-color 0.2s ease, color 0.2s ease;
  }

  /* Hover style */
  .sidebar .nav-link:hover {
    background-color: #a5b4fc; /* indigo-300 */
    color: #1e3a8a;
  }

  /* Active link style */
  .sidebar .nav-link.active {
    background-color: #a5b4fc; /* indigo-300 */
    color: #818cf8 !important; /* indigo-400 */
    font-weight: 600;
  }

  /* Icon color in all states */
  .sidebar .nav-link i {
    color: #818cf8 !important; /* match navbar color */
  }
</style>

<div class="sidebar pe-4 pb-3">
  <nav class="navbar navbar-light">
    <a class="navbar d-flex align-items-center px-3 py-2">
      <img src="Medilab.png" alt="logo">
      <span class="fs-4 fw-semibold">Medilab</span>
    </a>

    <div class="d-flex align-items-center ms-4 mb-4">
      <!-- optional content -->
    </div>

    <div class="navbar-nav w-100">
      <a href="patientdashboard.php" class="nav-item nav-link <?= ($current_page == 'patientdashboard.php') ? 'active' : '' ?>">
        <i class="fa fa-tachometer-alt me-2"></i>Dashboard
      </a>
      <!-- add more nav links here if needed -->
    </div>
  </nav>
</div>
