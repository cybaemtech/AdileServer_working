<?php
require_once './api/config/database.php';

echo "🔍 Checking for Missing EPIC and FEATURE Fields\n";
echo "===============================================\n\n";

try {
    $database = new Database();
    $pdo = $database->getConnection();
    
    if ($pdo === null) {
        echo "❌ Database connection failed\n";
        return;
    }
    
    // Check current table structure
    $stmt = $pdo->query("DESCRIBE work_items");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $existing_columns = array_column($columns, 'Field');
    
    // Define required fields for EPIC and FEATURE
    $required_fields = [
        'prototype_link' => [
            'type' => 'varchar(500)',
            'description' => 'Link to prototype or mockup',
            'for_types' => ['EPIC', 'FEATURE']
        ],
        'drag_drop_enabled' => [
            'type' => 'tinyint(1)',
            'description' => 'Enable drag and drop functionality',
            'for_types' => ['EPIC', 'FEATURE']
        ],
        'pdf_upload_path' => [
            'type' => 'varchar(500)',
            'description' => 'Path to uploaded PDF document',
            'for_types' => ['EPIC', 'FEATURE']
        ],
        'pdf_upload_blob' => [
            'type' => 'longblob',
            'description' => 'PDF file content as blob',
            'for_types' => ['EPIC', 'FEATURE']
        ],
        'prototype_status' => [
            'type' => "enum('not_started','in_progress','completed','approved')",
            'description' => 'Status of prototype development',
            'for_types' => ['EPIC', 'FEATURE']
        ],
        'mockup_link' => [
            'type' => 'varchar(500)',
            'description' => 'Link to design mockups',
            'for_types' => ['EPIC', 'FEATURE']
        ]
    ];
    
    echo "📋 Current Table Columns:\n";
    echo str_repeat("-", 50) . "\n";
    foreach ($existing_columns as $col) {
        echo "   ✅ $col\n";
    }
    
    echo "\n🔍 Checking for Missing Required Fields:\n";
    echo str_repeat("-", 50) . "\n";
    
    $missing_fields = [];
    
    foreach ($required_fields as $field => $config) {
        if (in_array($field, $existing_columns)) {
            echo "   ✅ $field - EXISTS\n";
        } else {
            echo "   ❌ $field - MISSING\n";
            $missing_fields[$field] = $config;
        }
    }
    
    if (empty($missing_fields)) {
        echo "\n🎉 All required fields are present!\n";
    } else {
        echo "\n⚠️  Missing Fields Detected: " . count($missing_fields) . "\n";
        echo str_repeat("=", 60) . "\n";
        
        // Generate SQL to add missing fields
        $alter_statements = [];
        
        foreach ($missing_fields as $field => $config) {
            $sql = "ALTER TABLE work_items ADD COLUMN $field {$config['type']} NULL";
            $alter_statements[] = $sql;
            
            echo "\n📝 Field: $field\n";
            echo "   Type: {$config['type']}\n";
            echo "   Description: {$config['description']}\n";
            echo "   For Types: " . implode(', ', $config['for_types']) . "\n";
            echo "   SQL: $sql;\n";
        }
        
        // Save SQL to file
        $sql_content = "-- SQL to add missing EPIC and FEATURE fields\n";
        $sql_content .= "-- Generated on " . date('Y-m-d H:i:s') . "\n\n";
        
        foreach ($alter_statements as $sql) {
            $sql_content .= $sql . ";\n";
        }
        
        file_put_contents('add-missing-fields.sql', $sql_content);
        echo "\n💾 SQL statements saved to 'add-missing-fields.sql'\n";
        
        // Optionally execute the ALTER statements
        echo "\n🚀 Do you want to add these fields now? (Executing ALTER statements...)\n";
        
        foreach ($alter_statements as $sql) {
            try {
                $pdo->exec($sql);
                $field_name = explode(' ', $sql)[5]; // Extract field name
                echo "   ✅ Added field: $field_name\n";
            } catch (PDOException $e) {
                echo "   ❌ Error adding field: " . $e->getMessage() . "\n";
            }
        }
    }
    
    // Check current EPIC and FEATURE items
    echo "\n📊 Current EPIC and FEATURE Items:\n";
    echo str_repeat("-", 50) . "\n";
    
    $stmt = $pdo->query("SELECT id, title, type, status FROM work_items WHERE type IN ('EPIC', 'FEATURE') ORDER BY type, id");
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($items)) {
        echo "   ⚠️  No EPIC or FEATURE items found\n";
    } else {
        foreach ($items as $item) {
            echo "   🏷️  {$item['type']} #{$item['id']}: {$item['title']} ({$item['status']})\n";
        }
    }
    
    echo "\n🔄 Re-checking table structure after changes...\n";
    echo str_repeat("-", 50) . "\n";
    
    $stmt = $pdo->query("DESCRIBE work_items");
    $new_columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $new_column_names = array_column($new_columns, 'Field');
    
    foreach ($required_fields as $field => $config) {
        if (in_array($field, $new_column_names)) {
            echo "   ✅ $field - NOW EXISTS\n";
        } else {
            echo "   ❌ $field - STILL MISSING\n";
        }
    }
    
} catch (PDOException $e) {
    echo "❌ Database Error: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n✨ Field check completed!\n";
?>
