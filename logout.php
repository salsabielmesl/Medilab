<?php
session_start();
include 'dp.php';

// ✅ Clear all session variables
$_SESSION = [];

// ✅ Destroy the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// ✅ Destroy the session itself
session_destroy();

// ✅ Delete the "rememberme" cookie if it exists
$secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
if (in_array($_SERVER['HTTP_HOST'], ['localhost', '127.0.0.1'])) {
    $secure = false;
}

if (PHP_VERSION_ID >= 70300) {
    setcookie("rememberme", "", [
        'expires' => time() - 3600,
        'path' => '/',
        'secure' => $secure,
        'httponly' => true,
        'samesite' => 'Strict'
    ]);
} else {
    setcookie("rememberme", "", time() - 3600, "/", "", $secure, true);
}

// ✅ Redirect to login page
header("Location: login.php");
exit;
