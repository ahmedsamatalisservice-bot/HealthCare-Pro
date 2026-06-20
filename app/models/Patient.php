<?php
/**
 * Patient Model
 * 
 * Manages patient profiles and information
 */

class Patient extends Model
{
    protected $table = 'patients';
    protected $fillable = ['user_id', 'dob', 'gender', 'address', 'blood_type', 'emergency_contact'];

    /**
     * Get patient with user information
     * 
     * @param int $id
     * @return array|null
     */
    public function withUser($id)
    {
        $query = "SELECT p.*, u.id as user_id, u.email, u.first_name, u.last_name, u.phone 
                  FROM {$this->table} p 
                  LEFT JOIN users u ON p.user_id = u.id 
                  WHERE p.id = ?";
        return $this->db->fetch($query, [$id]);
    }

    /**
     * Get all patients with user info
     * 
     * @return array
     */
    public function allWithUser()
    {
        $query = "SELECT p.*, u.email, u.first_name, u.last_name, u.phone 
                  FROM {$this->table} p 
                  LEFT JOIN users u ON p.user_id = u.id 
                  ORDER BY u.first_name";
        return $this->db->fetchAll($query);
    }

    /**
     * Get patient by user ID
     * 
     * @param int $userId
     * @return array|null
     */
    public function getByUserId($userId)
    {
        $query = "SELECT * FROM {$this->table} WHERE user_id = ?";
        return $this->db->fetch($query, [$userId]);
    }

    /**
     * Search patients
     * 
     * @param string $search
     * @return array
     */
    public function search($search)
    {
        $search = "%{$search}%";
        $query = "SELECT p.*, u.email, u.first_name, u.last_name, u.phone 
                  FROM {$this->table} p 
                  LEFT JOIN users u ON p.user_id = u.id 
                  WHERE u.first_name LIKE ? OR u.last_name LIKE ? OR u.email LIKE ? OR u.phone LIKE ?";
        return $this->db->fetchAll($query, [$search, $search, $search, $search]);
    }

    /**
     * Get patient medical history count
     * 
     * @param int $patientId
     * @return int
     */
    public function medicalHistoryCount($patientId)
    {
        $query = "SELECT COUNT(*) as count FROM medical_records WHERE patient_id = ?";
        $result = $this->db->fetch($query, [$patientId]);
        return $result['count'] ?? 0;
    }
}
?>
