<?php
include 'dp.php';
header('Content-Type: application/json');

$sql = "SELECT a.AppID, p.FullName, a.AppDate, a.StartTime, a.Price, a.Discount, a.Total, 
               a.DepID, a.DocID, a.Status, a.PaymentAmount, d.Name AS DoctorName, dep.DepName
        FROM appointments a
        LEFT JOIN patient p ON a.PatientID = p.PatientID
        LEFT JOIN doctor d ON a.DocID = d.DocID
        LEFT JOIN department dep ON a.DepID = dep.DepID";

$result = $conn->query($sql);

if (!$result) {
    echo json_encode(['error' => $conn->error]);
    exit;
}

$events = [];

while ($row = $result->fetch_assoc()) {
    $start = $row['AppDate'] . 'T' . $row['StartTime'];

    // Normalize status to lowercase
    $status = strtolower($row['Status'] ?? 'appointed');  // default to 'appointed' if null

    // Set event color based on status
    switch ($status) {
        case 'paid':
            $color = '#28a745'; // green
            break;
        case 'cancel':
        case 'canceled':
            $color = '#dc3545'; // red
            break;
        default:
            $color = '#007bff'; // blue (appointed or others)
    }

    $events[] = [
        'id' => $row['AppID'],
        'title' => $row['FullName'], 
        'start' => $start,
        'color' => $color,
        'backgroundColor' => $color,
        'borderColor' => $color,
        'extendedProps' => [
            'departmentID' => $row['DepID'],
            'departmentName' => $row['DepName'],
            'doctorID' => $row['DocID'],
            'doctorName' => $row['DoctorName'],
            'date' => $row['AppDate'],
            'start' => $row['StartTime'],
            'status' => $row['Status'],
            'payment' => $row['PaymentAmount'],
            'total' => $row['Total'],
            'price' => $row['Price'],  
            'phone' => $row['Phone'] ?? '',
        ],
    ];
}

echo json_encode($events);
?>
