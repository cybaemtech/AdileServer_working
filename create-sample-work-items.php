<?php
require_once './api/config/database.php';

function createSampleWorkItems() {
    $database = new Database();
    $pdo = $database->getConnection();
    
    if ($pdo === null) {
        echo "❌ Database connection failed\n";
        return;
    }
    
    echo "🚀 Creating Sample Work Items for All Types\n";
    echo "============================================\n\n";
    
    try {
        // Get existing project ID
        $stmt = $pdo->query("SELECT id FROM projects LIMIT 1");
        $project = $stmt->fetch();
        $projectId = $project ? $project['id'] : 1;
        
        // Get existing user ID for assignment
        $stmt = $pdo->query("SELECT id FROM users WHERE is_active = 1 LIMIT 1");
        $user = $stmt->fetch();
        $userId = $user ? $user['id'] : 1;
        
        // Sample work items data
        $workItems = [
            // EPIC
            [
                'title' => 'User Management System',
                'description' => 'Complete user management system with authentication, profiles, and permissions',
                'type' => 'EPIC',
                'status' => 'IN_PROGRESS',
                'priority' => 'HIGH',
                'project_id' => $projectId,
                'assignee_id' => $userId,
                'reporter_id' => $userId,
                'start_date' => '2026-03-01 00:00:00',
                'end_date' => '2026-04-30 23:59:59',
                'estimate' => 40.00
            ],
            
            // FEATURE
            [
                'title' => 'User Authentication',
                'description' => 'Implement secure login and registration system with email verification',
                'type' => 'FEATURE',
                'status' => 'IN_PROGRESS',
                'priority' => 'HIGH',
                'project_id' => $projectId,
                'assignee_id' => $userId,
                'reporter_id' => $userId,
                'start_date' => '2026-03-01 00:00:00',
                'end_date' => '2026-03-15 23:59:59',
                'estimate' => 13.00,
                'estimated_hours' => 20.00
            ],
            [
                'title' => 'User Profile Management',
                'description' => 'Allow users to manage their profiles, upload avatars, and update information',
                'type' => 'FEATURE',
                'status' => 'TODO',
                'priority' => 'MEDIUM',
                'project_id' => $projectId,
                'assignee_id' => $userId,
                'reporter_id' => $userId,
                'start_date' => '2026-03-16 00:00:00',
                'end_date' => '2026-03-30 23:59:59',
                'estimate' => 8.00,
                'estimated_hours' => 12.00
            ],
            
            // STORY
            [
                'title' => 'As a user, I want to register with email verification',
                'description' => 'User should be able to create an account and verify their email address before accessing the system',
                'type' => 'STORY',
                'status' => 'DONE',
                'priority' => 'HIGH',
                'project_id' => $projectId,
                'assignee_id' => $userId,
                'reporter_id' => $userId,
                'estimate' => 5.00,
                'estimated_hours' => 8.00,
                'actual_hours' => 6.50,
                'completed_at' => '2026-03-08 14:30:00'
            ],
            [
                'title' => 'As a user, I want to login securely',
                'description' => 'User should be able to login with email and password, with option for remember me',
                'type' => 'STORY',
                'status' => 'IN_PROGRESS',
                'priority' => 'HIGH',
                'project_id' => $projectId,
                'assignee_id' => $userId,
                'reporter_id' => $userId,
                'estimate' => 3.00,
                'estimated_hours' => 5.00
            ],
            [
                'title' => 'As a user, I want to reset my password',
                'description' => 'User should be able to reset forgotten password via email link',
                'type' => 'STORY',
                'status' => 'TODO',
                'priority' => 'MEDIUM',
                'project_id' => $projectId,
                'assignee_id' => $userId,
                'reporter_id' => $userId,
                'estimate' => 5.00,
                'estimated_hours' => 8.00
            ],
            
            // TASK
            [
                'title' => 'Create user registration form',
                'description' => 'Design and implement the frontend registration form with validation',
                'type' => 'TASK',
                'status' => 'DONE',
                'priority' => 'HIGH',
                'project_id' => $projectId,
                'assignee_id' => $userId,
                'reporter_id' => $userId,
                'estimated_hours' => 4.00,
                'actual_hours' => 3.50,
                'completed_at' => '2026-03-05 16:00:00'
            ],
            [
                'title' => 'Implement email verification service',
                'description' => 'Create backend service to send and verify email verification tokens',
                'type' => 'TASK',
                'status' => 'IN_PROGRESS',
                'priority' => 'HIGH',
                'project_id' => $projectId,
                'assignee_id' => $userId,
                'reporter_id' => $userId,
                'estimated_hours' => 6.00
            ],
            [
                'title' => 'Set up database tables for user management',
                'description' => 'Create and optimize database tables for users, sessions, and tokens',
                'type' => 'TASK',
                'status' => 'DONE',
                'priority' => 'HIGH',
                'project_id' => $projectId,
                'assignee_id' => $userId,
                'reporter_id' => $userId,
                'estimated_hours' => 3.00,
                'actual_hours' => 2.50,
                'completed_at' => '2026-03-02 10:30:00'
            ],
            [
                'title' => 'Write unit tests for authentication',
                'description' => 'Create comprehensive unit tests for login, registration, and password reset',
                'type' => 'TASK',
                'status' => 'TODO',
                'priority' => 'MEDIUM',
                'project_id' => $projectId,
                'assignee_id' => $userId,
                'reporter_id' => $userId,
                'estimated_hours' => 8.00
            ],
            
            // BUG
            [
                'title' => 'Login form validation not working on mobile',
                'description' => 'Email validation error messages are not displayed properly on mobile devices',
                'type' => 'BUG',
                'status' => 'TODO',
                'priority' => 'MEDIUM',
                'project_id' => $projectId,
                'assignee_id' => $userId,
                'reporter_id' => $userId,
                'bug_type' => 'UI',
                'severity' => 'MEDIUM',
                'current_behavior' => 'Error messages are hidden behind the keyboard on mobile devices',
                'expected_behavior' => 'Error messages should be visible and properly positioned on all devices',
                'estimated_hours' => 2.00
            ],
            [
                'title' => 'Password reset email not sending',
                'description' => 'Users are not receiving password reset emails in production environment',
                'type' => 'BUG',
                'status' => 'IN_PROGRESS',
                'priority' => 'HIGH',
                'project_id' => $projectId,
                'assignee_id' => $userId,
                'reporter_id' => $userId,
                'bug_type' => 'BACKEND',
                'severity' => 'HIGH',
                'current_behavior' => 'Password reset emails are not being sent to users',
                'expected_behavior' => 'Users should receive password reset emails within 2-3 minutes',
                'estimated_hours' => 4.00
            ],
            [
                'title' => 'Session timeout causes data loss',
                'description' => 'When user session expires, unsaved form data is lost without warning',
                'type' => 'BUG',
                'status' => 'DONE',
                'priority' => 'CRITICAL',
                'project_id' => $projectId,
                'assignee_id' => $userId,
                'reporter_id' => $userId,
                'bug_type' => 'BACKEND',
                'severity' => 'CRITICAL',
                'current_behavior' => 'Form data is lost when session expires without any notification',
                'expected_behavior' => 'System should warn user before session expires and save draft data',
                'estimated_hours' => 6.00,
                'actual_hours' => 8.00,
                'completed_at' => '2026-03-10 18:45:00'
            ]
        ];
        
        // Insert sample work items
        $insertQuery = "INSERT INTO work_items (
            title, description, type, status, priority, project_id, assignee_id, reporter_id,
            start_date, end_date, estimate, estimated_hours, actual_hours, completed_at,
            bug_type, severity, current_behavior, expected_behavior
        ) VALUES (
            :title, :description, :type, :status, :priority, :project_id, :assignee_id, :reporter_id,
            :start_date, :end_date, :estimate, :estimated_hours, :actual_hours, :completed_at,
            :bug_type, :severity, :current_behavior, :expected_behavior
        )";
        
        $stmt = $pdo->prepare($insertQuery);
        $created = 0;
        $errors = 0;
        
        foreach ($workItems as $item) {
            try {
                // Fill in missing keys with null
                $item = array_merge([
                    'start_date' => null,
                    'end_date' => null,
                    'estimate' => null,
                    'estimated_hours' => null,
                    'actual_hours' => null,
                    'completed_at' => null,
                    'bug_type' => null,
                    'severity' => null,
                    'current_behavior' => null,
                    'expected_behavior' => null
                ], $item);
                
                $stmt->execute($item);
                echo "✅ Created: " . $item['title'] . " (" . $item['type'] . ")\n";
                $created++;
            } catch (Exception $e) {
                echo "❌ Error creating '" . $item['title'] . "': " . $e->getMessage() . "\n";
                $errors++;
            }
        }
        
        echo "\n" . str_repeat("=", 50) . "\n";
        echo "📊 Summary:\n";
        echo "✅ Successfully created: $created items\n";
        echo "❌ Errors: $errors\n";
        
        if ($created > 0) {
            echo "\n🎉 Sample work items created successfully!\n";
            
            // Show updated statistics
            echo "\n📊 Updated Work Items Statistics:\n";
            echo str_repeat("-", 40) . "\n";
            
            $stmt = $pdo->query("SELECT type, COUNT(*) as count FROM work_items GROUP BY type ORDER BY count DESC");
            while ($row = $stmt->fetch()) {
                echo "• " . $row['type'] . ": " . $row['count'] . " items\n";
            }
        }
        
    } catch (Exception $e) {
        echo "❌ Fatal error: " . $e->getMessage() . "\n";
    }
}

createSampleWorkItems();
?>
