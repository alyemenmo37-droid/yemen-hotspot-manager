<?php
declare(strict_types=1);

class CreateUsersTable extends Migration
{
    public function up(): void
    {
        $this->execute("
            CREATE TABLE users (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                email VARCHAR(150) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                role ENUM('admin', 'manager', 'operator') NOT NULL DEFAULT 'operator',
                is_active BOOLEAN NOT NULL DEFAULT TRUE,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
    }
    
    public function down(): void
    {
        $this->execute("DROP TABLE IF EXISTS users");
    }
}
