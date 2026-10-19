<?php
/**
 * YHM - RateLimiter
 */

declare(strict_types=1);

class RateLimiter
{
    public function __construct(
        private Database $db,
    ) {
        $this->initTable();
    }
    
    private function initTable(): void
    {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS rate_limits (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                identifier VARCHAR(100) NOT NULL,
                action VARCHAR(50) NOT NULL,
                attempts INT UNSIGNED NOT NULL DEFAULT 1,
                first_attempt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                last_attempt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                blocked_until DATETIME NULL,
                UNIQUE KEY uniq_identifier_action (identifier, action)
            ) ENGINE=InnoDB
        ");
    }
    
    public function isAllowed(
        string $identifier,
        string $action,
        int $maxAttempts = 5,
        int $windowSeconds = 60,
        int $blockSeconds = 300
    ): array {
        // تحقق من الحظر
        $row = $this->db->fetchOne("
            SELECT * FROM rate_limits 
            WHERE identifier = ? AND action = ?
            AND blocked_until IS NOT NULL AND blocked_until > NOW()
        ", [$identifier, $action]);
        
        if ($row) {
            return [
                'allowed' => false,
                'reason' => 'blocked',
                'remaining' => strtotime($row['blocked_until']) - time(),
            ];
        }
        
        // جلب السجل
        $row = $this->db->fetchOne("
            SELECT * FROM rate_limits 
            WHERE identifier = ? AND action = ?
        ", [$identifier, $action]);
        
        if (!$row) {
            $this->db->query("
                INSERT INTO rate_limits (identifier, action, attempts)
                VALUES (?, ?, 1)
            ", [$identifier, $action]);
            
            return ['allowed' => true, 'attempts' => 1];
        }
        
        // انتهت النافذة؟
        if (time() - strtotime($row['first_attempt']) > $windowSeconds) {
            $this->db->query("
                UPDATE rate_limits 
                SET attempts = 1, first_attempt = NOW(), 
                    last_attempt = NOW(), blocked_until = NULL
                WHERE id = ?
            ", [$row['id']]);
            return ['allowed' => true, 'attempts' => 1];
        }
        
        // تجاوز؟
        if ($row['attempts'] >= $maxAttempts) {
            $this->db->query("
                UPDATE rate_limits 
                SET blocked_until = DATE_ADD(NOW(), INTERVAL ? SECOND)
                WHERE id = ?
            ", [$blockSeconds, $row['id']]);
            
            return [
                'allowed' => false,
                'reason' => 'max_attempts',
                'remaining' => $blockSeconds,
            ];
        }
        
        $this->db->query("
            UPDATE rate_limits 
            SET attempts = attempts + 1, last_attempt = NOW()
            WHERE id = ?
        ", [$row['id']]);
        
        return [
            'allowed' => true,
            'attempts' => $row['attempts'] + 1,
            'remaining' => $maxAttempts - $row['attempts'] - 1,
        ];
    }
    
    public function reset(string $identifier, string $action): void
    {
        $this->db->query("
            DELETE FROM rate_limits 
            WHERE identifier = ? AND action = ?
        ", [$identifier, $action]);
    }
}
