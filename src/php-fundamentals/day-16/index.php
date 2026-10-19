<?php
/**
 * YHM - Composer Demo Entry Point
 */

declare(strict_types=1);

// ═══════════════════════════════════════
// 1. تحميل Composer Autoloader
// ═══════════════════════════════════════
require_once __DIR__ . '/vendor/autoload.php';

// ═══════════════════════════════════════
// 2. استخدام الفئات (بدون require!)
// ═══════════════════════════════════════
use Yhm\Models\Card;
use Yhm\Services\CardService;
use Yhm\Utils\Str;
use Yhm\Utils\Validator;

echo "╔" . str_repeat("═", 60) . "╗\n";
echo "║  📦 YHM - Composer + PSR-4 Demo                ║\n";
echo "╚" . str_repeat("═", 60) . "╝\n\n";

// ═══════════════════════════════════════
// 3. اختبار Str
// ═══════════════════════════════════════
echo "📌 Str Utility:\n";
echo "   عشوائي: " . Str::random(10) . "\n";
echo "   إخفاء: " . Str::mask('1234567890') . "\n";
echo "   مال: " . Str::money(1500) . "\n";
echo "   بايتات: " . Str::bytes(5 * 1024 * 1024) . "\n";

// ═══════════════════════════════════════
// 4. اختبار Validator
// ═══════════════════════════════════════
echo "\n📌 Validator:\n";

try {
    echo "   ✅ " . Validator::cardNumber('1234567890') . "\n";
} catch (\Exception $e) {
    echo "   ❌ " . $e->getMessage() . "\n";
}

try {
    Validator::cardNumber('123');
} catch (\Exception $e) {
    echo "   ✅ رُفض: " . $e->getMessage() . "\n";
}

// ═══════════════════════════════════════
// 5. اختبار Card + CardService
// ═══════════════════════════════════════
echo "\n📌 CardService:\n";

$service = new CardService();

try {
    $service->add('1111111111', 250, '256k/1M');
    $service->add('2222222222', 500, '512k/2M');
    echo "   ✅ أُضيفت 2 بطاقات\n";
} catch (\Exception $e) {
    echo "   ❌ " . $e->getMessage() . "\n";
}

// توليد دفعة
echo "\n🎲 توليد 3 بطاقات عشوائية:\n";
$batch = $service->generate(3, 250, '256k/1M');
foreach ($batch as $card) {
    echo "   • {$card->number}\n";
}

// عرض الكل
echo "\n📋 كل البطاقات:\n";
foreach ($service->all() as $card) {
    echo "   " . $card->label() . "\n";
}

// ═══════════════════════════════════════
// 6. النتيجة
// ═══════════════════════════════════════
echo "\n" . str_repeat("═", 62) . "\n";
echo "🎉 Composer يعمل! لا require_once بعد اليوم!\n";
echo str_repeat("═", 62) . "\n\n";
