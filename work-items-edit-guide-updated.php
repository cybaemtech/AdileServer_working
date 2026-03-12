<?php
require_once './api/config/database.php';

echo "📝 Updated Work Items Edit Form Guide with New Fields\n";
echo "=====================================================\n\n";

try {
    $database = new Database();
    $pdo = $database->getConnection();
    
    if ($pdo === null) {
        echo "❌ Database connection failed\n";
        return;
    }
    
    // Define complete edit form structure with new fields
    $edit_form_config = [
        'EPIC' => [
            'basic_fields' => [
                'title' => ['type' => 'text', 'required' => true, 'label' => 'Epic Title'],
                'description' => ['type' => 'textarea', 'required' => true, 'label' => 'Epic Description'],
                'status' => ['type' => 'select', 'options' => ['TODO', 'IN_PROGRESS', 'DONE', 'ON_HOLD'], 'label' => 'Status'],
                'priority' => ['type' => 'select', 'options' => ['LOW', 'MEDIUM', 'HIGH', 'CRITICAL'], 'label' => 'Priority']
            ],
            'epic_specific' => [
                'business_value' => ['type' => 'textarea', 'label' => 'Business Value'],
                'epic_color' => ['type' => 'color', 'label' => 'Epic Color'],
                'start_date' => ['type' => 'date', 'label' => 'Start Date'],
                'end_date' => ['type' => 'date', 'label' => 'End Date']
            ],
            'prototype_design' => [
                'prototype_link' => ['type' => 'url', 'label' => 'Prototype Link'],
                'prototype_status' => ['type' => 'select', 'options' => ['not_started', 'in_progress', 'completed', 'approved'], 'label' => 'Prototype Status'],
                'mockup_link' => ['type' => 'url', 'label' => 'Design Mockups Link'],
                'drag_drop_enabled' => ['type' => 'checkbox', 'label' => 'Enable Drag & Drop']
            ],
            'file_uploads' => [
                'pdf_upload' => ['type' => 'file', 'accept' => '.pdf', 'label' => 'Upload Requirements PDF'],
                'screenshot_upload' => ['type' => 'file', 'accept' => 'image/*', 'label' => 'Upload Screenshot']
            ],
            'tracking' => [
                'assignee_id' => ['type' => 'user_select', 'label' => 'Assignee'],
                'tags' => ['type' => 'tags', 'label' => 'Tags'],
                'dependencies' => ['type' => 'json', 'label' => 'Dependencies']
            ]
        ],
        'FEATURE' => [
            'basic_fields' => [
                'title' => ['type' => 'text', 'required' => true, 'label' => 'Feature Title'],
                'description' => ['type' => 'textarea', 'required' => true, 'label' => 'Feature Description'],
                'status' => ['type' => 'select', 'options' => ['TODO', 'IN_PROGRESS', 'DONE', 'ON_HOLD'], 'label' => 'Status'],
                'priority' => ['type' => 'select', 'options' => ['LOW', 'MEDIUM', 'HIGH', 'CRITICAL'], 'label' => 'Priority'],
                'parent_id' => ['type' => 'epic_select', 'label' => 'Parent Epic']
            ],
            'feature_specific' => [
                'business_value' => ['type' => 'textarea', 'label' => 'Business Value'],
                'acceptance_criteria' => ['type' => 'textarea', 'label' => 'Acceptance Criteria'],
                'estimate' => ['type' => 'number', 'step' => '0.5', 'label' => 'Estimate (hours)']
            ],
            'prototype_design' => [
                'prototype_link' => ['type' => 'url', 'label' => 'Prototype Link'],
                'prototype_status' => ['type' => 'select', 'options' => ['not_started', 'in_progress', 'completed', 'approved'], 'label' => 'Prototype Status'],
                'mockup_link' => ['type' => 'url', 'label' => 'Design Mockups Link'],
                'drag_drop_enabled' => ['type' => 'checkbox', 'label' => 'Enable Drag & Drop']
            ],
            'file_uploads' => [
                'pdf_upload' => ['type' => 'file', 'accept' => '.pdf', 'label' => 'Upload Specifications PDF'],
                'screenshot_upload' => ['type' => 'file', 'accept' => 'image/*', 'label' => 'Upload Screenshot']
            ],
            'tracking' => [
                'assignee_id' => ['type' => 'user_select', 'label' => 'Assignee'],
                'tags' => ['type' => 'tags', 'label' => 'Tags'],
                'dependencies' => ['type' => 'json', 'label' => 'Dependencies']
            ]
        ],
        'STORY' => [
            'basic_fields' => [
                'title' => ['type' => 'text', 'required' => true, 'label' => 'Story Title'],
                'description' => ['type' => 'textarea', 'required' => true, 'label' => 'Story Description'],
                'status' => ['type' => 'select', 'options' => ['TODO', 'IN_PROGRESS', 'DONE', 'ON_HOLD'], 'label' => 'Status'],
                'priority' => ['type' => 'select', 'options' => ['LOW', 'MEDIUM', 'HIGH', 'CRITICAL'], 'label' => 'Priority'],
                'parent_id' => ['type' => 'feature_select', 'label' => 'Parent Feature']
            ],
            'story_specific' => [
                'acceptance_criteria' => ['type' => 'textarea', 'label' => 'Acceptance Criteria'],
                'story_points' => ['type' => 'select', 'options' => ['1', '2', '3', '5', '8', '13', '21'], 'label' => 'Story Points'],
                'estimate' => ['type' => 'number', 'step' => '0.5', 'label' => 'Estimate (hours)']
            ],
            'tracking' => [
                'assignee_id' => ['type' => 'user_select', 'label' => 'Assignee'],
                'tags' => ['type' => 'tags', 'label' => 'Tags'],
                'screenshot_upload' => ['type' => 'file', 'accept' => 'image/*', 'label' => 'Upload Screenshot']
            ]
        ],
        'TASK' => [
            'basic_fields' => [
                'title' => ['type' => 'text', 'required' => true, 'label' => 'Task Title'],
                'description' => ['type' => 'textarea', 'required' => true, 'label' => 'Task Description'],
                'status' => ['type' => 'select', 'options' => ['TODO', 'IN_PROGRESS', 'DONE', 'ON_HOLD'], 'label' => 'Status'],
                'priority' => ['type' => 'select', 'options' => ['LOW', 'MEDIUM', 'HIGH', 'CRITICAL'], 'label' => 'Priority'],
                'parent_id' => ['type' => 'story_select', 'label' => 'Parent Story']
            ],
            'task_specific' => [
                'estimated_hours' => ['type' => 'number', 'step' => '0.5', 'label' => 'Estimated Hours'],
                'actual_hours' => ['type' => 'number', 'step' => '0.5', 'label' => 'Actual Hours']
            ],
            'tracking' => [
                'assignee_id' => ['type' => 'user_select', 'required' => true, 'label' => 'Assignee'],
                'tags' => ['type' => 'tags', 'label' => 'Tags']
            ]
        ],
        'BUG' => [
            'basic_fields' => [
                'title' => ['type' => 'text', 'required' => true, 'label' => 'Bug Title'],
                'description' => ['type' => 'textarea', 'required' => true, 'label' => 'Bug Description'],
                'status' => ['type' => 'select', 'options' => ['TODO', 'IN_PROGRESS', 'DONE', 'ON_HOLD'], 'label' => 'Status'],
                'priority' => ['type' => 'select', 'options' => ['LOW', 'MEDIUM', 'HIGH', 'CRITICAL'], 'label' => 'Priority']
            ],
            'bug_specific' => [
                'bug_type' => ['type' => 'select', 'options' => ['functional', 'ui', 'performance', 'security'], 'label' => 'Bug Type'],
                'severity' => ['type' => 'select', 'options' => ['low', 'medium', 'high', 'critical'], 'label' => 'Severity'],
                'current_behavior' => ['type' => 'textarea', 'label' => 'Current Behavior'],
                'expected_behavior' => ['type' => 'textarea', 'label' => 'Expected Behavior'],
                'reproduction_steps' => ['type' => 'textarea', 'label' => 'Steps to Reproduce'],
                'environment' => ['type' => 'select', 'options' => ['development', 'testing', 'staging', 'production'], 'label' => 'Environment']
            ],
            'tracking' => [
                'reporter_id' => ['type' => 'user_select', 'required' => true, 'label' => 'Reporter'],
                'assignee_id' => ['type' => 'user_select', 'label' => 'Assignee'],
                'screenshot_upload' => ['type' => 'file', 'accept' => 'image/*', 'label' => 'Upload Screenshot']
            ]
        ]
    ];
    
    // Display the form configuration
    foreach ($edit_form_config as $type => $sections) {
        echo "🏷️  $type Edit Form:\n";
        echo str_repeat("-", 50) . "\n";
        
        foreach ($sections as $section_name => $fields) {
            echo "\n📋 " . ucwords(str_replace('_', ' ', $section_name)) . ":\n";
            
            foreach ($fields as $field_name => $config) {
                $required = isset($config['required']) && $config['required'] ? ' (Required)' : '';
                echo "   • {$config['label']}$required\n";
                echo "     Field: $field_name\n";
                echo "     Type: {$config['type']}\n";
                
                if (isset($config['options'])) {
                    echo "     Options: " . implode(', ', $config['options']) . "\n";
                }
                if (isset($config['accept'])) {
                    echo "     Accept: {$config['accept']}\n";
                }
                if (isset($config['step'])) {
                    echo "     Step: {$config['step']}\n";
                }
            }
        }
        echo "\n";
    }
    
    // Generate JSON configuration for frontend
    $json_config = json_encode($edit_form_config, JSON_PRETTY_PRINT);
    file_put_contents('work-items-edit-config-updated.json', $json_config);
    echo "💾 Updated JSON configuration saved to 'work-items-edit-config-updated.json'\n\n";
    
    // Generate HTML form examples for EPIC and FEATURE
    echo "📝 HTML Form Examples:\n";
    echo str_repeat("=", 50) . "\n\n";
    
    foreach (['EPIC', 'FEATURE'] as $type) {
        echo "<!-- $type Edit Form -->\n";
        echo "<form class=\"work-item-edit-form\" data-type=\"" . strtolower($type) . "\">\n";
        
        foreach ($edit_form_config[$type] as $section_name => $fields) {
            echo "  <!-- " . ucwords(str_replace('_', ' ', $section_name)) . " -->\n";
            echo "  <div class=\"form-section\">\n";
            echo "    <h3>" . ucwords(str_replace('_', ' ', $section_name)) . "</h3>\n";
            
            foreach ($fields as $field_name => $config) {
                $required = isset($config['required']) && $config['required'] ? ' required' : '';
                
                echo "    <div class=\"form-group\">\n";
                echo "      <label for=\"$field_name\">{$config['label']}</label>\n";
                
                switch ($config['type']) {
                    case 'text':
                    case 'url':
                    case 'number':
                    case 'date':
                        $step = isset($config['step']) ? " step=\"{$config['step']}\"" : '';
                        echo "      <input type=\"{$config['type']}\" id=\"$field_name\" name=\"$field_name\"$required$step>\n";
                        break;
                    case 'textarea':
                        echo "      <textarea id=\"$field_name\" name=\"$field_name\"$required></textarea>\n";
                        break;
                    case 'select':
                        echo "      <select id=\"$field_name\" name=\"$field_name\"$required>\n";
                        if (isset($config['options'])) {
                            foreach ($config['options'] as $option) {
                                echo "        <option value=\"$option\">$option</option>\n";
                            }
                        }
                        echo "      </select>\n";
                        break;
                    case 'checkbox':
                        echo "      <input type=\"checkbox\" id=\"$field_name\" name=\"$field_name\" value=\"1\">\n";
                        break;
                    case 'file':
                        $accept = isset($config['accept']) ? " accept=\"{$config['accept']}\"" : '';
                        echo "      <input type=\"file\" id=\"$field_name\" name=\"$field_name\"$accept>\n";
                        break;
                    case 'color':
                        echo "      <input type=\"color\" id=\"$field_name\" name=\"$field_name\">\n";
                        break;
                    default:
                        echo "      <!-- Custom field type: {$config['type']} -->\n";
                        echo "      <input type=\"text\" id=\"$field_name\" name=\"$field_name\"$required>\n";
                }
                
                echo "    </div>\n";
            }
            
            echo "  </div>\n";
        }
        
        echo "  <div class=\"form-actions\">\n";
        echo "    <button type=\"submit\">Save " . ucfirst(strtolower($type)) . "</button>\n";
        echo "    <button type=\"button\" class=\"cancel-btn\">Cancel</button>\n";
        echo "  </div>\n";
        echo "</form>\n\n";
    }
    
    // Check current database state
    echo "🔍 Current Database State Verification:\n";
    echo str_repeat("-", 50) . "\n";
    
    $stmt = $pdo->query("
        SELECT id, title, type, prototype_link, drag_drop_enabled, 
               pdf_upload_path, prototype_status, mockup_link 
        FROM work_items 
        WHERE type IN ('EPIC', 'FEATURE') 
        ORDER BY type, id
    ");
    
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($items as $item) {
        echo "✅ {$item['type']} #{$item['id']}: {$item['title']}\n";
        echo "   All new fields populated and ready for editing\n";
    }
    
} catch (PDOException $e) {
    echo "❌ Database Error: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n✨ Updated edit form guide completed!\n";
?>
