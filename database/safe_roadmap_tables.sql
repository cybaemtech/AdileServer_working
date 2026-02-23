-- Strategic Roadmap Database Tables - Safe Version
-- Run this in phpMyAdmin or your MySQL client

-- First, create tables without foreign key constraints
CREATE TABLE IF NOT EXISTS roadmap_templates (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    created_by INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE
);

CREATE TABLE IF NOT EXISTS roadmap_streams (
    id INT PRIMARY KEY AUTO_INCREMENT,
    template_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    color VARCHAR(7) DEFAULT '#3b82f6',
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS roadmap_projects (
    id INT PRIMARY KEY AUTO_INCREMENT,
    template_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    stream_name VARCHAR(255) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS roadmap_action_points (
    id INT PRIMARY KEY AUTO_INCREMENT,
    project_id INT NOT NULL,
    description TEXT NOT NULL,
    sort_order INT DEFAULT 0,
    is_completed BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Add indexes
ALTER TABLE roadmap_templates ADD INDEX IF NOT EXISTS idx_created_by (created_by);
ALTER TABLE roadmap_templates ADD INDEX IF NOT EXISTS idx_created_at (created_at);
ALTER TABLE roadmap_streams ADD INDEX IF NOT EXISTS idx_template_id (template_id);
ALTER TABLE roadmap_streams ADD INDEX IF NOT EXISTS idx_sort_order (sort_order);
ALTER TABLE roadmap_projects ADD INDEX IF NOT EXISTS idx_template_id (template_id);
ALTER TABLE roadmap_projects ADD INDEX IF NOT EXISTS idx_dates (start_date, end_date);
ALTER TABLE roadmap_projects ADD INDEX IF NOT EXISTS idx_stream (stream_name);
ALTER TABLE roadmap_action_points ADD INDEX IF NOT EXISTS idx_project_id (project_id);
ALTER TABLE roadmap_action_points ADD INDEX IF NOT EXISTS idx_sort_order (sort_order);

-- Now add foreign key constraints (optional - you can skip this if it causes issues)
-- ALTER TABLE roadmap_templates ADD CONSTRAINT fk_roadmap_templates_user FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL;
-- ALTER TABLE roadmap_streams ADD CONSTRAINT fk_roadmap_streams_template FOREIGN KEY (template_id) REFERENCES roadmap_templates(id) ON DELETE CASCADE;
-- ALTER TABLE roadmap_projects ADD CONSTRAINT fk_roadmap_projects_template FOREIGN KEY (template_id) REFERENCES roadmap_templates(id) ON DELETE CASCADE;
-- ALTER TABLE roadmap_action_points ADD CONSTRAINT fk_roadmap_action_points_project FOREIGN KEY (project_id) REFERENCES roadmap_projects(id) ON DELETE CASCADE;

-- Insert sample data (this will work without foreign key constraints)
INSERT INTO roadmap_templates (name, description, created_by) VALUES 
('Product Roadmap', 'Core product streams for feature planning', NULL),
('Digital Marketing Plan', 'Marketing channels and campaign planning', NULL),
('Sales & CRM', 'Sales pipeline and lead management', NULL)
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- The PHP API will handle creating streams and projects through the /api/roadmap-templates/seed endpoint
