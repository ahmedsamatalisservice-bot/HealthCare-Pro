<?php
/**
 * Doctor Model
 * 
 * Manages doctor profiles and availability
 */

class Doctor extends Model
{
    protected $table = 'doctors';
    protected $fillable = ['user_id', 'specialty_id', 'license_number', 'qualifications', 'bio', 'consultation_fee', 'is_available'];

    /**
     * Get doctor with user and specialty information
     * 
     * @param int $id
     * @return array|null
     */
    public function withDetails($id)
    {
        $query = "SELECT d.*, u.email, u.first_name, u.last_name, u.phone, s.name as specialty_name 
                  FROM {$this->table} d 
                  LEFT JOIN users u ON d.user_id = u.id 
                  LEFT JOIN specialties s ON d.specialty_id = s.id 
                  WHERE d.id = ?";
        return $this->db->fetch($query, [$id]);
    }

    /**
     * Get all available doctors
     * 
     * @return array
     */
    public function available()
    {
        $query = "SELECT d.*, u.email, u.first_name, u.last_name, s.name as specialty_name 
                  FROM {$this->table} d 
                  LEFT JOIN users u ON d.user_id = u.id 
                  LEFT JOIN specialties s ON d.specialty_id = s.id 
                  WHERE d.is_available = 1 AND u.is_active = 1";
        return $this->db->fetchAll($query);
    }

    /**
     * Get doctors by specialty
     * 
     * @param int $specialtyId
     * @return array
     */
    public function bySpecialty($specialtyId)
    {
        $query = "SELECT d.*, u.email, u.first_name, u.last_name, s.name as specialty_name 
                  FROM {$this->table} d 
                  LEFT JOIN users u ON d.user_id = u.id 
                  LEFT JOIN specialties s ON d.specialty_id = s.id 
                  WHERE d.specialty_id = ? AND d.is_available = 1";
        return $this->db->fetchAll($query, [$specialtyId]);
    }

    /**
     * Get doctor by user ID
     * 
     * @param int $userId
     * @return array|null
     */
    public function getByUserId($userId)
    {
        $query = "SELECT d.*, u.email, u.first_name, u.last_name, s.name as specialty_name 
                  FROM {$this->table} d 
                  LEFT JOIN users u ON d.user_id = u.id 
                  LEFT JOIN specialties s ON d.specialty_id = s.id 
                  WHERE d.user_id = ?";
        return $this->db->fetch($query, [$userId]);
    }

    /**
     * Get doctor appointments count
     * 
     * @param int $doctorId
     * @param string $status
     * @return int
     */
    public function appointmentsCount($doctorId, $status = 'completed')
    {
        $query = "SELECT COUNT(*) as count FROM appointments WHERE doctor_id = ? AND status = ?";
        $result = $this->db->fetch($query, [$doctorId, $status]);
        return $result['count'] ?? 0;
    }

    /**
     * Toggle doctor availability
     * 
     * @param int $id
     * @return int
     */
    public function toggleAvailability($id)
    {
        $doctor = $this->find($id);
        $newStatus = $doctor['is_available'] ? 0 : 1;
        return $this->update($id, ['is_available' => $newStatus]);
    }
}
?>
