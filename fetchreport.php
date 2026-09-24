<?php
require 'dp.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['AppID'])) {
    $appID = intval($_POST['AppID']);

    // Fetch appointment + doctor + patient + department info
    $sql = "SELECT 
                a.AppID, a.AppDate, a.StartTime,
                d.Name AS DoctorName,
                dep.DepName AS Specialization,
                p.FullName AS PatientName,
                p.DOB, p.Phone, p.Email
            FROM appointments a
            INNER JOIN doctor d ON a.DocID = d.DocID
            INNER JOIN department dep ON d.DepID = dep.DepID
            INNER JOIN patient p ON a.PatientID = p.PatientID
            WHERE a.AppID = ?
            LIMIT 1";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $appID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(['error' => 'Appointment not found']);
        exit;
    }

    $data = $result->fetch_assoc();

    // Fetch report if exists
    $sqlReport = "SELECT Assessment, Diagnosis, Prescription FROM reports WHERE AppID = ?";
    $stmtReport = $conn->prepare($sqlReport);
    $stmtReport->bind_param("i", $appID);
    $stmtReport->execute();
    $resultReport = $stmtReport->get_result();

    if ($resultReport->num_rows > 0) {
        $report = $resultReport->fetch_assoc();
        $data['Report'] = $report;
    } else {
        $data['Report'] = null;
    }

    // Format date fields for form inputs
    $data['AppDate'] = date('Y-m-d', strtotime($data['AppDate']));
    $data['DOB'] = date('Y-m-d', strtotime($data['DOB']));

    echo json_encode($data);
    exit;
}

echo json_encode(['error' => 'Invalid request']);
