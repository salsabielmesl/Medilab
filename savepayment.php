<?php
header('Content-Type: application/json');
include 'dp.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit;
}

$AppID = isset($_POST['AppID']) ? intval($_POST['AppID']) : 0;
$PaymentAmount = isset($_POST['PaymentAmount']) ? floatval($_POST['PaymentAmount']) : -1;

if ($AppID <= 0 || $PaymentAmount < 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid appointment ID or payment amount']);
    exit;
}

// Fetch current payment info from DB
$stmt = $conn->prepare("SELECT Total, PaymentAmount, Status FROM appointments WHERE AppID = ?");
if (!$stmt) {
    echo json_encode(['status' => 'error', 'message' => 'Prepare failed: '.$conn->error]);
    exit;
}
$stmt->bind_param("i", $AppID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Appointment not found']);
    exit;
}

$row = $result->fetch_assoc();
$total = floatval($row['Total']);
$currentPayment = floatval($row['PaymentAmount']);
$status = $row['Status'];

// Dynamically calculate remaining amount
$remaining = $total - $currentPayment;

// Debug check
if ($remaining <= 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Payment already completed',
        'debug' => [
            'Total' => $total,
            'Paid' => $currentPayment,
            'Remaining' => $remaining
        ]
    ]);
    exit;
}

if ($PaymentAmount > $remaining) {
    echo json_encode(['status' => 'error', 'message' => 'Payment amount exceeds remaining balance']);
    exit;
}

// Calculate new values
$newPaymentTotal = $currentPayment + $PaymentAmount;
$newRemaining = $total - $newPaymentTotal;

// Determine new status
$newStatus = $newRemaining <= 0 ? 'paid' : $status;

// Update database including RemainingAmount
$updateStmt = $conn->prepare("UPDATE appointments SET PaymentAmount = ?, RemainingAmount = ?, Status = ? WHERE AppID = ?");
if (!$updateStmt) {
    echo json_encode(['status' => 'error', 'message' => 'Prepare failed: '.$conn->error]);
    exit;
}

$updateStmt->bind_param("ddsi", $newPaymentTotal, $newRemaining, $newStatus, $AppID);

if ($updateStmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Payment recorded successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Update failed: '.$updateStmt->error]);
}

$stmt->close();
$updateStmt->close();
$conn->close();
