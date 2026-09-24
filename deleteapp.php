<?php
include 'dp.php'; // Your DB connection

if (isset($_GET['AppID'])) {
    $appID = intval($_GET['AppID']);

    $stmt = $conn->prepare("DELETE FROM calendar WHERE AppID = ?");
    $stmt->bind_param("i", $appID);

    if ($stmt->execute()) {
        // Redirect back or send success response
        header("Location: frontdesk.php?deleted=1");
        exit;
    } else {
        echo "Failed to delete appointment.";
    }
} else {
    echo "Invalid request.";
}
