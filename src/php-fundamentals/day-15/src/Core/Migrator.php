<?php
/**
 * Day 14 - Migrator
 * محرك تشغيل Migrations
 */

declare(strict_types=1);

class Migrator
{
    private array $migrations = [];
    
    public function __construct(
        private PDO $pdo,
        private string $migrationsPath,
    ) {
        $this->initTable();
        $this->loadMigrations();
    }
    
    /**
     * جدول تتبع الـ migrations
     */
    private function initTable(): void
    {
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS migrations (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                migration VARCHAR(255) NOT NULL UNIQUE,
                batch INT UNSIGNED NOT NULL,
                executed_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB
        ");
    }
    
    /**
     * تحميل كل ملفات Migration
     */
    private function loadMigrations(): void
    {
        $files = glob($this->migrationsPath . '/*.php');
        sort($files);
        
        foreach ($files as $file) {
            $this->migrations[] = $file;
        }
    }
    
    /**
     * تنفيذ كل الـ migrations غير المنفذة
     */
    public function migrate(): void
    {
        $executed = $this->getExecuted();
        $batch = $this->getNextBatch();
        $count = 0;
        
        foreach ($this->migrations as $file) {
            $name = basename($file, '.php');
            
            if (in_array($name, $executed, true)) {
                continue;
            }
            
            echo "🔄 Running: $name\n";
            
            // تحميل الكلاس
            require_once $file;
            $className = $this->getClassName($name);
            
            if (!class_exists($className)) {
                echo "   ⚠️ لم أجد الكلاس: $className\n";
                continue;
            }
            
            // تنفيذ
            try {
                $migration = new $className($this->pdo);
                $migration->up();
                
                // سجل
                $this->pdo->prepare("
                    INSERT INTO migrations (migration, batch)
                    VALUES (?, ?)
                ")->execute([$name, $batch]);
                
                echo "   ✅ تم\n";
                $count++;
                
            } catch (Exception $e) {
                echo "   ❌ فشل: " . $e->getMessage() . "\n";
                throw $e;
            }
        }
        
        if ($count === 0) {
            echo "✨ لا توجد migrations جديدة\n";
        } else {
            echo "\n🎉 تم تنفيذ $count migration(s) في batch #$batch\n";
        }
    }
    
    /**
     * التراجع عن آخر batch
     */
    public function rollback(): void
    {
        $lastBatch = $this->pdo->query("
            SELECT MAX(batch) FROM migrations
        ")->fetchColumn();
        
        if ($lastBatch === null) {
            echo "⚠️ لا يوجد ما يمكن التراجع عنه\n";
            return;
        }
        
        $rows = $this->pdo->prepare("
            SELECT migration FROM migrations 
            WHERE batch = ? 
            ORDER BY id DESC
        ");
        $rows->execute([$lastBatch]);
        $migrations = $rows->fetchAll(PDO::FETCH_COLUMN);
        
        echo "🔙 Rollback batch #$lastBatch\n";
        
        foreach ($migrations as $name) {
            $file = $this->migrationsPath . '/' . $name . '.php';
            
            if (!file_exists($file)) {
                echo "   ⚠️ الملف غير موجود: $name\n";
                continue;
            }
            
            echo "🔄 Rolling back: $name\n";
            
            require_once $file;
            $className = $this->getClassName($name);
            
            try {
                $migration = new $className($this->pdo);
                $migration->down();
                
                $this->pdo->prepare("
                    DELETE FROM migrations WHERE migration = ?
                ")->execute([$name]);
                
                echo "   ✅ تم\n";
                
            } catch (Exception $e) {
                echo "   ❌ فشل: " . $e->getMessage() . "\n";
            }
        }
    }
    
    /**
     * حالة الـ migrations
     */
    public function status(): void
    {
        $executed = $this->getExecuted();
        
        echo "\n📋 Migration Status:\n";
        echo str_repeat("─", 70) . "\n";
        
        foreach ($this->migrations as $file) {
            $name = basename($file, '.php');
            $isExecuted = in_array($name, $executed, true);
            
            $icon = $isExecuted ? '✅' : '⏳';
            $status = $isExecuted ? 'Ran' : 'Pending';
            
            printf("   %s %-50s [%s]\n", $icon, $name, $status);
        }
        
        echo str_repeat("─", 70) . "\n";
        printf("   الإجمالي: %d | المنفذة: %d | المعلقة: %d\n",
            count($this->migrations),
            count($executed),
            count($this->migrations) - count($executed)
        );
    }
    
    private function getExecuted(): array
    {
        return $this->pdo->query("
            SELECT migration FROM migrations ORDER BY id
        ")->fetchAll(PDO::FETCH_COLUMN);
    }
    
    private function getNextBatch(): int
    {
        return (int)$this->pdo->query("
            SELECT COALESCE(MAX(batch), 0) + 1 FROM migrations
        ")->fetchColumn();
    }
    
    private function getClassName(string $filename): string
    {
        // 2026_10_18_000001_create_categories_table
        // → CreateCategoriesTable
        $parts = explode('_', $filename);
        // إزالة التاريخ (4 أجزاء)
        $name = implode('_', array_slice($parts, 4));
        // تحويل إلى PascalCase
        return str_replace(' ', '', ucwords(str_replace('_', ' ', $name)));
    }
}
