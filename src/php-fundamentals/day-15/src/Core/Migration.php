<?php
/**
 * Day 14 - Migration Base Class
 * الفئة الأساسية لكل Migration
 */

declare(strict_types=1);

abstract class Migration
{
    protected PDO $pdo;
    
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }
    
    /**
     * تنفيذ التغيير
     */
    abstract public function up(): void;
    
    /**
     * التراجع عن التغيير
     */
    abstract public function down(): void;
    
    /**
     * اسم الـ Migration (من اسم الملف)
     */
    public function getName(): string
    {
        return static::class;
    }
    
    /**
     * Helper: تنفيذ SQL
     */
    protected function execute(string $sql): void
    {
        $this->pdo->exec($sql);
        echo "   ✅ " . substr($sql, 0, 80) . "...\n";
    }
}
