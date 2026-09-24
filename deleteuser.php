<?php
session_start();
include 'dp.php'; // DB connection

// Only allow if logged in and role is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: adminlogin.php?error=access_denied");
    exit;
}

// Check if id is provided and is a positive integer
if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    header("Location: adminusers.php?error=invalid_id");
    exit;
}

$userId = (int)$_GET['id'];

// Optional: Prevent deleting your own admin account
if (isset($_SESSION['id']) && $_SESSION['id'] == $userId) {
    header("Location: adminusers.php?error=cannot_delete_self");
    exit;
}

// Prepare and execute delete query
$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);

if ($stmt->execute()) {
    $stmt->close();
    header("Location: adminusers.php?deleted=1");
    exit;
} else {
    $stmt->close();
    header("Location: adminusers.php?error=delete_failed");
    exit;
}
