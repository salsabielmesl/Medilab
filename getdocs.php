<?php
include 'dp.php'; // Connect to the database

header('Content-Type: application/json');

$depID = 0;
if (isset($_GET['depID'])) {
    $depID = (int)$_GET['depID'];
} elseif (isset($_GET['DepID'])) {
    $depID = (int)$_GET['DepID'];
}

if ($depID <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid department ID'
    ]);
    exit;
}

$stmt = $conn->prepare("
    SELECT DocID, Name, ProfilePic,
           (SELECT DepName FROM department WHERE DepID = ?) AS DepName
    FROM doctor
    WHERE DepID = ?
    ORDER BY Name ASC
");
$stmt->bind_param("ii", $depID, $depID);
$stmt->execute();
$result = $stmt->get_result();

$doctors = [];
while ($row = $result->fetch_assoc()) {
    $doctors[] = $row;
}

echo json_encode([
    'success' => true,
    'doctors' => $doctors
]);
exit;
