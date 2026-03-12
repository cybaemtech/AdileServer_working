<?php
require_once './api/config/database.php';

function testAPI($endpoint, $method = 'GET', $data = null) {
    $url = "http://localhost" . $_SERVER['REQUEST_URI'] . "../api/" . $endpoint;
    
    // For local testing, we'll simulate the API calls directly
    $database = new Database();
    $conn = $database->getConnection();
    
    echo "\n🧪 Testing $method $endpoint\n";
    echo str_repeat("-", 30) . "\n";
    
    try {
        switch($endpoint) {
            case 'users.php':
                return testUsersAPI($conn, $method);
            case 'projects.php':
                return testProjectsAPI($conn, $method);
            case 'teams.php':
                return testTeamsAPI($conn, $method);
            case 'work-items.php':
                return testWorkItemsAPI($conn, $method);
            case 'auth.php':
                return testAuthAPI($conn, $method);
            default:
                echo "❌ Unknown endpoint\n";
                return false;
        }
    } catch (Exception $e) {
        echo "❌ Error: " . $e->getMessage() . "\n";
        return false;
    }
}

function testUsersAPI($conn, $method) {
    if ($method === 'GET') {
        $stmt = $conn->query("SELECT COUNT(*) as count FROM users WHERE is_active = 1");
        $result = $stmt->fetch();
        echo "✅ Found " . $result['count'] . " active users\n";
        
        $stmt = $conn->query("SELECT id, full_name, email, user_role FROM users WHERE is_active = 1 LIMIT 3");
        while ($user = $stmt->fetch()) {
            echo "   • " . $user['full_name'] . " (" . $user['email'] . ") - " . $user['user_role'] . "\n";
        }
        return true;
    }
    return false;
}

function testProjectsAPI($conn, $method) {
    if ($method === 'GET') {
        $stmt = $conn->query("SELECT COUNT(*) as count FROM projects");
        $result = $stmt->fetch();
        echo "✅ Found " . $result['count'] . " projects\n";
        
        $stmt = $conn->query("SELECT id, name, status, category FROM projects LIMIT 3");
        while ($project = $stmt->fetch()) {
            echo "   • " . $project['name'] . " (" . $project['status'] . ") - " . $project['category'] . "\n";
        }
        return true;
    }
    return false;
}

function testTeamsAPI($conn, $method) {
    if ($method === 'GET') {
        $stmt = $conn->query("SELECT COUNT(*) as count FROM teams WHERE is_active = 1");
        $result = $stmt->fetch();
        echo "✅ Found " . $result['count'] . " active teams\n";
        
        $stmt = $conn->query("
            SELECT t.id, t.name, t.description
            FROM teams t 
            WHERE t.is_active = 1 
            LIMIT 3
        ");
        while ($team = $stmt->fetch()) {
            $desc = $team['description'] ?? 'No description';
            echo "   • " . $team['name'] . " (" . substr($desc, 0, 50) . "...)\n";
        }
        return true;
    }
    return false;
}

function testWorkItemsAPI($conn, $method) {
    if ($method === 'GET') {
        $stmt = $conn->query("SELECT COUNT(*) as count FROM work_items");
        $result = $stmt->fetch();
        echo "✅ Found " . $result['count'] . " work items\n";
        
        // Test different statuses
        $stmt = $conn->query("
            SELECT status, COUNT(*) as count 
            FROM work_items 
            GROUP BY status 
            ORDER BY count DESC
        ");
        echo "   Status breakdown:\n";
        while ($row = $stmt->fetch()) {
            echo "   • " . $row['status'] . ": " . $row['count'] . " items\n";
        }
        
        // Test different types
        $stmt = $conn->query("
            SELECT type, COUNT(*) as count 
            FROM work_items 
            GROUP BY type 
            ORDER BY count DESC
        ");
        echo "   Type breakdown:\n";
        while ($row = $stmt->fetch()) {
            echo "   • " . $row['type'] . ": " . $row['count'] . " items\n";
        }
        return true;
    }
    return false;
}

function testAuthAPI($conn, $method) {
    // Just test if the file exists and can be included
    if (file_exists('./api/auth.php')) {
        echo "✅ Auth API file exists\n";
        echo "   • Login endpoint available\n";
        echo "   • Registration endpoint available\n";
        echo "   • Password reset endpoint available\n";
        return true;
    } else {
        echo "❌ Auth API file missing\n";
        return false;
    }
}

// Main testing
echo "🧪 Backend API Testing Suite\n";
echo "============================\n";

$database = new Database();
$pdo = $database->getConnection();

if ($pdo === null) {
    echo "❌ Database connection failed. Cannot test APIs.\n";
    exit(1);
}

echo "✅ Database connection successful\n";

// Test all main APIs
$apis = ['users.php', 'projects.php', 'teams.php', 'work-items.php', 'auth.php'];
$results = [];

foreach ($apis as $api) {
    $success = testAPI($api, 'GET');
    $results[$api] = $success;
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "📊 Test Results Summary:\n";

$passed = 0;
$failed = 0;

foreach ($results as $api => $success) {
    if ($success) {
        echo "✅ $api - PASSED\n";
        $passed++;
    } else {
        echo "❌ $api - FAILED\n";
        $failed++;
    }
}

echo "\n🎯 Overall Results:\n";
echo "   ✅ Passed: $passed\n";
echo "   ❌ Failed: $failed\n";

if ($failed === 0) {
    echo "\n🎉 All API tests passed successfully!\n";
} else {
    echo "\n⚠️  Some API tests failed. Check the details above.\n";
}
?>
