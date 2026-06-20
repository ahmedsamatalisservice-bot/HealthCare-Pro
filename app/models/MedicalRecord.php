<?php
/**
 * MedicalRecord Model
 * 
 * Manages patient electronic medical records
 */

class MedicalRecord extends Model
{
    protected $table = 'medical_records';
    protected $fillable = ['patient_id', 'doctor_id', 'appointment_id', 'visit_date', 'diagnosis', 'treatment', 'notes', 'attachments'];

    /**
     * Get medical record with patient and doctor details
     * 
     * @param int $id
     * @return array|null
     */
    public function withDetails($id)
    {
        $query = "SELECT m.*, 
                  up.first_name as patient_first, up.last_name as patient_last,
                  ud.first_name as doctor_first, ud.last_name as doctor_last
                  FROM {$this->table} m 
                  LEFT JOIN patients p ON m.patient_id = p.id 
                  LEFT JOIN users up ON p.user_id = up.id 
                  LEFT JOIN doctors d ON m.doctor_id = d.id 
                  LEFT JOIN users ud ON d.user_id = ud.id 
                  WHERE m.id = ?";
        return $this->db->fetch($query, [$id]);
    }

    /**
     * Get patient's medical records
     * 
     * @param int $patientId
     * @return array
     */
    public function forPatient($patientId)
    {
        $query = "SELECT m.*, ud.first_name as doctor_first, ud.last_name as doctor_last 
                  FROM {$this->table} m 
                  LEFT JOIN doctors d ON m.doctor_id = d.id 
                  LEFT JOIN users ud ON d.user_id = ud.id 
                  WHERE m.patient_id = ? 
                  ORDER BY m.visit_date DESC";
        return $this->db->fetchAll($query, [$patientId]);
    }

    /**
     * Get medical records created by doctor
     * 
     * @param int $doctorId
     * @return array
     */
    public function byDoctor($doctorId)
    {
        $query = "SELECT m.*, up.first_name as patient_first, up.last_name as patient_last 
                  FROM {$this->table} m 
                  LEFT JOIN patients p ON m.patient_id = p.id 
                  LEFT JOIN users up ON p.user_id = up.id 
                  WHERE m.doctor_id = ? 
                  ORDER BY m.visit_date DESC";
        return $this->db->fetchAll($query, [$doctorId]);
    }

    /**
     * Get medical record for appointment
     * 
     * @param int $appointmentId
     * @return array|null
     */
    public function byAppointment($appointmentId)
    {
        $query = "SELECT * FROM {$this->table} WHERE appointment_id = ? LIMIT 1";
        return $this->db->fetch($query, [$appointmentId]);
    }

    /**
     * Get patient's recent diagnosis
     * 
     * @param int $patientId
     * @param int $limit
     * @return array
     */
    public function recentDiagnosis($patientId, $limit = 5)
    {
        $query = "SELECT m.id, m.visit_date, m.diagnosis 
                  FROM {$this->table} m 
                  WHERE m.patient_id = ? AND m.diagnosis IS NOT NULL 
                  ORDER BY m.visit_date DESC 
                  LIMIT ?";
        return $this->db->fetchAll($query, [$patientId, $limit]);
    }

    /**
     * Add prescription to medical record
     * 
     * @param int $medicalRecordId
     * @param int $doctorId
     * @param array $prescription
     * @return int
     */
    public function addPrescription($medicalRecordId, $doctorId, $prescription)
    {
        $query = "INSERT INTO prescriptions (medical_record_id, prescribed_by, medication, dosage, instructions, start_date, end_date) 
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
        return $this->db->insert($query, [
            $medicalRecordId,
            $doctorId,
            $prescription['medication'],
            $prescription['dosage'] ?? null,
            $prescription['instructions'] ?? null,
            $prescription['start_date'] ?? date('Y-m-d'),
            $prescription['end_date'] ?? null
        ]);
    }
}
?>
