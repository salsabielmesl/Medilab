<?php
include 'dp.php'; // DB connection

if (isset($_GET['id'])) {
    $docID = intval($_GET['id']);

    // Step 1: Get the profile picture filename (if any)
    $stmt = $conn->prepare("SELECT ProfilePic FROM doctor WHERE DocID = ?");
    $stmt->bind_param("i", $docID);
    $stmt->execute();
    $stmt->bind_result($profilePic);
    $stmt->fetch();
    $stmt->close();

    // Step 2: Delete profile picture file if exists
    if (!empty($profilePic)) {
        $filePath = $profilePic; // adjust path if necessary
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    // Step 3: Delete the doctor record
    $stmt = $conn->prepare("DELETE FROM doctor WHERE DocID = ?");
    $stmt->bind_param("i", $docID);
    
    if ($stmt->execute()) {
        // Redirect to doclist.php with a deleted flag
        header("Location: doclist.php?deleted=1");
        exit;
    } else {
        echo "Failed to delete doctor.";
    }
} else {
    echo "Invalid request.";
}
?>
