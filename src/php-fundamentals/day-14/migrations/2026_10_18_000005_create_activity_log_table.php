<?php
declare(strict_types=1);

class CreateActivityLogTable extends Migration
{
    public function up(): void
    {
        $this->execute("
            CREATE TABLE activity_log (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                user_id BIGINT UNSIGNED NULL,
                action VARCHAR(50) NOT NULL,
                target_type VARCHAR(50) NULL,
                target_id BIGINT UNSIGNED NULL,
                details TEXT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
                INDEX idx_action (action)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
    }
    
    public function down(): void
    {
        $this->execute("DROP TABLE IF EXISTS activity_log");
    }
}
