<?php
session_start();
include 'dp.php'; // Your DB connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = $_POST['role'] ?? '';
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Validation
    if (empty($role) || empty($username) || empty($password)) {
        header("Location: adminusers.php?error=missing_fields");
        exit;
    }

    // Clean username: lowercase and replace spaces with underscores
    $finalUsername = preg_replace('/\s+/', '_', strtolower($username)); // e.g., "Ahmad Khaled" → "ahmad_khaled"

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Check if username already exists
    $checkQuery = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $checkQuery->bind_param("s", $finalUsername);
    $checkQuery->execute();
    $checkQuery->store_result();

    if ($checkQuery->num_rows > 0) {
        // Username already taken
        $checkQuery->close();
        header("Location: adminusers.php?error=username_taken");
        exit;
    }
    $checkQuery->close();

    // Insert into users table
    $insertQuery = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
    $insertQuery->bind_param("sss", $finalUsername, $hashedPassword, $role);

    if ($insertQuery->execute()) {
        header("Location: adminusers.php?success=1");
    } else {
        header("Location: adminusers.php?error=db_error");
    }

    $insertQuery->close();
    $conn->close();
} else {
    // Invalid access
    header("Location: adminusers.php?error=invalid_request");
    exit;
}
