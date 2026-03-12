<?php
require_once './api/config/database.php';

function checkDatabaseExists() {
    $database = new Database();
    $pdo = $database->getConnection();
    
    if ($pdo === null) {
        echo "❌ Database connection failed\n";
        return false;
    }
    
    echo "✅ Database connection successful\n";
    return $pdo;
}

function checkTableExists($pdo, $tableName) {
    try {
        $stmt = $pdo->prepare("SHOW TABLES LIKE ?");
        $stmt->execute([$tableName]);
        $result = $stmt->fetch();
        
        if ($result) {
            echo "✅ Table '$tableName' exists\n";
            return true;
        } else {
            echo "❌ Table '$tableName' does not exist\n";
            return false;
        }
    } catch (Exception $e) {
        echo "❌ Error checking table '$tableName': " . $e->getMessage() . "\n";
        return false;
    }
}

function showTableStructure($pdo, $tableName) {
    try {
        $result = $pdo->query("SHOW COLUMNS FROM $tableName");
        echo "\n📋 Columns in '$tableName' table:\n";
        echo str_repeat("-", 50) . "\n";
        printf("%-20s %-20s %-10s %-10s\n", "Field", "Type", "Null", "Key");
        echo str_repeat("-", 50) . "\n";
        
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            printf("%-20s %-20s %-10s %-10s\n", 
                $row['Field'], 
                $row['Type'], 
                $row['Null'], 
                $row['Key']
            );
        }
        echo str_repeat("-", 50) . "\n";
    } catch (Exception $e) {
        echo "❌ Error showing table structure: " . $e->getMessage() . "\n";
    }
}

function showDatabaseInfo($pdo) {
    try {
        // Get database name
        $stmt = $pdo->query("SELECT DATABASE() as db_name");
        $dbInfo = $stmt->fetch();
        echo "\n🗄️  Current Database: " . $dbInfo['db_name'] . "\n";
        
        // Get all tables
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        echo "\n📊 Tables in database (" . count($tables) . " total):\n";
        foreach ($tables as $table) {
            echo "  • $table\n";
        }
        
    } catch (Exception $e) {
        echo "❌ Error getting database info: " . $e->getMessage() . "\n";
    }
}

function createWorkItemsTable($pdo) {
    $sql = "CREATE TABLE IF NOT EXISTS work_items (
        id INT PRIMARY KEY AUTO_INCREMENT,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        type ENUM('Task', 'Bug', 'Feature', 'Epic', 'Story') NOT NULL DEFAULT 'Task',
        status ENUM('New', 'Active', 'Resolved', 'Closed', 'Removed') NOT NULL DEFAULT 'New',
        priority ENUM('Critical', 'High', 'Medium', 'Low') NOT NULL DEFAULT 'Medium',
        assigned_to INT,
        project_id INT,
        created_by INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        due_date DATE,
        story_points INT,
        tags JSON,
        attachment_urls JSON,
        FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL,
        FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
        FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    try {
        $pdo->exec($sql);
        echo "✅ work_items table created successfully\n";
        return true;
    } catch (Exception $e) {
        echo "❌ Error creating work_items table: " . $e->getMessage() . "\n";
        return false;
    }
}

// Main execution
echo "🔍 Database Connection and Table Check\n";
echo "=====================================\n\n";

$pdo = checkDatabaseExists();

if ($pdo) {
    showDatabaseInfo($pdo);
    
    echo "\n🔍 Checking specific tables:\n";
    echo "----------------------------\n";
    
    // Check important tables
    $importantTables = ['users', 'projects', 'teams', 'work_items'];
    
    foreach ($importantTables as $table) {
        $exists = checkTableExists($pdo, $table);
        if (!$exists && $table === 'work_items') {
            echo "\n🔧 Creating work_items table...\n";
            createWorkItemsTable($pdo);
        }
    }
    
    // Show work_items structure if it exists
    if (checkTableExists($pdo, 'work_items')) {
        showTableStructure($pdo, 'work_items');
    }
    
    echo "\n✅ Database check completed!\n";
} else {
    echo "\n❌ Cannot proceed without database connection.\n";
    echo "Please check your database configuration in api/config/database.php\n";
}
?>
