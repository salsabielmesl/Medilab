<?php
require_once 'dp.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $depID = intval($_GET['id']);

    // Check if department exists
    $check = $conn->prepare("SELECT DepID FROM department WHERE DepID = ?");
    $check->bind_param("i", $depID);
    $check->execute();
    $check->store_result();

    if ($check->num_rows === 0) {
        $check->close();
        header("Location: deplist.php?error=not_found");
        exit;
    }
    $check->close();

    // Proceed with delete - rely on ON DELETE CASCADE in DB
    $stmt = $conn->prepare("DELETE FROM department WHERE DepID = ?");
    $stmt->bind_param("i", $depID);

    if ($stmt->execute()) {
        $stmt->close();
        header("Location: deplist.php?deleted=1");
        exit;
    } else {
        echo " Failed to delete department: " . $stmt->error;
        $stmt->close();
    }
} else {
    echo " Invalid request.";
}
?>
