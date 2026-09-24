<?php
session_start();

include 'dp.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: adminlogin.php?error=invalid_request");
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

if (empty($username) || empty($password)) {
    header("Location: adminlogin.php?error=empty_fields");
    exit;
}

// Check user in users table
$stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows === 1) {
    $user = $result->fetch_assoc();

    if (password_verify($password, $user['password'])) {
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['user_id'] = $user['id'];

        // Redirect based on role
        if ($user['role'] === 'admin') {
            header("Location: index.php");
        } elseif ($user['role'] === 'doctor') {
            // Get DocID from doctor table
            $docStmt = $conn->prepare("SELECT DocID FROM doctor WHERE Name = ?");
            $docStmt->bind_param("s", $username);
            $docStmt->execute();
            $docResult = $docStmt->get_result();
            if ($docRow = $docResult->fetch_assoc()) {
                $_SESSION['DocID'] = $docRow['DocID'];
            }
            header("Location: doctordashboard.php");
        } elseif ($user['role'] === 'receptionist') {
            // Get RecepID from receptionist table
            $recStmt = $conn->prepare("SELECT RecepID FROM receptionist WHERE FullName = ?");
            $recStmt->bind_param("s", $username);
            $recStmt->execute();
            $recResult = $recStmt->get_result();
            if ($recRow = $recResult->fetch_assoc()) {
                $_SESSION['RecepID'] = $recRow['RecepID'];
            }
            header("Location: frontdesk.php");
        } else {
            header("Location: adminlogin.php?error=unknown_role");
        }

        exit;
    } else {
        header("Location: adminlogin.php?error=wrong_password");
        exit;
    }
} else {
    header("Location: adminlogin.php?error=user_not_found");
    exit;
}
?>
