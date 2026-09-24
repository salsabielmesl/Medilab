<?php
session_start();
include 'dp.php';
header('Content-Type: application/json');

// Use DocID from GET if set (for admin), else from session (doctor)
if (isset($_GET['DocID']) && intval($_GET['DocID']) > 0) {
    $docID = intval($_GET['DocID']);
} else {
    $docID = $_SESSION['DocID'] ?? 0;
}

$startParam = $_GET['start'] ?? null;
$endParam = $_GET['end'] ?? null;

$events = [];

if ($docID > 0 && $startParam && $endParam) {
    $sql = "SELECT ScheduleID, DepID, DocID, DayOfWeek, StartTime, EndTime FROM docschedule WHERE DocID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $docID);
    $stmt->execute();
    $result = $stmt->get_result();

    $days = [
        "Sunday" => 0,
        "Monday" => 1,
        "Tuesday" => 2,
        "Wednesday" => 3,
        "Thursday" => 4,
        "Friday" => 5,
        "Saturday" => 6,
    ];

    $startDate = new DateTime($startParam);
    $endDate = new DateTime($endParam);
    $endDate->modify('+1 day'); // Include end day

    while ($row = $result->fetch_assoc()) {
        $dowNum = $days[$row['DayOfWeek']] ?? null;
        if ($dowNum === null) continue;

        $period = new DatePeriod($startDate, new DateInterval('P1D'), $endDate);

        foreach ($period as $date) {
            if ((int)$date->format('w') === $dowNum) {
                $dateStr = $date->format('Y-m-d');
                $startDateTimeStr = $dateStr . 'T' . substr($row['StartTime'], 0, 5) . ':00';
                $endDateTimeStr = $dateStr . 'T' . substr($row['EndTime'], 0, 5) . ':00';

                $events[] = [
                    'id' => $row['ScheduleID'] . '_' . $dateStr,
                    'title' => 'Scheduled',
                    'start' => $startDateTimeStr,
                    'end' => $endDateTimeStr,
                    'extendedProps' => [
                        'depID' => $row['DepID'],
                        'docID' => $row['DocID'],
                    ]
                ];
            }
        }
    }
}

echo json_encode($events);
