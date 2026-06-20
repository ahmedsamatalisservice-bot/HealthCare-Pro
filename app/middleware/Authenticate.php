<?php
/**
 * Authentication Middleware
 * 
 * Protects routes that require user authentication
 */

class Authenticate
{
    /**
     * Check if request passes authentication
     * 
     * @param string|array $allowedRoles Optional roles to check
     * @return bool
     */
    public static function guard($allowedRoles = null)
    {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Check if user is authenticated
        if (!Auth::check()) {
            header('Location: ' . APP_URL . '/login.php');
            exit;
        }

        // Check session timeout
        if (!Auth::checkTimeout()) {
            exit;
        }

        // Check role if specified
        if ($allowedRoles !== null) {
            if (!Auth::hasRole($allowedRoles)) {
                header('HTTP/1.1 403 Forbidden');
                die('Access Denied');
            }
        }

        return true;
    }
}
?>
