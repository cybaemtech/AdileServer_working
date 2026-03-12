<?php
require_once './api/config/database.php';

function executeQueries($pdo, $queries) {
    $results = [];
    foreach ($queries as $name => $query) {
        try {
            $pdo->exec($query);
            $results[$name] = ['status' => 'success', 'message' => 'Executed successfully'];
            echo "✅ $name - Success\n";
        } catch (Exception $e) {
            $results[$name] = ['status' => 'error', 'message' => $e->getMessage()];
            echo "❌ $name - Error: " . $e->getMessage() . "\n";
        }
    }
    return $results;
}

// Database initialization queries
$queries = [
    'users_table' => "
        CREATE TABLE IF NOT EXISTS users (
            id INT PRIMARY KEY AUTO_INCREMENT,
            external_id VARCHAR(100) UNIQUE,
            email VARCHAR(255) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            full_name VARCHAR(255) NOT NULL,
            role ENUM('admin', 'project_manager', 'developer', 'tester', 'viewer') NOT NULL DEFAULT 'developer',
            is_active BOOLEAN DEFAULT TRUE,
            email_verified BOOLEAN DEFAULT FALSE,
            profile_picture VARCHAR(500),
            phone VARCHAR(20),
            timezone VARCHAR(50) DEFAULT 'UTC',
            last_login TIMESTAMP NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_email (email),
            INDEX idx_role (role),
            INDEX idx_active (is_active)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ",
    
    'teams_table' => "
        CREATE TABLE IF NOT EXISTS teams (
            id INT PRIMARY KEY AUTO_INCREMENT,
            external_id VARCHAR(100) UNIQUE,
            name VARCHAR(255) NOT NULL,
            description TEXT,
            team_lead_id INT,
            is_active BOOLEAN DEFAULT TRUE,
            created_by INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (team_lead_id) REFERENCES users(id) ON DELETE SET NULL,
            FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
            INDEX idx_team_lead (team_lead_id),
            INDEX idx_active (is_active)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ",
    
    'team_members_table' => "
        CREATE TABLE IF NOT EXISTS team_members (
            id INT PRIMARY KEY AUTO_INCREMENT,
            team_id INT NOT NULL,
            user_id INT NOT NULL,
            role ENUM('lead', 'member', 'observer') DEFAULT 'member',
            joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            is_active BOOLEAN DEFAULT TRUE,
            UNIQUE KEY unique_team_user (team_id, user_id),
            FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            INDEX idx_team (team_id),
            INDEX idx_user (user_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ",
    
    'projects_table' => "
        CREATE TABLE IF NOT EXISTS projects (
            id INT PRIMARY KEY AUTO_INCREMENT,
            external_id VARCHAR(100) UNIQUE,
            name VARCHAR(255) NOT NULL,
            description TEXT,
            status ENUM('planning', 'active', 'on_hold', 'completed', 'cancelled') DEFAULT 'planning',
            priority ENUM('low', 'medium', 'high', 'critical') DEFAULT 'medium',
            start_date DATE,
            end_date DATE,
            budget DECIMAL(15,2),
            owner_id INT,
            team_id INT,
            is_active BOOLEAN DEFAULT TRUE,
            created_by INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE SET NULL,
            FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE SET NULL,
            FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
            INDEX idx_status (status),
            INDEX idx_owner (owner_id),
            INDEX idx_team (team_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ",
    
    'project_members_table' => "
        CREATE TABLE IF NOT EXISTS project_members (
            id INT PRIMARY KEY AUTO_INCREMENT,
            project_id INT NOT NULL,
            user_id INT NOT NULL,
            role ENUM('manager', 'developer', 'tester', 'analyst', 'designer') DEFAULT 'developer',
            joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            is_active BOOLEAN DEFAULT TRUE,
            UNIQUE KEY unique_project_user (project_id, user_id),
            FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            INDEX idx_project (project_id),
            INDEX idx_user (user_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ",
    
    'work_items_table' => "
        CREATE TABLE IF NOT EXISTS work_items (
            id INT PRIMARY KEY AUTO_INCREMENT,
            external_id VARCHAR(100) UNIQUE,
            title VARCHAR(200) NOT NULL,
            description TEXT,
            tags TEXT,
            type ENUM('EPIC','FEATURE','STORY','TASK','BUG') NOT NULL DEFAULT 'TASK',
            status ENUM('TODO','IN_PROGRESS','DONE','ON_HOLD') DEFAULT 'TODO',
            priority ENUM('LOW','MEDIUM','HIGH','CRITICAL') DEFAULT 'MEDIUM',
            project_id INT NOT NULL,
            parent_id INT NULL,
            assignee_id INT NULL,
            reporter_id INT NULL,
            updated_by INT NULL,
            last_updated_by INT NULL,
            estimate DECIMAL(10,2) NULL COMMENT 'Story points or hours',
            start_date TIMESTAMP NULL,
            end_date TIMESTAMP NULL,
            completed_at TIMESTAMP NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            startDate DATE NULL,
            endDate DATE NULL,
            createdByName VARCHAR(400) NULL,
            createdByEmail VARCHAR(255) NULL,
            github_url VARCHAR(255) NULL,
            bug_type VARCHAR(20) NULL,
            current_behavior TEXT NULL,
            expected_behavior TEXT NULL,
            severity VARCHAR(20) NULL,
            estimated_hours DECIMAL(10,2) NULL,
            actual_hours DECIMAL(6,2) NULL,
            reference_url VARCHAR(500) NULL,
            screenshot_path VARCHAR(500) NULL,
            screenshot VARCHAR(255) NULL,
            screenshot_blob LONGBLOB NULL,
            actual_hrs DECIMAL(10,2) NULL,
            FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
            FOREIGN KEY (parent_id) REFERENCES work_items(id) ON DELETE SET NULL,
            FOREIGN KEY (assignee_id) REFERENCES users(id) ON DELETE SET NULL,
            FOREIGN KEY (reporter_id) REFERENCES users(id) ON DELETE SET NULL,
            FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL,
            FOREIGN KEY (last_updated_by) REFERENCES users(id) ON DELETE SET NULL,
            INDEX idx_project (project_id),
            INDEX idx_assignee (assignee_id),
            INDEX idx_reporter (reporter_id),
            INDEX idx_status (status),
            INDEX idx_type (type),
            INDEX idx_priority (priority),
            INDEX idx_parent (parent_id),
            INDEX idx_tags (tags(255)),
            INDEX idx_start_date (start_date),
            INDEX idx_end_date (end_date),
            INDEX idx_github_url (github_url),
            INDEX idx_bug_type (bug_type),
            INDEX idx_severity (severity),
            INDEX idx_estimated_hours (estimated_hours)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ",
    
    'work_item_assignees_table' => "
        CREATE TABLE IF NOT EXISTS work_item_assignees (
            id INT PRIMARY KEY AUTO_INCREMENT,
            work_item_id INT NOT NULL,
            user_id INT NOT NULL,
            assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            assigned_by INT NOT NULL,
            is_active BOOLEAN DEFAULT TRUE,
            UNIQUE KEY unique_work_item_user (work_item_id, user_id),
            FOREIGN KEY (work_item_id) REFERENCES work_items(id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (assigned_by) REFERENCES users(id) ON DELETE CASCADE,
            INDEX idx_work_item (work_item_id),
            INDEX idx_user (user_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ",
    
    'work_item_history_table' => "
        CREATE TABLE IF NOT EXISTS work_item_history (
            id INT PRIMARY KEY AUTO_INCREMENT,
            work_item_id INT NOT NULL,
            field_name VARCHAR(100) NOT NULL,
            old_value TEXT,
            new_value TEXT,
            changed_by INT NOT NULL,
            changed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (work_item_id) REFERENCES work_items(id) ON DELETE CASCADE,
            FOREIGN KEY (changed_by) REFERENCES users(id) ON DELETE CASCADE,
            INDEX idx_work_item (work_item_id),
            INDEX idx_changed_by (changed_by),
            INDEX idx_field (field_name),
            INDEX idx_changed_at (changed_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ",
    
    'roadmap_templates_table' => "
        CREATE TABLE IF NOT EXISTS roadmap_templates (
            id INT PRIMARY KEY AUTO_INCREMENT,
            name VARCHAR(255) NOT NULL,
            description TEXT,
            template_data JSON,
            is_default BOOLEAN DEFAULT FALSE,
            created_by INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
            INDEX idx_created_by (created_by),
            INDEX idx_is_default (is_default)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ",
    
    'roadmap_streams_table' => "
        CREATE TABLE IF NOT EXISTS roadmap_streams (
            id INT PRIMARY KEY AUTO_INCREMENT,
            name VARCHAR(255) NOT NULL,
            description TEXT,
            color VARCHAR(7) DEFAULT '#007bff',
            sort_order INT DEFAULT 0,
            is_active BOOLEAN DEFAULT TRUE,
            created_by INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
            INDEX idx_sort_order (sort_order),
            INDEX idx_active (is_active)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ",
    
    'roadmap_projects_table' => "
        CREATE TABLE IF NOT EXISTS roadmap_projects (
            id INT PRIMARY KEY AUTO_INCREMENT,
            name VARCHAR(255) NOT NULL,
            description TEXT,
            stream_id INT,
            start_date DATE,
            end_date DATE,
            progress INT DEFAULT 0 CHECK (progress >= 0 AND progress <= 100),
            status ENUM('not_started', 'in_progress', 'completed', 'delayed', 'cancelled') DEFAULT 'not_started',
            priority ENUM('low', 'medium', 'high', 'critical') DEFAULT 'medium',
            created_by INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (stream_id) REFERENCES roadmap_streams(id) ON DELETE SET NULL,
            FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
            INDEX idx_stream (stream_id),
            INDEX idx_dates (start_date, end_date),
            INDEX idx_status (status)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ",
    
    'roadmap_action_points_table' => "
        CREATE TABLE IF NOT EXISTS roadmap_action_points (
            id INT PRIMARY KEY AUTO_INCREMENT,
            project_id INT NOT NULL,
            title VARCHAR(255) NOT NULL,
            description TEXT,
            assigned_to INT,
            due_date DATE,
            status ENUM('pending', 'in_progress', 'completed', 'cancelled') DEFAULT 'pending',
            priority ENUM('low', 'medium', 'high', 'critical') DEFAULT 'medium',
            created_by INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (project_id) REFERENCES roadmap_projects(id) ON DELETE CASCADE,
            FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL,
            FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
            INDEX idx_project (project_id),
            INDEX idx_assigned_to (assigned_to),
            INDEX idx_due_date (due_date),
            INDEX idx_status (status)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ",
    
    'project_bug_reports_table' => "
        CREATE TABLE IF NOT EXISTS project_bug_reports (
            id INT PRIMARY KEY AUTO_INCREMENT,
            project_id INT NOT NULL,
            title VARCHAR(255) NOT NULL,
            description TEXT NOT NULL,
            steps_to_reproduce TEXT,
            expected_behavior TEXT,
            actual_behavior TEXT,
            severity ENUM('low', 'medium', 'high', 'critical') DEFAULT 'medium',
            status ENUM('open', 'in_progress', 'resolved', 'closed') DEFAULT 'open',
            reported_by INT NOT NULL,
            assigned_to INT,
            resolved_at TIMESTAMP NULL,
            resolution_notes TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
            FOREIGN KEY (reported_by) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL,
            INDEX idx_project (project_id),
            INDEX idx_reported_by (reported_by),
            INDEX idx_assigned_to (assigned_to),
            INDEX idx_status (status),
            INDEX idx_severity (severity)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ",
    
    'project_access_settings_table' => "
        CREATE TABLE IF NOT EXISTS project_access_settings (
            id INT PRIMARY KEY AUTO_INCREMENT,
            project_id INT NOT NULL,
            access_type ENUM('public', 'private', 'temporary') DEFAULT 'private',
            temporary_access_token VARCHAR(255) UNIQUE,
            temporary_access_expires_at TIMESTAMP NULL,
            is_active BOOLEAN DEFAULT TRUE,
            created_by INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY unique_project_access (project_id),
            FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
            FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
            INDEX idx_access_type (access_type),
            INDEX idx_token (temporary_access_token),
            INDEX idx_expires (temporary_access_expires_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ",
    
    'images_table' => "
        CREATE TABLE IF NOT EXISTS images (
            id INT PRIMARY KEY AUTO_INCREMENT,
            filename VARCHAR(255) NOT NULL,
            original_filename VARCHAR(255) NOT NULL,
            file_path VARCHAR(500) NOT NULL,
            file_size INT NOT NULL,
            mime_type VARCHAR(100) NOT NULL,
            uploaded_by INT NOT NULL,
            entity_type ENUM('work_item', 'project', 'user', 'bug_report') NOT NULL,
            entity_id INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE CASCADE,
            INDEX idx_entity (entity_type, entity_id),
            INDEX idx_uploaded_by (uploaded_by),
            INDEX idx_filename (filename)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    "
];

echo "🚀 Database Schema Creation Script\n";
echo "==================================\n\n";

try {
    $database = new Database();
    $pdo = $database->getConnection();
    
    if ($pdo === null) {
        echo "❌ Failed to connect to database\n";
        exit(1);
    }
    
    echo "✅ Database connection successful\n\n";
    echo "Creating/Updating tables...\n";
    echo str_repeat("-", 40) . "\n";
    
    $results = executeQueries($pdo, $queries);
    
    echo "\n" . str_repeat("-", 40) . "\n";
    echo "📊 Summary:\n";
    
    $success = 0;
    $errors = 0;
    
    foreach ($results as $name => $result) {
        if ($result['status'] === 'success') {
            $success++;
        } else {
            $errors++;
            echo "   ❌ $name: " . $result['message'] . "\n";
        }
    }
    
    echo "   ✅ Successful: $success\n";
    echo "   ❌ Errors: $errors\n";
    
    if ($errors === 0) {
        echo "\n🎉 All tables created/updated successfully!\n";
    } else {
        echo "\n⚠️  Some tables had issues. Please check the errors above.\n";
    }
    
} catch (Exception $e) {
    echo "❌ Fatal error: " . $e->getMessage() . "\n";
    exit(1);
}
?>
