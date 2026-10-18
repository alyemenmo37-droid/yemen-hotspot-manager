<?php
declare(strict_types=1);

class CreateSpeedLevelsTable extends Migration
{
    public function up(): void
    {
        $this->execute("
            CREATE TABLE speed_levels (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                category_id INT UNSIGNED NOT NULL,
                name VARCHAR(50) NOT NULL,
                rate_limit VARCHAR(20) NOT NULL,
                price INT UNSIGNED NOT NULL,
                duration_minutes INT UNSIGNED NOT NULL DEFAULT 1440,
                data_limit_mb BIGINT UNSIGNED NULL,
                is_default BOOLEAN NOT NULL DEFAULT FALSE,
                FOREIGN KEY (category_id) REFERENCES categories(id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
    }
    
    public function down(): void
    {
        $this->execute("DROP TABLE IF EXISTS speed_levels");
    }
}
