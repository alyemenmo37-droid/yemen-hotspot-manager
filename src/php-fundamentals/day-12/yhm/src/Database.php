<?php
/**
 * YHM - Database Class (PDO-based)
 * نسخة MySQL من Database
 */

declare(strict_types=1);

class Database
{
    private ?PDO $pdo = null;
    
    public function __construct(
        private array $config,
    ) {}
    
    /**
     * الاتصال بقاعدة البيانات
     */
    public function connect(): PDO
    {
        if ($this->pdo === null) {
            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=%s',
                $this->config['host'],
                $this->config['database'],
                $this->config['charset'] ?? 'utf8mb4'
            );
            
            try {
                $this->pdo = new PDO(
                    $dsn,
                    $this->config['username'],
                    $this->config['password'],
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                    ]
                );
            } catch (PDOException $e) {
                throw new RuntimeException(
                    "فشل الاتصال بقاعدة البيانات: " . $e->getMessage()
                );
            }
        }
        
        return $this->pdo;
    }
    
    /**
     * تنفيذ استعلام
     */
    public function query(string $sql, array $params = []): PDOStatement
    {
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
    
    /**
     * جلب سجل واحد
     */
    public function fetchOne(string $sql, array $params = []): ?array
    {
        $result = $this->query($sql, $params)->fetch();
        return $result === false ? null : $result;
    }
    
    /**
     * جلب سجلات متعددة
     */
    public function fetchAll(string $sql, array $params = []): array
    {
        return $this->query($sql, $params)->fetchAll();
    }
    
    /**
     * جلب قيمة واحدة
     */
    public function fetchColumn(string $sql, array $params = []): mixed
    {
        return $this->query($sql, $params)->fetchColumn();
    }
    
    /**
     * إدراج
     */
    public function insert(string $table, array $data): int
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        
        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        
        $this->query($sql, array_values($data));
        
        return (int) $this->connect()->lastInsertId();
    }
    
    /**
     * تحديث
     */
    public function update(string $table, array $data, string $where, array $whereParams = []): int
    {
        $set = implode(', ', array_map(fn($k) => "$k = ?", array_keys($data)));
        
        $sql = "UPDATE $table SET $set WHERE $where";
        
        $stmt = $this->query($sql, [...array_values($data), ...$whereParams]);
        
        return $stmt->rowCount();
    }
    
    /**
     * حذف
     */
    public function delete(string $table, string $where, array $params = []): int
    {
        $sql = "DELETE FROM $table WHERE $where";
        return $this->query($sql, $params)->rowCount();
    }
    
    /**
     * Transaction
     */
    public function transaction(callable $callback): mixed
    {
        $pdo = $this->connect();
        $pdo->beginTransaction();
        
        try {
            $result = $callback($this);
            $pdo->commit();
            return $result;
        } catch (Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
}
