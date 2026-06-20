<?php
/**
 * HealthCare Pro - Application Bootstrap
 * 
 * This is the main entry point for the application
 */

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set error handler
ini_set('display_errors', 0);

// Load configuration
require_once __DIR__ . '/../config/config.php';

// Autoloader - load classes
spl_autoload_register(function ($class) {
    // Try helpers first
    $helperFile = MODELS_PATH . '/../helpers/' . $class . '.php';
    if (file_exists($helperFile)) {
        require $helperFile;
        return;
    }

    // Try models
    $modelFile = MODELS_PATH . '/' . $class . '.php';
    if (file_exists($modelFile)) {
        require $modelFile;
        return;
    }

    // Try controllers
    $controllerFile = CONTROLLERS_PATH . '/' . $class . '.php';
    if (file_exists($controllerFile)) {
        require $controllerFile;
        return;
    }

    // Try middleware
    $middlewareFile = APP_PATH . '/middleware/' . $class . '.php';
    if (file_exists($middlewareFile)) {
        require $middlewareFile;
        return;
    }
});

// Initialize core helpers
require_once APP_PATH . '/helpers/Database.php';
require_once APP_PATH . '/helpers/Auth.php';
require_once APP_PATH . '/helpers/Validator.php';
require_once APP_PATH . '/middleware/Authenticate.php';

// Check session timeout
Auth::checkTimeout();

// Define request method and path
define('REQUEST_METHOD', $_SERVER['REQUEST_METHOD']);
define('REQUEST_PATH', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Error handling function
function handleError($message, $code = 400)
{
    if (APP_DEBUG) {
        http_response_code($code);
        die(json_encode(['error' => $message]));
    }
    error_log($message);
    http_response_code($code);
    die('An error occurred. Please try again later.');
}

// Success response helper
function successResponse($data, $message = 'Success', $code = 200)
{
    http_response_code($code);
    return json_encode([
        'status' => 'success',
        'message' => $message,
        'data' => $data
    ]);
}

// Error response helper
function errorResponse($message, $code = 400)
{
    http_response_code($code);
    return json_encode([
        'status' => 'error',
        'message' => $message
    ]);
}

// Routing - basic dispatcher
class Router
{
    private static $routes = [];

    public static function get($path, $callback)
    {
        self::$routes['GET'][$path] = $callback;
    }

    public static function post($path, $callback)
    {
        self::$routes['POST'][$path] = $callback;
    }

    public static function put($path, $callback)
    {
        self::$routes['PUT'][$path] = $callback;
    }

    public static function delete($path, $callback)
    {
        self::$routes['DELETE'][$path] = $callback;
    }

    public static function dispatch()
    {
        $method = REQUEST_METHOD;
        $path = REQUEST_PATH;

        // Remove public prefix
        $path = str_replace('/public', '', $path);

        if (isset(self::$routes[$method][$path])) {
            call_user_func(self::$routes[$method][$path]);
        } else {
            http_response_code(404);
            die('404 Not Found');
        }
    }
}

?>
