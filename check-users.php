<?php
require_once 'api/config/database.php';

$database = new Database();
$conn = $database->getConnection();

try {
    $stmt = $conn->prepare("SELECT id, username, email FROM users LIMIT 5");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Existing users:\n";
    foreach ($users as $user) {
        echo "ID: {$user['id']}, Username: {$user['username']}, Email: {$user['email']}\n";
    }
    
    if (empty($users)) {
        echo "No users found in the database.\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
