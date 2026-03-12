<?php
require_once './api/config/database.php';

function checkWorkItemsStructure() {
    $database = new Database();
    $pdo = $database->getConnection();
    
    if ($pdo === null) {
        echo "❌ Database connection failed\n";
        return;
    }
    
    echo "🔍 Work Items Data Analysis\n";
    echo "===========================\n\n";
    
    try {
        // Check table structure first
        echo "📋 Current Table Structure:\n";
        echo str_repeat("-", 50) . "\n";
        
        $result = $pdo->query("SHOW COLUMNS FROM work_items");
        $columns = [];
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $columns[] = $row['Field'];
            printf("%-25s %-30s %-8s\n", $row['Field'], $row['Type'], $row['Null']);
        }
        
        // Check data distribution by type
        echo "\n📊 Data Distribution by Type:\n";
        echo str_repeat("-", 40) . "\n";
        
        $stmt = $pdo->query("SELECT type, COUNT(*) as count FROM work_items GROUP BY type ORDER BY count DESC");
        $totalItems = 0;
        while ($row = $stmt->fetch()) {
            echo "• " . $row['type'] . ": " . $row['count'] . " items\n";
            $totalItems += $row['count'];
        }
        echo "\nTotal Work Items: $totalItems\n";
        
        // Check data distribution by status
        echo "\n📊 Data Distribution by Status:\n";
        echo str_repeat("-", 40) . "\n";
        
        $stmt = $pdo->query("SELECT status, COUNT(*) as count FROM work_items GROUP BY status ORDER BY count DESC");
        while ($row = $stmt->fetch()) {
            echo "• " . $row['status'] . ": " . $row['count'] . " items\n";
        }
        
        // Check data distribution by priority
        echo "\n📊 Data Distribution by Priority:\n";
        echo str_repeat("-", 40) . "\n";
        
        $stmt = $pdo->query("SELECT priority, COUNT(*) as count FROM work_items GROUP BY priority ORDER BY count DESC");
        while ($row = $stmt->fetch()) {
            echo "• " . $row['priority'] . ": " . $row['count'] . " items\n";
        }
        
        // Show sample data for each type
        echo "\n📝 Sample Data for Each Type:\n";
        echo str_repeat("-", 50) . "\n";
        
        $types = ['EPIC', 'FEATURE', 'STORY', 'TASK', 'BUG'];
        foreach ($types as $type) {
            echo "\n🏷️  $type Items:\n";
            $stmt = $pdo->prepare("SELECT id, title, status, priority, description FROM work_items WHERE type = ? LIMIT 3");
            $stmt->execute([$type]);
            
            if ($stmt->rowCount() > 0) {
                while ($item = $stmt->fetch()) {
                    echo "   • ID:" . $item['id'] . " | " . $item['title'] . " (" . $item['status'] . ", " . $item['priority'] . ")\n";
                    if ($item['description']) {
                        echo "     Description: " . substr($item['description'], 0, 80) . "...\n";
                    }
                }
            } else {
                echo "   ❌ No $type items found\n";
            }
        }
        
        // Check for missing essential columns for different work item types
        echo "\n🔍 Checking Essential Columns for Different Work Item Types:\n";
        echo str_repeat("-", 60) . "\n";
        
        $essentialColumns = [
            'general' => ['id', 'title', 'description', 'type', 'status', 'priority', 'project_id'],
            'epic' => ['parent_id', 'start_date', 'end_date'],
            'feature' => ['parent_id', 'estimate'],
            'story' => ['parent_id', 'estimate', 'assignee_id'],
            'task' => ['assignee_id', 'estimate', 'estimated_hours', 'actual_hours'],
            'bug' => ['severity', 'bug_type', 'current_behavior', 'expected_behavior', 'reporter_id']
        ];
        
        foreach ($essentialColumns as $category => $columnList) {
            echo "\n📋 $category columns:\n";
            foreach ($columnList as $col) {
                if (in_array($col, $columns)) {
                    echo "   ✅ $col - EXISTS\n";
                } else {
                    echo "   ❌ $col - MISSING\n";
                }
            }
        }
        
        // Check for NULL data in important fields
        echo "\n🔍 Data Quality Check:\n";
        echo str_repeat("-", 30) . "\n";
        
        $qualityChecks = [
            'Items without title' => "SELECT COUNT(*) as count FROM work_items WHERE title IS NULL OR title = ''",
            'Items without description' => "SELECT COUNT(*) as count FROM work_items WHERE description IS NULL OR description = ''",
            'Items without project' => "SELECT COUNT(*) as count FROM work_items WHERE project_id IS NULL",
            'Items without assignee' => "SELECT COUNT(*) as count FROM work_items WHERE assignee_id IS NULL",
            'BUG items without severity' => "SELECT COUNT(*) as count FROM work_items WHERE type = 'BUG' AND severity IS NULL",
            'TASK items without estimate' => "SELECT COUNT(*) as count FROM work_items WHERE type = 'TASK' AND estimated_hours IS NULL"
        ];
        
        foreach ($qualityChecks as $check => $query) {
            $stmt = $pdo->query($query);
            $result = $stmt->fetch();
            $icon = $result['count'] > 0 ? '⚠️' : '✅';
            echo "$icon $check: " . $result['count'] . "\n";
        }
        
    } catch (Exception $e) {
        echo "❌ Error: " . $e->getMessage() . "\n";
    }
}

checkWorkItemsStructure();
?>
