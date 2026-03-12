<?php
require_once './api/config/database.php';

echo "🔄 Populating EPIC and FEATURE Fields with Sample Data\n";
echo "======================================================\n\n";

try {
    $database = new Database();
    $pdo = $database->getConnection();
    
    if ($pdo === null) {
        echo "❌ Database connection failed\n";
        return;
    }
    
    // Get current EPIC and FEATURE items
    $stmt = $pdo->query("SELECT id, title, type, status FROM work_items WHERE type IN ('EPIC', 'FEATURE') ORDER BY type, id");
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($items)) {
        echo "⚠️  No EPIC or FEATURE items found to update\n";
        return;
    }
    
    echo "📊 Found " . count($items) . " items to update:\n";
    foreach ($items as $item) {
        echo "   • {$item['type']} #{$item['id']}: {$item['title']}\n";
    }
    echo "\n";
    
    // Sample data for different items
    $sample_data = [
        'EPIC' => [
            [
                'prototype_link' => 'https://www.figma.com/proto/abc123/carbinhive-prototype',
                'drag_drop_enabled' => 1,
                'pdf_upload_path' => '/uploads/epic_requirements_2040.pdf',
                'prototype_status' => 'in_progress',
                'mockup_link' => 'https://www.figma.com/file/abc123/carbinhive-mockups'
            ],
            [
                'prototype_link' => 'https://invis.io/prototype/user-management-v2',
                'drag_drop_enabled' => 1,
                'pdf_upload_path' => '/uploads/user_management_specs_2041.pdf',
                'prototype_status' => 'completed',
                'mockup_link' => 'https://invis.io/design/user-management-mockups'
            ]
        ],
        'FEATURE' => [
            [
                'prototype_link' => 'https://marvelapp.com/prototype/auth-feature',
                'drag_drop_enabled' => 0,
                'pdf_upload_path' => '/uploads/auth_requirements_2042.pdf',
                'prototype_status' => 'approved',
                'mockup_link' => 'https://marvelapp.com/design/auth-wireframes'
            ],
            [
                'prototype_link' => 'https://proto.io/player/profile-management',
                'drag_drop_enabled' => 1,
                'pdf_upload_path' => '/uploads/profile_specs_2043.pdf',
                'prototype_status' => 'not_started',
                'mockup_link' => 'https://proto.io/design/profile-mockups'
            ]
        ]
    ];
    
    $epic_index = 0;
    $feature_index = 0;
    $updated_count = 0;
    
    foreach ($items as $item) {
        $data_index = $item['type'] === 'EPIC' ? $epic_index++ : $feature_index++;
        $sample_set = $sample_data[$item['type']][$data_index] ?? $sample_data[$item['type']][0];
        
        // Update the item with sample data
        $sql = "UPDATE work_items SET 
                   prototype_link = :prototype_link,
                   drag_drop_enabled = :drag_drop_enabled,
                   pdf_upload_path = :pdf_upload_path,
                   prototype_status = :prototype_status,
                   mockup_link = :mockup_link,
                   updated_at = CURRENT_TIMESTAMP
                WHERE id = :id";
        
        $stmt = $pdo->prepare($sql);
        $params = array_merge($sample_set, ['id' => $item['id']]);
        
        if ($stmt->execute($params)) {
            echo "✅ Updated {$item['type']} #{$item['id']}: {$item['title']}\n";
            echo "   • Prototype: {$sample_set['prototype_link']}\n";
            echo "   • Drag & Drop: " . ($sample_set['drag_drop_enabled'] ? 'Enabled' : 'Disabled') . "\n";
            echo "   • PDF: {$sample_set['pdf_upload_path']}\n";
            echo "   • Status: {$sample_set['prototype_status']}\n";
            echo "   • Mockups: {$sample_set['mockup_link']}\n\n";
            $updated_count++;
        } else {
            echo "❌ Failed to update {$item['type']} #{$item['id']}\n";
        }
    }
    
    echo "🎉 Successfully updated $updated_count items with new field data\n\n";
    
    // Verify the updates
    echo "🔍 Verifying Updates:\n";
    echo str_repeat("-", 50) . "\n";
    
    $stmt = $pdo->query("
        SELECT id, title, type, prototype_link, drag_drop_enabled, 
               pdf_upload_path, prototype_status, mockup_link 
        FROM work_items 
        WHERE type IN ('EPIC', 'FEATURE') 
        ORDER BY type, id
    ");
    
    $updated_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($updated_items as $item) {
        echo "🏷️  {$item['type']} #{$item['id']}: {$item['title']}\n";
        echo "   📋 Prototype: " . ($item['prototype_link'] ?: 'Not set') . "\n";
        echo "   🖱️  Drag & Drop: " . ($item['drag_drop_enabled'] ? 'Yes' : 'No') . "\n";
        echo "   📄 PDF: " . ($item['pdf_upload_path'] ?: 'Not set') . "\n";
        echo "   🔄 Status: " . ($item['prototype_status'] ?: 'Not set') . "\n";
        echo "   🎨 Mockups: " . ($item['mockup_link'] ?: 'Not set') . "\n\n";
    }
    
} catch (PDOException $e) {
    echo "❌ Database Error: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "✨ Sample data population completed!\n";
?>
