<?php
/**
 * YHM - Database
 */

declare(strict_types=1);

class Database
{
    private ?PDO $pdo = null;
    
    public function __construct(
        private array $config,
    ) {}
    
    public function connect(): PDO
    {
        if ($this->pdo === null) {
            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=%s',
                $this->config['host'],
                $this->config['database'],
                $this->config['charset'] ?? 'utf8mb4'
            );
            
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
        }
        
        return $this->pdo;
    }
    
    public function query(string $sql, array $params = []): PDOStatement
    {
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
    
    public function fetchOne(string $sql, array $params = []): ?array
    {
        $result = $this->query($sql, $params)->fetch();
        return $result === false ? null : $result;
    }
    
    public function fetchAll(string $sql, array $params = []): array
    {
        return $this->query($sql, $params)->fetchAll();
    }
    
    public function fetchColumn(string $sql, array $params = []): mixed
    {
        return $this->query($sql, $params)->fetchColumn();
    }
    
    public function insert(string $table, array $data): int
    {
        $cols = implode(', ', array_keys($data));
        $phs = implode(', ', array_fill(0, count($data), '?'));
        $this->query("INSERT INTO $table ($cols) VALUES ($phs)", array_values($data));
        return (int) $this->connect()->lastInsertId();
    }
    
    public function update(string $table, array $data, string $where, array $params = []): int
    {
        $set = implode(', ', array_map(fn($k) => "$k = ?", array_keys($data)));
        return $this->query(
            "UPDATE $table SET $set WHERE $where",
            [...array_values($data), ...$params]
        )->rowCount();
    }
    
    public function delete(string $table, string $where, array $params = []): int
    {
        return $this->query("DELETE FROM $table WHERE $where", $params)->rowCount();
    }
    
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
