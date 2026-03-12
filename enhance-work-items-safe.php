<?php
require_once './api/config/database.php';

function enhanceWorkItemsTableSafe() {
    $database = new Database();
    $pdo = $database->getConnection();
    
    if ($pdo === null) {
        echo "❌ Database connection failed\n";
        return;
    }
    
    echo "🔧 Work Items Table Safe Enhancement Script\n";
    echo "============================================\n\n";
    
    try {
        // Function to check if column exists
        function columnExists($pdo, $tableName, $columnName) {
            $stmt = $pdo->prepare("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = ? AND COLUMN_NAME = ? AND TABLE_SCHEMA = DATABASE()");
            $stmt->execute([$tableName, $columnName]);
            return $stmt->fetch() !== false;
        }
        
        // Function to check if index exists
        function indexExists($pdo, $tableName, $indexName) {
            $stmt = $pdo->prepare("SELECT INDEX_NAME FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_NAME = ? AND INDEX_NAME = ? AND TABLE_SCHEMA = DATABASE()");
            $stmt->execute([$tableName, $indexName]);
            return $stmt->fetch() !== false;
        }
        
        // Define additional columns with their queries
        $newColumns = [
            'acceptance_criteria' => "ALTER TABLE work_items ADD COLUMN acceptance_criteria TEXT COMMENT 'Acceptance criteria for stories and features'",
            'story_points' => "ALTER TABLE work_items ADD COLUMN story_points INT DEFAULT NULL COMMENT 'Story points for agile estimation'",
            'blocked_reason' => "ALTER TABLE work_items ADD COLUMN blocked_reason TEXT DEFAULT NULL COMMENT 'Reason why item is blocked'",
            'is_blocked' => "ALTER TABLE work_items ADD COLUMN is_blocked BOOLEAN DEFAULT FALSE COMMENT 'Whether item is blocked'",
            'epic_color' => "ALTER TABLE work_items ADD COLUMN epic_color VARCHAR(7) DEFAULT '#3b82f6' COMMENT 'Color for epic visualization'",
            'component' => "ALTER TABLE work_items ADD COLUMN component VARCHAR(100) DEFAULT NULL COMMENT 'Component or module this item belongs to'",
            'version' => "ALTER TABLE work_items ADD COLUMN version VARCHAR(20) DEFAULT NULL COMMENT 'Target version for this item'",
            'environment' => "ALTER TABLE work_items ADD COLUMN environment ENUM('development', 'testing', 'staging', 'production') DEFAULT NULL COMMENT 'Environment where bug was found'",
            'reproduction_steps' => "ALTER TABLE work_items ADD COLUMN reproduction_steps TEXT DEFAULT NULL COMMENT 'Steps to reproduce the bug'",
            'business_value' => "ALTER TABLE work_items ADD COLUMN business_value TEXT DEFAULT NULL COMMENT 'Business value and justification'",
            'dependencies' => "ALTER TABLE work_items ADD COLUMN dependencies JSON DEFAULT NULL COMMENT 'List of dependent work item IDs'",
            'labels' => "ALTER TABLE work_items ADD COLUMN labels JSON DEFAULT NULL COMMENT 'Labels for categorization'",
            'watchers' => "ALTER TABLE work_items ADD COLUMN watchers JSON DEFAULT NULL COMMENT 'User IDs watching this item'",
            'time_tracking' => "ALTER TABLE work_items ADD COLUMN time_tracking JSON DEFAULT NULL COMMENT 'Detailed time tracking data'",
            'custom_fields' => "ALTER TABLE work_items ADD COLUMN custom_fields JSON DEFAULT NULL COMMENT 'Custom field values'",
            'resolution' => "ALTER TABLE work_items ADD COLUMN resolution ENUM('fixed', 'wont_fix', 'duplicate', 'cannot_reproduce', 'works_as_designed') DEFAULT NULL COMMENT 'Bug resolution type'",
            'fix_version' => "ALTER TABLE work_items ADD COLUMN fix_version VARCHAR(20) DEFAULT NULL COMMENT 'Version where bug was fixed'",
            'affects_version' => "ALTER TABLE work_items ADD COLUMN affects_version VARCHAR(20) DEFAULT NULL COMMENT 'Version where bug was found'"
        ];
        
        echo "🔄 Adding new columns...\n";
        echo str_repeat("-", 40) . "\n";
        
        $addedColumns = [];
        $skippedColumns = [];
        $errorColumns = [];
        
        foreach ($newColumns as $columnName => $query) {
            if (columnExists($pdo, 'work_items', $columnName)) {
                echo "⏭️  Column '$columnName' already exists\n";
                $skippedColumns[] = $columnName;
            } else {
                try {
                    $pdo->exec($query);
                    echo "✅ Added column '$columnName'\n";
                    $addedColumns[] = $columnName;
                } catch (Exception $e) {
                    echo "❌ Error adding '$columnName': " . $e->getMessage() . "\n";
                    $errorColumns[] = $columnName;
                }
            }
        }
        
        // Add indexes for new columns
        $newIndexes = [
            'idx_story_points' => "CREATE INDEX idx_story_points ON work_items(story_points)",
            'idx_is_blocked' => "CREATE INDEX idx_is_blocked ON work_items(is_blocked)",
            'idx_component' => "CREATE INDEX idx_component ON work_items(component)",
            'idx_version' => "CREATE INDEX idx_version ON work_items(version)",
            'idx_environment' => "CREATE INDEX idx_environment ON work_items(environment)",
            'idx_resolution' => "CREATE INDEX idx_resolution ON work_items(resolution)",
            'idx_type_status' => "CREATE INDEX idx_type_status ON work_items(type, status)",
            'idx_project_type' => "CREATE INDEX idx_project_type ON work_items(project_id, type)",
            'idx_assignee_status' => "CREATE INDEX idx_assignee_status ON work_items(assignee_id, status)"
        ];
        
        if (!empty($addedColumns)) {
            echo "\n🔄 Adding indexes for new columns...\n";
            echo str_repeat("-", 40) . "\n";
            
            foreach ($newIndexes as $indexName => $query) {
                if (indexExists($pdo, 'work_items', $indexName)) {
                    echo "⏭️  Index '$indexName' already exists\n";
                } else {
                    try {
                        $pdo->exec($query);
                        echo "✅ Added index '$indexName'\n";
                    } catch (Exception $e) {
                        echo "❌ Error adding index '$indexName': " . $e->getMessage() . "\n";
                    }
                }
            }
        }
        
        // Update existing data only for columns that were successfully added
        if (!empty($addedColumns)) {
            echo "\n🔄 Populating new columns with data...\n";
            echo str_repeat("-", 50) . "\n";
            
            $updateQueries = [];
            
            if (in_array('story_points', $addedColumns)) {
                $updateQueries['Set story points for stories'] = "UPDATE work_items SET story_points = CASE 
                    WHEN priority = 'CRITICAL' THEN 8
                    WHEN priority = 'HIGH' THEN 5
                    WHEN priority = 'MEDIUM' THEN 3
                    WHEN priority = 'LOW' THEN 1
                    END WHERE type IN ('STORY', 'FEATURE') AND story_points IS NULL";
            }
            
            if (in_array('epic_color', $addedColumns)) {
                $updateQueries['Set epic colors'] = "UPDATE work_items SET epic_color = CASE
                    WHEN priority = 'CRITICAL' THEN '#dc3545'
                    WHEN priority = 'HIGH' THEN '#fd7e14'
                    WHEN priority = 'MEDIUM' THEN '#20c997'
                    WHEN priority = 'LOW' THEN '#6c757d'
                    END WHERE type = 'EPIC' AND epic_color = '#3b82f6'";
            }
            
            if (in_array('component', $addedColumns)) {
                $updateQueries['Set components'] = "UPDATE work_items SET component = CASE
                    WHEN title LIKE '%frontend%' OR title LIKE '%form%' OR title LIKE '%UI%' THEN 'Frontend'
                    WHEN title LIKE '%backend%' OR title LIKE '%API%' OR title LIKE '%service%' THEN 'Backend'
                    WHEN title LIKE '%database%' OR title LIKE '%DB%' THEN 'Database'
                    WHEN title LIKE '%test%' THEN 'Testing'
                    ELSE 'General'
                    END WHERE component IS NULL";
            }
            
            if (in_array('environment', $addedColumns)) {
                $updateQueries['Set environment for bugs'] = "UPDATE work_items SET environment = CASE
                    WHEN severity = 'CRITICAL' THEN 'production'
                    WHEN severity = 'HIGH' THEN 'staging'
                    WHEN severity = 'MEDIUM' THEN 'testing'
                    ELSE 'development'
                    END WHERE type = 'BUG' AND environment IS NULL";
            }
            
            if (in_array('acceptance_criteria', $addedColumns)) {
                $updateQueries['Add basic acceptance criteria'] = "UPDATE work_items SET acceptance_criteria = CONCAT(
                    'Given: User needs to ', LOWER(SUBSTRING(title, 1, 50)), 
                    '\\nWhen: User performs the required action\\nThen: System should respond appropriately'
                ) WHERE type IN ('STORY', 'FEATURE') AND acceptance_criteria IS NULL";
            }
            
            if (in_array('reproduction_steps', $addedColumns)) {
                $updateQueries['Add reproduction steps for bugs'] = "UPDATE work_items SET reproduction_steps = CONCAT(
                    '1. Navigate to the affected area\\n2. Perform the action that causes the issue\\n3. Observe the problem\\n\\nExpected: ', expected_behavior
                ) WHERE type = 'BUG' AND reproduction_steps IS NULL AND expected_behavior IS NOT NULL";
            }
            
            foreach ($updateQueries as $description => $query) {
                try {
                    $result = $pdo->exec($query);
                    echo "✅ $description ($result rows updated)\n";
                } catch (Exception $e) {
                    echo "❌ $description: " . $e->getMessage() . "\n";
                }
            }
        }
        
        echo "\n" . str_repeat("=", 60) . "\n";
        echo "📊 Enhancement Summary:\n";
        echo "✅ Columns added: " . count($addedColumns) . "\n";
        echo "⏭️  Columns skipped (existed): " . count($skippedColumns) . "\n";
        echo "❌ Column errors: " . count($errorColumns) . "\n";
        
        if (!empty($addedColumns)) {
            echo "\n🎉 Successfully added columns:\n";
            foreach ($addedColumns as $col) {
                echo "  • $col\n";
            }
        }
        
        // Show final table info
        $result = $pdo->query("SHOW COLUMNS FROM work_items");
        $finalColumns = $result->rowCount();
        echo "\n📋 Total columns now: $finalColumns\n";
        
        // Show sample data with new columns
        if (!empty($addedColumns)) {
            echo "\n📝 Sample Data with New Columns:\n";
            echo str_repeat("-", 50) . "\n";
            
            $sampleColumns = array_merge(['id', 'title', 'type', 'status', 'priority'], array_slice($addedColumns, 0, 5));
            $columnList = implode(', ', $sampleColumns);
            
            $stmt = $pdo->query("SELECT $columnList FROM work_items WHERE type = 'STORY' LIMIT 1");
            if ($item = $stmt->fetch()) {
                foreach ($item as $key => $value) {
                    $displayValue = $value ?? 'NULL';
                    if (strlen($displayValue) > 50) {
                        $displayValue = substr($displayValue, 0, 47) . '...';
                    }
                    echo "• $key: $displayValue\n";
                }
            }
        }
        
        echo "\n✅ Enhancement process completed!\n";
        
    } catch (Exception $e) {
        echo "❌ Fatal error: " . $e->getMessage() . "\n";
    }
}

enhanceWorkItemsTableSafe();
?>
