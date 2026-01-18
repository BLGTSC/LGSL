<?php
/**
 * CSX16-SERVER STATS - Configuration File
 * Upload this to: csx16.ro/servers/backend/config.php
 */

// Database Credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'your_database_user');     // CHANGE THIS
define('DB_PASS', 'your_database_password'); // CHANGE THIS
define('DB_NAME', 'your_database_name');     // CHANGE THIS

// Application Settings
define('SITE_URL', 'https://csx16.ro/servers');
define('DEBUG_MODE', false); // Set to true for development, false for production

// Database Connection (PDO)
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
    // In production, log this instead of showing it
    if (DEBUG_MODE) {
        die("Connection failed: " . $e->getMessage());
    } else {
        die("Database connection error. Please check config.php.");
    }
}

// CORS Headers (Allow requests from the frontend)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}
?>