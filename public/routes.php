<?php
/**
 * Application Routes
 * 
 * Define application routes and dispatch to appropriate controllers
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load configuration
require_once __DIR__ . '/../config/config.php';

// Auto-load classes
spl_autoload_register(function ($class) {
    $paths = [
        APP_PATH . '/helpers/',
        APP_PATH . '/models/',
        APP_PATH . '/controllers/',
        APP_PATH . '/middleware/'
    ];

    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) {
            require $file;
            return;
        }
    }
});

// Load helpers and middleware
require_once APP_PATH . '/helpers/Database.php';
require_once APP_PATH . '/helpers/Auth.php';
require_once APP_PATH . '/helpers/Validator.php';
require_once APP_PATH . '/middleware/Authenticate.php';

// Get request path
$request_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$request_path = str_replace('/public', '', $request_path);
$request_path = str_replace('/index.php', '', $request_path);

// Route dispatcher
switch ($request_path) {
    // Authentication routes
    case '/':
    case '/index.php':
        if (Auth::check()) {
            header('Location: /public/dashboard');
            exit;
        }
        $controller = new AuthController();
        $controller->showLogin();
        break;

    case '/login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller = new AuthController();
            $controller->login();
        } else {
            $controller = new AuthController();
            $controller->showLogin();
        }
        break;

    case '/register':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller = new AuthController();
            $controller->register();
        } else {
            $controller = new AuthController();
            $controller->showRegister();
        }
        break;

    case '/logout':
        $controller = new AuthController();
        $controller->logout();
        break;

    // Dashboard
    case '/dashboard':
        $controller = new DashboardController();
        $controller->index();
        break;

    // Default 404
    default:
        http_response_code(404);
        echo "404 - Page not found: " . htmlspecialchars($request_path);
        break;
}
?>
