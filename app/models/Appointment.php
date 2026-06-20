<?php
/**
 * Appointment Model
 * 
 * Manages patient-doctor appointments
 */

class Appointment extends Model
{
    protected $table = 'appointments';
    protected $fillable = ['patient_id', 'doctor_id', 'scheduled_at', 'duration_minutes', 'status', 'reason', 'created_by'];

    /**
     * Get appointment with patient and doctor details
     * 
     * @param int $id
     * @return array|null
     */
    public function withDetails($id)
    {
        $query = "SELECT a.*, 
                  p.id as patient_id, up.first_name as patient_first, up.last_name as patient_last, up.email as patient_email,
                  d.id as doctor_id, ud.first_name as doctor_first, ud.last_name as doctor_last, ud.email as doctor_email
                  FROM {$this->table} a 
                  LEFT JOIN patients p ON a.patient_id = p.id 
                  LEFT JOIN users up ON p.user_id = up.id 
                  LEFT JOIN doctors d ON a.doctor_id = d.id 
                  LEFT JOIN users ud ON d.user_id = ud.id 
                  WHERE a.id = ?";
        return $this->db->fetch($query, [$id]);
    }

    /**
     * Get appointments for patient
     * 
     * @param int $patientId
     * @param string $status
     * @return array
     */
    public function forPatient($patientId, $status = null)
    {
        if ($status) {
            $query = "SELECT a.*, ud.first_name as doctor_first, ud.last_name as doctor_last 
                      FROM {$this->table} a 
                      LEFT JOIN doctors d ON a.doctor_id = d.id 
                      LEFT JOIN users ud ON d.user_id = ud.id 
                      WHERE a.patient_id = ? AND a.status = ? 
                      ORDER BY a.scheduled_at DESC";
            return $this->db->fetchAll($query, [$patientId, $status]);
        }
        
        $query = "SELECT a.*, ud.first_name as doctor_first, ud.last_name as doctor_last 
                  FROM {$this->table} a 
                  LEFT JOIN doctors d ON a.doctor_id = d.id 
                  LEFT JOIN users ud ON d.user_id = ud.id 
                  WHERE a.patient_id = ? 
                  ORDER BY a.scheduled_at DESC";
        return $this->db->fetchAll($query, [$patientId]);
    }

    /**
     * Get appointments for doctor
     * 
     * @param int $doctorId
     * @param string $status
     * @return array
     */
    public function forDoctor($doctorId, $status = null)
    {
        if ($status) {
            $query = "SELECT a.*, up.first_name as patient_first, up.last_name as patient_last 
                      FROM {$this->table} a 
                      LEFT JOIN patients p ON a.patient_id = p.id 
                      LEFT JOIN users up ON p.user_id = up.id 
                      WHERE a.doctor_id = ? AND a.status = ? 
                      ORDER BY a.scheduled_at ASC";
            return $this->db->fetchAll($query, [$doctorId, $status]);
        }
        
        $query = "SELECT a.*, up.first_name as patient_first, up.last_name as patient_last 
                  FROM {$this->table} a 
                  LEFT JOIN patients p ON a.patient_id = p.id 
                  LEFT JOIN users up ON p.user_id = up.id 
                  WHERE a.doctor_id = ? 
                  ORDER BY a.scheduled_at ASC";
        return $this->db->fetchAll($query, [$doctorId]);
    }

    /**
     * Get today's appointments
     * 
     * @return array
     */
    public function today()
    {
        $query = "SELECT a.*, up.first_name as patient_first, up.last_name as patient_last, 
                  ud.first_name as doctor_first, ud.last_name as doctor_last 
                  FROM {$this->table} a 
                  LEFT JOIN patients p ON a.patient_id = p.id 
                  LEFT JOIN users up ON p.user_id = up.id 
                  LEFT JOIN doctors d ON a.doctor_id = d.id 
                  LEFT JOIN users ud ON d.user_id = ud.id 
                  WHERE DATE(a.scheduled_at) = CURDATE()
                  ORDER BY a.scheduled_at ASC";
        return $this->db->fetchAll($query);
    }

    /**
     * Get pending appointments
     * 
     * @return array
     */
    public function pending()
    {
        $query = "SELECT a.*, up.first_name as patient_first, up.last_name as patient_last 
                  FROM {$this->table} a 
                  LEFT JOIN patients p ON a.patient_id = p.id 
                  LEFT JOIN users up ON p.user_id = up.id 
                  WHERE a.status = 'pending'
                  ORDER BY a.created_at DESC";
        return $this->db->fetchAll($query);
    }

    /**
     * Confirm appointment
     * 
     * @param int $id
     * @return int
     */
    public function confirm($id)
    {
        return $this->update($id, ['status' => 'confirmed']);
    }

    /**
     * Reject appointment
     * 
     * @param int $id
     * @return int
     */
    public function reject($id)
    {
        return $this->update($id, ['status' => 'rejected']);
    }

    /**
     * Complete appointment
     * 
     * @param int $id
     * @return int
     */
    public function complete($id)
    {
        return $this->update($id, ['status' => 'completed']);
    }

    /**
     * Check for appointment conflicts
     * 
     * @param int $doctorId
     * @param string $scheduledAt
     * @param int $durationMinutes
     * @return bool
     */
    public function hasConflict($doctorId, $scheduledAt, $durationMinutes = 30)
    {
        $endTime = date('Y-m-d H:i:s', strtotime("+{$durationMinutes} minutes", strtotime($scheduledAt)));
        $query = "SELECT COUNT(*) as count FROM {$this->table} 
                  WHERE doctor_id = ? 
                  AND status NOT IN ('rejected', 'cancelled')
                  AND scheduled_at < ? 
                  AND DATE_ADD(scheduled_at, INTERVAL duration_minutes MINUTE) > ?";
        $result = $this->db->fetch($query, [$doctorId, $endTime, $scheduledAt]);
        return ($result['count'] ?? 0) > 0;
    }
}
?>
