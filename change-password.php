<?php
session_start();
require 'dp.php';

// ✅ Determine user type
if (isset($_SESSION['user_id'])) {
    $userType = 'user';
    $id = $_SESSION['user_id'];
} elseif (isset($_SESSION['PatientID'])) {
    $userType = 'patient';
    $id = $_SESSION['PatientID'];
} else {
    $_SESSION['message'] = "Please log in first.";
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current = trim($_POST['current_password'] ?? '');
    $new = trim($_POST['new_password'] ?? '');
    $confirm = trim($_POST['confirm_password'] ?? '');

    // Basic validation
    if (empty($current) || empty($new) || empty($confirm)) {
        $_SESSION['message'] = "All password fields are required.";
        redirectBack($userType);
    }

    if ($new !== $confirm) {
        $_SESSION['message'] = "New passwords do not match.";
        redirectBack($userType);
    }

    if (strlen($new) < 8) {
        $_SESSION['message'] = "New password must be at least 8 characters long.";
        redirectBack($userType);
    }

    // ✅ Fetch hashed password
    if ($userType === 'user') {
        $stmt = $conn->prepare("SELECT password, Role FROM users WHERE id = ?");
    } else {
        $stmt = $conn->prepare("SELECT Password FROM patient WHERE PatientID = ?");
    }

    if (!$stmt) {
        $_SESSION['message'] = "Database error.";
        redirectBack($userType);
    }

    $stmt->bind_param("i", $id);
    $stmt->execute();

    if ($userType === 'user') {
        $stmt->bind_result($hashedPassword, $role);
    } else {
        $stmt->bind_result($hashedPassword);
    }

    if (!$stmt->fetch()) {
        $_SESSION['message'] = "Account not found.";
        $stmt->close();
        redirectBack($userType);
    }
    $stmt->close();

    // ✅ Verify current password
    if (!password_verify($current, $hashedPassword)) {
        $_SESSION['message'] = "Current password is incorrect.";
        redirectBack($userType);
    }

    // ✅ Update password
    $newHashed = password_hash($new, PASSWORD_DEFAULT);

    if ($userType === 'user') {
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
    } else {
        $stmt = $conn->prepare("UPDATE patient SET Password = ? WHERE PatientID = ?");
    }

    if (!$stmt) {
        $_SESSION['message'] = "Database error while updating.";
        redirectBack($userType);
    }

    $stmt->bind_param("si", $newHashed, $id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Password changed successfully.";
    } else {
        $_SESSION['message'] = "Failed to update password.";
    }

    $stmt->close();

    // ✅ Redirect based on role
    if ($userType === 'patient') {
        header('Location: patientdashboard.php');
    } else {
        switch (strtolower($role)) {
            case 'admin':
                header('Location: index.php');
                break;
            case 'doctor':
                header('Location: doctordashboard.php');
                break;
            case 'receptionist':
                header('Location: frontdesk.php');
                break;
            default:
                header('Location: index.php'); // fallback
        }
    }

    exit();
}

// ✅ Helper function to redirect back to profile
function redirectBack($userType) {
    if ($userType === 'patient') {
        header('Location: profile.php');
    } else {
        header('Location: profile.php');
    }
    exit();
}
?>
