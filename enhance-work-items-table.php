<?php
require_once './api/config/database.php';

function enhanceWorkItemsTable() {
    $database = new Database();
    $pdo = $database->getConnection();
    
    if ($pdo === null) {
        echo "❌ Database connection failed\n";
        return;
    }
    
    echo "🔧 Work Items Table Enhancement Script\n";
    echo "======================================\n\n";
    
    try {
        // Get current columns
        $result = $pdo->query("SHOW COLUMNS FROM work_items");
        $existingColumns = [];
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $existingColumns[] = $row['Field'];
        }
        
        echo "📋 Current columns: " . count($existingColumns) . " found\n\n";
        
        // Define additional/enhanced columns that might be missing
        $enhancementQueries = [
            'Add acceptance_criteria' => "ALTER TABLE work_items ADD COLUMN IF NOT EXISTS acceptance_criteria TEXT COMMENT 'Acceptance criteria for stories and features'",
            
            'Add story_points' => "ALTER TABLE work_items ADD COLUMN IF NOT EXISTS story_points INT DEFAULT NULL COMMENT 'Story points for agile estimation'",
            
            'Add blocked_reason' => "ALTER TABLE work_items ADD COLUMN IF NOT EXISTS blocked_reason TEXT DEFAULT NULL COMMENT 'Reason why item is blocked'",
            
            'Add is_blocked' => "ALTER TABLE work_items ADD COLUMN IF NOT EXISTS is_blocked BOOLEAN DEFAULT FALSE COMMENT 'Whether item is blocked'",
            
            'Add epic_color' => "ALTER TABLE work_items ADD COLUMN IF NOT EXISTS epic_color VARCHAR(7) DEFAULT '#3b82f6' COMMENT 'Color for epic visualization'",
            
            'Add component' => "ALTER TABLE work_items ADD COLUMN IF NOT EXISTS component VARCHAR(100) DEFAULT NULL COMMENT 'Component or module this item belongs to'",
            
            'Add version' => "ALTER TABLE work_items ADD COLUMN IF NOT EXISTS version VARCHAR(20) DEFAULT NULL COMMENT 'Target version for this item'",
            
            'Add environment' => "ALTER TABLE work_items ADD COLUMN IF NOT EXISTS environment ENUM('development', 'testing', 'staging', 'production') DEFAULT NULL COMMENT 'Environment where bug was found'",
            
            'Add reproduction_steps' => "ALTER TABLE work_items ADD COLUMN IF NOT EXISTS reproduction_steps TEXT DEFAULT NULL COMMENT 'Steps to reproduce the bug'",
            
            'Add business_value' => "ALTER TABLE work_items ADD COLUMN IF NOT EXISTS business_value TEXT DEFAULT NULL COMMENT 'Business value and justification'",
            
            'Add dependencies' => "ALTER TABLE work_items ADD COLUMN IF NOT EXISTS dependencies JSON DEFAULT NULL COMMENT 'List of dependent work item IDs'",
            
            'Add labels' => "ALTER TABLE work_items ADD COLUMN IF NOT EXISTS labels JSON DEFAULT NULL COMMENT 'Labels for categorization'",
            
            'Add watchers' => "ALTER TABLE work_items ADD COLUMN IF NOT EXISTS watchers JSON DEFAULT NULL COMMENT 'User IDs watching this item'",
            
            'Add time_tracking' => "ALTER TABLE work_items ADD COLUMN IF NOT EXISTS time_tracking JSON DEFAULT NULL COMMENT 'Detailed time tracking data'",
            
            'Add custom_fields' => "ALTER TABLE work_items ADD COLUMN IF NOT EXISTS custom_fields JSON DEFAULT NULL COMMENT 'Custom field values'",
            
            'Add resolution' => "ALTER TABLE work_items ADD COLUMN IF NOT EXISTS resolution ENUM('fixed', 'wont_fix', 'duplicate', 'cannot_reproduce', 'works_as_designed') DEFAULT NULL COMMENT 'Bug resolution type'",
            
            'Add fix_version' => "ALTER TABLE work_items ADD COLUMN IF NOT EXISTS fix_version VARCHAR(20) DEFAULT NULL COMMENT 'Version where bug was fixed'",
            
            'Add affects_version' => "ALTER TABLE work_items ADD COLUMN IF NOT EXISTS affects_version VARCHAR(20) DEFAULT NULL COMMENT 'Version where bug was found'",
        ];
        
        // Index enhancement queries
        $indexQueries = [
            'Add index on story_points' => "CREATE INDEX IF NOT EXISTS idx_story_points ON work_items(story_points)",
            'Add index on is_blocked' => "CREATE INDEX IF NOT EXISTS idx_is_blocked ON work_items(is_blocked)",
            'Add index on component' => "CREATE INDEX IF NOT EXISTS idx_component ON work_items(component)",
            'Add index on version' => "CREATE INDEX IF NOT EXISTS idx_version ON work_items(version)",
            'Add index on environment' => "CREATE INDEX IF NOT EXISTS idx_environment ON work_items(environment)",
            'Add index on resolution' => "CREATE INDEX IF NOT EXISTS idx_resolution ON work_items(resolution)",
            'Add composite index on type_status' => "CREATE INDEX IF NOT EXISTS idx_type_status ON work_items(type, status)",
            'Add composite index on project_type' => "CREATE INDEX IF NOT EXISTS idx_project_type ON work_items(project_id, type)",
            'Add composite index on assignee_status' => "CREATE INDEX IF NOT EXISTS idx_assignee_status ON work_items(assignee_id, status)",
        ];
        
        echo "🔄 Adding enhanced columns...\n";
        echo str_repeat("-", 40) . "\n";
        
        $successCount = 0;
        $skipCount = 0;
        $errorCount = 0;
        
        foreach ($enhancementQueries as $description => $query) {
            try {
                $pdo->exec($query);
                echo "✅ $description\n";
                $successCount++;
            } catch (Exception $e) {
                if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
                    echo "⏭️  $description (already exists)\n";
                    $skipCount++;
                } else {
                    echo "❌ $description: " . $e->getMessage() . "\n";
                    $errorCount++;
                }
            }
        }
        
        echo "\n🔄 Adding performance indexes...\n";
        echo str_repeat("-", 40) . "\n";
        
        foreach ($indexQueries as $description => $query) {
            try {
                $pdo->exec($query);
                echo "✅ $description\n";
                $successCount++;
            } catch (Exception $e) {
                if (strpos($e->getMessage(), 'Duplicate key name') !== false) {
                    echo "⏭️  $description (already exists)\n";
                    $skipCount++;
                } else {
                    echo "❌ $description: " . $e->getMessage() . "\n";
                    $errorCount++;
                }
            }
        }
        
        // Update existing data with some enhanced values
        echo "\n🔄 Updating existing data with enhanced values...\n";
        echo str_repeat("-", 50) . "\n";
        
        $updateQueries = [
            'Set story points for stories' => "UPDATE work_items SET story_points = CASE 
                WHEN priority = 'CRITICAL' THEN 8
                WHEN priority = 'HIGH' THEN 5
                WHEN priority = 'MEDIUM' THEN 3
                WHEN priority = 'LOW' THEN 1
                END WHERE type IN ('STORY', 'FEATURE') AND story_points IS NULL",
                
            'Set epic colors' => "UPDATE work_items SET epic_color = CASE
                WHEN priority = 'CRITICAL' THEN '#dc3545'
                WHEN priority = 'HIGH' THEN '#fd7e14'
                WHEN priority = 'MEDIUM' THEN '#20c997'
                WHEN priority = 'LOW' THEN '#6c757d'
                END WHERE type = 'EPIC' AND epic_color = '#3b82f6'",
                
            'Set components for tasks' => "UPDATE work_items SET component = CASE
                WHEN title LIKE '%frontend%' OR title LIKE '%form%' OR title LIKE '%UI%' THEN 'Frontend'
                WHEN title LIKE '%backend%' OR title LIKE '%API%' OR title LIKE '%service%' THEN 'Backend'
                WHEN title LIKE '%database%' OR title LIKE '%DB%' THEN 'Database'
                WHEN title LIKE '%test%' THEN 'Testing'
                ELSE 'General'
                END WHERE component IS NULL",
                
            'Set environment for bugs' => "UPDATE work_items SET environment = CASE
                WHEN severity = 'CRITICAL' THEN 'production'
                WHEN severity = 'HIGH' THEN 'staging'
                WHEN severity = 'MEDIUM' THEN 'testing'
                ELSE 'development'
                END WHERE type = 'BUG' AND environment IS NULL",
                
            'Add basic acceptance criteria' => "UPDATE work_items SET acceptance_criteria = CONCAT(
                'Given: ', SUBSTRING(description, 1, 100), 
                '\\nWhen: User performs the action\\nThen: Expected outcome should occur'
            ) WHERE type IN ('STORY', 'FEATURE') AND acceptance_criteria IS NULL"
        ];
        
        foreach ($updateQueries as $description => $query) {
            try {
                $result = $pdo->exec($query);
                echo "✅ $description ($result rows updated)\n";
            } catch (Exception $e) {
                echo "❌ $description: " . $e->getMessage() . "\n";
            }
        }
        
        echo "\n" . str_repeat("=", 60) . "\n";
        echo "📊 Enhancement Summary:\n";
        echo "✅ Successful operations: $successCount\n";
        echo "⏭️  Skipped (already exists): $skipCount\n";
        echo "❌ Errors: $errorCount\n";
        
        if ($errorCount === 0) {
            echo "\n🎉 Work items table enhancement completed successfully!\n";
        } else {
            echo "\n⚠️  Some enhancements had issues. Please check the errors above.\n";
        }
        
        // Show final column count
        $result = $pdo->query("SHOW COLUMNS FROM work_items");
        $finalColumns = $result->rowCount();
        echo "\n📋 Final column count: $finalColumns columns\n";
        
        // Show sample enhanced data
        echo "\n📝 Sample Enhanced Work Item:\n";
        echo str_repeat("-", 40) . "\n";
        
        $stmt = $pdo->query("SELECT id, title, type, status, priority, story_points, epic_color, component, environment FROM work_items LIMIT 1");
        if ($item = $stmt->fetch()) {
            foreach ($item as $key => $value) {
                if ($value !== null) {
                    echo "• $key: $value\n";
                }
            }
        }
        
    } catch (Exception $e) {
        echo "❌ Fatal error: " . $e->getMessage() . "\n";
    }
}

enhanceWorkItemsTable();
?>
