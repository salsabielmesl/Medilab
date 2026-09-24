<nav class="navbar navbar-expand sticky-top px-4 py-0" style="background-color: #818CF8;">
    <a href="index.html" class="navbar-brand d-flex d-lg-none me-4">
        <h2 class="text-primary mb-0"><i class="fa fa-hashtag"></i></h2>
    </a>
    <a href="#" class="sidebar-toggler flex-shrink-0 text-dark">
        <i class="fa fa-bars"></i>
    </a>
    
    <div class="navbar-nav align-items-center ms-auto">
        <div class="nav-item dropdown">
            <a href="#" class="nav-link dropdown-toggle text-dark" data-bs-toggle="dropdown">
  
  <span class="d-none d-lg-inline-flex">
    <?= htmlspecialchars($_SESSION['FullName'] ?? 'Guest') ?>
  </span>
</a>
            <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm rounded-3 mt-2">
                 <li>
                    <a class="dropdown-item" href="profile.php">
                        <i class="fa fa-user me-2"></i>Profile
                    </a>
                </li>
                <li><a class="dropdown-item fw-medium text-danger" href="logout.php">
                    <i class="fa fa-sign-out-alt me-2"></i>Log Out</a></li>
            </ul>
        </div>
    </div>
</nav>
