<?php
// Database configuration
// Load environment variables from .env file (for shared hosting)
require_once __DIR__ . '/env_loader.php';
function getDatabaseConnection() {
    // Check if we're in Replit environment (has DATABASE_URL environment variable)
    $database_url = $_ENV['DATABASE_URL'] ?? getenv('DATABASE_URL');
    
    if ($database_url) {
        // Replit PostgreSQL configuration
        $db_info = parse_url($database_url);
        $host = $db_info['host'];
        $port = $db_info['port'];
        $dbname = ltrim($db_info['path'], '/');
        $user = $db_info['user'];
        $password = $db_info['pass'];
        
        try {
            $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (PDOException $e) {
            die('Connection failed: ' . $e->getMessage());
        }
    } else {
        // Production MySQL configuration using environment variables
        $host = $_ENV['DB_HOST'] ?? getenv('DB_HOST') ?? 'localhost';
        $dbname = $_ENV['DB_NAME'] ?? getenv('DB_NAME');
        $username = $_ENV['DB_USER'] ?? getenv('DB_USER');
        $password = $_ENV['DB_PASS'] ?? getenv('DB_PASS');
        $port = $_ENV['DB_PORT'] ?? getenv('DB_PORT') ?? '3306';
        
        // Validate that required credentials are available
        if (!$dbname || !$username || !$password) {
            die('Database credentials not found. Please set DB_NAME, DB_USER, and DB_PASS environment variables.');
        }
        
        try {
            // Use MySQL PDO connection for production
            $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            return $pdo;
        } catch (PDOException $e) {
            error_log('Database connection failed: ' . $e->getMessage());
            die('Database connection failed. Please check your configuration.');
        }
    }
}

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>