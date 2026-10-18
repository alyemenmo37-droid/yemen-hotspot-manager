<?php
/**
 * YHM - OOP Entry Point
 * اليوم 6 - المشروع الشامل
 */

declare(strict_types=1);

// ═══════════════════════════════════════
// 1. تحميل الملفات
// ═══════════════════════════════════════
require_once __DIR__ . '/src/Logger.php';
require_once __DIR__ . '/src/Database.php';
require_once __DIR__ . '/src/Models/Card.php';
require_once __DIR__ . '/src/Services/CardService.php';

// ═══════════════════════════════════════
// 2. الإعداد
// ═══════════════════════════════════════
$dataDir = __DIR__ . '/data';
$cardsFile = $dataDir . '/cards.json';
$logFile = $dataDir . '/yhm.log';

// تنظيف للاختبار
if (is_dir($dataDir)) {
    array_map('unlink', glob("$dataDir/*"));
} else {
    mkdir($dataDir, 0755, true);
}

// ═══════════════════════════════════════
// 3. إنشاء الخدمات
// ═══════════════════════════════════════
$db = new Database($cardsFile);
$logger = new Logger($logFile);
$service = new CardService($db, $logger);

// ═══════════════════════════════════════
// 4. العرض
// ═══════════════════════════════════════
echo "\n";
echo "╔" . str_repeat("═", 60) . "╗\n";
echo "║  📡 YHM - نظام OOP كامل (اليوم 6)                       ║\n";
echo "╚" . str_repeat("═", 60) . "╝\n\n";


// ────────────────────────────────────────
// اختبار 1: إضافة يدوية
// ────────────────────────────────────────
echo "📝 إضافة 3 بطاقات يدوية:\n";

try {
    $service->add('1234567890', 250, '256k/1M');
    $service->add('0987654321', 500, '512k/2M');
    $service->add('1122334455', 1000, '2M/15M');
    echo "   ✅ تم إضافة 3 بطاقات\n";
} catch (Exception $e) {
    echo "   ❌ خطأ: " . $e->getMessage() . "\n";
}


// ────────────────────────────────────────
// اختبار 2: كشف التكرار
// ────────────────────────────────────────
echo "\n🔒 اختبار كشف التكرار:\n";

try {
    $service->add('1234567890', 300, '256k/1M');
    echo "   ❌ لم يُكشف التكرار!\n";
} catch (Exception $e) {
    echo "   ✅ كُشف: " . $e->getMessage() . "\n";
}


// ────────────────────────────────────────
// اختبار 3: توليد دفعة
// ────────────────────────────────────────
echo "\n🎲 توليد 5 بطاقات عشوائية:\n";

$batch = $service->generateBatch(5, 250, '256k/1M');
foreach ($batch as $card) {
    echo "   • {$card->number}\n";
}


// ────────────────────────────────────────
// اختبار 4: تفعيل
// ────────────────────────────────────────
echo "\n⚡ تفعيل بطاقة:\n";

$service->activate('1234567890');
$service->activate('0987654321');

$card = $service->find('1234567890');
if ($card) {
    echo "   " . $card->label() . "\n";
}


// ────────────────────────────────────────
// اختبار 5: عرض كل البطاقات
// ────────────────────────────────────────
echo "\n📋 كل البطاقات:\n";
echo str_repeat("─", 65) . "\n";

foreach ($service->all() as $card) {
    echo "   " . $card->label() . "\n";
}

echo str_repeat("─", 65) . "\n";


// ────────────────────────────────────────
// اختبار 6: الإحصائيات
// ────────────────────────────────────────
$stats = $service->stats();

echo "\n📊 الإحصائيات:\n";
echo "   إجمالي البطاقات: {$stats['total']}\n";
echo "   القيمة الإجمالية: " . number_format($stats['totalValue']) . " ريال\n";
echo "   المبيعات (المفعّلة): " . number_format($stats['totalSales']) . " ريال\n";

echo "\n   حسب الحالة:\n";
foreach ($stats['byStatus'] as $status => $count) {
    printf("     • %-10s: %d\n", $status, $count);
}


// ────────────────────────────────────────
// اختبار 7: فلترة
// ────────────────────────────────────────
echo "\n🔍 البطاقات النشطة فقط:\n";
foreach ($service->byStatus(Card::STATUS_ACTIVE) as $card) {
    echo "   " . $card->label() . "\n";
}


// ────────────────────────────────────────
// اختبار 8: السجل
// ────────────────────────────────────────
echo "\n📋 آخر 5 عمليات:\n";
foreach ($logger->getRecent(5) as $line) {
    echo "   " . trim($line) . "\n";
}


// ────────────────────────────────────────
// الختام
// ────────────────────────────────────────
echo "\n" . str_repeat("═", 62) . "\n";
echo "🎉 YHM اليوم 6 مكتمل — نظام OOP احترافي!\n";
echo str_repeat("═", 62) . "\n\n";
