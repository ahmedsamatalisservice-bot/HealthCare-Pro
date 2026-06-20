<?php
/**
 * Patient Controller
 * 
 * Manages patient-related operations
 */

class PatientController
{
    private $patientModel;
    private $userModel;

    public function __construct()
    {
        Authenticate::guard([ROLE_SUPER_ADMIN, ROLE_RECEPTIONIST, ROLE_DOCTOR, ROLE_PATIENT]);
        $this->patientModel = new Patient();
        $this->userModel = new User();
    }

    /**
     * List all patients
     */
    public function index()
    {
        $page = (int)($_GET['page'] ?? 1);
        $search = $_GET['search'] ?? '';

        if ($search) {
            $patients = $this->patientModel->search($search);
        } else {
            $patients = $this->patientModel->allWithUser();
        }

        $data = [
            'patients' => $patients,
            'search' => $search,
            'total' => count($patients),
        ];

        include VIEWS_PATH . '/patients/list.php';
    }

    /**
     * Show patient details
     */
    public function show()
    {
        $patientId = (int)($_GET['id'] ?? 0);

        if (!$patientId) {
            header('Location: /patients');
            exit;
        }

        $patient = $this->patientModel->withUser($patientId);

        if (!$patient) {
            die('Patient not found');
        }

        // Check permission: patient can only view own record
        if (Auth::role() === ROLE_PATIENT) {
            $userPatient = $this->patientModel->getByUserId(Auth::id());
            if ($userPatient['id'] !== $patientId) {
                die('Access Denied');
            }
        }

        $data = [
            'patient' => $patient,
        ];

        include VIEWS_PATH . '/patients/show.php';
    }

    /**
     * Show create patient form
     */
    public function create()
    {
        Authenticate::guard([ROLE_SUPER_ADMIN, ROLE_RECEPTIONIST]);
        include VIEWS_PATH . '/patients/create.php';
    }

    /**
     * Store new patient
     */
    public function store()
    {
        Authenticate::guard([ROLE_SUPER_ADMIN, ROLE_RECEPTIONIST]);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            die('Method Not Allowed');
        }

        // Validate input
        $email = Validator::sanitize($_POST['email'] ?? '');
        $firstName = Validator::sanitize($_POST['first_name'] ?? '');
        $lastName = Validator::sanitize($_POST['last_name'] ?? '');
        $dob = $_POST['dob'] ?? null;
        $gender = $_POST['gender'] ?? null;
        $bloodType = $_POST['blood_type'] ?? null;
        $phone = Validator::sanitize($_POST['phone'] ?? '');

        $errors = [];

        if (!Validator::required($email) || !Validator::email($email)) {
            $errors[] = 'Valid email is required';
        } elseif ($this->userModel->findByEmail($email)) {
            $errors[] = 'Email already exists';
        }

        if (!Validator::required($firstName)) {
            $errors[] = 'First name is required';
        }

        if (!Validator::required($lastName)) {
            $errors[] = 'Last name is required';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header('Location: /patients/create');
            exit;
        }

        try {
            // Create user
            $userId = $this->userModel->register([
                'role_id' => 4, // Patient role
                'email' => $email,
                'password' => 'TempPass@' . rand(10000, 99999),
                'first_name' => $firstName,
                'last_name' => $lastName,
                'phone' => $phone,
                'is_active' => 1
            ]);

            // Create patient profile
            $this->patientModel->create([
                'user_id' => $userId,
                'dob' => $dob,
                'gender' => $gender,
                'blood_type' => $bloodType,
            ]);

            $_SESSION['success'] = 'Patient registered successfully';
            header('Location: /patients');
            exit;
        } catch (Exception $e) {
            error_log('Patient creation error: ' . $e->getMessage());
            $_SESSION['errors'] = ['An error occurred during registration'];
            header('Location: /patients/create');
            exit;
        }
    }

    /**
     * Show edit form
     */
    public function edit()
    {
        $patientId = (int)($_GET['id'] ?? 0);

        if (!$patientId) {
            header('Location: /patients');
            exit;
        }

        $patient = $this->patientModel->withUser($patientId);

        if (!$patient) {
            die('Patient not found');
        }

        $data = ['patient' => $patient];
        include VIEWS_PATH . '/patients/edit.php';
    }

    /**
     * Update patient
     */
    public function update()
    {
        Authenticate::guard([ROLE_SUPER_ADMIN, ROLE_RECEPTIONIST]);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            die('Method Not Allowed');
        }

        $patientId = (int)($_POST['id'] ?? 0);

        if (!$patientId) {
            die('Invalid patient ID');
        }

        $patient = $this->patientModel->find($patientId);

        if (!$patient) {
            die('Patient not found');
        }

        $dob = $_POST['dob'] ?? null;
        $gender = $_POST['gender'] ?? null;
        $bloodType = $_POST['blood_type'] ?? null;
        $address = Validator::sanitize($_POST['address'] ?? '');

        $this->patientModel->update($patientId, [
            'dob' => $dob,
            'gender' => $gender,
            'blood_type' => $bloodType,
            'address' => $address,
        ]);

        $_SESSION['success'] = 'Patient updated successfully';
        header('Location: /patients/' . $patientId);
        exit;
    }

    /**
     * Delete patient
     */
    public function delete()
    {
        Authenticate::guard([ROLE_SUPER_ADMIN]);

        $patientId = (int)($_POST['id'] ?? $_GET['id'] ?? 0);

        if (!$patientId) {
            die('Invalid patient ID');
        }

        $this->patientModel->delete($patientId);

        $_SESSION['success'] = 'Patient deleted successfully';
        header('Location: /patients');
        exit;
    }
}
?>
