<?php
/**
 * Day 10 - PDO Connection
 * YHM Project
 */

declare(strict_types=1);

echo "╔" . str_repeat("═", 55) . "╗\n";
echo "║  🔌 اختبار الاتصال بقاعدة YHM                        ║\n";
echo "╚" . str_repeat("═", 55) . "╝\n\n";

// ═══════════════════════════════════════
// 1. الإعدادات
// ═══════════════════════════════════════
$host = '127.0.0.1';
$dbname = 'yhm_db';
$username = 'yhm_user';
$password = 'alyemenmo370123';

// ═══════════════════════════════════════
// 2. محاولة الاتصال
// ═══════════════════════════════════════
try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
    
    echo "✅ تم الاتصال بنجاح!\n";
    echo "   Server: " . $pdo->getAttribute(PDO::ATTR_SERVER_VERSION) . "\n";
    echo "   Driver: " . $pdo->getAttribute(PDO::ATTR_DRIVER_NAME) . "\n";
    echo "   Database: $dbname\n\n";
    
} catch (PDOException $e) {
    echo "❌ فشل الاتصال: " . $e->getMessage() . "\n";
    exit(1);
}

// ═══════════════════════════════════════
// 3. اختبار استعلام بسيط
// ═══════════════════════════════════════
echo "📊 إحصائيات سريعة:\n";

$total = $pdo->query("SELECT COUNT(*) FROM cards")->fetchColumn();
echo "   إجمالي البطاقات: $total\n";

$active = $pdo->query("SELECT COUNT(*) FROM cards WHERE status = 'active'")->fetchColumn();
echo "   البطاقات النشطة: $active\n";

$tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
echo "\n📋 الجداول المتاحة:\n";
foreach ($tables as $table) {
    echo "   • $table\n";
}

echo "\n✅ PDO يعمل بشكل مثالي!\n";
