<?php
require 'dp.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $appID = $_POST['AppID'] ?? null;
    $action = $_POST['action'] ?? '';

    if ($appID) {
        if ($action === 'accept') {
            $stmt = $conn->prepare("UPDATE appointments SET Approved = 1 WHERE AppID = ?");
            $stmt->bind_param("i", $appID);
            $stmt->execute();
        } elseif ($action === 'decline') {
            $stmt = $conn->prepare("UPDATE appointments SET Status = 'Cancel' WHERE AppID = ?");
            $stmt->bind_param("i", $appID);
            $stmt->execute();
        }
    }

    header("Location: doctordashboard.php");
    exit;
}
?>
