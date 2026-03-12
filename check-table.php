<?php
require_once './api/config/database.php';

try {
    $database = new Database();
    $pdo = $database->getConnection();
    
    if ($pdo === null) {
        throw new Exception("Failed to connect to database");
    }
    
    $result = $pdo->query('SHOW COLUMNS FROM work_items');
    echo "Columns in work_items table:\n";
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        echo $row['Field'] . ' (' . $row['Type'] . ")\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
