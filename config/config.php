<?php
/**
 * HealthCare Pro - Application Configuration
 * 
 * This file contains core application settings
 */

// Load environment variables from .env
require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

// Load .env file if it exists
if (file_exists(__DIR__ . '/../.env')) {
    $dotenv = Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->load();
}

// Application Settings
define('APP_NAME', $_ENV['APP_NAME'] ?? 'HealthCare Pro');
define('APP_ENV', $_ENV['APP_ENV'] ?? 'production');
define('APP_DEBUG', $_ENV['APP_DEBUG'] === 'true' ?? false);
define('APP_URL', $_ENV['APP_URL'] ?? 'http://localhost:8000');

// Database Settings
define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');
define('DB_PORT', $_ENV['DB_PORT'] ?? 3306);
define('DB_NAME', $_ENV['DB_NAME'] ?? 'healthcare_pro');
define('DB_USER', $_ENV['DB_USER'] ?? 'root');
define('DB_PASS', $_ENV['DB_PASS'] ?? '');

// Session Settings
define('SESSION_LIFETIME', $_ENV['SESSION_LIFETIME'] ?? 1440);
define('SESSION_DRIVER', $_ENV['SESSION_DRIVER'] ?? 'file');

// Security Settings
define('PASSWORD_HASH_ALGO', $_ENV['PASSWORD_HASH_ALGO'] ?? 'bcrypt');
define('BCRYPT_ROUNDS', (int)($_ENV['BCRYPT_ROUNDS'] ?? 10));

// Timezone
define('APP_TIMEZONE', $_ENV['APP_TIMEZONE'] ?? 'UTC');
date_default_timezone_set(APP_TIMEZONE);

// Error Reporting
if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/../storage/logs/error.log');
}

// Paths
define('BASE_PATH', realpath(__DIR__ . '/..'));
define('APP_PATH', BASE_PATH . '/app');
define('VIEWS_PATH', APP_PATH . '/views');
define('MODELS_PATH', APP_PATH . '/models');
define('CONTROLLERS_PATH', APP_PATH . '/controllers');
define('CONFIG_PATH', BASE_PATH . '/config');
define('STORAGE_PATH', BASE_PATH . '/storage');
define('LOGS_PATH', STORAGE_PATH . '/logs');
define('UPLOADS_PATH', STORAGE_PATH . '/uploads');

// User Roles
define('ROLE_SUPER_ADMIN', 'super_admin');
define('ROLE_DOCTOR', 'doctor');
define('ROLE_RECEPTIONIST', 'receptionist');
define('ROLE_PATIENT', 'patient');

return [
    'app' => [
        'name' => APP_NAME,
        'env' => APP_ENV,
        'debug' => APP_DEBUG,
        'url' => APP_URL,
    ],
    'database' => [
        'host' => DB_HOST,
        'port' => DB_PORT,
        'name' => DB_NAME,
        'user' => DB_USER,
        'pass' => DB_PASS,
    ],
];
?>
