<?php
require 'dp.php';

header('Content-Type: application/json');

// Turn off visible errors but log them
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

// Read and decode raw JSON input
$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true);

// Validate input
if (!isset($data['PatientID'])) {
    echo json_encode([]);
    exit;
}

$patientID = intval($data['PatientID']);

$sql = "
    SELECT 
        r.ReportID,
        r.Assessment,
        r.Diagnosis,
        r.Prescription,
        r.CreatedAt,
        a.AppDate,
        a.StartTime,
        d.Name AS DoctorName,
        dep.DepName AS Department
    FROM reports r
    JOIN appointments a ON r.AppID = a.AppID
    JOIN doctor d ON a.DocID = d.DocID
    JOIN department dep ON d.DepID = dep.DepID
    WHERE a.PatientID = ?
    ORDER BY r.CreatedAt DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $patientID);
$stmt->execute();
$result = $stmt->get_result();

$reports = [];
while ($row = $result->fetch_assoc()) {
    $reports[] = $row;
}

echo json_encode($reports);
