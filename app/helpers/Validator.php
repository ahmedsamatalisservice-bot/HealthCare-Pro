<?php
/**
 * Input Validation Helper
 * 
 * Provides validation methods for user input
 */

class Validator
{
    private $errors = [];

    /**
     * Validate email format
     * 
     * @param string $email
     * @return bool
     */
    public static function email($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validate password strength
     * 
     * @param string $password
     * @return bool
     */
    public static function password($password)
    {
        // At least 8 characters, 1 uppercase, 1 lowercase, 1 number, 1 special char
        return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password);
    }

    /**
     * Validate phone number
     * 
     * @param string $phone
     * @return bool
     */
    public static function phone($phone)
    {
        return preg_match('/^[0-9\-\+\s\(\)]{7,20}$/', $phone);
    }

    /**
     * Validate required field
     * 
     * @param mixed $value
     * @return bool
     */
    public static function required($value)
    {
        return !empty(trim($value ?? ''));
    }

    /**
     * Validate minimum length
     * 
     * @param string $value
     * @param int $min
     * @return bool
     */
    public static function minLength($value, $min)
    {
        return strlen($value) >= $min;
    }

    /**
     * Validate maximum length
     * 
     * @param string $value
     * @param int $max
     * @return bool
     */
    public static function maxLength($value, $max)
    {
        return strlen($value) <= $max;
    }

    /**
     * Sanitize user input
     * 
     * @param string $input
     * @return string
     */
    public static function sanitize($input)
    {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
}
?>
