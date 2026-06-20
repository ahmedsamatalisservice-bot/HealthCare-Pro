<?php
/**
 * Appointment Controller
 * 
 * Manages appointment bookings and management
 */

class AppointmentController
{
    private $appointmentModel;
    private $patientModel;
    private $doctorModel;
    private $invoiceModel;

    public function __construct()
    {
        Authenticate::guard();
        $this->appointmentModel = new Appointment();
        $this->patientModel = new Patient();
        $this->doctorModel = new Doctor();
        $this->invoiceModel = new Invoice();
    }

    /**
     * List appointments
     */
    public function index()
    {
        $role = Auth::role();
        $userId = Auth::id();

        switch ($role) {
            case ROLE_DOCTOR:
                $doctor = $this->doctorModel->getByUserId($userId);
                $appointments = $this->appointmentModel->forDoctor($doctor['id'] ?? 0);
                break;
            case ROLE_PATIENT:
                $patient = $this->patientModel->getByUserId($userId);
                $appointments = $this->appointmentModel->forPatient($patient['id'] ?? 0);
                break;
            case ROLE_RECEPTIONIST:
            case ROLE_SUPER_ADMIN:
                $appointments = $this->appointmentModel->all();
                break;
            default:
                die('Unauthorized');
        }

        $data = ['appointments' => $appointments];
        include VIEWS_PATH . '/appointments/list.php';
    }

    /**
     * Show appointment details
     */
    public function show()
    {
        $appointmentId = (int)($_GET['id'] ?? 0);

        if (!$appointmentId) {
            header('Location: /appointments');
            exit;
        }

        $appointment = $this->appointmentModel->withDetails($appointmentId);

        if (!$appointment) {
            die('Appointment not found');
        }

        $data = ['appointment' => $appointment];
        include VIEWS_PATH . '/appointments/show.php';
    }

    /**
     * Show booking form
     */
    public function create()
    {
        Authenticate::guard([ROLE_PATIENT, ROLE_RECEPTIONIST]);

        $doctors = $this->doctorModel->available();

        $data = ['doctors' => $doctors];
        include VIEWS_PATH . '/appointments/create.php';
    }

    /**
     * Store new appointment
     */
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            die('Method Not Allowed');
        }

        $doctorId = (int)($_POST['doctor_id'] ?? 0);
        $scheduledAt = $_POST['scheduled_at'] ?? '';
        $reason = Validator::sanitize($_POST['reason'] ?? '');

        $role = Auth::role();
        $userId = Auth::id();

        // Determine patient ID
        if ($role === ROLE_PATIENT) {
            $patient = $this->patientModel->getByUserId($userId);
            $patientId = $patient['id'] ?? 0;
        } else {
            $patientId = (int)($_POST['patient_id'] ?? 0);
        }

        if (!$patientId || !$doctorId || !$scheduledAt) {
            die('Missing required fields');
        }

        // Check for conflicts
        if ($this->appointmentModel->hasConflict($doctorId, $scheduledAt)) {
            $_SESSION['error'] = 'Doctor has a conflicting appointment';
            header('Location: /appointments/create');
            exit;
        }

        try {
            $appointmentId = $this->appointmentModel->create([
                'patient_id' => $patientId,
                'doctor_id' => $doctorId,
                'scheduled_at' => $scheduledAt,
                'duration_minutes' => 30,
                'reason' => $reason,
                'created_by' => $userId,
                'status' => 'pending'
            ]);

            // Create invoice for appointment
            $doctor = $this->doctorModel->find($doctorId);
            $invoiceNumber = $this->invoiceModel->generateInvoiceNumber();

            $this->invoiceModel->create([
                'invoice_number' => $invoiceNumber,
                'patient_id' => $patientId,
                'appointment_id' => $appointmentId,
                'total_amount' => $doctor['consultation_fee'] ?? 50.00,
                'status' => 'unpaid'
            ]);

            $_SESSION['success'] = 'Appointment booked successfully';
            header('Location: /appointments');
            exit;
        } catch (Exception $e) {
            error_log('Appointment booking error: ' . $e->getMessage());
            $_SESSION['error'] = 'An error occurred while booking the appointment';
            header('Location: /appointments/create');
            exit;
        }
    }

    /**
     * Confirm appointment (Doctor/Admin)
     */
    public function confirm()
    {
        Authenticate::guard([ROLE_DOCTOR, ROLE_SUPER_ADMIN, ROLE_RECEPTIONIST]);

        $appointmentId = (int)($_POST['id'] ?? $_GET['id'] ?? 0);

        if (!$appointmentId) {
            die('Invalid appointment ID');
        }

        $this->appointmentModel->confirm($appointmentId);

        $_SESSION['success'] = 'Appointment confirmed';
        header('Location: /appointments');
        exit;
    }

    /**
     * Reject appointment
     */
    public function reject()
    {
        Authenticate::guard([ROLE_DOCTOR, ROLE_SUPER_ADMIN, ROLE_RECEPTIONIST]);

        $appointmentId = (int)($_POST['id'] ?? $_GET['id'] ?? 0);

        if (!$appointmentId) {
            die('Invalid appointment ID');
        }

        $this->appointmentModel->reject($appointmentId);

        $_SESSION['success'] = 'Appointment rejected';
        header('Location: /appointments');
        exit;
    }

    /**
     * Complete appointment
     */
    public function complete()
    {
        Authenticate::guard([ROLE_DOCTOR, ROLE_SUPER_ADMIN]);

        $appointmentId = (int)($_POST['id'] ?? $_GET['id'] ?? 0);

        if (!$appointmentId) {
            die('Invalid appointment ID');
        }

        $this->appointmentModel->complete($appointmentId);

        $_SESSION['success'] = 'Appointment marked as completed';
        header('Location: /appointments');
        exit;
    }

    /**
     * Cancel appointment
     */
    public function cancel()
    {
        $appointmentId = (int)($_POST['id'] ?? $_GET['id'] ?? 0);

        if (!$appointmentId) {
            die('Invalid appointment ID');
        }

        $this->appointmentModel->update($appointmentId, ['status' => 'cancelled']);

        $_SESSION['success'] = 'Appointment cancelled';
        header('Location: /appointments');
        exit;
    }
}
?>
