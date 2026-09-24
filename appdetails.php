<?php
include 'dp.php';

$type = $_GET['type'] ?? '';
//appointments
if ($type === 'appointments') {
    $sql = "
        SELECT p.FullName, p.Gender, p.DOB, p.Phone, a.StartTime, a.Status
        FROM appointments a
        INNER JOIN patient p ON a.PatientID = p.PatientID
        ORDER BY a.StartTime DESC
    ";
    $result = $conn->query($sql);

    if (!$result) {
        echo '<div class="text-danger">Error fetching appointments: ' . htmlspecialchars($conn->error) . '</div>';
        exit;
    }

    if ($result->num_rows === 0) {
        echo '<div class="text-center">No appointments found.</div>';
        exit;
    }

    echo '<table class="table table-striped table-bordered">';
    echo '<thead style="background-color: #818CF8; color: white;">';
    echo '<tr>';
    echo '<th>Patient Name</th>';
    echo '<th>Gender</th>';
    echo '<th>DOB</th>';
    echo '<th>Phone</th>';
    echo '<th>Appointment Date</th>';
    echo '<th>Status</th>';
    echo '</tr>';
    echo '</thead><tbody>';

    while ($row = $result->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($row['FullName']) . '</td>';
        echo '<td>' . htmlspecialchars($row['Gender']) . '</td>';
        echo '<td>' . htmlspecialchars($row['DOB']) . '</td>';
        echo '<td>' . htmlspecialchars($row['Phone']) . '</td>';
        echo '<td>' . htmlspecialchars(date('Y-m-d H:i', strtotime($row['StartTime']))) . '</td>';
        echo '<td>' . htmlspecialchars($row['Status']) . '</td>';
        echo '</tr>';
    }

    echo '</tbody></table>';
    exit;
}

//  doctors
if ($type === 'doctors') {
    $sql = "
        SELECT d.DocID, d.Name, d.DOB, d.ProfilePic, department.DepName 
        FROM doctor d
        JOIN department ON d.DepID = department.DepID
    ";
    $result = $conn->query($sql);

    if (!$result) {
        echo '<div class="text-danger">Error fetching doctors: ' . htmlspecialchars($conn->error) . '</div>';
        exit;
    }

    if ($result->num_rows === 0) {
        echo '<div class="text-center">No doctors found.</div>';
        exit;
    }

    echo '<table class="table table-striped table-bordered align-middle text-center">';
    echo '<thead style="background-color: #818CF8; color: white;">';
    echo '<tr>';
    echo '<th>Picture</th>';
    echo '<th>Name</th>';
    echo '<th>Department</th>';
    echo '<th>DOB</th>';
    echo '</tr>';
    echo '</thead><tbody>';

    while ($row = $result->fetch_assoc()) {
        echo '<tr>';
        echo '<td>';
        if (!empty($row['ProfilePic'])) {
            echo '<img src="uploads/' . htmlspecialchars($row['ProfilePic']) . '" alt="Doctor" style="height: 45px; width: 45px; border-radius: 5px; object-fit: cover;">';
        } else {
            echo '<span class="text-muted">N/A</span>';
        }
        echo '</td>';
        echo '<td>' . htmlspecialchars($row['Name']) . '</td>';
        echo '<td>' . htmlspecialchars($row['DepName']) . '</td>';
        echo '<td>' . htmlspecialchars($row['DOB']) . '</td>';
        echo '</tr>';
    }

    echo '</tbody></table>';
    exit;
}
//receptionists
if ($type === 'receptionists') {
    $sql = "SELECT FullName, PhoneNum, DOB FROM receptionist ORDER BY FullName";
    $result = $conn->query($sql);

    if (!$result) {
        echo '<div class="text-danger">Error fetching receptionists: ' . htmlspecialchars($conn->error) . '</div>';
        exit;
    }

    if ($result->num_rows === 0) {
        echo '<div class="text-center">No receptionists found.</div>';
        exit;
    }

    echo '<table class="table table-striped table-bordered">';
    echo '<thead style="background-color: #818CF8; color: white;">';
    echo '<tr><th>Full Name</th><th>Phone</th><th>DOB</th></tr>';
    echo '</thead><tbody>';

    while ($row = $result->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($row['FullName']) . '</td>';
        echo '<td>' . htmlspecialchars($row['PhoneNum']) . '</td>';
        echo '<td>' . htmlspecialchars($row['DOB']) . '</td>';
        echo '</tr>';
    }

    echo '</tbody></table>';
    exit;
}
// departments
if ($type === 'departments') {
    $sql = "SELECT DepName, Description, Image FROM department ORDER BY DepName ASC";
    $result = $conn->query($sql);
    if (!$result) {
        echo '<div class="text-danger">Error fetching departments: ' . htmlspecialchars($conn->error) . '</div>';
        exit;
    }
    if ($result->num_rows === 0) {
        echo '<div class="text-center">No departments found.</div>';
        exit;
    }

    echo '<table class="table table-striped table-bordered">';
    echo '<thead style="background-color: #818CF8; color: white;">';
    echo '<tr><th>Image</th><th>Department Name</th><th>Description</th></tr>';
    echo '</thead><tbody>';
    while ($row = $result->fetch_assoc()) {
        echo '<tr>';
        echo '<td>';
        if (!empty($row['Image'])) {
            echo '<img src="uploads/departments/' . htmlspecialchars($row['Image']) . '" style="height: 50px; width: auto; object-fit: contain;">';
        } else {
            echo '<span class="text-muted">No Image</span>';
        }
        echo '</td>';
        echo '<td>' . htmlspecialchars($row['DepName']) . '</td>';
        echo '<td>' . htmlspecialchars($row['Description']) . '</td>';
        echo '</tr>';
    }
    echo '</tbody></table>';
    exit;
}
//patients
if ($type === 'patients') {
    $sql = "SELECT PatientID, FullName, Gender, DOB, Phone FROM patient ORDER BY FullName ASC";
    $result = $conn->query($sql);

    if (!$result) {
        echo '<div class="text-danger">Error fetching patients: ' . htmlspecialchars($conn->error) . '</div>';
        exit;
    }

    if ($result->num_rows === 0) {
        echo '<div class="text-center">No patients found.</div>';
        exit;
    }

    echo '<table class="table table-striped table-bordered">';
    echo '<thead style="background-color: #818CF8; color: white;">';
    echo '<tr><th>Patient Name</th><th>Gender</th><th>Date of Birth</th><th>Phone Number</th></tr>';
    echo '</thead><tbody>';

    while ($row = $result->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($row['FullName']) . '</td>';
        echo '<td>' . htmlspecialchars($row['Gender']) . '</td>';
        echo '<td>' . htmlspecialchars($row['DOB']) . '</td>';
        echo '<td>' . htmlspecialchars($row['Phone']) . '</td>';
        echo '</tr>';
    }

    echo '</tbody></table>';
    exit;
}
?>

