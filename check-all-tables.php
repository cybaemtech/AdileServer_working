<?php
require_once './api/config/database.php';

function showAllTableStructures() {
    $database = new Database();
    $pdo = $database->getConnection();
    
    if ($pdo === null) {
        echo "❌ Database connection failed\n";
        return;
    }
    
    echo "📋 Complete Database Schema Report\n";
    echo "==================================\n\n";
    
    try {
        // Get all tables
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        foreach ($tables as $table) {
            echo "🗂️  Table: $table\n";
            echo str_repeat("-", strlen($table) + 10) . "\n";
            
            // Get table structure
            $result = $pdo->query("SHOW COLUMNS FROM $table");
            printf("%-25s %-25s %-8s %-8s %-10s %-20s\n", 
                "Field", "Type", "Null", "Key", "Default", "Extra");
            echo str_repeat("-", 100) . "\n";
            
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                printf("%-25s %-25s %-8s %-8s %-10s %-20s\n", 
                    $row['Field'], 
                    $row['Type'], 
                    $row['Null'], 
                    $row['Key'],
                    $row['Default'] ?? 'NULL',
                    $row['Extra']
                );
            }
            
            // Get row count
            try {
                $countStmt = $pdo->query("SELECT COUNT(*) as count FROM $table");
                $count = $countStmt->fetch();
                echo "\n📊 Row count: " . $count['count'] . "\n";
            } catch (Exception $e) {
                echo "\n⚠️  Could not get row count: " . $e->getMessage() . "\n";
            }
            
            echo "\n" . str_repeat("=", 100) . "\n\n";
        }
        
        // Summary
        echo "📊 Database Summary:\n";
        echo "-------------------\n";
        echo "Total tables: " . count($tables) . "\n";
        echo "Tables found:\n";
        foreach ($tables as $table) {
            echo "  • $table\n";
        }
        
    } catch (Exception $e) {
        echo "❌ Error: " . $e->getMessage() . "\n";
    }
}

showAllTableStructures();
?>
