<?php
/**
 * Invoice Controller
 * 
 * Manages billing and payments
 */

class InvoiceController
{
    private $invoiceModel;
    private $patientModel;
    private $userModel;

    public function __construct()
    {
        Authenticate::guard();
        $this->invoiceModel = new Invoice();
        $this->patientModel = new Patient();
        $this->userModel = new User();
    }

    /**
     * List invoices
     */
    public function index()
    {
        $role = Auth::role();
        $userId = Auth::id();

        switch ($role) {
            case ROLE_PATIENT:
                $patient = $this->patientModel->getByUserId($userId);
                $invoices = $this->invoiceModel->forPatient($patient['id'] ?? 0);
                break;
            case ROLE_SUPER_ADMIN:
            case ROLE_RECEPTIONIST:
                $invoices = $this->invoiceModel->all();
                break;
            default:
                die('Unauthorized');
        }

        $data = ['invoices' => $invoices];
        include VIEWS_PATH . '/invoices/list.php';
    }

    /**
     * Show invoice details
     */
    public function show()
    {
        $invoiceId = (int)($_GET['id'] ?? 0);

        if (!$invoiceId) {
            header('Location: /invoices');
            exit;
        }

        $invoice = $this->invoiceModel->withDetails($invoiceId);

        if (!$invoice) {
            die('Invoice not found');
        }

        // Check permission
        if (Auth::role() === ROLE_PATIENT) {
            $patient = $this->patientModel->getByUserId(Auth::id());
            if ($patient['id'] !== $invoice['patient_id']) {
                die('Access Denied');
            }
        }

        $payments = $this->getPayments($invoiceId);
        $totalPaid = $this->invoiceModel->getTotalPayments($invoiceId);

        $data = [
            'invoice' => $invoice,
            'payments' => $payments,
            'total_paid' => $totalPaid,
            'outstanding' => max(0, $invoice['total_amount'] - $totalPaid)
        ];

        include VIEWS_PATH . '/invoices/show.php';
    }

    /**
     * Record payment
     */
    public function pay()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            die('Method Not Allowed');
        }

        $invoiceId = (int)($_POST['invoice_id'] ?? 0);
        $amount = (float)($_POST['amount'] ?? 0);
        $method = $_POST['method'] ?? 'cash';

        if (!$invoiceId || $amount <= 0) {
            die('Invalid payment details');
        }

        try {
            $this->invoiceModel->addPayment($invoiceId, $amount, $method);

            $_SESSION['success'] = 'Payment recorded successfully';
            header('Location: /invoices/' . $invoiceId);
            exit;
        } catch (Exception $e) {
            error_log('Payment error: ' . $e->getMessage());
            $_SESSION['error'] = 'An error occurred while recording payment';
            header('Location: /invoices/' . $invoiceId);
            exit;
        }
    }

    /**
     * Generate invoice PDF (future implementation)
     */
    public function download()
    {
        $invoiceId = (int)($_GET['id'] ?? 0);

        if (!$invoiceId) {
            die('Invalid invoice ID');
        }

        // TODO: Implement PDF generation
        $_SESSION['info'] = 'PDF download feature coming soon';
        header('Location: /invoices/' . $invoiceId);
        exit;
    }

    /**
     * Get outstanding invoices
     */
    public function outstanding()
    {
        Authenticate::guard([ROLE_SUPER_ADMIN, ROLE_RECEPTIONIST]);

        $invoices = $this->invoiceModel->outstanding();

        $data = ['invoices' => $invoices];
        include VIEWS_PATH . '/invoices/outstanding.php';
    }

    /**
     * Get revenue report
     */
    public function report()
    {
        Authenticate::guard([ROLE_SUPER_ADMIN]);

        $month = (int)($_GET['month'] ?? date('m'));
        $year = (int)($_GET['year'] ?? date('Y'));

        $revenue = $this->invoiceModel->monthlyRevenue($month, $year);
        $totalRevenue = $this->invoiceModel->totalRevenue();

        $data = [
            'month' => $month,
            'year' => $year,
            'revenue' => $revenue['total'] ?? 0,
            'total_revenue' => $totalRevenue['total'] ?? 0,
        ];

        include VIEWS_PATH . '/invoices/report.php';
    }

    /**
     * Get payment records for invoice
     */
    private function getPayments($invoiceId)
    {
        $db = new Database();
        $query = "SELECT * FROM payments WHERE invoice_id = ? ORDER BY paid_at DESC";
        return $db->fetchAll($query, [$invoiceId]);
    }
}
?>
