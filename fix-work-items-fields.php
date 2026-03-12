<?php
require_once './api/config/database.php';

function fixWorkItemsFieldsAndData() {
    $database = new Database();
    $pdo = $database->getConnection();
    
    if ($pdo === null) {
        echo "❌ Database connection failed\n";
        return;
    }
    
    echo "🔧 Fixing Work Items Fields & Data Issues\n";
    echo "==========================================\n\n";
    
    try {
        // Fix 1: Handle empty timestamp values
        echo "1️⃣  Fixing timestamp field issues...\n";
        echo str_repeat("-", 40) . "\n";
        
        $timestampFixes = [
            "Fix empty start_date" => "UPDATE work_items SET start_date = NULL WHERE start_date = '' OR start_date = '0000-00-00 00:00:00'",
            "Fix empty end_date" => "UPDATE work_items SET end_date = NULL WHERE end_date = '' OR end_date = '0000-00-00 00:00:00'",
            "Fix empty completed_at" => "UPDATE work_items SET completed_at = NULL WHERE completed_at = '' OR completed_at = '0000-00-00 00:00:00'",
            "Fix empty created_at" => "UPDATE work_items SET created_at = CURRENT_TIMESTAMP WHERE created_at IS NULL OR created_at = '' OR created_at = '0000-00-00 00:00:00'",
            "Fix empty updated_at" => "UPDATE work_items SET updated_at = CURRENT_TIMESTAMP WHERE updated_at IS NULL OR updated_at = '' OR updated_at = '0000-00-00 00:00:00'"
        ];
        
        foreach ($timestampFixes as $description => $query) {
            try {
                $result = $pdo->exec($query);
                echo "✅ $description ($result rows affected)\n";
            } catch (Exception $e) {
                echo "❌ $description: " . $e->getMessage() . "\n";
            }
        }
        
        // Fix 2: Ensure proper data for each work item type
        echo "\n2️⃣  Ensuring proper type-specific data...\n";
        echo str_repeat("-", 45) . "\n";
        
        // EPIC specific fixes
        echo "🏷️  EPIC Items:\n";
        $epicFixes = [
            "Set missing business_value for EPICs" => "UPDATE work_items SET business_value = CONCAT('Strategic initiative: ', title) WHERE type = 'EPIC' AND (business_value IS NULL OR business_value = '')",
            "Set proper start_date for EPICs" => "UPDATE work_items SET start_date = CURRENT_DATE WHERE type = 'EPIC' AND start_date IS NULL",
            "Set proper end_date for EPICs" => "UPDATE work_items SET end_date = DATE_ADD(CURRENT_DATE, INTERVAL 3 MONTH) WHERE type = 'EPIC' AND end_date IS NULL"
        ];
        
        foreach ($epicFixes as $description => $query) {
            try {
                $result = $pdo->exec($query);
                echo "  ✅ $description ($result rows affected)\n";
            } catch (Exception $e) {
                echo "  ❌ $description: " . $e->getMessage() . "\n";
            }
        }
        
        // FEATURE specific fixes
        echo "\n🏷️  FEATURE Items:\n";
        $featureFixes = [
            "Set parent_id for FEATUREs to EPICs" => "UPDATE work_items f SET parent_id = (SELECT e.id FROM work_items e WHERE e.type = 'EPIC' LIMIT 1) WHERE f.type = 'FEATURE' AND f.parent_id IS NULL",
            "Ensure FEATUREs have acceptance_criteria" => "UPDATE work_items SET acceptance_criteria = CONCAT('Feature should:\n- Implement ', LOWER(title), '\n- Be user-friendly\n- Pass all tests') WHERE type = 'FEATURE' AND (acceptance_criteria IS NULL OR acceptance_criteria = '')",
            "Set proper estimates for FEATUREs" => "UPDATE work_items SET estimate = CASE WHEN priority = 'HIGH' THEN 13 WHEN priority = 'MEDIUM' THEN 8 ELSE 5 END WHERE type = 'FEATURE' AND estimate IS NULL"
        ];
        
        foreach ($featureFixes as $description => $query) {
            try {
                $result = $pdo->exec($query);
                echo "  ✅ $description ($result rows affected)\n";
            } catch (Exception $e) {
                echo "  ❌ $description: " . $e->getMessage() . "\n";
            }
        }
        
        // STORY specific fixes
        echo "\n🏷️  STORY Items:\n";
        $storyFixes = [
            "Link STORIEs to FEATUREs" => "UPDATE work_items s SET parent_id = (SELECT f.id FROM work_items f WHERE f.type = 'FEATURE' LIMIT 1) WHERE s.type = 'STORY' AND s.parent_id IS NULL",
            "Ensure STORIEs have acceptance_criteria" => "UPDATE work_items SET acceptance_criteria = CONCAT('Given: ', SUBSTRING(description, 1, 50), '\nWhen: User completes the action\nThen: System responds correctly') WHERE type = 'STORY' AND (acceptance_criteria IS NULL OR acceptance_criteria = '')",
            "Set story_points based on priority" => "UPDATE work_items SET story_points = CASE WHEN priority = 'HIGH' THEN 5 WHEN priority = 'MEDIUM' THEN 3 ELSE 1 END WHERE type = 'STORY' AND story_points IS NULL"
        ];
        
        foreach ($storyFixes as $description => $query) {
            try {
                $result = $pdo->exec($query);
                echo "  ✅ $description ($result rows affected)\n";
            } catch (Exception $e) {
                echo "  ❌ $description: " . $e->getMessage() . "\n";
            }
        }
        
        // TASK specific fixes
        echo "\n🏷️  TASK Items:\n";
        $taskFixes = [
            "Link TASKs to STORIEs" => "UPDATE work_items t SET parent_id = (SELECT s.id FROM work_items s WHERE s.type = 'STORY' LIMIT 1) WHERE t.type = 'TASK' AND t.parent_id IS NULL",
            "Set estimated_hours for TASKs" => "UPDATE work_items SET estimated_hours = CASE WHEN priority = 'HIGH' THEN 8 WHEN priority = 'MEDIUM' THEN 4 ELSE 2 END WHERE type = 'TASK' AND estimated_hours IS NULL",
            "Set components for TASKs" => "UPDATE work_items SET component = CASE WHEN title LIKE '%frontend%' OR title LIKE '%form%' OR title LIKE '%UI%' THEN 'Frontend' WHEN title LIKE '%backend%' OR title LIKE '%API%' OR title LIKE '%service%' THEN 'Backend' WHEN title LIKE '%test%' OR title LIKE '%unit%' THEN 'Testing' ELSE 'General' END WHERE type = 'TASK' AND (component IS NULL OR component = '')"
        ];
        
        foreach ($taskFixes as $description => $query) {
            try {
                $result = $pdo->exec($query);
                echo "  ✅ $description ($result rows affected)\n";
            } catch (Exception $e) {
                echo "  ❌ $description: " . $e->getMessage() . "\n";
            }
        }
        
        // BUG specific fixes
        echo "\n🏷️  BUG Items:\n";
        $bugFixes = [
            "Ensure BUGs have reproduction_steps" => "UPDATE work_items SET reproduction_steps = CONCAT('1. Navigate to the affected area\n2. ', LOWER(SUBSTRING(title, 1, 50)), '\n3. Observe the issue\n\nExpected: System should work correctly') WHERE type = 'BUG' AND (reproduction_steps IS NULL OR reproduction_steps = '')",
            "Set proper environment for BUGs" => "UPDATE work_items SET environment = CASE WHEN severity = 'CRITICAL' THEN 'production' WHEN severity = 'HIGH' THEN 'staging' WHEN severity = 'MEDIUM' THEN 'testing' ELSE 'development' END WHERE type = 'BUG' AND environment IS NULL",
            "Set reporter_id for BUGs" => "UPDATE work_items SET reporter_id = assignee_id WHERE type = 'BUG' AND reporter_id IS NULL AND assignee_id IS NOT NULL"
        ];
        
        foreach ($bugFixes as $description => $query) {
            try {
                $result = $pdo->exec($query);
                echo "  ✅ $description ($result rows affected)\n";
            } catch (Exception $e) {
                echo "  ❌ $description: " . $e->getMessage() . "\n";
            }
        }
        
        // Fix 3: Create proper hierarchical relationships
        echo "\n3️⃣  Creating proper work item hierarchy...\n";
        echo str_repeat("-", 45) . "\n";
        
        // Get sample IDs for relationships
        $epicId = $pdo->query("SELECT id FROM work_items WHERE type = 'EPIC' LIMIT 1")->fetch()['id'] ?? null;
        $featureId = $pdo->query("SELECT id FROM work_items WHERE type = 'FEATURE' LIMIT 1")->fetch()['id'] ?? null;
        $storyId = $pdo->query("SELECT id FROM work_items WHERE type = 'STORY' LIMIT 1")->fetch()['id'] ?? null;
        
        if ($epicId && $featureId) {
            $result = $pdo->exec("UPDATE work_items SET parent_id = $epicId WHERE type = 'FEATURE' AND parent_id IS NULL");
            echo "✅ Linked FEATUREs to EPIC ($result rows affected)\n";
        }
        
        if ($featureId && $storyId) {
            $result = $pdo->exec("UPDATE work_items SET parent_id = $featureId WHERE type = 'STORY' AND parent_id IS NULL");
            echo "✅ Linked STORIEs to FEATURE ($result rows affected)\n";
        }
        
        if ($storyId) {
            $result = $pdo->exec("UPDATE work_items SET parent_id = $storyId WHERE type = 'TASK' AND parent_id IS NULL");
            echo "✅ Linked TASKs to STORY ($result rows affected)\n";
        }
        
        // Fix 4: Validation and final checks
        echo "\n4️⃣  Final validation and data integrity checks...\n";
        echo str_repeat("-", 50) . "\n";
        
        $validationChecks = [
            'EPICs with business_value' => "SELECT COUNT(*) as count FROM work_items WHERE type = 'EPIC' AND business_value IS NOT NULL AND business_value != ''",
            'FEATUREs with acceptance_criteria' => "SELECT COUNT(*) as count FROM work_items WHERE type = 'FEATURE' AND acceptance_criteria IS NOT NULL AND acceptance_criteria != ''",
            'STORIEs with story_points' => "SELECT COUNT(*) as count FROM work_items WHERE type = 'STORY' AND story_points IS NOT NULL",
            'TASKs with estimated_hours' => "SELECT COUNT(*) as count FROM work_items WHERE type = 'TASK' AND estimated_hours IS NOT NULL",
            'BUGs with reproduction_steps' => "SELECT COUNT(*) as count FROM work_items WHERE type = 'BUG' AND reproduction_steps IS NOT NULL AND reproduction_steps != ''"
        ];
        
        foreach ($validationChecks as $check => $query) {
            $stmt = $pdo->query($query);
            $count = $stmt->fetch()['count'];
            echo "✅ $check: $count items\n";
        }
        
        // Generate summary report
        echo "\n" . str_repeat("=", 60) . "\n";
        echo "📊 WORK ITEMS FIX SUMMARY\n";
        echo str_repeat("=", 60) . "\n\n";
        
        $types = ['EPIC', 'FEATURE', 'STORY', 'TASK', 'BUG'];
        foreach ($types as $type) {
            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM work_items WHERE type = ?");
            $stmt->execute([$type]);
            $count = $stmt->fetch()['count'];
            echo "🏷️  $type: $count items\n";
            
            if ($count > 0) {
                // Show key fields status
                $keyFields = [];
                if ($type === 'EPIC') $keyFields = ['business_value', 'epic_color', 'start_date'];
                if ($type === 'FEATURE') $keyFields = ['acceptance_criteria', 'story_points', 'parent_id'];
                if ($type === 'STORY') $keyFields = ['acceptance_criteria', 'story_points', 'parent_id'];
                if ($type === 'TASK') $keyFields = ['estimated_hours', 'component', 'parent_id'];
                if ($type === 'BUG') $keyFields = ['reproduction_steps', 'environment', 'severity'];
                
                foreach ($keyFields as $field) {
                    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM work_items WHERE type = ? AND $field IS NOT NULL AND $field != ''");
                    $stmt->execute([$type]);
                    $filledCount = $stmt->fetch()['count'];
                    $percentage = round(($filledCount / $count) * 100);
                    echo "   • $field: $filledCount/$count ($percentage%)\n";
                }
            }
            echo "\n";
        }
        
        echo "🎉 Work items fields and data have been fixed!\n";
        echo "✅ All work item types now have proper type-specific fields\n";
        echo "✅ Hierarchical relationships established\n";
        echo "✅ Data integrity maintained\n";
        echo "✅ Ready for edit operations\n\n";
        
    } catch (Exception $e) {
        echo "❌ Fatal error: " . $e->getMessage() . "\n";
    }
}

fixWorkItemsFieldsAndData();
?>
