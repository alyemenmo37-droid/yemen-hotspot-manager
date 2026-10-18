<?php
/**
 * YHM - MySQL Version
 * اليوم 10
 */

declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/src/Database.php';
require_once __DIR__ . '/src/Models/Card.php';
require_once __DIR__ . '/src/Services/CardService.php';

// ═══════════════════════════════════════
// الإعداد
// ═══════════════════════════════════════
$config = require __DIR__ . '/config.php';
$db = new Database($config);
$service = new CardService($db);

// ═══════════════════════════════════════
// الاختبار
// ═══════════════════════════════════════

echo "\n";
echo "╔" . str_repeat("═", 60) . "╗\n";
echo "║  📡 YHM - MySQL Version (اليوم 10)                      ║\n";
echo "╚" . str_repeat("═", 60) . "╝\n\n";

// 1. تنظيف بيانات الاختبار
echo "🧹 تنظيف بيانات الاختبار...\n";
$db->query("DELETE FROM cards WHERE number IN ('1111111111', '2222222222', '3333333333')");
$db->query("DELETE FROM activity_log WHERE action IN ('create', 'activate', 'batch')");

// 2. إضافة بطاقات
echo "\n📝 إضافة 3 بطاقات:\n";

try {
    $service->add('1111111111', 250, '256k/1M');
    $service->add('2222222222', 500, '512k/2M');
    $service->add('3333333333', 1000, '2M/15M');
    echo "   ✅ تم إضافة 3 بطاقات\n";
} catch (Exception $e) {
    echo "   ❌ " . $e->getMessage() . "\n";
}

// 3. توليد دفعة
echo "\n🎲 توليد 5 بطاقات عشوائية:\n";
$batch = $service->generateBatch(5, 250, '256k/1M');
foreach ($batch as $card) {
    echo "   • {$card->number}\n";
}

// 4. تفعيل
echo "\n⚡ تفعيل بطاقات:\n";
$service->activate('1111111111');
$service->activate('2222222222');
echo "   ✅ تم تفعيل 2 بطاقات\n";

// 5. عرض الكل
echo "\n📋 كل البطاقات:\n";
echo str_repeat("─", 65) . "\n";

foreach ($service->all() as $card) {
    echo "   " . $card->label() . "\n";
}

echo str_repeat("─", 65) . "\n";

// 6. الإحصائيات
$stats = $service->stats();

echo "\n📊 الإحصائيات:\n";
echo "   إجمالي: {$stats['total']}\n";
echo "   نشطة: {$stats['byStatus']['active']}\n";
echo "   غير مستخدمة: {$stats['byStatus']['unused']}\n";
echo "   المبيعات: " . number_format($stats['sales']) . " ريال\n";

// 7. البحث
echo "\n🔍 البحث عن 1111111111:\n";
$found = $service->find('1111111111');
if ($found) {
    echo "   " . $found->label() . "\n";
}

// 8. تنظيف
echo "\n🧹 تنظيف...\n";
foreach (['1111111111', '2222222222', '3333333333'] as $num) {
    $service->delete($num);
}
foreach ($batch as $card) {
    $service->delete($card->number);
}
echo "   ✅ تم التنظيف\n";

echo "\n" . str_repeat("═", 62) . "\n";
echo "🎉 YHM MySQL مكتمل — اليوم 10!\n";
echo str_repeat("═", 62) . "\n\n";
