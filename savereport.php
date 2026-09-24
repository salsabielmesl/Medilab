<?php
require 'dp.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $appID = intval($_POST['AppID']);
    $assessment = trim($_POST['Assessment']);
    $diagnosis = trim($_POST['Diagnosis']);
    $prescription = trim($_POST['Prescription']);

    // Basic validation
    if (!$appID || !$assessment || !$diagnosis || !$prescription) {
        die("All fields are required.");
    }

    // Check if report exists
    $checkSql = "SELECT ReportID FROM reports WHERE AppID = ?";
    $stmtCheck = $conn->prepare($checkSql);
    $stmtCheck->bind_param("i", $appID);
    $stmtCheck->execute();
    $resultCheck = $stmtCheck->get_result();

    if ($resultCheck->num_rows > 0) {
        // Update existing report
        $updateSql = "UPDATE reports SET Assessment = ?, Diagnosis = ?, Prescription = ? WHERE AppID = ?";
        $stmtUpdate = $conn->prepare($updateSql);
        $stmtUpdate->bind_param("sssi", $assessment, $diagnosis, $prescription, $appID);
        $stmtUpdate->execute();
    } else {
        // Insert new report
        $insertSql = "INSERT INTO reports (AppID, Assessment, Diagnosis, Prescription) VALUES (?, ?, ?, ?)";
        $stmtInsert = $conn->prepare($insertSql);
        $stmtInsert->bind_param("isss", $appID, $assessment, $diagnosis, $prescription);
        $stmtInsert->execute();
    }

    // Redirect back to dashboard or show success message
    header("Location: doctordashboard.php");
    exit;
}
