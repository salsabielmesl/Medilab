<?php
include 'dp.php';

if (isset($_GET['DepID'])) {
    $depID = intval($_GET['DepID']);

    $stmt = $conn->prepare("SELECT DocID, Name FROM doctor WHERE DepID = ?");
    $stmt->bind_param("i", $depID);
    $stmt->execute();
    $result = $stmt->get_result();

   

    while ($row = $result->fetch_assoc()) {
        echo '<option value="' . htmlspecialchars($row['DocID']) . '">' . htmlspecialchars($row['Name']) . '</option>';
    }
}
?>
