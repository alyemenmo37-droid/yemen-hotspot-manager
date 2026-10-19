<?php
declare(strict_types=1);

class CreateCardsTable extends Migration
{
    public function up(): void
    {
        $this->execute("
            CREATE TABLE cards (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                number VARCHAR(20) NOT NULL UNIQUE,
                amount INT UNSIGNED NOT NULL DEFAULT 0,
                speed VARCHAR(20) NOT NULL DEFAULT '256k/1M',
                speed_level_id INT UNSIGNED NULL,
                data_limit_mb BIGINT UNSIGNED NULL,
                data_used_mb BIGINT UNSIGNED NOT NULL DEFAULT 0,
                status ENUM('unused', 'active', 'expired', 'stopped') 
                    NOT NULL DEFAULT 'unused',
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                activated_at DATETIME NULL,
                expires_at DATETIME NULL,
                INDEX idx_number (number),
                INDEX idx_status (status),
                FOREIGN KEY (speed_level_id) REFERENCES speed_levels(id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
    }
    
    public function down(): void
    {
        $this->execute("DROP TABLE IF EXISTS cards");
    }
}
