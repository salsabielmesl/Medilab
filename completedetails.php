<?php
session_start();
require_once 'dp.php';

if (!isset($_SESSION['PatientID'])) {
    header("Location: login.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate inputs
    $dob = $_POST['dob'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $phone = trim($_POST['phone'] ?? '');

    // Validate DOB format YYYY-MM-DD
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dob)) {
        $error = "Invalid date format for Date of Birth.";
    } elseif (!in_array($gender, ['Male', 'Female', 'Other'])) {
        $error = "Please select a valid gender.";
    } elseif (empty($phone) || !preg_match('/^\+?\d{6,15}$/', $phone)) {
        $error = "Please enter a valid phone number (digits, optional +).";
    } else {
        // Update patient info
        $stmt = $conn->prepare("UPDATE patient SET DOB = ?, Gender = ?, Phone = ? WHERE PatientID = ?");
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("sssi", $dob, $gender, $phone, $_SESSION['PatientID']);

        if ($stmt->execute()) {
            header("Location: patientdashboard.php");
            exit;
        } else {
            $error = "Failed to save details. Please try again.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Complete Your Profile</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-light d-flex justify-content-center align-items-center" style="height: 100vh;">
  <div class="card p-4 shadow" style="max-width: 400px; width: 100%;">
    <h4 class="mb-3">Complete Your Profile</h4>

    <?php if ($error): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" novalidate>
      <div class="mb-3">
        <label for="dob" class="form-label">Date of Birth</label>
        <input type="date" class="form-control" id="dob" name="dob" required />
      </div>

      <div class="mb-3">
        <label class="form-label d-block">Gender</label>
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="radio" name="gender" id="genderMale" value="Male" required />
          <label class="form-check-label" for="genderMale">Male</label>
        </div>
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="radio" name="gender" id="genderFemale" value="Female" />
          <label class="form-check-label" for="genderFemale">Female</label>
        </div>
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="radio" name="gender" id="genderOther" value="Other" />
          <label class="form-check-label" for="genderOther">Other</label>
        </div>
      </div>

      <div class="mb-3">
        <label for="phone" class="form-label">Phone Number</label>
        <input
          type="tel"
          class="form-control"
          id="phone"
          name="phone"
          placeholder="+1234567890"
          pattern="\+?\d{6,15}"
          required
        />
        <div class="form-text">Enter digits only, optional +, min 6 and max 15 digits.</div>
      </div>

      <button type="submit" class="btn btn-primary w-100">Submit</button>
    </form>
  </div>
</body>
</html>
