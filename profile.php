<?php
session_start();

// If password changed successfully, trigger redirect
$showSuccess = false;
if (isset($_SESSION['password_changed']) && $_SESSION['password_changed'] === true) {
    $showSuccess = true;
    unset($_SESSION['password_changed']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Change Password - Profile</title>

  <!-- Bootstrap & SweetAlert2 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- Fonts & Styles -->
  <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      margin: 0;
      padding: 0;
      background: linear-gradient(135deg, indigo, #4b0082);
      font-family: 'Quicksand', sans-serif;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .glass-card {
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      border-radius: 20px;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
      border: 1px solid rgba(255, 255, 255, 0.2);
      padding: 2rem;
      color: #fff;
    }

    .glass-card label {
      color: #fff;
      font-weight: 600;
    }

    .glass-card input {
      background: rgba(255, 255, 255, 0.3);
      color: #fff;
      border: none;
    }

    .glass-card input::placeholder {
      color: rgba(255,255,255,0.6);
    }

    .glass-card input:focus {
      background: rgba(255, 255, 255, 0.4);
      box-shadow: none;
    }

    .glass-card h3 {
      font-weight: 700;
      text-align: center;
      margin-bottom: 1.5rem;
    }

    .btn-warning {
      font-weight: 600;
      width: 100%;
    }
  </style>
</head>
<body>
  <div class="glass-card" style="max-width: 500px; width: 100%;">
    <h3>Change Password</h3>

    <?php if (!empty($_SESSION['message'])): ?>
      <div class="alert alert-info alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($_SESSION['message']); unset($_SESSION['message']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php endif; ?>

    <form action="change-password.php" method="POST" novalidate>
      <div class="mb-3">
        <label for="current_password" class="form-label">Current Password</label>
        <input type="password" class="form-control" id="current_password" name="current_password" required autocomplete="current-password">
      </div>
      <div class="mb-3">
        <label for="new_password" class="form-label">New Password</label>
        <input type="password" class="form-control" id="new_password" name="new_password" required autocomplete="new-password" minlength="8">
      </div>
      <div class="mb-3">
        <label for="confirm_password" class="form-label">Confirm New Password</label>
        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required autocomplete="new-password" minlength="8">
      </div>
      <button type="submit" name="change_password" class="btn btn-warning">Change Password</button>
    </form>
  </div>

  <script>
    <?php if ($showSuccess): ?>
    Swal.fire({
      title: 'Password Changed!',
      text: 'You will be redirected shortly...',
      icon: 'success',
      timer: 2000,
      showConfirmButton: false
    }).then(() => {
      window.location.href = document.referrer || 'dashboard.php';
    });
    <?php endif; ?>
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>