<?php
session_start();
include 'dp.php';

if (!isset($_GET['AppID'])) {
    die("Invalid request");
}

$AppID = (int)$_GET['AppID'];

// Optional: You can add session-based security check here to ensure user can only delete their own appointments.

$stmt = $conn->prepare("DELETE FROM appointments WHERE AppID = ?");
$stmt->bind_param("i", $AppID);

if ($stmt->execute()) {
    header("Location: patientdashboard.php");
    exit;
} else {
    echo "Failed to delete appointment: " . $conn->error;
}
?>
