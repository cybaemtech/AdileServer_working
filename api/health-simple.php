<?php
// Simple health check
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: https://agile.cybaemtech.app:90');
header('Access-Control-Allow-Credentials: true');

echo json_encode([
    'status' => 'ok',
    'timestamp' => date('Y-m-d H:i:s'),
    'php_version' => phpversion(),
    'server' => $_SERVER['SERVER_NAME'] ?? 'unknown'
]);
?>
