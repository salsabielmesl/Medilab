<?php
include 'dp.php'; // database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $FullName = $_POST['FullName'];
    $Gender = $_POST['Gender'];
    $DOB = $_POST['DOB'];
    $Phone = $_POST['Phone'];

    // Check if editing (PatientID is set)
    if (!empty($_POST['PatientID'])) {
        $PatientID = $_POST['PatientID'];

        $sql = "UPDATE patient SET FullName = ?, Gender = ?, DOB = ?, Phone = ? WHERE PatientID = ?";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("ssssi", $FullName, $Gender, $DOB, $Phone, $PatientID);

        if ($stmt->execute()) {
            header("Location: patients.php?updated=1");
            exit;
        } else {
            echo "Update Error: " . $stmt->error;
        }

    } else {
        // Add new patient
        $sql = "INSERT INTO patient (FullName, Gender, DOB, Phone) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("ssss", $FullName, $Gender, $DOB, $Phone);

        if ($stmt->execute()) {
            header("Location: patients.php?success=1");
            exit;
        } else {
            echo "Insert Error: " . $stmt->error;
        }
    }
}
?>
