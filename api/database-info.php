<?php
// Database info and users listing
require_once 'config/database.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    if (!$conn) {
        throw new Exception("Database connection failed");
    }

    $result = [];
    
    // Get database info
    $dbInfo = $conn->query("SELECT DATABASE() as db_name")->fetch();
    $result['database'] = $dbInfo['db_name'];
    
    // Get tables
    $tablesStmt = $conn->query("SHOW TABLES");
    $tables = $tablesStmt->fetchAll(PDO::FETCH_COLUMN);
    $result['tables'] = $tables;
    
    // Check if users table exists and get some sample data
    if (in_array('users', $tables)) {
        // Get table structure
        $structureStmt = $conn->query("DESCRIBE users");
        $result['users_structure'] = $structureStmt->fetchAll();
        
        // Get user count
        $countStmt = $conn->query("SELECT COUNT(*) as count FROM users");
        $result['users_count'] = $countStmt->fetch()['count'];
        
        // Get first few users (without passwords)
        $usersStmt = $conn->query("SELECT id, username, email, full_name, role, created_at FROM users LIMIT 5");
        $result['sample_users'] = $usersStmt->fetchAll();
    }
    
    echo json_encode([
        'status' => 'success',
        'data' => $result,
        'timestamp' => date('Y-m-d H:i:s')
    ], JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}
?>
