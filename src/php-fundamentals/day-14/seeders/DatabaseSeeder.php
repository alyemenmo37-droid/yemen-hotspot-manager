<?php
/**
 * Database Seeder
 * يملأ البيانات الأولية
 */

declare(strict_types=1);

class DatabaseSeeder
{
    public function __construct(
        private PDO $pdo,
    ) {}
    
    public function run(): void
    {
        echo "🌱 Seeding database...\n";
        
        // 1. Categories
        $this->seedCategories();
        
        // 2. Speed Levels
        $this->seedSpeedLevels();
        
        // 3. Admin User
        $this->seedAdminUser();
        
        echo "✅ Database seeded successfully!\n";
    }
    
    private function seedCategories(): void
    {
        // تحقق إذا موجودة
        if ($this->pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn() > 0) {
            echo "   ⏭️ Categories already seeded\n";
            return;
        }
        
        $stmt = $this->pdo->prepare("INSERT INTO categories (name) VALUES (?)");
        
        foreach (['بطيء', 'متوسطة', 'عالية', 'مفتوح'] as $name) {
            $stmt->execute([$name]);
        }
        
        echo "   ✅ 4 categories seeded\n";
    }
    
    private function seedSpeedLevels(): void
    {
        if ($this->pdo->query("SELECT COUNT(*) FROM speed_levels")->fetchColumn() > 0) {
            echo "   ⏭️ Speed levels already seeded\n";
            return;
        }
        
        $stmt = $this->pdo->prepare("
            INSERT INTO speed_levels 
            (category_id, name, rate_limit, price, duration_minutes, data_limit_mb, is_default)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        $levels = [
            [1, 'بطيء - أسبوعي', '512k/512k', 100, 10080, 500, 0],
            [2, 'متوسطة - يومي', '256k/1M', 250, 1440, 1024, 1],
            [3, 'عالية - 3 أيام', '512k/2M', 500, 4320, 3072, 0],
            [4, 'مفتوح - شهري', '2M/15M', 2000, 43200, 102400, 0],
        ];
        
        foreach ($levels as $level) {
            $stmt->execute($level);
        }
        
        echo "   ✅ 4 speed levels seeded\n";
    }
    
    private function seedAdminUser(): void
    {
        if ($this->pdo->query("SELECT COUNT(*) FROM users")->fetchColumn() > 0) {
            echo "   ⏭️ Admin user already exists\n";
            return;
        }
        
        $this->pdo->prepare("
            INSERT INTO users (name, email, password, role)
            VALUES (?, ?, ?, 'admin')
        ")->execute([
            'المدير',
            'admin@yhm.local',
            password_hash('admin123', PASSWORD_DEFAULT),
        ]);
        
        echo "   ✅ Admin user seeded (admin@yhm.local / admin123)\n";
    }
}
