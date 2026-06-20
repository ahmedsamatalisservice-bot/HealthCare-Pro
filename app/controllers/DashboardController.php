<?php
/**
 * Dashboard Controller
 * 
 * Main dashboard for authenticated users
 */

class DashboardController
{
    private $userModel;
    private $appointmentModel;
    private $invoiceModel;
    private $patientModel;
    private $doctorModel;

    public function __construct()
    {
        Authenticate::guard();
        
        $this->userModel = new User();
        $this->appointmentModel = new Appointment();
        $this->invoiceModel = new Invoice();
        $this->patientModel = new Patient();
        $this->doctorModel = new Doctor();
    }

    /**
     * Show dashboard based on user role
     */
    public function index()
    {
        $role = Auth::role();
        $userId = Auth::id();

        $data = [
            'user' => $this->userModel->withRole($userId),
        ];

        switch ($role) {
            case ROLE_SUPER_ADMIN:
                $this->adminDashboard($data);
                break;
            case ROLE_DOCTOR:
                $this->doctorDashboard($data);
                break;
            case ROLE_RECEPTIONIST:
                $this->receptionistDashboard($data);
                break;
            case ROLE_PATIENT:
                $this->patientDashboard($data);
                break;
            default:
                Auth::logout();
        }
    }

    /**
     * Admin dashboard
     */
    private function adminDashboard(&$data)
    {
        $data['stats'] = [
            'total_patients' => $this->patientModel->count(),
            'total_doctors' => $this->doctorModel->count(),
            'total_appointments' => $this->appointmentModel->count(),
            'total_revenue' => $this->invoiceModel->totalRevenue()['total'] ?? 0,
        ];

        $data['today_appointments'] = $this->appointmentModel->today();
        $data['pending_appointments'] = $this->appointmentModel->pending();
        $data['outstanding_invoices'] = $this->invoiceModel->outstanding();

        include VIEWS_PATH . '/dashboard/admin-dashboard.php';
    }

    /**
     * Doctor dashboard
     */
    private function doctorDashboard(&$data)
    {
        $doctor = $this->doctorModel->getByUserId(Auth::id());
        $doctorId = $doctor['id'] ?? null;

        if (!$doctorId) {
            die('Doctor profile not found');
        }

        $data['doctor'] = $doctor;
        $data['stats'] = [
            'pending_appointments' => count($this->appointmentModel->forDoctor($doctorId, 'pending')),
            'total_patients' => count($this->appointmentModel->forDoctor($doctorId)),
            'completed_appointments' => $this->doctorModel->appointmentsCount($doctorId),
        ];

        $data['today_appointments'] = array_filter(
            $this->appointmentModel->forDoctor($doctorId),
            fn($apt) => date('Y-m-d', strtotime($apt['scheduled_at'])) === date('Y-m-d')
        );

        include VIEWS_PATH . '/dashboard/doctor-dashboard.php';
    }

    /**
     * Receptionist dashboard
     */
    private function receptionistDashboard(&$data)
    {
        $data['stats'] = [
            'pending_appointments' => count($this->appointmentModel->pending()),
            'today_appointments' => count($this->appointmentModel->today()),
            'total_patients' => $this->patientModel->count(),
        ];

        $data['pending_appointments'] = $this->appointmentModel->pending();
        $data['today_appointments'] = $this->appointmentModel->today();

        include VIEWS_PATH . '/dashboard/receptionist-dashboard.php';
    }

    /**
     * Patient dashboard
     */
    private function patientDashboard(&$data)
    {
        $patient = $this->patientModel->getByUserId(Auth::id());
        $patientId = $patient['id'] ?? null;

        if (!$patientId) {
            die('Patient profile not found');
        }

        $data['patient'] = $patient;
        $data['stats'] = [
            'upcoming_appointments' => 0,
            'pending_invoices' => 0,
            'outstanding_balance' => $this->invoiceModel->outstandingBalance($patientId),
        ];

        $appointments = $this->appointmentModel->forPatient($patientId);
        $data['stats']['upcoming_appointments'] = count(array_filter(
            $appointments,
            fn($apt) => strtotime($apt['scheduled_at']) > time() && $apt['status'] !== 'cancelled'
        ));

        $data['stats']['pending_invoices'] = count($this->invoiceModel->forPatient($patientId, 'unpaid'));

        $data['recent_appointments'] = array_slice($appointments, 0, 5);
        $data['recent_invoices'] = array_slice($this->invoiceModel->forPatient($patientId), 0, 5);

        include VIEWS_PATH . '/dashboard/patient-dashboard.php';
    }
}
?>
