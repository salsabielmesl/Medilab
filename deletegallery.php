<?php
include 'dp.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['image_path'])) {
    $imagePath = $_POST['image_path'];

    // Delete from filesystem
    if (file_exists($imagePath)) {
        unlink($imagePath);
    }

    // Delete from database
    $stmt = $conn->prepare("DELETE FROM gallery WHERE image = ?");
    $stmt->bind_param("s", $imagePath);
    $stmt->execute();

    header("Location: editgallery.php?deleted=1");
    exit();
}
?>
