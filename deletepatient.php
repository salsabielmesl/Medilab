<?php
require_once 'dp.php';

if (!isset($_GET['id'])) {
    header("Location: patients.php");
    exit;
}

$id = intval($_GET['id']);
$stmt = $conn->prepare("DELETE FROM patient WHERE PatientID = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: patients.php?deleted=1");
exit;
?>
