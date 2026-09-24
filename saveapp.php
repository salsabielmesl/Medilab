<?php
session_start();
include 'dp.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit;
}

$action = $_POST['action'] ?? '';

try {
    if ($action === 'add' || $action === 'update') {
        $PatientID  = $_POST['PatientID'] ?? '';
        $DepID      = $_POST['DepID'] ?? '';
        $DocID      = $_POST['DocID'] ?? '';
        $AppDate    = $_POST['AppDate'] ?? '';
        $StartTime  = $_POST['StartTime'] ?? '';
        $Price      = $_POST['Price'] ?? 0;
        $Discount   = $_POST['Discount'] ?? 0;
        $message    = $_POST['message'] ?? '';

        // Validate required fields
        if (!$PatientID || !$DepID || !$DocID || !$AppDate || !$StartTime) {
            throw new Exception('Missing required fields');
        }

        // Validate date format YYYY-MM-DD
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $AppDate)) {
            throw new Exception("Invalid AppDate format: $AppDate");
        }

        // Validate time format HH:MM or HH:MM:SS
        if (!preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $StartTime)) {
            throw new Exception("Invalid StartTime format: $StartTime");
        }

        // Fetch FullName from patient table
        $stmt = $conn->prepare("SELECT FullName FROM patient WHERE PatientID = ?");
        $stmt->bind_param("i", $PatientID);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            throw new Exception("Patient not found");
        }
        $patient = $result->fetch_assoc();
        $FullName = $patient['FullName'];
        $stmt->close();

        if ($action === 'add') {
           $Status = 'Appointed';

// Determine if it's a patient or receptionist
if (isset($_SESSION['RecepID'])) {
    // Receptionist is logged in
    $RecepID = $_SESSION['RecepID'];

    $stmt = $conn->prepare("INSERT INTO appointments 
        (PatientID, FullName, DepID, DocID, AppDate, StartTime, Price, Discount, Status, RecepID, message)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param("isiissddsis", 
        $PatientID, $FullName, $DepID, $DocID, $AppDate, $StartTime, 
        $Price, $Discount, $Status, $RecepID, $message);

} else {
    // Patient is booking — no RecepID
    $stmt = $conn->prepare("INSERT INTO appointments 
        (PatientID, FullName, DepID, DocID, AppDate, StartTime, Price, Discount, Status, message)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param("isiissddss", 
        $PatientID, $FullName, $DepID, $DocID, $AppDate, $StartTime, 
        $Price, $Discount, $Status, $message);
}


            $stmt->execute();
            $stmt->close();

           // After successful insert:
echo json_encode(['status' => 'success', 'message' => 'Appointment added successfully']);



            exit;

        } elseif ($action === 'update') {
            $AppID = $_POST['AppID'] ?? '';
            if (!$AppID) throw new Exception('Missing AppID');

            $stmt = $conn->prepare("UPDATE appointments SET 
                PatientID=?, FullName=?, DepID=?, DocID=?, AppDate=?, StartTime=?, 
                Price=?, Discount=?, message=? WHERE AppID=?");

            $stmt->bind_param("isiissddsi", 
                $PatientID, $FullName, $DepID, $DocID, $AppDate, $StartTime, 
                $Price, $Discount, $message, $AppID
            );

            $stmt->execute();
            $stmt->close();

            echo json_encode(['status' => 'success', 'message' => 'Appointment updated successfully']);
            exit;
        }

    } elseif ($action === 'delete') {
        $AppID = $_POST['AppID'] ?? '';
        if (!$AppID) {
            echo json_encode(['status' => 'error', 'message' => 'Missing AppID']);
            exit;
        }

        // Soft delete: update status to 'Cancel'
        $stmt = $conn->prepare("UPDATE appointments SET Status = 'Cancel' WHERE AppID = ?");
        $stmt->bind_param("i", $AppID);
        $stmt->execute();
        $stmt->close();

        echo json_encode(['status' => 'success', 'message' => 'Appointment canceled successfully']);
        exit;
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
        exit;
    }
} catch (Exception $ex) {
    echo json_encode(['status' => 'error', 'message' => $ex->getMessage()]);
    exit;
}
