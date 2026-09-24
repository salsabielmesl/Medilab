<?php
require 'config.php';

$defaultPic = 'default.png'; // Make sure this image exists in your uploads folder

$users = [
    ['doctor1', 'pass123', 'doctor'],
    ['reception1', 'pass123', 'receptionist'],
];

foreach ($users as $user) {
    $username = $user[0];
    $passwordPlain = $user[1];
    $role = $user[2];

    // Check if user already exists to avoid duplicates
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
        echo "User $username already exists.<br>";
        continue;
    }

    // Hash the password and insert user
    $hashedPassword = password_hash($passwordPlain, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (username, password, role, profile_pic) VALUES (?, ?, ?, ?)");
    $stmt->execute([$username, $hashedPassword, $role, $defaultPic]);
    echo "User $username inserted.<br>";
}
