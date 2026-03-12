<?php
require_once './api/config/database.php';

function checkWorkItemFieldsByType() {
    $database = new Database();
    $pdo = $database->getConnection();
    
    if ($pdo === null) {
        echo "❌ Database connection failed\n";
        return;
    }
    
    echo "🔍 Work Item Type-Specific Fields Analysis\n";
    echo "===========================================\n\n";
    
    try {
        // Get all columns from work_items table
        $result = $pdo->query("SHOW COLUMNS FROM work_items");
        $allColumns = [];
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $allColumns[] = $row['Field'];
        }
        
        // Define required fields for each work item type
        $typeRequiredFields = [
            'EPIC' => [
                'basic' => ['title', 'description', 'status', 'priority', 'project_id'],
                'specific' => ['start_date', 'end_date', 'epic_color', 'business_value'],
                'optional' => ['parent_id', 'assignee_id', 'tags', 'estimate']
            ],
            'FEATURE' => [
                'basic' => ['title', 'description', 'status', 'priority', 'project_id'],
                'specific' => ['parent_id', 'estimate', 'acceptance_criteria', 'story_points'],
                'optional' => ['assignee_id', 'start_date', 'end_date', 'component']
            ],
            'STORY' => [
                'basic' => ['title', 'description', 'status', 'priority', 'project_id'],
                'specific' => ['parent_id', 'assignee_id', 'estimate', 'story_points', 'acceptance_criteria'],
                'optional' => ['start_date', 'end_date', 'component', 'dependencies']
            ],
            'TASK' => [
                'basic' => ['title', 'description', 'status', 'priority', 'project_id'],
                'specific' => ['assignee_id', 'estimated_hours', 'actual_hours', 'component'],
                'optional' => ['parent_id', 'start_date', 'end_date', 'dependencies']
            ],
            'BUG' => [
                'basic' => ['title', 'description', 'status', 'priority', 'project_id'],
                'specific' => ['severity', 'bug_type', 'environment', 'reporter_id', 'current_behavior', 'expected_behavior'],
                'optional' => ['reproduction_steps', 'assignee_id', 'resolution', 'fix_version']
            ]
        ];
        
        // Check each work item type
        foreach ($typeRequiredFields as $type => $fieldGroups) {
            echo "📋 $type Work Item Fields:\n";
            echo str_repeat("-", 35) . "\n";
            
            $missingFields = [];
            $presentFields = [];
            
            foreach ($fieldGroups as $group => $fields) {
                echo "  $group fields:\n";
                foreach ($fields as $field) {
                    if (in_array($field, $allColumns)) {
                        echo "    ✅ $field - EXISTS\n";
                        $presentFields[] = $field;
                    } else {
                        echo "    ❌ $field - MISSING\n";
                        $missingFields[] = $field;
                    }
                }
                echo "\n";
            }
            
            // Check actual data for this type
            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM work_items WHERE type = ?");
            $stmt->execute([$type]);
            $count = $stmt->fetch()['count'];
            
            if ($count > 0) {
                echo "  📊 Sample $type data:\n";
                
                // Get a sample record to see what fields have data
                $sampleFields = implode(', ', array_slice($presentFields, 0, 10));
                if ($sampleFields) {
                    $stmt = $pdo->prepare("SELECT $sampleFields FROM work_items WHERE type = ? LIMIT 1");
                    $stmt->execute([$type]);
                    $sample = $stmt->fetch();
                    
                    if ($sample) {
                        foreach ($sample as $field => $value) {
                            $displayValue = $value ?? 'NULL';
                            if (strlen($displayValue) > 50) {
                                $displayValue = substr($displayValue, 0, 47) . '...';
                            }
                            echo "    • $field: $displayValue\n";
                        }
                    }
                }
            } else {
                echo "  ⚠️  No $type items found in database\n";
            }
            
            echo "\n" . str_repeat("=", 50) . "\n\n";
        }
        
        // Generate CREATE/UPDATE queries for missing fields
        if (!empty($missingFields)) {
            echo "🔧 SQL Queries to Add Missing Fields:\n";
            echo str_repeat("-", 40) . "\n";
            
            $uniqueMissingFields = array_unique($missingFields);
            foreach ($uniqueMissingFields as $field) {
                $query = generateAddColumnQuery($field);
                if ($query) {
                    echo "$query;\n";
                }
            }
        }
        
        // Show data completeness for each type
        echo "\n📊 Data Completeness Analysis:\n";
        echo str_repeat("-", 35) . "\n";
        
        foreach (array_keys($typeRequiredFields) as $type) {
            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM work_items WHERE type = ?");
            $stmt->execute([$type]);
            $count = $stmt->fetch()['count'];
            
            if ($count > 0) {
                echo "\n$type Items ($count total):\n";
                
                // Check key fields for this type
                $keyFields = ['title', 'description', 'assignee_id', 'start_date', 'end_date'];
                if ($type === 'STORY' || $type === 'FEATURE') {
                    $keyFields[] = 'story_points';
                    $keyFields[] = 'acceptance_criteria';
                }
                if ($type === 'BUG') {
                    $keyFields = array_merge($keyFields, ['severity', 'environment']);
                }
                
                foreach ($keyFields as $field) {
                    if (in_array($field, $allColumns)) {
                        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM work_items WHERE type = ? AND $field IS NOT NULL AND $field != ''");
                        $stmt->execute([$type]);
                        $filledCount = $stmt->fetch()['count'];
                        $percentage = round(($filledCount / $count) * 100, 1);
                        
                        $status = $percentage >= 80 ? '✅' : ($percentage >= 50 ? '⚠️' : '❌');
                        echo "  $status $field: $filledCount/$count ($percentage%)\n";
                    }
                }
            }
        }
        
    } catch (Exception $e) {
        echo "❌ Error: " . $e->getMessage() . "\n";
    }
}

function generateAddColumnQuery($fieldName) {
    $columnDefinitions = [
        'epic_color' => "ALTER TABLE work_items ADD COLUMN epic_color VARCHAR(7) DEFAULT '#3b82f6' COMMENT 'Color for epic visualization'",
        'business_value' => "ALTER TABLE work_items ADD COLUMN business_value TEXT COMMENT 'Business value description'",
        'acceptance_criteria' => "ALTER TABLE work_items ADD COLUMN acceptance_criteria TEXT COMMENT 'Acceptance criteria for stories/features'",
        'story_points' => "ALTER TABLE work_items ADD COLUMN story_points INT DEFAULT NULL COMMENT 'Story points for estimation'",
        'component' => "ALTER TABLE work_items ADD COLUMN component VARCHAR(100) COMMENT 'System component'",
        'dependencies' => "ALTER TABLE work_items ADD COLUMN dependencies JSON COMMENT 'Dependent item IDs'",
        'environment' => "ALTER TABLE work_items ADD COLUMN environment ENUM('development', 'testing', 'staging', 'production') COMMENT 'Bug environment'",
        'reproduction_steps' => "ALTER TABLE work_items ADD COLUMN reproduction_steps TEXT COMMENT 'Bug reproduction steps'",
        'resolution' => "ALTER TABLE work_items ADD COLUMN resolution ENUM('fixed', 'wont_fix', 'duplicate', 'cannot_reproduce') COMMENT 'Bug resolution'",
        'fix_version' => "ALTER TABLE work_items ADD COLUMN fix_version VARCHAR(50) COMMENT 'Version where bug was fixed'"
    ];
    
    return $columnDefinitions[$fieldName] ?? null;
}

checkWorkItemFieldsByType();
?>
