<?php
// Central API Router - handles all /api/* requests
// Start session before anything else
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config/cors.php';
setCorsHeaders();

// Handle OPTIONS preflight immediately
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

header('Content-Type: application/json');

// Parse the original REQUEST_URI to determine the resource and sub-path
// IIS URL Rewrite keeps original REQUEST_URI intact after internal rewrite
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$path = parse_url($requestUri, PHP_URL_PATH) ?? '/';

// Normalise: strip site sub-folder prefix if needed (e.g. /Agile/api/...)
// Match patterns like:
//   /api/login-otp/send-otp
//   /api/auth/login
//   /api/work-items/123
$resource = '';
$subpath  = '/';

$knownResources = [
    'auth', 'users', 'teams', 'projects', 'work-items',
    'test', 'invite', 'email-verification',
    'login-otp', 'auth-debug', 'setup-users', 'simple-login',
    'project-bug-reports', 'roadmap-templates', 'health'
];

// Build regex from known resources
$resourceList = implode('|', array_map('preg_quote', $knownResources));
if (preg_match('~(?:^|/)api/(' . $resourceList . ')(/[^?]*)?~i', $path, $matches)) {
    $resource = strtolower($matches[1]);
    $subpath  = rtrim($matches[2] ?? '/', '/');
    if ($subpath === '') $subpath = '/';
} else {
    // Not a known API resource
    http_response_code(404);
    echo json_encode(['error' => 'API endpoint not found', 'path' => $path]);
    exit;
}

// Pass sub-path and resource info to individual handlers
$_SERVER['AGILE_API_PATH']  = $subpath;
$_SERVER['AGILE_RESOURCE']  = $resource;

// Dispatch to the correct handler file
$apiDir = __DIR__;

switch ($resource) {
    case 'auth':
        include $apiDir . '/auth.php';
        break;
    case 'users':
        include $apiDir . '/users.php';
        break;
    case 'teams':
        include $apiDir . '/teams.php';
        break;
    case 'projects':
        include $apiDir . '/projects.php';
        break;
    case 'work-items':
        include $apiDir . '/work-items.php';
        break;
    case 'login-otp':
        include $apiDir . '/login-otp.php';
        break;
    case 'invite':
        include $apiDir . '/invite.php';
        break;
    case 'email-verification':
        include $apiDir . '/email-verification.php';
        break;
    case 'project-bug-reports':
        include $apiDir . '/project-bug-reports.php';
        break;
    case 'roadmap-templates':
        include $apiDir . '/roadmap-templates.php';
        break;
    case 'health':
        include $apiDir . '/health.php';
        break;
    case 'auth-debug':
        include $apiDir . '/auth-debug.php';
        break;
    case 'setup-users':
        include $apiDir . '/setup-users.php';
        break;
    case 'simple-login':
        include $apiDir . '/simple-login.php';
        break;
    case 'test':
        include $apiDir . '/test.php';
        break;
    default:
        http_response_code(404);
        echo json_encode(['error' => 'API endpoint not found', 'resource' => $resource]);
        break;
}
?>