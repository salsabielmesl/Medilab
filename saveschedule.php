<?php
include 'dp.php';
header('Content-Type: application/json');

$response = ['success' => false];

$ScheduleID = isset($_POST['ScheduleID']) ? intval($_POST['ScheduleID']) : 0;
$depID     = $_POST['DepID'] ?? '';
$docID     = $_POST['DocID'] ?? null;
$dayOfWeek = $_POST['DayOfWeek'] ?? '';
$startTime = $_POST['StartTime'] ?? '';
$endTime   = $_POST['EndTime'] ?? '';

// Validate required fields
if (!$depID || !$docID || !$dayOfWeek || !$startTime || !$endTime) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

$depID = (int)$depID;
$docID = (int)$docID;

try {
    if ($ScheduleID > 0) {
        $stmt = $conn->prepare("UPDATE docschedule SET 
            DocID = ?, 
            DepID = ?, 
            DayOfWeek = ?, 
            StartTime = ?, 
            EndTime = ? 
            WHERE ScheduleID = ?");
        $stmt->bind_param("iisssi", $docID, $depID, $dayOfWeek, $startTime, $endTime, $ScheduleID);
    } else {
        $stmt = $conn->prepare("INSERT INTO docschedule 
            (DocID, DepID, DayOfWeek, StartTime, EndTime) 
            VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iisss", $docID, $depID, $dayOfWeek, $startTime, $endTime);
    }

    if ($stmt->execute()) {
        $response['success'] = true;
        $response['ScheduleID'] = $ScheduleID > 0 ? $ScheduleID : $stmt->insert_id;
        $response['message'] = $ScheduleID > 0 ? 'Schedule updated successfully' : 'Schedule added successfully';
    } else {
        $response['message'] = 'DB error: ' . $stmt->error;
    }

    $stmt->close();
} catch (Exception $e) {
    $response['message'] = 'Server error: ' . $e->getMessage();
}

echo json_encode($response);
