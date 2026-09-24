<?php
include 'dp.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'];
    $FullName = $_POST['FullName'];
    $PhoneNum = $_POST['PhoneNum'];
    $DOB = $_POST['DOB'];

    if ($action === 'add') {
        // Add new receptionist
        $sql = "INSERT INTO receptionist (FullName, PhoneNum, DOB) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("sss", $FullName, $PhoneNum, $DOB);

        if ($stmt->execute()) {
            header("Location: stafflist.php?success=1");
            exit;
        } else {
            echo "Error: " . $stmt->error;
        }

    } elseif ($action === 'edit') {
        // Update existing receptionist
        $RecepID = $_POST['RecepID'];

        $sql = "UPDATE receptionist SET FullName = ?, PhoneNum = ?, DOB = ? WHERE RecepID = ?";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("sssi", $FullName, $PhoneNum, $DOB, $RecepID);

        if ($stmt->execute()) {
            header("Location: stafflist.php?updated=1");
            exit;
        } else {
            echo "Error: " . $stmt->error;
        }

    } else {
        echo "Invalid action.";
    }
}
?>
