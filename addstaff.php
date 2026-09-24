<?php
include 'dp.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $appID = $_POST['AppID'] ?? null;
    $staffType = $_POST['StaffType'];
    $depID = $_POST['DepID'];
    $scheduleTime = $_POST['ScheduleTime'];
    $staffID = ($staffType === 'Doctor') ? $_POST['DocID'] : $_POST['RecID'];

    // Dummy values (replace with real data if needed)
    $fullName = 'Staff Member';
    $gender = 'Other';
    $dob = '1990-01-01';
    $phone = '00000000';
    $appStart = date('Y-m-d') . ' ' . $scheduleTime;

    if ($appID) {
        $stmt = $conn->prepare("UPDATE calendar SET FullName=?, Gender=?, DOB=?, PhoneNum=?, DepID=?, DocID=?, AppStart=? WHERE AppID=?");
        $stmt->bind_param("sssssssi", $fullName, $gender, $dob, $phone, $depID, $staffID, $appStart, $appID);
    } else {
        $stmt = $conn->prepare("INSERT INTO calendar (FullName, Gender, DOB, PhoneNum, DepID, DocID, AppStart) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $fullName, $gender, $dob, $phone, $depID, $staffID, $appStart);
    }

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => $stmt->error]);
    }
}
