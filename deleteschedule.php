<?php
include 'dp.php'; // your DB connection

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $scheduleID = $_POST['ScheduleID'] ?? null;
    if (!$scheduleID) {
        echo json_encode(['success' => false, 'message' => 'ScheduleID missing']);
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM docschedule WHERE ScheduleID = ?");
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]);
        exit;
    }

    $stmt->bind_param('i', $scheduleID);
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Delete failed: ' . $stmt->error]);
    }
    $stmt->close();
    exit;
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}
?>
