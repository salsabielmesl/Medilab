<?php
session_start();
require_once 'dp.php'; 

$error = '';
$login_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // SIGN UP
    if (isset($_POST['signup'])) {
        $name = trim($_POST['signupName']);
        $email = trim($_POST['signupEmail']);
        $password = $_POST['signupPassword'];
        $confirmPassword = $_POST['signupConfirmPassword'];

        if ($password !== $confirmPassword) {
            $error = "Passwords do not match.";
        } else {
            $check = $conn->prepare("SELECT * FROM patient WHERE Email = ?");
            if (!$check) {
                die("Prepare failed: " . $conn->error);
            }
            $check->bind_param("s", $email);
            $check->execute();
            $result = $check->get_result();

            if ($result->num_rows > 0) {
                $error = "Email is already registered.";
            } else {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $dummyDOB = '1970-01-01';
                $stmt = $conn->prepare("INSERT INTO patient (FullName, Email, Password, DOB) VALUES (?, ?, ?, ?)");
                if (!$stmt) {
                    die("Prepare failed: " . $conn->error);
                }
                $stmt->bind_param("ssss", $name, $email, $hashedPassword, $dummyDOB);

                if ($stmt->execute()) {
                    $_SESSION['PatientID'] = $conn->insert_id;
                    $_SESSION['FullName'] = $name;
                    header("Location: completedetails.php");
                    exit;
                } else {
                    $error = "Registration failed. Please try again.";
                }
                $stmt->close();
            }
            $check->close();
        }
    }

    // LOGIN
    if (isset($_POST['login'])) {
        $email = trim($_POST['loginEmail']);
        $password = $_POST['loginPassword'];

        $stmt = $conn->prepare("SELECT PatientID, FullName, Password FROM patient WHERE Email = ?");
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['Password'])) {
                $_SESSION['PatientID'] = $user['PatientID'];
                $_SESSION['FullName'] = $user['FullName'];
                header("Location: patientdashboard.php");
                exit;
            } else {
                $login_error = "Incorrect password.";
            }
        } else {
            $login_error = "No account found with that email.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Medilab Login/Signup</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    html, body {
      height: 100%;
      margin: 0;
      background: linear-gradient(135deg, #cfd9df 0%, #e2ebf0 100%);
      font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
    }
    .login-wrapper {
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 15px;
    }
    .login-box {
      background: rgba(255, 255, 255, 0.95);
      border-radius: 15px;
      padding: 30px 25px 25px;
      max-width: 450px;
      width: 100%;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
      text-align: center;
      backdrop-filter: blur(10px);
      transition: box-shadow 0.3s ease;
    }
    .login-box:hover {
      box-shadow: 0 12px 36px rgba(0, 0, 0, 0.18);
    }
    .login-box img {
      width: 320px;
      max-width: 100%;
      height: auto;
      border-radius: 12px;
      margin-bottom: 20px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    .form-floating>input:focus {
      border-color: #0d6efd;
      box-shadow: 0 0 0 0.25rem rgb(13 110 253 / 0.25);
    }
    .btn-login {
      padding: 10px;
      font-size: 1rem;
      font-weight: 600;
      border-radius: 8px;
      transition: background-color 0.3s ease;
    }
    .btn-login:hover {
      background-color: #084298;
    }
    .no-hover:hover {
      background-color: #0d6efd !important;
    }
    .forgot-link {
      display: block;
      margin-top: 12px;
      font-size: 0.85rem;
      color: #6c757d;
      text-decoration: none;
      transition: color 0.2s ease;
    }
    .forgot-link:hover {
      color: #0d6efd;
      text-decoration: underline;
    }
    .toggle-link {
      margin-top: 16px;
      font-size: 0.85rem;
      color: #0d6efd;
      cursor: pointer;
      display: block;
    }
    .form-section {
      display: none;
    }
    .form-section.active {
      display: block;
    }
    .signup-form .form-floating {
      margin-bottom: 0.5rem !important;
    }
    .signup-form input {
      padding: 6px 10px !important;
      font-size: 0.85rem !important;
    }
    .signup-form label {
      font-size: 0.8rem !important;
    }
    .signup-form .btn-login {
      padding: 8px;
      font-size: 0.9rem;
    }
    .alert {
      text-align: left;
    }
  </style>
</head>

<body>
  <div class="login-wrapper">
    <div class="login-box shadow-sm">
      <img src="medilabpic2.png" alt="Login Image" />

      <?php if (!empty($error)): ?>
        <div class="alert alert-danger text-start"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form id="loginForm" class="form-section active" method="POST" action="">
        <input type="hidden" name="login" value="1" />
        <div class="form-floating mb-3 text-start">
          <input type="email" class="form-control" id="loginEmail" name="loginEmail" placeholder="name@example.com" required />
          <label for="loginEmail">Email address</label>
        </div>

        <div class="form-floating mb-3 text-start">
          <input type="password" class="form-control" id="loginPassword" name="loginPassword" placeholder="Password" required />
          <label for="loginPassword">Password</label>
        </div>

        <button type="submit" class="btn btn-primary btn-login w-100">Login</button>
        
        <span class="toggle-link" onclick="toggleForm()">Don't have an account? Sign Up</span>

        <?php if (!empty($login_error)): ?>
          <div class="alert alert-danger mt-3 text-start"><?= htmlspecialchars($login_error) ?></div>
        <?php endif; ?>
      </form>

      <form id="signupForm" class="form-section signup-form" method="POST" action="">
        <input type="hidden" name="signup" value="1" />

        <div class="form-floating mb-3 text-start">
          <input type="text" class="form-control" id="signupName" name="signupName" placeholder="Full Name" required />
          <label for="signupName">Full Name</label>
        </div>

        <div class="form-floating mb-3 text-start">
          <input type="email" class="form-control" id="signupEmail" name="signupEmail" placeholder="name@example.com" required />
          <label for="signupEmail">Email address</label>
        </div>

        <div class="form-floating mb-3 text-start">
          <input type="password" class="form-control" id="signupPassword" name="signupPassword" placeholder="Password" required />
          <label for="signupPassword">Password</label>
        </div>

        <div class="form-floating mb-3 text-start">
          <input type="password" class="form-control" id="signupConfirmPassword" name="signupConfirmPassword" placeholder="Confirm Password" required />
          <label for="signupConfirmPassword">Confirm Password</label>
        </div>

        <button type="submit" class="btn btn-primary btn-login no-hover w-100">Sign Up</button>
        <span class="toggle-link" onclick="toggleForm()">Already have an account? Login</span>
      </form>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    function toggleForm() {
      document.getElementById("loginForm").classList.toggle("active");
      document.getElementById("signupForm").classList.toggle("active");
    }
  </script>
</body>
</html>
