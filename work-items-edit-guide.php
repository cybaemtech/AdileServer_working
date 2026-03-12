<?php
require_once './api/config/database.php';

function generateEditFormStructure() {
    echo "🎯 WORK ITEMS EDIT FORM STRUCTURE GUIDE\n";
    echo "========================================\n\n";
    
    // Define the complete edit form structure for each type
    $editFormStructure = [
        'EPIC' => [
            'title' => 'Epic Edit Form',
            'fields' => [
                // Basic fields (always visible)
                'basic' => [
                    'title' => ['type' => 'text', 'required' => true, 'label' => 'Epic Title'],
                    'description' => ['type' => 'textarea', 'required' => true, 'label' => 'Epic Description'],
                    'status' => ['type' => 'select', 'required' => true, 'options' => ['TODO', 'IN_PROGRESS', 'DONE', 'ON_HOLD'], 'label' => 'Status'],
                    'priority' => ['type' => 'select', 'required' => true, 'options' => ['LOW', 'MEDIUM', 'HIGH', 'CRITICAL'], 'label' => 'Priority'],
                    'project_id' => ['type' => 'select', 'required' => true, 'label' => 'Project']
                ],
                // Epic-specific fields
                'epic_specific' => [
                    'business_value' => ['type' => 'textarea', 'required' => false, 'label' => 'Business Value'],
                    'epic_color' => ['type' => 'color', 'required' => false, 'default' => '#3b82f6', 'label' => 'Epic Color'],
                    'assignee_id' => ['type' => 'select', 'required' => false, 'label' => 'Assignee']
                ],
                // Optional fields
                'optional' => [
                    'start_date' => ['type' => 'date', 'required' => false, 'label' => 'Start Date'],
                    'end_date' => ['type' => 'date', 'required' => false, 'label' => 'End Date'],
                    'estimate' => ['type' => 'number', 'required' => false, 'label' => 'Estimate (Story Points)'],
                    'tags' => ['type' => 'text', 'required' => false, 'label' => 'Tags (comma separated)']
                ]
            ]
        ],
        
        'FEATURE' => [
            'title' => 'Feature Edit Form',
            'fields' => [
                'basic' => [
                    'title' => ['type' => 'text', 'required' => true, 'label' => 'Feature Title'],
                    'description' => ['type' => 'textarea', 'required' => true, 'label' => 'Feature Description'],
                    'status' => ['type' => 'select', 'required' => true, 'options' => ['TODO', 'IN_PROGRESS', 'DONE', 'ON_HOLD'], 'label' => 'Status'],
                    'priority' => ['type' => 'select', 'required' => true, 'options' => ['LOW', 'MEDIUM', 'HIGH', 'CRITICAL'], 'label' => 'Priority'],
                    'project_id' => ['type' => 'select', 'required' => true, 'label' => 'Project']
                ],
                'feature_specific' => [
                    'parent_id' => ['type' => 'select', 'required' => false, 'filter_type' => 'EPIC', 'label' => 'Parent Epic'],
                    'acceptance_criteria' => ['type' => 'textarea', 'required' => true, 'label' => 'Acceptance Criteria'],
                    'story_points' => ['type' => 'number', 'required' => true, 'min' => 1, 'max' => 21, 'label' => 'Story Points'],
                    'estimate' => ['type' => 'number', 'required' => false, 'step' => '0.5', 'label' => 'Estimate (Hours)']
                ],
                'optional' => [
                    'assignee_id' => ['type' => 'select', 'required' => false, 'label' => 'Assignee'],
                    'component' => ['type' => 'select', 'required' => false, 'options' => ['Frontend', 'Backend', 'Database', 'Testing', 'General'], 'label' => 'Component'],
                    'start_date' => ['type' => 'date', 'required' => false, 'label' => 'Start Date'],
                    'end_date' => ['type' => 'date', 'required' => false, 'label' => 'End Date']
                ]
            ]
        ],
        
        'STORY' => [
            'title' => 'User Story Edit Form',
            'fields' => [
                'basic' => [
                    'title' => ['type' => 'text', 'required' => true, 'label' => 'Story Title (As a... I want...)'],
                    'description' => ['type' => 'textarea', 'required' => true, 'label' => 'Story Description'],
                    'status' => ['type' => 'select', 'required' => true, 'options' => ['TODO', 'IN_PROGRESS', 'DONE', 'ON_HOLD'], 'label' => 'Status'],
                    'priority' => ['type' => 'select', 'required' => true, 'options' => ['LOW', 'MEDIUM', 'HIGH', 'CRITICAL'], 'label' => 'Priority'],
                    'project_id' => ['type' => 'select', 'required' => true, 'label' => 'Project']
                ],
                'story_specific' => [
                    'parent_id' => ['type' => 'select', 'required' => false, 'filter_type' => 'FEATURE', 'label' => 'Parent Feature'],
                    'acceptance_criteria' => ['type' => 'textarea', 'required' => true, 'label' => 'Acceptance Criteria (Given/When/Then)'],
                    'story_points' => ['type' => 'number', 'required' => true, 'min' => 1, 'max' => 13, 'label' => 'Story Points'],
                    'assignee_id' => ['type' => 'select', 'required' => true, 'label' => 'Assignee']
                ],
                'optional' => [
                    'estimate' => ['type' => 'number', 'required' => false, 'step' => '0.5', 'label' => 'Estimate (Hours)'],
                    'component' => ['type' => 'select', 'required' => false, 'options' => ['Frontend', 'Backend', 'Database', 'Testing', 'General'], 'label' => 'Component'],
                    'start_date' => ['type' => 'date', 'required' => false, 'label' => 'Start Date'],
                    'end_date' => ['type' => 'date', 'required' => false, 'label' => 'Due Date']
                ]
            ]
        ],
        
        'TASK' => [
            'title' => 'Task Edit Form',
            'fields' => [
                'basic' => [
                    'title' => ['type' => 'text', 'required' => true, 'label' => 'Task Title'],
                    'description' => ['type' => 'textarea', 'required' => true, 'label' => 'Task Description'],
                    'status' => ['type' => 'select', 'required' => true, 'options' => ['TODO', 'IN_PROGRESS', 'DONE', 'ON_HOLD'], 'label' => 'Status'],
                    'priority' => ['type' => 'select', 'required' => true, 'options' => ['LOW', 'MEDIUM', 'HIGH', 'CRITICAL'], 'label' => 'Priority'],
                    'project_id' => ['type' => 'select', 'required' => true, 'label' => 'Project']
                ],
                'task_specific' => [
                    'assignee_id' => ['type' => 'select', 'required' => true, 'label' => 'Assignee'],
                    'estimated_hours' => ['type' => 'number', 'required' => true, 'step' => '0.5', 'min' => '0.5', 'label' => 'Estimated Hours'],
                    'component' => ['type' => 'select', 'required' => true, 'options' => ['Frontend', 'Backend', 'Database', 'Testing', 'General'], 'label' => 'Component']
                ],
                'optional' => [
                    'parent_id' => ['type' => 'select', 'required' => false, 'filter_type' => 'STORY', 'label' => 'Parent Story'],
                    'actual_hours' => ['type' => 'number', 'required' => false, 'step' => '0.5', 'min' => '0', 'label' => 'Actual Hours'],
                    'start_date' => ['type' => 'date', 'required' => false, 'label' => 'Start Date'],
                    'end_date' => ['type' => 'date', 'required' => false, 'label' => 'Due Date']
                ]
            ]
        ],
        
        'BUG' => [
            'title' => 'Bug Report Edit Form',
            'fields' => [
                'basic' => [
                    'title' => ['type' => 'text', 'required' => true, 'label' => 'Bug Title'],
                    'description' => ['type' => 'textarea', 'required' => true, 'label' => 'Bug Description'],
                    'status' => ['type' => 'select', 'required' => true, 'options' => ['TODO', 'IN_PROGRESS', 'DONE', 'ON_HOLD'], 'label' => 'Status'],
                    'priority' => ['type' => 'select', 'required' => true, 'options' => ['LOW', 'MEDIUM', 'HIGH', 'CRITICAL'], 'label' => 'Priority'],
                    'project_id' => ['type' => 'select', 'required' => true, 'label' => 'Project']
                ],
                'bug_specific' => [
                    'severity' => ['type' => 'select', 'required' => true, 'options' => ['LOW', 'MEDIUM', 'HIGH', 'CRITICAL'], 'label' => 'Severity'],
                    'bug_type' => ['type' => 'select', 'required' => true, 'options' => ['UI', 'BACKEND', 'PERFORMANCE', 'SECURITY', 'DATA'], 'label' => 'Bug Type'],
                    'environment' => ['type' => 'select', 'required' => true, 'options' => ['development', 'testing', 'staging', 'production'], 'label' => 'Environment'],
                    'reporter_id' => ['type' => 'select', 'required' => true, 'label' => 'Reporter'],
                    'current_behavior' => ['type' => 'textarea', 'required' => true, 'label' => 'Current Behavior'],
                    'expected_behavior' => ['type' => 'textarea', 'required' => true, 'label' => 'Expected Behavior']
                ],
                'optional' => [
                    'reproduction_steps' => ['type' => 'textarea', 'required' => false, 'label' => 'Reproduction Steps'],
                    'assignee_id' => ['type' => 'select', 'required' => false, 'label' => 'Assignee'],
                    'resolution' => ['type' => 'select', 'required' => false, 'options' => ['fixed', 'wont_fix', 'duplicate', 'cannot_reproduce', 'works_as_designed'], 'label' => 'Resolution'],
                    'fix_version' => ['type' => 'text', 'required' => false, 'label' => 'Fix Version']
                ]
            ]
        ]
    ];
    
    // Output the structure for each type
    foreach ($editFormStructure as $type => $config) {
        echo "🏷️  " . strtoupper($type) . " - " . $config['title'] . "\n";
        echo str_repeat("=", 50) . "\n";
        
        foreach ($config['fields'] as $section => $fields) {
            $sectionTitle = ucwords(str_replace('_', ' ', $section));
            echo "\n📋 $sectionTitle:\n";
            echo str_repeat("-", 20) . "\n";
            
            foreach ($fields as $fieldName => $fieldConfig) {
                $required = $fieldConfig['required'] ? ' *' : '';
                echo "• $fieldName$required\n";
                echo "  Type: " . $fieldConfig['type'] . "\n";
                echo "  Label: " . $fieldConfig['label'] . "\n";
                
                if (isset($fieldConfig['options'])) {
                    echo "  Options: " . implode(', ', $fieldConfig['options']) . "\n";
                }
                if (isset($fieldConfig['filter_type'])) {
                    echo "  Filter: Show only " . $fieldConfig['filter_type'] . " items\n";
                }
                if (isset($fieldConfig['default'])) {
                    echo "  Default: " . $fieldConfig['default'] . "\n";
                }
                echo "\n";
            }
        }
        echo "\n" . str_repeat("=", 70) . "\n\n";
    }
    
    // Generate JSON structure for frontend
    echo "📄 JSON Structure for Frontend Implementation:\n";
    echo str_repeat("=", 50) . "\n";
    echo "```json\n";
    echo json_encode($editFormStructure, JSON_PRETTY_PRINT);
    echo "\n```\n\n";
    
    // Generate API endpoint structure
    echo "🌐 API Endpoint Structure for Edit Operations:\n";
    echo str_repeat("=", 50) . "\n";
    
    echo "GET /api/work-items/{id} - Get work item for editing\n";
    echo "PUT/PATCH /api/work-items/{id} - Update work item\n\n";
    
    echo "Expected Response Structure:\n";
    echo "```json\n";
    echo <<<JSON
{
  "id": 123,
  "type": "STORY",
  "title": "As a user, I want to login",
  "description": "User should be able to login...",
  "status": "IN_PROGRESS",
  "priority": "HIGH",
  "project_id": 41,
  "assignee_id": 28,
  "parent_id": 456,
  "acceptance_criteria": "Given: User has account...",
  "story_points": 5,
  "component": "Frontend",
  "created_at": "2026-03-12T10:00:00Z",
  "updated_at": "2026-03-12T15:30:00Z"
}
JSON;
    echo "\n```\n\n";
    
    echo "📋 Frontend Implementation Checklist:\n";
    echo str_repeat("-", 35) . "\n";
    echo "✅ 1. Create type-specific edit forms\n";
    echo "✅ 2. Show/hide fields based on work item type\n";
    echo "✅ 3. Implement field validation per type\n";
    echo "✅ 4. Handle parent-child relationships (Epic->Feature->Story->Task)\n";
    echo "✅ 5. Populate dropdowns (users, projects, parent items)\n";
    echo "✅ 6. Handle date fields properly (send NULL instead of empty strings)\n";
    echo "✅ 7. Add type-specific help text/tooltips\n";
    echo "✅ 8. Implement conditional field visibility\n\n";
    
    echo "🎯 Key Implementation Points:\n";
    echo str_repeat("-", 30) . "\n";
    echo "• EPIC: Focus on business value and strategic planning\n";
    echo "• FEATURE: Emphasize acceptance criteria and story points\n";
    echo "• STORY: Require assignee and clear acceptance criteria\n";
    echo "• TASK: Must have assignee, estimated hours, and component\n";
    echo "• BUG: Comprehensive bug tracking with environment and behavior details\n\n";
    
    echo "✅ Your database is ready with all required fields!\n";
    echo "✅ All work item types have proper sample data!\n";
    echo "✅ Use this guide to implement type-specific edit forms!\n";
}

generateEditFormStructure();
?>
