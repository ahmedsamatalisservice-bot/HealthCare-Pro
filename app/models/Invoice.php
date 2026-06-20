<?php
/**
 * Invoice Model
 * 
 * Manages billing invoices and payments
 */

class Invoice extends Model
{
    protected $table = 'invoices';
    protected $fillable = ['invoice_number', 'patient_id', 'appointment_id', 'issued_at', 'due_date', 'total_amount', 'status', 'notes'];

    /**
     * Get invoice with patient details
     * 
     * @param int $id
     * @return array|null
     */
    public function withDetails($id)
    {
        $query = "SELECT i.*, up.first_name, up.last_name, up.email 
                  FROM {$this->table} i 
                  LEFT JOIN patients p ON i.patient_id = p.id 
                  LEFT JOIN users up ON p.user_id = up.id 
                  WHERE i.id = ?";
        return $this->db->fetch($query, [$id]);
    }

    /**
     * Get invoices for patient
     * 
     * @param int $patientId
     * @param string $status
     * @return array
     */
    public function forPatient($patientId, $status = null)
    {
        if ($status) {
            $query = "SELECT * FROM {$this->table} WHERE patient_id = ? AND status = ? ORDER BY issued_at DESC";
            return $this->db->fetchAll($query, [$patientId, $status]);
        }
        
        $query = "SELECT * FROM {$this->table} WHERE patient_id = ? ORDER BY issued_at DESC";
        return $this->db->fetchAll($query, [$patientId]);
    }

    /**
     * Get outstanding invoices
     * 
     * @return array
     */
    public function outstanding()
    {
        $query = "SELECT i.*, up.first_name, up.last_name 
                  FROM {$this->table} i 
                  LEFT JOIN patients p ON i.patient_id = p.id 
                  LEFT JOIN users up ON p.user_id = up.id 
                  WHERE i.status IN ('unpaid', 'partial')
                  ORDER BY i.due_date ASC";
        return $this->db->fetchAll($query);
    }

    /**
     * Get total revenue
     * 
     * @return array
     */
    public function totalRevenue()
    {
        $query = "SELECT COALESCE(SUM(total_amount), 0) as total FROM {$this->table} WHERE status = 'paid'";
        return $this->db->fetch($query);
    }

    /**
     * Get monthly revenue
     * 
     * @param int $month
     * @param int $year
     * @return array
     */
    public function monthlyRevenue($month, $year)
    {
        $query = "SELECT COALESCE(SUM(total_amount), 0) as total 
                  FROM {$this->table} 
                  WHERE status = 'paid' 
                  AND MONTH(issued_at) = ? 
                  AND YEAR(issued_at) = ?";
        return $this->db->fetch($query, [$month, $year]);
    }

    /**
     * Generate unique invoice number
     * 
     * @return string
     */
    public function generateInvoiceNumber()
    {
        $prefix = 'INV-' . date('Ym');
        $query = "SELECT COUNT(*) as count FROM {$this->table} WHERE invoice_number LIKE ?";
        $result = $this->db->fetch($query, ["{$prefix}%"]);
        $count = ($result['count'] ?? 0) + 1;
        return $prefix . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Add payment to invoice
     * 
     * @param int $invoiceId
     * @param float $amount
     * @param string $method
     * @param string $transactionRef
     * @return int
     */
    public function addPayment($invoiceId, $amount, $method = 'cash', $transactionRef = null)
    {
        // Insert payment
        $query = "INSERT INTO payments (invoice_id, amount, method, transaction_ref) VALUES (?, ?, ?, ?)";
        $this->db->insert($query, [$invoiceId, $amount, $method, $transactionRef]);

        // Update invoice status
        $invoice = $this->find($invoiceId);
        $totalPaid = $this->getTotalPayments($invoiceId);

        if ($totalPaid >= $invoice['total_amount']) {
            $this->update($invoiceId, ['status' => 'paid']);
        } elseif ($totalPaid > 0) {
            $this->update($invoiceId, ['status' => 'partial']);
        }

        return $invoiceId;
    }

    /**
     * Get total payments for invoice
     * 
     * @param int $invoiceId
     * @return float
     */
    public function getTotalPayments($invoiceId)
    {
        $query = "SELECT COALESCE(SUM(amount), 0) as total FROM payments WHERE invoice_id = ?";
        $result = $this->db->fetch($query, [$invoiceId]);
        return $result['total'] ?? 0;
    }

    /**
     * Get outstanding balance for patient
     * 
     * @param int $patientId
     * @return float
     */
    public function outstandingBalance($patientId)
    {
        $query = "SELECT COALESCE(SUM(i.total_amount - COALESCE((SELECT SUM(amount) FROM payments WHERE invoice_id = i.id), 0)), 0) as balance 
                  FROM {$this->table} i 
                  WHERE i.patient_id = ? AND i.status IN ('unpaid', 'partial')";
        $result = $this->db->fetch($query, [$patientId]);
        return $result['balance'] ?? 0;
    }
}
?>
