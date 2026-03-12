<?php
require_once './api/config/database.php';

echo "🎯 FINAL SYSTEM STATUS REPORT\n";
echo "=============================\n\n";

// 1. Database Connection Test
echo "1️⃣  Database Connection\n";
echo "   ---------------------\n";
try {
    $database = new Database();
    $pdo = $database->getConnection();
    
    if ($pdo === null) {
        echo "   ❌ Database connection FAILED\n";
        exit(1);
    }
    
    // Get database info
    $stmt = $pdo->query("SELECT DATABASE() as db_name");
    $dbInfo = $stmt->fetch();
    echo "   ✅ Connection: SUCCESS\n";
    echo "   📂 Database: " . $dbInfo['db_name'] . "\n";
    echo "   🌐 Environment: " . (strpos($_SERVER['SCRIPT_FILENAME'] ?? '', 'inetpub') !== false ? 'PRODUCTION' : 'LOCAL') . "\n";
    
} catch (Exception $e) {
    echo "   ❌ Database Error: " . $e->getMessage() . "\n";
    exit(1);
}

// 2. Table Status
echo "\n2️⃣  Database Tables\n";
echo "   ----------------\n";
$requiredTables = [
    'users' => 'User management',
    'teams' => 'Team organization', 
    'projects' => 'Project tracking',
    'work_items' => 'Work item management',
    'project_members' => 'Project memberships',
    'team_members' => 'Team memberships'
];

$totalTables = 0;
$existingTables = 0;

foreach ($requiredTables as $table => $description) {
    $totalTables++;
    try {
        $stmt = $pdo->prepare("SHOW TABLES LIKE ?");
        $stmt->execute([$table]);
        $exists = $stmt->fetch() !== false;
        
        if ($exists) {
            $countStmt = $pdo->query("SELECT COUNT(*) as count FROM $table");
            $count = $countStmt->fetch();
            echo "   ✅ $table (" . $count['count'] . " records) - $description\n";
            $existingTables++;
        } else {
            echo "   ❌ $table - MISSING - $description\n";
        }
    } catch (Exception $e) {
        echo "   ⚠️  $table - ERROR: " . $e->getMessage() . "\n";
    }
}

// 3. API Endpoints
echo "\n3️⃣  API Endpoints\n";
echo "   --------------\n";
$apiFiles = [
    'users.php' => 'User management API',
    'teams.php' => 'Team management API',
    'projects.php' => 'Project management API', 
    'work-items.php' => 'Work items API',
    'auth.php' => 'Authentication API'
];

$totalAPIs = 0;
$existingAPIs = 0;

foreach ($apiFiles as $file => $description) {
    $totalAPIs++;
    $filePath = './api/' . $file;
    if (file_exists($filePath)) {
        echo "   ✅ $file - $description\n";
        $existingAPIs++;
    } else {
        echo "   ❌ $file - MISSING - $description\n";
    }
}

// 4. Sample Data Test
echo "\n4️⃣  Sample Data Test\n";
echo "   ------------------\n";
try {
    // Test users
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE is_active = 1");
    $users = $stmt->fetch();
    echo "   👥 Active Users: " . $users['count'] . "\n";
    
    // Test projects  
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM projects");
    $projects = $stmt->fetch();
    echo "   📂 Projects: " . $projects['count'] . "\n";
    
    // Test work items
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM work_items");
    $workItems = $stmt->fetch();
    echo "   📝 Work Items: " . $workItems['count'] . "\n";
    
    // Test teams
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM teams WHERE is_active = 1");
    $teams = $stmt->fetch();
    echo "   🏢 Active Teams: " . $teams['count'] . "\n";
    
} catch (Exception $e) {
    echo "   ❌ Data test error: " . $e->getMessage() . "\n";
}

// 5. System Information
echo "\n5️⃣  System Information\n";
echo "   -------------------\n";
echo "   🐘 PHP Version: " . PHP_VERSION . "\n";
echo "   ⏰ Server Time: " . date('Y-m-d H:i:s') . "\n";
echo "   🌍 Timezone: " . date_default_timezone_get() . "\n";
echo "   💾 Memory Limit: " . ini_get('memory_limit') . "\n";

// 6. Quick Health Status
echo "\n6️⃣  Overall Health Status\n";
echo "   -----------------------\n";

$issues = [];
if ($existingTables < $totalTables) {
    $issues[] = "Missing " . ($totalTables - $existingTables) . " required tables";
}
if ($existingAPIs < $totalAPIs) {
    $issues[] = "Missing " . ($totalAPIs - $existingAPIs) . " API endpoints";
}

if (empty($issues)) {
    echo "   🎉 STATUS: HEALTHY - All systems operational!\n";
    echo "   ✅ Database: Connected\n";
    echo "   ✅ Tables: $existingTables/$totalTables available\n";
    echo "   ✅ APIs: $existingAPIs/$totalAPIs available\n";
} else {
    echo "   ⚠️  STATUS: DEGRADED - Some issues found:\n";
    foreach ($issues as $issue) {
        echo "      • $issue\n";
    }
}

// 7. Quick Access URLs
echo "\n7️⃣  Quick Access\n";
echo "   -------------\n";
$baseUrl = 'http://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . dirname($_SERVER['REQUEST_URI'] ?? '');
echo "   🔍 Health Check: $baseUrl/health-check.html\n";
echo "   📊 Database Check: $baseUrl/check-database.php\n";
echo "   🧪 API Tests: $baseUrl/test-api.php\n";
echo "   📋 Table Structures: $baseUrl/check-all-tables.php\n";

echo "\n" . str_repeat("=", 50) . "\n";
echo "✅ System check completed successfully!\n";
echo "📅 Report generated: " . date('Y-m-d H:i:s T') . "\n";
?>
