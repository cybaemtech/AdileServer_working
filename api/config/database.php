<?php
class Database {
    private $conn;

    public function getConnection() {
        $this->conn = null;
        
        try {
            // Auto-detect environment: more accurate detection
            $httpHost = $_SERVER['HTTP_HOST'] ?? '';
            $serverName = $_SERVER['SERVER_NAME'] ?? '';
            $scriptPath = $_SERVER['SCRIPT_FILENAME'] ?? '';
            
            $isLocal = (
                $httpHost === 'localhost:5000' ||
                $httpHost === 'localhost' ||
                $httpHost === 'localhost:3000' ||
                $httpHost === '127.0.0.1:5000' ||
                $serverName === 'localhost' ||
                (php_sapi_name() === 'cli' && strpos($scriptPath, 'xampp') !== false)
            );
            
            // Force production for the production domain or IIS server
            if (
                $httpHost === 'agile.cybaemtech.app' || 
                $serverName === 'agile.cybaemtech.app' ||
                strpos($scriptPath, 'inetpub') !== false ||
                $httpHost === 'agile.cybaemtech.app:90'
            ) {
                $isLocal = false;
            }
            
            if ($isLocal) {
                // Local XAMPP configuration
                $host = 'localhost';
                $port = '3306';
                $dbname = 'agile';
                $username = 'root';  
                $password = '';
            } else {
                // Production cPanel configuration
                $host = '10.0.0.49';  // Updated to correct MySQL server IP
                $port = '3306';
                $dbname = 'cybaemtechin_agile';
                $username = 'cybaemtechin_agile';  
                $password = 'Agile@9090$';
            }
            
            $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
            
            $this->conn = new PDO($dsn, $username, $password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
            // Test the connection with a simple query
            $testStmt = $this->conn->prepare("SELECT 1");
            $testStmt->execute();
            
            // Log successful connection (for debugging)
            error_log("Database connection successful to: $dbname on " . ($isLocal ? 'LOCAL' : 'PRODUCTION') . " environment");
            
        } catch(PDOException $exception) {
            // Log connection error with details for debugging
            error_log("Database connection error: " . $exception->getMessage());
            error_log("DSN used: " . $dsn);
            error_log("Username: " . $username);
            error_log("Environment: " . ($isLocal ? 'LOCAL' : 'PRODUCTION'));
            
            // Don't expose connection details in production
            $this->conn = null;
        }
        return $this->conn;
    }
}
?>