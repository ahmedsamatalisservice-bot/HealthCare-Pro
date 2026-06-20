<?php
/**
 * Database Helper Class
 * 
 * Provides PDO connection and query execution
 */

class Database
{
    private static $connection = null;
    private $pdo;

    /**
     * Constructor - initialize database connection
     */
    public function __construct()
    {
        $this->pdo = self::getConnection();
    }

    /**
     * Get or create singleton database connection
     * 
     * @return PDO
     * @throws PDOException
     */
    public static function getConnection()
    {
        if (self::$connection === null) {
            try {
                $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4';
                self::$connection = new PDO(
                    $dsn,
                    DB_USER,
                    DB_PASS,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    ]
                );
            } catch (PDOException $e) {
                error_log('Database connection failed: ' . $e->getMessage());
                throw $e;
            }
        }
        return self::$connection;
    }

    /**
     * Execute a prepared statement
     * 
     * @param string $query SQL query with placeholders
     * @param array $params Parameters to bind
     * @return PDOStatement
     */
    public function execute($query, $params = [])
    {
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Fetch all results
     * 
     * @param string $query SQL query
     * @param array $params Parameters to bind
     * @return array
     */
    public function fetchAll($query, $params = [])
    {
        return $this->execute($query, $params)->fetchAll();
    }

    /**
     * Fetch single row
     * 
     * @param string $query SQL query
     * @param array $params Parameters to bind
     * @return array|null
     */
    public function fetch($query, $params = [])
    {
        return $this->execute($query, $params)->fetch();
    }

    /**
     * Insert record and return last insert ID
     * 
     * @param string $query SQL query
     * @param array $params Parameters to bind
     * @return string Last inserted ID
     */
    public function insert($query, $params = [])
    {
        $this->execute($query, $params);
        return $this->pdo->lastInsertId();
    }

    /**
     * Update or delete records
     * 
     * @param string $query SQL query
     * @param array $params Parameters to bind
     * @return int Number of affected rows
     */
    public function update($query, $params = [])
    {
        return $this->execute($query, $params)->rowCount();
    }

    /**
     * Begin transaction
     */
    public function beginTransaction()
    {
        $this->pdo->beginTransaction();
    }

    /**
     * Commit transaction
     */
    public function commit()
    {
        $this->pdo->commit();
    }

    /**
     * Rollback transaction
     */
    public function rollback()
    {
        $this->pdo->rollBack();
    }
}
?>
