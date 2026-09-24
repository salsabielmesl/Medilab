<?php
$pdo = new PDO("mysql:host=localhost:3308;dbname=medilab;charset=utf8", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>
