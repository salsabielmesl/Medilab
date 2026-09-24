<?php
include 'dp.php'; // DB connection

if (isset($_GET['id'])) {
    $recepID = intval($_GET['id']);

    // Step 1: Delete the receptionist record
    $stmt = $conn->prepare("DELETE FROM receptionist WHERE RecepID = ?");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("i", $recepID);
    
    if ($stmt->execute()) {
        // Redirect to stafflist.php with a deleted flag
        header("Location: stafflist.php?deleted=1");
        exit;
    } else {
        echo "Failed to delete receptionist: " . $stmt->error;
    }
} else {
    echo "Invalid request.";
}
?>