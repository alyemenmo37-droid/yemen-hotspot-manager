<?php
/**
 * Day 5 - JSON Basics
 */

echo "╔" . str_repeat("═", 50) . "╗\n";
echo "║  📋 أساسيات JSON في PHP                       ║\n";
echo "╚" . str_repeat("═", 50) . "╝\n\n";


// ═══════════════════════════════════════
// 1. من مصفوفة PHP إلى JSON
// ═══════════════════════════════════════
$cards = [
    ['number' => '1234567890', 'amount' => 250, 'status' => 'active'],
    ['number' => '0987654321', 'amount' => 500, 'status' => 'unused'],
    ['number' => '1122334455', 'amount' => 1000, 'status' => 'expired'],
];

// بدون تنسيق
$json1 = json_encode($cards);
echo "بدون تنسيق:\n$json1\n\n";

// مع تنسيق جميل
$json2 = json_encode($cards, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
echo "مع تنسيق:\n$json2\n\n";


// ═══════════════════════════════════════
// 2. من JSON إلى مصفوفة
// ═══════════════════════════════════════
$decoded = json_decode($json2, true);  // true = مصفوفة

echo "عدد البطاقات بعد الفك: " . count($decoded) . "\n";
foreach ($decoded as $card) {
    echo "  - {$card['number']} | {$card['amount']} ريال | {$card['status']}\n";
}


// ═══════════════════════════════════════
// 3. كائن (Object) بدل مصفوفة
// ═══════════════════════════════════════
$obj = json_decode($json2);  // بدون true = object

echo "\nككائن (object):\n";
echo "  أول بطاقة: " . $obj[0]->number . "\n";
echo "  السعر: " . $obj[0]->amount . "\n";


// ═══════════════════════════════════════
// 4. معالجة الأخطاء
// ═══════════════════════════════════════
$invalidJson = '{"name": "test", }';  // JSON غير صالح

$result = json_decode($invalidJson, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    echo "\n❌ خطأ في JSON: " . json_last_error_msg() . "\n";
}


// ═══════════════════════════════════════
// 5. كتابة JSON إلى ملف
// ═══════════════════════════════════════
file_put_contents(
    'cards.json',
    json_encode($cards, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
);

echo "\n✅ تم حفظ cards.json\n";


// ═══════════════════════════════════════
// 6. قراءة JSON من ملف
// ═══════════════════════════════════════
if (file_exists('cards.json')) {
    $content = file_get_contents('cards.json');
    $loadedCards = json_decode($content, true);
    
    echo "📖 عدد البطاقات من الملف: " . count($loadedCards) . "\n";
}


// ═══════════════════════════════════════
// 7. نصائح JSON
// ═══════════════════════════════════════
echo "\n💡 خيارات مفيدة:\n";
echo "  JSON_PRETTY_PRINT  → تنسيق جميل\n";
echo "  JSON_UNESCAPED_UNICODE → احتفظ بالعربية\n";
echo "  JSON_UNESCAPED_SLASHES → لا تهرب / \n";

$options = JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;
$nice = json_encode(['url' => 'http://example.com/البطاقات'], $options);
echo "\nمثال:\n$nice\n";


echo "\n✅ اكتمل التمرين!\n";
