-- Strategic Roadmap Database Tables
-- This script creates all necessary tables for the strategic roadmap feature

-- Templates table - stores roadmap templates
CREATE TABLE IF NOT EXISTS roadmap_templates (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_created_by (created_by),
    INDEX idx_created_at (created_at)
);

-- Streams table - stores the streams/services for each template
CREATE TABLE IF NOT EXISTS roadmap_streams (
    id INT PRIMARY KEY AUTO_INCREMENT,
    template_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    color VARCHAR(7) DEFAULT '#3b82f6', -- hex color code
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (template_id) REFERENCES roadmap_templates(id) ON DELETE CASCADE,
    INDEX idx_template_id (template_id),
    INDEX idx_sort_order (sort_order)
);

-- Projects table - stores individual projects in the roadmap
CREATE TABLE IF NOT EXISTS roadmap_projects (
    id INT PRIMARY KEY AUTO_INCREMENT,
    template_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    stream_name VARCHAR(255) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (template_id) REFERENCES roadmap_templates(id) ON DELETE CASCADE,
    INDEX idx_template_id (template_id),
    INDEX idx_dates (start_date, end_date),
    INDEX idx_stream (stream_name)
);

-- Action points table - stores action points for each project
CREATE TABLE IF NOT EXISTS roadmap_action_points (
    id INT PRIMARY KEY AUTO_INCREMENT,
    project_id INT NOT NULL,
    description TEXT NOT NULL,
    sort_order INT DEFAULT 0,
    is_completed BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES roadmap_projects(id) ON DELETE CASCADE,
    INDEX idx_project_id (project_id),
    INDEX idx_sort_order (sort_order)
);

-- Insert default templates if they don't exist (using NULL for created_by to avoid foreign key issues)
INSERT INTO roadmap_templates (name, description, created_by) 
VALUES 
('Product Roadmap', 'Core product streams for feature planning', NULL),
('Digital Marketing Plan', 'Marketing channels and campaign planning', NULL),
('Sales & CRM', 'Sales pipeline and lead management', NULL)
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- Get template IDs for inserting streams and projects
SET @product_template_id = (SELECT id FROM roadmap_templates WHERE name = 'Product Roadmap' LIMIT 1);
SET @marketing_template_id = (SELECT id FROM roadmap_templates WHERE name = 'Digital Marketing Plan' LIMIT 1);
SET @sales_template_id = (SELECT id FROM roadmap_templates WHERE name = 'Sales & CRM' LIMIT 1);

-- Insert streams for Product Roadmap
INSERT INTO roadmap_streams (template_id, name, color, sort_order) 
VALUES 
(@product_template_id, 'Growth', '#10b981', 1),
(@product_template_id, 'Retention', '#3b82f6', 2),
(@product_template_id, 'Platform', '#8b5cf6', 3),
(@product_template_id, 'Infrastructure', '#f59e0b', 4),
(@product_template_id, 'Experience', '#ec4899', 5)
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- Insert streams for Marketing Plan
INSERT INTO roadmap_streams (template_id, name, color, sort_order) 
VALUES 
(@marketing_template_id, 'SEO', '#06b6d4', 1),
(@marketing_template_id, 'Paid Ads', '#ef4444', 2),
(@marketing_template_id, 'Social Media', '#84cc16', 3),
(@marketing_template_id, 'Email Marketing', '#f97316', 4),
(@marketing_template_id, 'Content', '#a855f7', 5)
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- Insert streams for Sales & CRM
INSERT INTO roadmap_streams (template_id, name, color, sort_order) 
VALUES 
(@sales_template_id, 'Lead Generation', '#14b8a6', 1),
(@sales_template_id, 'Outreach', '#f43f5e', 2),
(@sales_template_id, 'Pipeline', '#22c55e', 3),
(@sales_template_id, 'Closing', '#eab308', 4),
(@sales_template_id, 'Account Management', '#6366f1', 5)
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- Sample projects for Product Roadmap (you can remove these if not needed)
INSERT INTO roadmap_projects (template_id, name, start_date, end_date, stream_name, description) 
VALUES 
(@product_template_id, 'User Onboarding v2', '2025-01-15', '2025-03-20', 'Growth', 'Redesign the user onboarding experience'),
(@product_template_id, 'Analytics Dashboard', '2025-02-10', '2025-05-15', 'Platform', 'Build comprehensive analytics dashboard'),
(@product_template_id, 'Mobile App MVP', '2025-03-01', '2025-07-30', 'Growth', 'Develop minimum viable mobile application')
ON DUPLICATE KEY UPDATE name = VALUES(name);
