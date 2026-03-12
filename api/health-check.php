<?php
require_once './api/config/database.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

function checkDatabaseConnection() {
    try {
        $database = new Database();
        $pdo = $database->getConnection();
        
        if ($pdo === null) {
            return [
                'status' => 'failed',
                'message' => 'Database connection failed',
                'connected' => false
            ];
        }
        
        // Test with a simple query
        $stmt = $pdo->query("SELECT 1 as test");
        $result = $stmt->fetch();
        
        return [
            'status' => 'success',
            'message' => 'Database connection successful',
            'connected' => true,
            'test_query' => $result['test'] === 1
        ];
    } catch (Exception $e) {
        return [
            'status' => 'error',
            'message' => 'Database error: ' . $e->getMessage(),
            'connected' => false
        ];
    }
}

function checkRequiredTables($pdo) {
    $requiredTables = ['users', 'projects', 'teams', 'work_items'];
    $tableStatus = [];
    
    foreach ($requiredTables as $table) {
        try {
            $stmt = $pdo->prepare("SHOW TABLES LIKE ?");
            $stmt->execute([$table]);
            $exists = $stmt->fetch() !== false;
            
            $tableStatus[$table] = [
                'exists' => $exists,
                'status' => $exists ? 'found' : 'missing'
            ];
            
            if ($exists) {
                // Get row count
                $countStmt = $pdo->query("SELECT COUNT(*) as count FROM $table");
                $count = $countStmt->fetch();
                $tableStatus[$table]['row_count'] = (int)$count['count'];
            }
        } catch (Exception $e) {
            $tableStatus[$table] = [
                'exists' => false,
                'status' => 'error',
                'error' => $e->getMessage()
            ];
        }
    }
    
    return $tableStatus;
}

function checkAPIEndpoints() {
    $baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . 
               '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']);
    
    $endpoints = [
        'users' => ['url' => $baseUrl . '/api/users.php', 'file' => 'users.php'],
        'projects' => ['url' => $baseUrl . '/api/projects.php', 'file' => 'projects.php'],
        'teams' => ['url' => $baseUrl . '/api/teams.php', 'file' => 'teams.php'],
        'work_items' => ['url' => $baseUrl . '/api/work-items.php', 'file' => 'work-items.php'],
        'auth' => ['url' => $baseUrl . '/api/auth.php', 'file' => 'auth.php']
    ];
    
    $endpointStatus = [];
    
    foreach ($endpoints as $name => $info) {
        $filePath = './api/' . $info['file'];
        $endpointStatus[$name] = [
            'url' => $info['url'],
            'file_exists' => file_exists($filePath),
            'status' => file_exists($filePath) ? 'available' : 'missing'
        ];
    }
    
    return $endpointStatus;
}

function getSystemInfo() {
    return [
        'php_version' => PHP_VERSION,
        'server' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
        'host' => $_SERVER['HTTP_HOST'] ?? 'Unknown',
        'current_time' => date('Y-m-d H:i:s'),
        'timezone' => date_default_timezone_get(),
        'memory_limit' => ini_get('memory_limit'),
        'max_execution_time' => ini_get('max_execution_time')
    ];
}

// Main health check
$healthCheck = [
    'timestamp' => date('Y-m-d H:i:s'),
    'status' => 'checking',
    'system' => getSystemInfo(),
    'database' => checkDatabaseConnection(),
    'tables' => [],
    'api_endpoints' => checkAPIEndpoints()
];

// Only check tables if database connection is successful
if ($healthCheck['database']['connected']) {
    $database = new Database();
    $pdo = $database->getConnection();
    $healthCheck['tables'] = checkRequiredTables($pdo);
}

// Determine overall status
$overallStatus = 'healthy';
$issues = [];

if (!$healthCheck['database']['connected']) {
    $overallStatus = 'unhealthy';
    $issues[] = 'Database connection failed';
}

foreach ($healthCheck['tables'] as $table => $status) {
    if (!$status['exists']) {
        $overallStatus = 'degraded';
        $issues[] = "Table '$table' is missing";
    }
}

foreach ($healthCheck['api_endpoints'] as $endpoint => $status) {
    if ($status['status'] === 'missing') {
        $overallStatus = 'degraded';
        $issues[] = "API endpoint '$endpoint' file is missing";
    }
}

$healthCheck['status'] = $overallStatus;
$healthCheck['issues'] = $issues;
$healthCheck['summary'] = [
    'database_connected' => $healthCheck['database']['connected'],
    'tables_found' => count(array_filter($healthCheck['tables'], fn($t) => $t['exists'] ?? false)),
    'tables_total' => count($healthCheck['tables']),
    'endpoints_available' => count(array_filter($healthCheck['api_endpoints'], fn($e) => $e['file_exists'])),
    'endpoints_total' => count($healthCheck['api_endpoints'])
];

// Output the health check results
echo json_encode($healthCheck, JSON_PRETTY_PRINT);
?>
