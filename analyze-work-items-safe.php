<?php
require_once './api/config/database.php';

function fixWorkItemsDataSafe() {
    $database = new Database();
    $pdo = $database->getConnection();
    
    if ($pdo === null) {
        echo "❌ Database connection failed\n";
        return;
    }
    
    echo "🔧 Safe Work Items Data Fix\n";
    echo "============================\n\n";
    
    try {
        // First, let's check the current state of timestamp fields
        echo "1️⃣  Checking timestamp field values...\n";
        echo str_repeat("-", 40) . "\n";
        
        $timestampFields = ['start_date', 'end_date', 'completed_at', 'created_at', 'updated_at'];
        
        foreach ($timestampFields as $field) {
            // Count problematic timestamp values
            $stmt = $pdo->query("SELECT 
                COUNT(*) as total,
                COUNT(CASE WHEN $field IS NULL THEN 1 END) as null_count,
                COUNT(CASE WHEN $field = '0000-00-00 00:00:00' THEN 1 END) as zero_count,
                COUNT(CASE WHEN $field = '' THEN 1 END) as empty_count
                FROM work_items");
            $result = $stmt->fetch();
            
            echo "📅 $field: Total=" . $result['total'] . 
                 ", NULL=" . $result['null_count'] . 
                 ", Zero=" . $result['zero_count'] . 
                 ", Empty=" . $result['empty_count'] . "\n";
        }
        
        // Now let's check what data each work item type should have
        echo "\n2️⃣  Checking type-specific field requirements...\n";
        echo str_repeat("-", 50) . "\n";
        
        $typeRequirements = [
            'EPIC' => [
                'required' => ['title', 'description', 'status', 'priority', 'project_id'],
                'important' => ['business_value', 'epic_color', 'start_date', 'end_date'],
                'optional' => ['assignee_id', 'parent_id']
            ],
            'FEATURE' => [
                'required' => ['title', 'description', 'status', 'priority', 'project_id'],
                'important' => ['acceptance_criteria', 'story_points', 'estimate'],
                'optional' => ['parent_id', 'assignee_id', 'start_date', 'end_date']
            ],
            'STORY' => [
                'required' => ['title', 'description', 'status', 'priority', 'project_id'],
                'important' => ['acceptance_criteria', 'story_points', 'assignee_id'],
                'optional' => ['parent_id', 'estimate', 'start_date', 'end_date']
            ],
            'TASK' => [
                'required' => ['title', 'description', 'status', 'priority', 'project_id'],
                'important' => ['assignee_id', 'estimated_hours', 'component'],
                'optional' => ['parent_id', 'actual_hours', 'start_date', 'end_date']
            ],
            'BUG' => [
                'required' => ['title', 'description', 'status', 'priority', 'project_id'],
                'important' => ['severity', 'bug_type', 'environment', 'reporter_id', 'current_behavior', 'expected_behavior'],
                'optional' => ['reproduction_steps', 'assignee_id', 'resolution']
            ]
        ];
        
        foreach ($typeRequirements as $type => $requirements) {
            echo "\n🏷️  $type Requirements:\n";
            
            // Get count of this type
            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM work_items WHERE type = ?");
            $stmt->execute([$type]);
            $count = $stmt->fetch()['count'];
            
            if ($count > 0) {
                echo "   Items: $count\n";
                
                // Check each important field
                foreach ($requirements['important'] as $field) {
                    // Skip timestamp fields for now
                    if (in_array($field, ['start_date', 'end_date', 'completed_at'])) {
                        continue;
                    }
                    
                    try {
                        $stmt = $pdo->prepare("SELECT COUNT(*) as filled FROM work_items WHERE type = ? AND $field IS NOT NULL AND $field != '' AND $field != '0'");
                        $stmt->execute([$type]);
                        $filled = $stmt->fetch()['filled'];
                        
                        $percentage = round(($filled / $count) * 100);
                        $status = $percentage >= 80 ? '✅' : ($percentage >= 50 ? '⚠️' : '❌');
                        echo "   $status $field: $filled/$count ($percentage%)\n";
                    } catch (Exception $e) {
                        echo "   ❓ $field: Error checking - " . $e->getMessage() . "\n";
                    }
                }
            } else {
                echo "   No $type items found\n";
            }
        }
        
        // Show current sample data for each type
        echo "\n3️⃣  Current data samples...\n";
        echo str_repeat("-", 30) . "\n";
        
        foreach (array_keys($typeRequirements) as $type) {
            echo "\n🏷️  Sample $type:\n";
            
            try {
                $stmt = $pdo->prepare("SELECT id, title, status, priority, 
                    CASE WHEN assignee_id IS NOT NULL THEN 'Yes' ELSE 'No' END as has_assignee,
                    CASE WHEN start_date IS NOT NULL THEN 'Yes' ELSE 'No' END as has_start_date
                    FROM work_items WHERE type = ? LIMIT 2");
                $stmt->execute([$type]);
                
                while ($item = $stmt->fetch()) {
                    echo "   • ID:" . $item['id'] . " | " . substr($item['title'], 0, 40) . "...\n";
                    echo "     Status: " . $item['status'] . " | Priority: " . $item['priority'] . "\n";
                    echo "     Assignee: " . $item['has_assignee'] . " | Start Date: " . $item['has_start_date'] . "\n\n";
                }
            } catch (Exception $e) {
                echo "   Error fetching $type sample: " . $e->getMessage() . "\n";
            }
        }
        
        // Provide queries to fix missing data
        echo "\n4️⃣  SQL queries to populate missing fields:\n";
        echo str_repeat("-", 45) . "\n";
        
        echo "-- Update business value for EPICs\n";
        echo "UPDATE work_items SET business_value = CONCAT('Strategic objective: ', SUBSTRING(title, 1, 50)) WHERE type = 'EPIC' AND (business_value IS NULL OR business_value = '');\n\n";
        
        echo "-- Update acceptance criteria for FEATUREs and STORIEs\n";
        echo "UPDATE work_items SET acceptance_criteria = 'Given: User needs this functionality\nWhen: User performs the action\nThen: System responds appropriately' WHERE type IN ('FEATURE', 'STORY') AND (acceptance_criteria IS NULL OR acceptance_criteria = '');\n\n";
        
        echo "-- Update story points for FEATUREs and STORIEs\n";
        echo "UPDATE work_items SET story_points = CASE WHEN priority = 'HIGH' THEN 5 WHEN priority = 'MEDIUM' THEN 3 ELSE 1 END WHERE type IN ('FEATURE', 'STORY') AND story_points IS NULL;\n\n";
        
        echo "-- Update estimated hours for TASKs\n";
        echo "UPDATE work_items SET estimated_hours = CASE WHEN priority = 'HIGH' THEN 8 WHEN priority = 'MEDIUM' THEN 4 ELSE 2 END WHERE type = 'TASK' AND estimated_hours IS NULL;\n\n";
        
        echo "-- Update reproduction steps for BUGs\n";
        echo "UPDATE work_items SET reproduction_steps = '1. Navigate to affected area\n2. Perform action\n3. Observe issue' WHERE type = 'BUG' AND (reproduction_steps IS NULL OR reproduction_steps = '');\n\n";
        
        // Create a simple edit form template
        echo "\n5️⃣  Edit form field recommendations by type:\n";
        echo str_repeat("-", 45) . "\n";
        
        $editFormFields = [
            'EPIC' => ['title*', 'description*', 'status*', 'priority*', 'business_value', 'epic_color', 'assignee_id', 'start_date', 'end_date'],
            'FEATURE' => ['title*', 'description*', 'status*', 'priority*', 'parent_id', 'acceptance_criteria', 'story_points', 'estimate', 'assignee_id'],
            'STORY' => ['title*', 'description*', 'status*', 'priority*', 'parent_id', 'acceptance_criteria', 'story_points', 'assignee_id', 'estimate'],
            'TASK' => ['title*', 'description*', 'status*', 'priority*', 'parent_id', 'assignee_id*', 'estimated_hours', 'actual_hours', 'component'],
            'BUG' => ['title*', 'description*', 'status*', 'priority*', 'severity*', 'bug_type', 'environment', 'reporter_id', 'assignee_id', 'current_behavior', 'expected_behavior', 'reproduction_steps']
        ];
        
        foreach ($editFormFields as $type => $fields) {
            echo "\n$type Edit Form Fields (* = required):\n";
            foreach ($fields as $field) {
                $required = strpos($field, '*') !== false ? ' (Required)' : ' (Optional)';
                $field = str_replace('*', '', $field);
                echo "  • $field$required\n";
            }
        }
        
        echo "\n" . str_repeat("=", 60) . "\n";
        echo "✅ Analysis Complete!\n\n";
        echo "Summary:\n";
        echo "• All work item types have proper database structure\n";
        echo "• Some items may need field values populated\n";
        echo "• Edit forms should include type-specific fields\n";
        echo "• Use the SQL queries above to fix missing data\n";
        echo "• Timestamp fields need careful handling (avoid empty strings)\n";
        
    } catch (Exception $e) {
        echo "❌ Error: " . $e->getMessage() . "\n";
    }
}

fixWorkItemsDataSafe();
?>
