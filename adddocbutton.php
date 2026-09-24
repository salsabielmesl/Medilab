<?php
include 'dp.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'] ?? '';

    // Validate and sanitize inputs
    $Name = trim($_POST['Name'] ?? '');
    $DOB = $_POST['DOB'] ?? '';
    $Department = filter_var($_POST['DepID'] ?? '', FILTER_VALIDATE_INT);
    $hasImage = !empty($_FILES['ProfilePic']['name']);

    if (empty($Name) || strlen($Name) > 100) {
        die("Invalid or missing name.");
    }

    $date = DateTime::createFromFormat('Y-m-d', $DOB);
    if (!$date || $date->format('Y-m-d') !== $DOB) {
        die("Invalid date format.");
    }

    if ($Department === false) {
        die("Invalid department.");
    }

    $image = '';
    if ($hasImage) {
        $ext = strtolower(pathinfo($_FILES['ProfilePic']['name'], PATHINFO_EXTENSION));
        $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (!in_array($ext, $allowedExts)) {
            die("Invalid image type.");
        }

        $image = uniqid('doc_', true) . '.' . $ext;
        $target = "uploads/" . $image;

        if (!move_uploaded_file($_FILES['ProfilePic']['tmp_name'], $target)) {
            die("Failed to upload profile picture.");
        }
    }

    if ($action === 'add') {
        // Add doctor
        $sql = "INSERT INTO doctor (Name, DOB, ProfilePic, DepID) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("sssi", $Name, $DOB, $image, $Department);

        if ($stmt->execute()) {
            header("Location: doclist.php?success=1");
            exit;
        } else {
            die("Error: " . $stmt->error);
        }

    } elseif ($action === 'edit') {
        $DocID = filter_var($_POST['DocID'] ?? '', FILTER_VALIDATE_INT);
        if ($DocID === false) {
            die("Invalid doctor ID.");
        }

        if ($hasImage) {
            $sql = "UPDATE doctor SET Name = ?, DOB = ?, ProfilePic = ?, DepID = ? WHERE DocID = ?";
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                die("Prepare failed: " . $conn->error);
            }
            $stmt->bind_param("sssii", $Name, $DOB, $image, $Department, $DocID);
        } else {
            $sql = "UPDATE doctor SET Name = ?, DOB = ?, DepID = ? WHERE DocID = ?";
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                die("Prepare failed: " . $conn->error);
            }
            $stmt->bind_param("ssii", $Name, $DOB, $Department, $DocID);
        }

        if ($stmt->execute()) {
            header("Location: doclist.php?updated=1");
            exit;
        } else {
            die("Error updating doctor: " . $stmt->error);
        }

    } else {
        die("Invalid action.");
    }
}
?>
