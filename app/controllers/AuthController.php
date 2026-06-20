<?php
/**
 * Authentication Controller
 * 
 * Handles login, logout, and user registration
 */

class AuthController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    /**
     * Show login page
     */
    public function showLogin()
    {
        if (Auth::check()) {
            header('Location: ' . APP_URL . '/dashboard');
            exit;
        }
        include VIEWS_PATH . '/auth/login.php';
    }

    /**
     * Process login
     */
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            die('Method Not Allowed');
        }

        $email = Validator::sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        // Validate input
        if (!Validator::required($email) || !Validator::required($password)) {
            return $this->loginError('Email and password are required');
        }

        if (!Validator::email($email)) {
            return $this->loginError('Invalid email format');
        }

        // Find user by email
        $user = $this->userModel->findByEmail($email);

        if (!$user) {
            return $this->loginError('Invalid credentials');
        }

        // Check if user is active
        if (!$user['is_active']) {
            return $this->loginError('Account is inactive');
        }

        // Verify password
        if (!Auth::verifyPassword($password, $user['password_hash'])) {
            return $this->loginError('Invalid credentials');
        }

        // Get user role
        $userWithRole = $this->userModel->withRole($user['id']);

        // Set session
        Auth::login($user['id'], $userWithRole['role_name'], $user['email']);

        // Log audit
        $this->logAudit($user['id'], 'LOGIN_SUCCESS', $_SERVER['REMOTE_ADDR'] ?? '');

        // Redirect to dashboard
        header('Location: ' . APP_URL . '/dashboard');
        exit;
    }

    /**
     * Show registration page
     */
    public function showRegister()
    {
        if (Auth::check()) {
            header('Location: ' . APP_URL . '/dashboard');
            exit;
        }
        include VIEWS_PATH . '/auth/register.php';
    }

    /**
     * Process registration
     */
    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            die('Method Not Allowed');
        }

        $email = Validator::sanitize($_POST['email'] ?? '');
        $firstName = Validator::sanitize($_POST['first_name'] ?? '');
        $lastName = Validator::sanitize($_POST['last_name'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $roleId = (int)($_POST['role_id'] ?? 4); // Default to Patient role

        // Validate input
        $errors = [];

        if (!Validator::required($email)) {
            $errors[] = 'Email is required';
        } elseif (!Validator::email($email)) {
            $errors[] = 'Invalid email format';
        } elseif ($this->userModel->findByEmail($email)) {
            $errors[] = 'Email already registered';
        }

        if (!Validator::required($firstName)) {
            $errors[] = 'First name is required';
        }

        if (!Validator::required($lastName)) {
            $errors[] = 'Last name is required';
        }

        if (!Validator::required($password)) {
            $errors[] = 'Password is required';
        } elseif (!Validator::password($password)) {
            $errors[] = 'Password must be at least 8 characters with uppercase, lowercase, number, and special character';
        }

        if ($password !== $confirmPassword) {
            $errors[] = 'Passwords do not match';
        }

        if (!empty($errors)) {
            $_SESSION['registration_errors'] = $errors;
            header('Location: ' . APP_URL . '/register');
            exit;
        }

        // Create user
        try {
            $userId = $this->userModel->register([
                'role_id' => $roleId,
                'email' => $email,
                'password' => $password,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'is_active' => 1
            ]);

            // If patient role, create patient profile
            if ($roleId === 4) {
                $patientModel = new Patient();
                $patientModel->create(['user_id' => $userId]);
            }

            $_SESSION['registration_success'] = 'Registration successful! Please login.';
            $this->logAudit($userId, 'REGISTER_SUCCESS', $_SERVER['REMOTE_ADDR'] ?? '');
            
            header('Location: ' . APP_URL . '/login');
            exit;
        } catch (Exception $e) {
            error_log('Registration error: ' . $e->getMessage());
            $_SESSION['registration_errors'] = ['An error occurred during registration'];
            header('Location: ' . APP_URL . '/register');
            exit;
        }
    }

    /**
     * Logout user
     */
    public function logout()
    {
        $this->logAudit(Auth::id(), 'LOGOUT', $_SERVER['REMOTE_ADDR'] ?? '');
        Auth::logout();
    }

    /**
     * Handle login error
     * 
     * @param string $message
     */
    private function loginError($message)
    {
        $_SESSION['login_error'] = $message;
        header('Location: ' . APP_URL . '/login');
        exit;
    }

    /**
     * Log audit entry
     * 
     * @param int $userId
     * @param string $action
     * @param string $ipAddress
     */
    private function logAudit($userId, $action, $ipAddress)
    {
        try {
            $db = new Database();
            $query = "INSERT INTO audit_logs (user_id, action, ip_address) VALUES (?, ?, ?)";
            $db->insert($query, [$userId, $action, $ipAddress]);
        } catch (Exception $e) {
            error_log('Audit log error: ' . $e->getMessage());
        }
    }
}
?>
