<?php
include 'dp.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'] ?? '';
    $depName = $_POST['DepName'] ?? '';
    $description = $_POST['Description'] ?? '';

    // Handle image if uploaded
    $imageName = null;
    if (!empty($_FILES['DepImage']['name'])) {
        $ext = strtolower(pathinfo($_FILES['DepImage']['name'], PATHINFO_EXTENSION));
        $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (!in_array($ext, $allowedExts)) {
            die("Invalid image type. Only JPG, PNG, GIF, WEBP allowed.");
        }

        $imageName = uniqid('dep_', true) . '.' . $ext;
        $uploadPath = 'uploads/departments/' . $imageName;

        if (!is_dir('uploads/departments')) {
            mkdir('uploads/departments', 0777, true);
        }

        if (!move_uploaded_file($_FILES['DepImage']['tmp_name'], $uploadPath)) {
            die("Failed to upload image.");
        }
    }

    if ($action === 'add') {
        $sql = "INSERT INTO department (DepName, Description, Image) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if (!$stmt) die("Prepare failed: " . $conn->error);

        $stmt->bind_param("sss", $depName, $description, $imageName);

        if ($stmt->execute()) {
            header("Location: deplist.php?status=added"); // ✅ CHANGED
            exit;
        } else {
            die("Error adding department: " . $stmt->error);
        }
    }

    elseif ($action === 'edit') {
        $depID = $_POST['DepID'] ?? 0;
        if (!$depID) die("Department ID missing for update.");

        // Get current image from DB
        $result = $conn->query("SELECT Image FROM department WHERE DepID = $depID");
        $currentImage = ($result && $row = $result->fetch_assoc()) ? $row['Image'] : null;

        // If a new image was uploaded, delete the old one
        if ($imageName && $currentImage && file_exists("uploads/departments/$currentImage")) {
            unlink("uploads/departments/$currentImage");
        }

        // Use new image if uploaded, otherwise keep old
        $finalImage = $imageName ?: $currentImage;

        $sql = "UPDATE department SET DepName = ?, Description = ?, Image = ? WHERE DepID = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) die("Prepare failed: " . $conn->error);

        $stmt->bind_param("sssi", $depName, $description, $finalImage, $depID);

        if ($stmt->execute()) {
            header("Location: deplist.php?status=edited"); // ✅ CHANGED
            exit;
        } else {
            die("Error updating department: " . $stmt->error);
        }
    }

    else {
        die("Invalid action.");
    }
} else {
    die("Invalid request method.");
}
