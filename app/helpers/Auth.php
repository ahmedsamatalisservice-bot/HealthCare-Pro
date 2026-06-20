<?php
/**
 * Authentication Helper Class
 * 
 * Manages user authentication, login, logout, and session management
 */

class Auth
{
    /**
     * Hash password using bcrypt
     * 
     * @param string $password Plain text password
     * @return string Hashed password
     */
    public static function hashPassword($password)
    {
        return password_hash(
            $password,
            PASSWORD_BCRYPT,
            ['cost' => BCRYPT_ROUNDS]
        );
    }

    /**
     * Verify password against hash
     * 
     * @param string $password Plain text password
     * @param string $hash Password hash
     * @return bool
     */
    public static function verifyPassword($password, $hash)
    {
        return password_verify($password, $hash);
    }

    /**
     * Check if user is authenticated
     * 
     * @return bool
     */
    public static function check()
    {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    /**
     * Get current authenticated user ID
     * 
     * @return int|null
     */
    public static function id()
    {
        return $_SESSION['user_id'] ?? null;
    }

    /**
     * Get current user's role
     * 
     * @return string|null
     */
    public static function role()
    {
        return $_SESSION['user_role'] ?? null;
    }

    /**
     * Check if user has a specific role
     * 
     * @param string|array $roles
     * @return bool
     */
    public static function hasRole($roles)
    {
        if (!self::check()) {
            return false;
        }

        $userRole = self::role();
        $roles = is_array($roles) ? $roles : [$roles];
        
        return in_array($userRole, $roles);
    }

    /**
     * Login user
     * 
     * @param int $userId User ID
     * @param string $role User role
     * @param string $email User email
     */
    public static function login($userId, $role, $email)
    {
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_role'] = $role;
        $_SESSION['user_email'] = $email;
        $_SESSION['login_time'] = time();
    }

    /**
     * Logout user
     */
    public static function logout()
    {
        session_destroy();
        header('Location: ' . APP_URL . '/login.php');
        exit;
    }

    /**
     * Check session timeout
     * 
     * @return bool
     */
    public static function checkTimeout()
    {
        $timeout = SESSION_LIFETIME * 60; // Convert to seconds
        
        if (self::check() && isset($_SESSION['login_time'])) {
            if (time() - $_SESSION['login_time'] > $timeout) {
                self::logout();
                return false;
            }
        }
        
        return true;
    }
}
?>
