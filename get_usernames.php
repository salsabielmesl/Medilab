<?php
include 'dp.php';

$role = $_GET['role'] ?? '';
$data = [];

if ($role === 'admin') {
    $query = "SELECT Name FROM admin";
    $column = 'Name';
} elseif ($role === 'doctor') {
    $query = "SELECT Name FROM doctor";
    $column = 'Name';
} elseif ($role === 'receptionist') {
    $query = "SELECT FullName FROM receptionist";
    $column = 'FullName';
} else {
    echo json_encode([]);
    exit;
}

$result = $conn->query($query);

while ($row = $result->fetch_assoc()) {
    $data[] = $row[$column];
}

echo json_encode($data);
