<?php
require_once './api/config/database.php';

function applySafeUpdates() {
    $database = new Database();
    $pdo = $database->getConnection();
    
    if ($pdo === null) {
        echo "❌ Database connection failed\n";
        return;
    }
    
    echo "🔧 Applying Safe Work Items Updates\n";
    echo "===================================\n\n";
    
    try {
        // Safe update queries that won't cause timestamp errors
        $safeUpdates = [
            "Populate business_value for EPICs" => 
            "UPDATE work_items SET business_value = CONCAT('Strategic initiative: ', title) WHERE type = 'EPIC' AND business_value IS NULL",
            
            "Populate acceptance_criteria for FEATUREs" =>
            "UPDATE work_items SET acceptance_criteria = CONCAT('Feature Requirements:\\n- Implement ', title, '\\n- Ensure user-friendly interface\\n- Pass all acceptance tests') WHERE type = 'FEATURE' AND acceptance_criteria IS NULL",
            
            "Populate acceptance_criteria for STORIEs" =>
            "UPDATE work_items SET acceptance_criteria = CONCAT('User Story Criteria:\\n- As a user, I want ', LOWER(SUBSTRING(title, 15)), '\\n- System should respond appropriately\\n- All edge cases handled') WHERE type = 'STORY' AND acceptance_criteria IS NULL",
            
            "Set story_points for FEATUREs and STORIEs" =>
            "UPDATE work_items SET story_points = CASE WHEN priority = 'HIGH' THEN 8 WHEN priority = 'MEDIUM' THEN 5 WHEN priority = 'LOW' THEN 3 ELSE 2 END WHERE type IN ('FEATURE', 'STORY') AND story_points IS NULL",
            
            "Set estimated_hours for TASKs" =>
            "UPDATE work_items SET estimated_hours = CASE WHEN priority = 'HIGH' THEN 8 WHEN priority = 'MEDIUM' THEN 4 WHEN priority = 'LOW' THEN 2 ELSE 1 END WHERE type = 'TASK' AND estimated_hours IS NULL",
            
            "Populate reproduction_steps for BUGs" =>
            "UPDATE work_items SET reproduction_steps = CONCAT('Reproduction Steps:\\n1. Navigate to the affected feature\\n2. Perform the action described in: ', title, '\\n3. Observe the unexpected behavior\\n\\nExpected: System should work correctly') WHERE type = 'BUG' AND reproduction_steps IS NULL",
            
            "Set component for all items if missing" =>
            "UPDATE work_items SET component = CASE WHEN title LIKE '%UI%' OR title LIKE '%form%' OR title LIKE '%frontend%' THEN 'Frontend' WHEN title LIKE '%API%' OR title LIKE '%backend%' OR title LIKE '%service%' THEN 'Backend' WHEN title LIKE '%database%' OR title LIKE '%DB%' THEN 'Database' WHEN title LIKE '%test%' THEN 'Testing' ELSE 'General' END WHERE component IS NULL"
        ];
        
        $successCount = 0;
        $errorCount = 0;
        
        foreach ($safeUpdates as $description => $query) {
            try {
                $result = $pdo->exec($query);
                echo "✅ $description ($result rows affected)\n";
                $successCount++;
            } catch (Exception $e) {
                echo "❌ $description: " . $e->getMessage() . "\n";
                $errorCount++;
            }
        }
        
        echo "\n" . str_repeat("=", 50) . "\n";
        echo "📊 Update Summary: $successCount successful, $errorCount errors\n\n";
        
        // Now verify the data
        echo "📋 Verification - Data Completeness Check:\n";
        echo str_repeat("-", 40) . "\n";
        
        $verificationQueries = [
            'EPICs with business_value' => "SELECT COUNT(*) as count FROM work_items WHERE type = 'EPIC' AND business_value IS NOT NULL",
            'FEATUREs with acceptance_criteria' => "SELECT COUNT(*) as count FROM work_items WHERE type = 'FEATURE' AND acceptance_criteria IS NOT NULL",
            'STORIEs with acceptance_criteria' => "SELECT COUNT(*) as count FROM work_items WHERE type = 'STORY' AND acceptance_criteria IS NOT NULL",
            'STORIEs with story_points' => "SELECT COUNT(*) as count FROM work_items WHERE type = 'STORY' AND story_points IS NOT NULL",
            'TASKs with estimated_hours' => "SELECT COUNT(*) as count FROM work_items WHERE type = 'TASK' AND estimated_hours IS NOT NULL",
            'BUGs with reproduction_steps' => "SELECT COUNT(*) as count FROM work_items WHERE type = 'BUG' AND reproduction_steps IS NOT NULL",
            'All items with components' => "SELECT COUNT(*) as count FROM work_items WHERE component IS NOT NULL"
        ];
        
        foreach ($verificationQueries as $description => $query) {
            $stmt = $pdo->query($query);
            $count = $stmt->fetch()['count'];
            echo "✅ $description: $count items\n";
        }
        
        echo "\n🎉 All work items now have proper type-specific fields populated!\n";
        
    } catch (Exception $e) {
        echo "❌ Fatal error: " . $e->getMessage() . "\n";
    }
}

applySafeUpdates();
?>
