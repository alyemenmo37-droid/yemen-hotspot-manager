#!/usr/bin/env php
<?php
/**
 * YHM Migrations CLI
 * 
 * Usage:
 *   php migrate.php migrate   — تنفيذ كل الـ migrations
 *   php migrate.php rollback  — التراجع عن آخر batch
 *   php migrate.php status    — حالة الـ migrations
 *   php migrate.php seed      — ملء البيانات
 *   php migrate.php fresh     — إعادة إنشاء كل شيء
 */

declare(strict_types=1);

require_once __DIR__ . '/src/Core/Migration.php';
require_once __DIR__ . '/src/Core/Migrator.php';
require_once __DIR__ . '/database/seeders/DatabaseSeeder.php';

// ═══════════════════════════════════════
// الاتصال
// ═══════════════════════════════════════
$config = require __DIR__ . '/../day-10/config.php';

$dsn = "mysql:host={$config['host']};dbname={$config['database']};charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $config['username'], $config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die("❌ فشل الاتصال: " . $e->getMessage() . "\n");
}

// ═══════════════════════════════════════
// الأمر
// ═══════════════════════════════════════
$command = $argv[1] ?? 'help';
//$migrator = new Migrator($pdo, __DIR__ . '/migrations');
$migrator = new Migrator($pdo, __DIR__ . '/database/migrations');

switch ($command) {
    case 'migrate':
        echo "🚀 Running migrations...\n";
        echo str_repeat("─", 70) . "\n";
        $migrator->migrate();
        break;
        
    case 'rollback':
        echo "🔙 Rolling back...\n";
        echo str_repeat("─", 70) . "\n";
        $migrator->rollback();
        break;
        
    case 'status':
        $migrator->status();
        break;
        
    case 'seed':
        echo "🌱 Seeding...\n";
        echo str_repeat("─", 70) . "\n";
        $seeder = new DatabaseSeeder($pdo);
        $seeder->run();
        break;
        
    case 'fresh':
        echo "🔄 Fresh migration...\n";
        echo str_repeat("─", 70) . "\n";
        
        // احذف كل الجداول
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
        $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
        foreach ($tables as $table) {
            if ($table !== 'migrations') {
                $pdo->exec("DROP TABLE IF EXISTS $table");
                echo "   🗑️ حُذف: $table\n";
            }
        }
        $pdo->exec("TRUNCATE migrations");
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
        
        echo "\n";
        $migrator->migrate();
        echo "\n";
        $seeder = new DatabaseSeeder($pdo);
        $seeder->run();
        break;
        
    case 'help':
    default:
        echo "YHM Migrations CLI\n";
        echo "==================\n\n";
        echo "Usage:\n";
        echo "  php migrate.php migrate   — تنفيذ migrations\n";
        echo "  php migrate.php rollback  — التراجع عن آخر batch\n";
        echo "  php migrate.php status    — حالة migrations\n";
        echo "  php migrate.php seed      — ملء البيانات\n";
        echo "  php migrate.php fresh     — إعادة كل شيء\n\n";
        break;
}
