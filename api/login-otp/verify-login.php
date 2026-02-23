<?php
// Physical file for /api/login-otp/verify-login
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

// Start session before any output
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set the API path so login-otp.php knows which sub-route to handle
$_SERVER['AGILE_API_PATH'] = '/verify-login';

// Include the main login-otp handler
require_once __DIR__ . '/../login-otp.php';
?>
