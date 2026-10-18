<?php
/**
 * Migration: إنشاء جدول الفئات
 */

declare(strict_types=1);

class CreateCategoriesTable extends Migration
{
    public function up(): void
    {
        $this->execute("
            CREATE TABLE categories (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(50) NOT NULL UNIQUE,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
    }
    
    public function down(): void
    {
        $this->execute("DROP TABLE IF EXISTS categories");
    }
}
