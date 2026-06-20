<?php
/**
 * Medical Record Controller
 * 
 * Manages electronic medical records
 */

class MedicalRecordController
{
    private $medicalRecordModel;
    private $appointmentModel;
    private $patientModel;
    private $doctorModel;

    public function __construct()
    {
        Authenticate::guard();
        $this->medicalRecordModel = new MedicalRecord();
        $this->appointmentModel = new Appointment();
        $this->patientModel = new Patient();
        $this->doctorModel = new Doctor();
    }

    /**
     * List medical records
     */
    public function index()
    {
        $role = Auth::role();
        $userId = Auth::id();

        switch ($role) {
            case ROLE_PATIENT:
                $patient = $this->patientModel->getByUserId($userId);
                $records = $this->medicalRecordModel->forPatient($patient['id'] ?? 0);
                break;
            case ROLE_DOCTOR:
                $doctor = $this->doctorModel->getByUserId($userId);
                $records = $this->medicalRecordModel->byDoctor($doctor['id'] ?? 0);
                break;
            case ROLE_SUPER_ADMIN:
                $records = $this->medicalRecordModel->all();
                break;
            default:
                die('Unauthorized');
        }

        $data = ['records' => $records];
        include VIEWS_PATH . '/medical-records/list.php';
    }

    /**
     * Show medical record
     */
    public function show()
    {
        $recordId = (int)($_GET['id'] ?? 0);

        if (!$recordId) {
            header('Location: /medical-records');
            exit;
        }

        $record = $this->medicalRecordModel->withDetails($recordId);

        if (!$record) {
            die('Medical record not found');
        }

        $data = ['record' => $record];
        include VIEWS_PATH . '/medical-records/show.php';
    }

    /**
     * Create medical record form (after appointment)
     */
    public function create()
    {
        Authenticate::guard([ROLE_DOCTOR, ROLE_SUPER_ADMIN]);

        $appointmentId = (int)($_GET['appointment_id'] ?? 0);
        $appointment = null;

        if ($appointmentId) {
            $appointment = $this->appointmentModel->withDetails($appointmentId);
        }

        $data = ['appointment' => $appointment];
        include VIEWS_PATH . '/medical-records/create.php';
    }

    /**
     * Store medical record
     */
    public function store()
    {
        Authenticate::guard([ROLE_DOCTOR, ROLE_SUPER_ADMIN]);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            die('Method Not Allowed');
        }

        $patientId = (int)($_POST['patient_id'] ?? 0);
        $appointmentId = (int)($_POST['appointment_id'] ?? 0);
        $diagnosis = Validator::sanitize($_POST['diagnosis'] ?? '');
        $treatment = Validator::sanitize($_POST['treatment'] ?? '');
        $notes = Validator::sanitize($_POST['notes'] ?? '');

        if (!$patientId) {
            die('Invalid patient ID');
        }

        try {
            $doctorId = null;
            if (Auth::role() === ROLE_DOCTOR) {
                $doctor = $this->doctorModel->getByUserId(Auth::id());
                $doctorId = $doctor['id'] ?? null;
            }

            $recordId = $this->medicalRecordModel->create([
                'patient_id' => $patientId,
                'doctor_id' => $doctorId,
                'appointment_id' => $appointmentId ?: null,
                'diagnosis' => $diagnosis,
                'treatment' => $treatment,
                'notes' => $notes,
            ]);

            // Handle prescriptions if any
            if (isset($_POST['medications']) && is_array($_POST['medications'])) {
                foreach ($_POST['medications'] as $medication) {
                    if (!empty($medication['name'])) {
                        $this->medicalRecordModel->addPrescription($recordId, $doctorId, $medication);
                    }
                }
            }

            $_SESSION['success'] = 'Medical record created successfully';
            header('Location: /medical-records/' . $recordId);
            exit;
        } catch (Exception $e) {
            error_log('Medical record creation error: ' . $e->getMessage());
            $_SESSION['error'] = 'An error occurred while creating the record';
            header('Location: /medical-records/create');
            exit;
        }
    }

    /**
     * Edit medical record
     */
    public function edit()
    {
        Authenticate::guard([ROLE_DOCTOR, ROLE_SUPER_ADMIN]);

        $recordId = (int)($_GET['id'] ?? 0);

        if (!$recordId) {
            header('Location: /medical-records');
            exit;
        }

        $record = $this->medicalRecordModel->withDetails($recordId);

        if (!$record) {
            die('Medical record not found');
        }

        $data = ['record' => $record];
        include VIEWS_PATH . '/medical-records/edit.php';
    }

    /**
     * Update medical record
     */
    public function update()
    {
        Authenticate::guard([ROLE_DOCTOR, ROLE_SUPER_ADMIN]);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            die('Method Not Allowed');
        }

        $recordId = (int)($_POST['id'] ?? 0);

        if (!$recordId) {
            die('Invalid record ID');
        }

        $diagnosis = Validator::sanitize($_POST['diagnosis'] ?? '');
        $treatment = Validator::sanitize($_POST['treatment'] ?? '');
        $notes = Validator::sanitize($_POST['notes'] ?? '');

        $this->medicalRecordModel->update($recordId, [
            'diagnosis' => $diagnosis,
            'treatment' => $treatment,
            'notes' => $notes,
        ]);

        $_SESSION['success'] = 'Medical record updated successfully';
        header('Location: /medical-records/' . $recordId);
        exit;
    }
}
?>
