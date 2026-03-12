<?php
require_once './api/config/database.php';

function createWorkItemEditQueries() {
    $database = new Database();
    $pdo = $database->getConnection();
    
    if ($pdo === null) {
        echo "❌ Database connection failed\n";
        return;
    }
    
    echo "🔧 Work Items Edit Form Field Fixes\n";
    echo "====================================\n\n";
    
    try {
        // First, let's see what work items we currently have
        echo "1️⃣  Current Work Items Overview:\n";
        echo str_repeat("-", 35) . "\n";
        
        $stmt = $pdo->query("SELECT type, COUNT(*) as count FROM work_items GROUP BY type ORDER BY count DESC");
        while ($row = $stmt->fetch()) {
            echo "🏷️  " . $row['type'] . ": " . $row['count'] . " items\n";
        }
        
        echo "\n2️⃣  Sample Data Check (first item of each type):\n";
        echo str_repeat("-", 50) . "\n";
        
        $types = ['EPIC', 'FEATURE', 'STORY', 'TASK', 'BUG'];
        
        foreach ($types as $type) {
            echo "\n📋 $type Sample:\n";
            
            $stmt = $pdo->prepare("SELECT id, title, status, priority FROM work_items WHERE type = ? LIMIT 1");
            $stmt->execute([$type]);
            $item = $stmt->fetch();
            
            if ($item) {
                echo "   ID: " . $item['id'] . "\n";
                echo "   Title: " . $item['title'] . "\n";
                echo "   Status: " . $item['status'] . "\n";
                echo "   Priority: " . $item['priority'] . "\n";
                
                // Check type-specific fields
                if ($type === 'EPIC') {
                    $stmt = $pdo->prepare("SELECT business_value, epic_color FROM work_items WHERE id = ?");
                    $stmt->execute([$item['id']]);
                    $extra = $stmt->fetch();
                    echo "   Business Value: " . ($extra['business_value'] ?? 'NULL') . "\n";
                    echo "   Epic Color: " . ($extra['epic_color'] ?? 'NULL') . "\n";
                }
                
                if ($type === 'FEATURE' || $type === 'STORY') {
                    $stmt = $pdo->prepare("SELECT acceptance_criteria, story_points FROM work_items WHERE id = ?");
                    $stmt->execute([$item['id']]);
                    $extra = $stmt->fetch();
                    echo "   Acceptance Criteria: " . (isset($extra['acceptance_criteria']) ? 'SET' : 'NULL') . "\n";
                    echo "   Story Points: " . ($extra['story_points'] ?? 'NULL') . "\n";
                }
                
                if ($type === 'TASK') {
                    $stmt = $pdo->prepare("SELECT estimated_hours, actual_hours, component FROM work_items WHERE id = ?");
                    $stmt->execute([$item['id']]);
                    $extra = $stmt->fetch();
                    echo "   Estimated Hours: " . ($extra['estimated_hours'] ?? 'NULL') . "\n";
                    echo "   Actual Hours: " . ($extra['actual_hours'] ?? 'NULL') . "\n";
                    echo "   Component: " . ($extra['component'] ?? 'NULL') . "\n";
                }
                
                if ($type === 'BUG') {
                    $stmt = $pdo->prepare("SELECT severity, bug_type, environment FROM work_items WHERE id = ?");
                    $stmt->execute([$item['id']]);
                    $extra = $stmt->fetch();
                    echo "   Severity: " . ($extra['severity'] ?? 'NULL') . "\n";
                    echo "   Bug Type: " . ($extra['bug_type'] ?? 'NULL') . "\n";
                    echo "   Environment: " . ($extra['environment'] ?? 'NULL') . "\n";
                }
            } else {
                echo "   No $type items found\n";
            }
        }
        
        echo "\n3️⃣  Safe SQL Updates for Missing Data:\n";
        echo str_repeat("-", 40) . "\n";
        
        echo "-- Safe updates that won't cause timestamp errors:\n\n";
        
        // Generate safe update queries
        $safeQueries = [
            "-- Populate business_value for EPICs" => 
            "UPDATE work_items SET business_value = CONCAT('Strategic initiative: ', title) WHERE type = 'EPIC' AND business_value IS NULL;",
            
            "-- Populate acceptance_criteria for FEATUREs" =>
            "UPDATE work_items SET acceptance_criteria = CONCAT('Feature Requirements:\n- Implement ', title, '\n- Ensure user-friendly interface\n- Pass all acceptance tests') WHERE type = 'FEATURE' AND acceptance_criteria IS NULL;",
            
            "-- Populate acceptance_criteria for STORIEs" =>
            "UPDATE work_items SET acceptance_criteria = CONCAT('User Story Criteria:\n- As a user, I want ', LOWER(SUBSTRING(title, 15)), '\n- System should respond appropriately\n- All edge cases handled') WHERE type = 'STORY' AND acceptance_criteria IS NULL;",
            
            "-- Set story_points for FEATUREs and STORIEs" =>
            "UPDATE work_items SET story_points = CASE WHEN priority = 'HIGH' THEN 8 WHEN priority = 'MEDIUM' THEN 5 WHEN priority = 'LOW' THEN 3 ELSE 2 END WHERE type IN ('FEATURE', 'STORY') AND story_points IS NULL;",
            
            "-- Set estimated_hours for TASKs" =>
            "UPDATE work_items SET estimated_hours = CASE WHEN priority = 'HIGH' THEN 8 WHEN priority = 'MEDIUM' THEN 4 WHEN priority = 'LOW' THEN 2 ELSE 1 END WHERE type = 'TASK' AND estimated_hours IS NULL;",
            
            "-- Populate reproduction_steps for BUGs" =>
            "UPDATE work_items SET reproduction_steps = CONCAT('Reproduction Steps:\n1. Navigate to the affected feature\n2. Perform the action described in: ', title, '\n3. Observe the unexpected behavior\n\nExpected: System should work correctly') WHERE type = 'BUG' AND reproduction_steps IS NULL;",
            
            "-- Set component for all items if missing" =>
            "UPDATE work_items SET component = CASE WHEN title LIKE '%UI%' OR title LIKE '%form%' OR title LIKE '%frontend%' THEN 'Frontend' WHEN title LIKE '%API%' OR title LIKE '%backend%' OR title LIKE '%service%' THEN 'Backend' WHEN title LIKE '%database%' OR title LIKE '%DB%' THEN 'Database' WHEN title LIKE '%test%' THEN 'Testing' ELSE 'General' END WHERE component IS NULL;"
        ];
        
        foreach ($safeQueries as $description => $query) {
            echo "$description\n";
            echo "$query\n\n";
        }
        
        echo "4️⃣  Edit Form Field Mapping:\n";
        echo str_repeat("-", 30) . "\n";
        
        $editFormMapping = [
            'EPIC' => [
                'Basic Fields' => ['title', 'description', 'status', 'priority', 'project_id'],
                'Epic Specific' => ['business_value', 'epic_color', 'assignee_id'],
                'Optional' => ['tags', 'estimate', 'parent_id']
            ],
            'FEATURE' => [
                'Basic Fields' => ['title', 'description', 'status', 'priority', 'project_id'],
                'Feature Specific' => ['acceptance_criteria', 'story_points', 'parent_id'],
                'Optional' => ['assignee_id', 'estimate', 'component']
            ],
            'STORY' => [
                'Basic Fields' => ['title', 'description', 'status', 'priority', 'project_id'],
                'Story Specific' => ['acceptance_criteria', 'story_points', 'assignee_id', 'parent_id'],
                'Optional' => ['estimate', 'component', 'dependencies']
            ],
            'TASK' => [
                'Basic Fields' => ['title', 'description', 'status', 'priority', 'project_id'],
                'Task Specific' => ['assignee_id', 'estimated_hours', 'component'],
                'Optional' => ['actual_hours', 'parent_id', 'dependencies']
            ],
            'BUG' => [
                'Basic Fields' => ['title', 'description', 'status', 'priority', 'project_id'],
                'Bug Specific' => ['severity', 'bug_type', 'environment', 'reporter_id', 'current_behavior', 'expected_behavior'],
                'Optional' => ['reproduction_steps', 'assignee_id', 'resolution', 'fix_version']
            ]
        ];
        
        foreach ($editFormMapping as $type => $fieldGroups) {
            echo "\n🏷️  $type Edit Form Fields:\n";
            foreach ($fieldGroups as $groupName => $fields) {
                echo "   $groupName:\n";
                foreach ($fields as $field) {
                    echo "     • $field\n";
                }
            }
            echo "\n";
        }
        
        echo "\n5️⃣  Recommended Action Plan:\n";
        echo str_repeat("-", 30) . "\n";
        
        echo "✅ Step 1: Run the safe SQL updates above\n";
        echo "✅ Step 2: Update your edit forms to include type-specific fields\n";
        echo "✅ Step 3: Add validation for required fields per type\n";
        echo "✅ Step 4: Test edit functionality with each work item type\n";
        echo "✅ Step 5: Handle date fields carefully (use NULL instead of empty strings)\n\n";
        
        echo "🎯 Key Points for Edit Forms:\n";
        echo "• EPIC items need: business_value, epic_color\n";
        echo "• FEATURE items need: acceptance_criteria, story_points, parent_id\n";
        echo "• STORY items need: acceptance_criteria, story_points, assignee_id, parent_id\n";
        echo "• TASK items need: assignee_id, estimated_hours, component\n";
        echo "• BUG items need: severity, bug_type, environment, reporter_id, current_behavior, expected_behavior\n";
        
    } catch (Exception $e) {
        echo "❌ Error: " . $e->getMessage() . "\n";
    }
}

createWorkItemEditQueries();
?>
