<?php
/**
 * Base Model Class
 * 
 * Provides common database operations for all models
 */

class Model
{
    protected $table;
    protected $db;
    protected $fillable = [];

    /**
     * Constructor - initialize database connection
     */
    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Get all records from table
     * 
     * @return array
     */
    public function all()
    {
        $query = "SELECT * FROM {$this->table}";
        return $this->db->fetchAll($query);
    }

    /**
     * Get record by ID
     * 
     * @param int $id
     * @return array|null
     */
    public function find($id)
    {
        $query = "SELECT * FROM {$this->table} WHERE id = ?";
        return $this->db->fetch($query, [$id]);
    }

    /**
     * Get first record matching condition
     * 
     * @param string $column
     * @param mixed $value
     * @return array|null
     */
    public function findBy($column, $value)
    {
        $query = "SELECT * FROM {$this->table} WHERE {$column} = ? LIMIT 1";
        return $this->db->fetch($query, [$value]);
    }

    /**
     * Get all records matching condition
     * 
     * @param string $column
     * @param mixed $value
     * @return array
     */
    public function where($column, $value)
    {
        $query = "SELECT * FROM {$this->table} WHERE {$column} = ?";
        return $this->db->fetchAll($query, [$value]);
    }

    /**
     * Create new record
     * 
     * @param array $data
     * @return int Last inserted ID
     */
    public function create($data)
    {
        // Filter fillable attributes
        $data = array_intersect_key($data, array_flip($this->fillable));

        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $query = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";

        return $this->db->insert($query, array_values($data));
    }

    /**
     * Update record
     * 
     * @param int $id
     * @param array $data
     * @return int Rows affected
     */
    public function update($id, $data)
    {
        // Filter fillable attributes
        $data = array_intersect_key($data, array_flip($this->fillable));

        $set = implode(', ', array_map(fn($key) => "{$key} = ?", array_keys($data)));
        $query = "UPDATE {$this->table} SET {$set} WHERE id = ?";

        $values = array_values($data);
        $values[] = $id;

        return $this->db->update($query, $values);
    }

    /**
     * Delete record
     * 
     * @param int $id
     * @return int Rows affected
     */
    public function delete($id)
    {
        $query = "DELETE FROM {$this->table} WHERE id = ?";
        return $this->db->update($query, [$id]);
    }

    /**
     * Count total records
     * 
     * @return int
     */
    public function count()
    {
        $query = "SELECT COUNT(*) as total FROM {$this->table}";
        $result = $this->db->fetch($query);
        return $result['total'] ?? 0;
    }

    /**
     * Paginate records
     * 
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function paginate($page = 1, $perPage = 15)
    {
        $offset = ($page - 1) * $perPage;
        $query = "SELECT * FROM {$this->table} LIMIT ? OFFSET ?";
        return $this->db->fetchAll($query, [$perPage, $offset]);
    }
}
?>
