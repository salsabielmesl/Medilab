<?php
session_start();

// Destroy all session variables
$_SESSION = [];
session_unset();
session_destroy();

// Redirect to login page
header("Location: adminlogin.php");
exit;
?>
