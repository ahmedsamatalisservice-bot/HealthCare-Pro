<?php
/**
 * User Model
 * 
 * Manages user accounts across all roles
 */

class User extends Model
{
    protected $table = 'users';
    protected $fillable = ['role_id', 'email', 'password_hash', 'first_name', 'last_name', 'phone', 'is_active'];

    /**
     * Get user by email
     * 
     * @param string $email
     * @return array|null
     */
    public function findByEmail($email)
    {
        $query = "SELECT * FROM {$this->table} WHERE email = ? LIMIT 1";
        return $this->db->fetch($query, [$email]);
    }

    /**
     * Get all active users
     * 
     * @return array
     */
    public function active()
    {
        $query = "SELECT * FROM {$this->table} WHERE is_active = 1";
        return $this->db->fetchAll($query);
    }

    /**
     * Get users by role
     * 
     * @param int $roleId
     * @return array
     */
    public function byRole($roleId)
    {
        $query = "SELECT u.* FROM {$this->table} u WHERE u.role_id = ?";
        return $this->db->fetchAll($query, [$roleId]);
    }

    /**
     * Create new user with hashed password
     * 
     * @param array $data
     * @return int User ID
     */
    public function register($data)
    {
        $data['password_hash'] = Auth::hashPassword($data['password']);
        unset($data['password']);
        return $this->create($data);
    }

    /**
     * Get user with role information
     * 
     * @param int $id
     * @return array|null
     */
    public function withRole($id)
    {
        $query = "SELECT u.*, r.name as role_name FROM {$this->table} u 
                  LEFT JOIN roles r ON u.role_id = r.id 
                  WHERE u.id = ?";
        return $this->db->fetch($query, [$id]);
    }

    /**
     * Deactivate user
     * 
     * @param int $id
     * @return int
     */
    public function deactivate($id)
    {
        return $this->update($id, ['is_active' => 0]);
    }

    /**
     * Activate user
     * 
     * @param int $id
     * @return int
     */
    public function activate($id)
    {
        return $this->update($id, ['is_active' => 1]);
    }
}
?>
