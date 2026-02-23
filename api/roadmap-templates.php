<?php
// Strategic Roadmap Templates API
// Handles all roadmap template operations

// Start output buffering and session
ob_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config/cors.php';
require_once __DIR__ . '/config/database.php';

// Set CORS headers
setCorsHeaders();

$database = new Database();
$conn = $database->getConnection();

$method = $_SERVER['REQUEST_METHOD'];

// Handle OPTIONS preflight requests
if ($method === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Get the path from URL
$path = $_SERVER['AGILE_API_PATH'] ?? ($_SERVER['PATH_INFO'] ?? '');
if ($path === '' && isset($_SERVER['REQUEST_URI'])) {
    $uriPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
    if (preg_match('~/api/roadmap-templates(/.*)?$~i', $uriPath, $m)) { 
        $path = $m[1] ?? '/'; 
    }
}
if ($path === '' || $path === '/') {
    $path = '/';
}

// Parse path for ID extraction
$pathParts = explode('/', trim($path, '/'));
$templateId = isset($pathParts[0]) && is_numeric($pathParts[0]) ? (int)$pathParts[0] : null;

// Route requests
switch ($method . ':' . $path) {
    case 'GET:/':
        getAllTemplates($conn);
        break;
    
    case 'POST:/':
        createTemplate($conn);
        break;
    
    case 'POST:/seed':
        seedTemplates($conn);
        break;
    
    default:
        if ($templateId && preg_match('/^\/\d+$/', $path)) {
            switch ($method) {
                case 'GET':
                    getTemplate($conn, $templateId);
                    break;
                case 'PUT':
                    updateTemplate($conn, $templateId);
                    break;
                case 'DELETE':
                    deleteTemplate($conn, $templateId);
                    break;
                default:
                    http_response_code(405);
                    echo json_encode(['error' => 'Method not allowed']);
            }
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Endpoint not found', 'path' => $path]);
        }
        break;
}

function getAllTemplates($conn) {
    try {
        // Get all templates with their streams and projects
        $stmt = $conn->prepare("
            SELECT rt.id, rt.name, rt.description, rt.created_at, rt.updated_at
            FROM roadmap_templates rt 
            WHERE rt.is_active = 1 
            ORDER BY rt.created_at DESC
        ");
        $stmt->execute();
        $templates = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($templates as &$template) {
            // Get streams for this template
            $streamStmt = $conn->prepare("
                SELECT name, color 
                FROM roadmap_streams 
                WHERE template_id = ? 
                ORDER BY sort_order ASC
            ");
            $streamStmt->execute([$template['id']]);
            $streams = $streamStmt->fetchAll(PDO::FETCH_COLUMN);
            $template['streams'] = $streams;
            
            // Get projects for this template
            $projectStmt = $conn->prepare("
                SELECT rp.id, rp.name, rp.start_date as startDate, rp.end_date as endDate, 
                       rp.stream_name as stream, rp.description
                FROM roadmap_projects rp
                WHERE rp.template_id = ?
                ORDER BY rp.start_date ASC
            ");
            $projectStmt->execute([$template['id']]);
            $projects = $projectStmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Get action points for each project
            foreach ($projects as &$project) {
                $actionStmt = $conn->prepare("
                    SELECT description 
                    FROM roadmap_action_points 
                    WHERE project_id = ? 
                    ORDER BY sort_order ASC
                ");
                $actionStmt->execute([$project['id']]);
                $actionPoints = $actionStmt->fetchAll(PDO::FETCH_COLUMN);
                $project['actionPoints'] = $actionPoints;
            }
            
            $template['projects'] = $projects;
        }
        
        echo json_encode($templates);
        
    } catch (PDOException $e) {
        error_log("Get templates error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['error' => 'Database error']);
    }
}

function getTemplate($conn, $templateId) {
    try {
        $stmt = $conn->prepare("
            SELECT rt.id, rt.name, rt.description, rt.created_at, rt.updated_at
            FROM roadmap_templates rt 
            WHERE rt.id = ? AND rt.is_active = 1
        ");
        $stmt->execute([$templateId]);
        $template = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$template) {
            http_response_code(404);
            echo json_encode(['error' => 'Template not found']);
            return;
        }
        
        // Get streams
        $streamStmt = $conn->prepare("
            SELECT name, color 
            FROM roadmap_streams 
            WHERE template_id = ? 
            ORDER BY sort_order ASC
        ");
        $streamStmt->execute([$templateId]);
        $streams = $streamStmt->fetchAll(PDO::FETCH_COLUMN);
        $template['streams'] = $streams;
        
        // Get projects with action points
        $projectStmt = $conn->prepare("
            SELECT rp.id, rp.name, rp.start_date as startDate, rp.end_date as endDate, 
                   rp.stream_name as stream, rp.description
            FROM roadmap_projects rp
            WHERE rp.template_id = ?
            ORDER BY rp.start_date ASC
        ");
        $projectStmt->execute([$templateId]);
        $projects = $projectStmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($projects as &$project) {
            $actionStmt = $conn->prepare("
                SELECT description 
                FROM roadmap_action_points 
                WHERE project_id = ? 
                ORDER BY sort_order ASC
            ");
            $actionStmt->execute([$project['id']]);
            $actionPoints = $actionStmt->fetchAll(PDO::FETCH_COLUMN);
            $project['actionPoints'] = $actionPoints;
        }
        
        $template['projects'] = $projects;
        
        echo json_encode($template);
        
    } catch (PDOException $e) {
        error_log("Get template error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['error' => 'Database error']);
    }
}

function createTemplate($conn) {
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input || !isset($input['name']) || !isset($input['streams'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required fields: name, streams']);
            return;
        }
        
        $name = trim($input['name']);
        $description = trim($input['description'] ?? '');
        $streams = $input['streams'] ?? [];
        $projects = $input['projects'] ?? [];
        $createdBy = $_SESSION['user_id'] ?? 1; // Default to user 1 if no session
        
        if (empty($name) || empty($streams)) {
            http_response_code(400);
            echo json_encode(['error' => 'Name and streams are required']);
            return;
        }
        
        $conn->beginTransaction();
        
        // Create template
        $stmt = $conn->prepare("
            INSERT INTO roadmap_templates (name, description, created_by) 
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$name, $description, $createdBy]);
        $templateId = $conn->lastInsertId();
        
        // Create streams
        $streamStmt = $conn->prepare("
            INSERT INTO roadmap_streams (template_id, name, sort_order) 
            VALUES (?, ?, ?)
        ");
        foreach ($streams as $index => $streamName) {
            $streamStmt->execute([$templateId, $streamName, $index + 1]);
        }
        
        // Create projects if provided
        if (!empty($projects)) {
            $projectStmt = $conn->prepare("
                INSERT INTO roadmap_projects (template_id, name, start_date, end_date, stream_name, description) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            
            $actionStmt = $conn->prepare("
                INSERT INTO roadmap_action_points (project_id, description, sort_order) 
                VALUES (?, ?, ?)
            ");
            
            foreach ($projects as $project) {
                $projectStmt->execute([
                    $templateId,
                    $project['name'],
                    $project['startDate'],
                    $project['endDate'],
                    $project['stream'],
                    $project['description'] ?? ''
                ]);
                
                $projectId = $conn->lastInsertId();
                
                // Add action points
                if (!empty($project['actionPoints'])) {
                    foreach ($project['actionPoints'] as $index => $actionPoint) {
                        $actionStmt->execute([$projectId, $actionPoint, $index + 1]);
                    }
                }
            }
        }
        
        $conn->commit();
        
        // Return the created template
        getTemplate($conn, $templateId);
        
    } catch (PDOException $e) {
        $conn->rollBack();
        error_log("Create template error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['error' => 'Database error']);
    }
}

function updateTemplate($conn, $templateId) {
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid JSON input']);
            return;
        }
        
        $conn->beginTransaction();
        
        // Update template basic info
        if (isset($input['name']) || isset($input['description'])) {
            $stmt = $conn->prepare("UPDATE roadmap_templates SET name = ?, description = ? WHERE id = ?");
            $stmt->execute([
                $input['name'] ?? '',
                $input['description'] ?? '',
                $templateId
            ]);
        }
        
        // Update streams if provided
        if (isset($input['streams'])) {
            // Delete existing streams
            $conn->prepare("DELETE FROM roadmap_streams WHERE template_id = ?")->execute([$templateId]);
            
            // Insert new streams
            $streamStmt = $conn->prepare("INSERT INTO roadmap_streams (template_id, name, sort_order) VALUES (?, ?, ?)");
            foreach ($input['streams'] as $index => $streamName) {
                $streamStmt->execute([$templateId, $streamName, $index + 1]);
            }
        }
        
        // Update projects if provided
        if (isset($input['projects'])) {
            // Delete existing projects (cascades to action points)
            $conn->prepare("DELETE FROM roadmap_projects WHERE template_id = ?")->execute([$templateId]);
            
            // Insert new projects
            $projectStmt = $conn->prepare("
                INSERT INTO roadmap_projects (template_id, name, start_date, end_date, stream_name, description) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            
            $actionStmt = $conn->prepare("
                INSERT INTO roadmap_action_points (project_id, description, sort_order) 
                VALUES (?, ?, ?)
            ");
            
            foreach ($input['projects'] as $project) {
                $projectStmt->execute([
                    $templateId,
                    $project['name'],
                    $project['startDate'],
                    $project['endDate'],
                    $project['stream'],
                    $project['description'] ?? ''
                ]);
                
                $projectId = $conn->lastInsertId();
                
                // Add action points
                if (!empty($project['actionPoints'])) {
                    foreach ($project['actionPoints'] as $index => $actionPoint) {
                        $actionStmt->execute([$projectId, $actionPoint, $index + 1]);
                    }
                }
            }
        }
        
        $conn->commit();
        
        // Return the updated template
        getTemplate($conn, $templateId);
        
    } catch (PDOException $e) {
        $conn->rollBack();
        error_log("Update template error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['error' => 'Database error']);
    }
}

function deleteTemplate($conn, $templateId) {
    try {
        $stmt = $conn->prepare("UPDATE roadmap_templates SET is_active = 0 WHERE id = ?");
        $stmt->execute([$templateId]);
        
        if ($stmt->rowCount() === 0) {
            http_response_code(404);
            echo json_encode(['error' => 'Template not found']);
            return;
        }
        
        echo json_encode(['message' => 'Template deleted successfully']);
        
    } catch (PDOException $e) {
        error_log("Delete template error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['error' => 'Database error']);
    }
}

function seedTemplates($conn) {
    try {
        // Default templates data
        $defaultTemplates = [
            [
                'name' => 'Product Roadmap',
                'description' => 'Core product streams for feature planning',
                'streams' => ['Growth', 'Retention', 'Platform', 'Infrastructure', 'Experience'],
                'projects' => [
                    ['name' => 'User Onboarding v2', 'startDate' => '2025-01-15', 'endDate' => '2025-03-20', 'stream' => 'Growth', 'actionPoints' => ['Redesign signup flow', 'Add social login', 'Create tutorial videos']],
                    ['name' => 'Analytics Dashboard', 'startDate' => '2025-02-10', 'endDate' => '2025-05-15', 'stream' => 'Platform', 'actionPoints' => ['Define key metrics', 'Build data pipeline', 'Design UI mockups']],
                    ['name' => 'Mobile App MVP', 'startDate' => '2025-03-01', 'endDate' => '2025-07-30', 'stream' => 'Growth', 'actionPoints' => ['iOS development', 'Android development', 'App store optimization']],
                    ['name' => 'Customer Success Portal', 'startDate' => '2025-04-05', 'endDate' => '2025-06-25', 'stream' => 'Retention', 'actionPoints' => ['Self-service knowledge base', 'Ticket system', 'Live chat integration']],
                    ['name' => 'Infrastructure Upgrade', 'startDate' => '2025-06-01', 'endDate' => '2025-09-15', 'stream' => 'Infrastructure', 'actionPoints' => ['Migrate to cloud', 'Implement CI/CD', 'Security audit']],
                ]
            ],
            [
                'name' => 'Digital Marketing Plan',
                'description' => 'Marketing channels and campaign planning',
                'streams' => ['SEO', 'Paid Ads', 'Social Media', 'Email Marketing', 'Content'],
                'projects' => [
                    ['name' => 'SEO Audit & Revamp', 'startDate' => '2025-01-10', 'endDate' => '2025-03-25', 'stream' => 'SEO', 'actionPoints' => ['Keyword research', 'On-page optimization', 'Backlink strategy']],
                    ['name' => 'Google Ads Launch', 'startDate' => '2025-02-05', 'endDate' => '2025-04-15', 'stream' => 'Paid Ads', 'actionPoints' => ['Campaign setup', 'Ad copy creation', 'Budget allocation']],
                    ['name' => 'Instagram Growth', 'startDate' => '2025-03-01', 'endDate' => '2025-06-20', 'stream' => 'Social Media', 'actionPoints' => ['Content calendar', 'Influencer outreach', 'Reels strategy']],
                    ['name' => 'Newsletter Relaunch', 'startDate' => '2025-04-10', 'endDate' => '2025-05-30', 'stream' => 'Email Marketing', 'actionPoints' => ['Template redesign', 'Segmentation setup', 'A/B subject lines']],
                ]
            ],
            [
                'name' => 'Sales & CRM',
                'description' => 'Sales pipeline and lead management',
                'streams' => ['Lead Generation', 'Outreach', 'Pipeline', 'Closing', 'Account Management'],
                'projects' => [
                    ['name' => 'ICP Definition', 'startDate' => '2025-01-05', 'endDate' => '2025-02-28', 'stream' => 'Lead Generation', 'actionPoints' => ['Market research', 'Persona creation', 'Scoring model']],
                    ['name' => 'Cold Outreach System', 'startDate' => '2025-02-15', 'endDate' => '2025-04-20', 'stream' => 'Outreach', 'actionPoints' => ['Email sequences', 'LinkedIn automation', 'Call scripts']],
                    ['name' => 'CRM Implementation', 'startDate' => '2025-03-10', 'endDate' => '2025-06-30', 'stream' => 'Pipeline', 'actionPoints' => ['HubSpot setup', 'Pipeline stages', 'Reporting dashboards']],
                ]
            ]
        ];

        $conn->beginTransaction();

        foreach ($defaultTemplates as $templateData) {
            // Insert template
            $stmt = $conn->prepare("
                INSERT INTO roadmap_templates (name, description, created_at, updated_at, is_active) 
                VALUES (?, ?, NOW(), NOW(), 1)
            ");
            $stmt->execute([$templateData['name'], $templateData['description']]);
            $templateId = $conn->lastInsertId();

            // Insert streams
            foreach ($templateData['streams'] as $index => $streamName) {
                $streamStmt = $conn->prepare("
                    INSERT INTO roadmap_streams (template_id, name, color, sort_order) 
                    VALUES (?, ?, ?, ?)
                ");
                $color = ['#10b981', '#3b82f6', '#8b5cf6', '#f59e0b', '#ec4899'][$index % 5];
                $streamStmt->execute([$templateId, $streamName, $color, $index]);
            }

            // Insert projects
            foreach ($templateData['projects'] as $projectData) {
                $projectStmt = $conn->prepare("
                    INSERT INTO roadmap_projects (template_id, name, start_date, end_date, stream_name, description) 
                    VALUES (?, ?, ?, ?, ?, ?)
                ");
                $projectStmt->execute([
                    $templateId, 
                    $projectData['name'], 
                    $projectData['startDate'], 
                    $projectData['endDate'], 
                    $projectData['stream'], 
                    ''
                ]);
                $projectId = $conn->lastInsertId();

                // Insert action points
                foreach ($projectData['actionPoints'] as $index => $actionPoint) {
                    $actionStmt = $conn->prepare("
                        INSERT INTO roadmap_action_points (project_id, description, sort_order) 
                        VALUES (?, ?, ?)
                    ");
                    $actionStmt->execute([$projectId, $actionPoint, $index]);
                }
            }
        }

        $conn->commit();
        echo json_encode(['message' => 'Sample templates created successfully']);
        
    } catch (Exception $e) {
        $conn->rollBack();
        error_log("Seed templates error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['error' => 'Failed to seed templates: ' . $e->getMessage()]);
    }
}

// Flush output buffer
if (ob_get_level()) {
    ob_end_flush();
}
?>
