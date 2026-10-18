<?php
/**
 * Day 13 - SQL Injection Demo
 * ⚠️ هذا الملف للتعليم فقط - لا تستخدمه في الإنتاج
 */

declare(strict_types=1);

require_once __DIR__ . '/../day-10/bootstrap.php';

$pdo = getPDO();

echo "╔" . str_repeat("═", 70) . "╗\n";
echo "║  🚨 SQL Injection - عرض حي                     ║\n";
echo "╚" . str_repeat("═", 70) . "╝\n\n";


// ═══════════════════════════════════════
// 1. مدخلات ضارة
// ═══════════════════════════════════════
$malicious = "1' OR '1'='1";
$malicious2 = "'; DROP TABLE cards; --";
$malicious3 = "1' UNION SELECT * FROM users --";

echo "🎯 المدخلات الضارة:\n";
echo "   1: $malicious\n";
echo "   2: $malicious2\n";
echo "   3: $malicious3\n\n";


// ═══════════════════════════════════════
// 2. الطريقة القديمة (خطر!)
// ═══════════════════════════════════════
echo "❌ الطريقة القديمة (خطر!):\n";
echo str_repeat("─", 70) . "\n";

// ⚠️ لا تستخدم هذه في الإنتاج!
$sql = "SELECT * FROM cards WHERE id = '$malicious'";
echo "   SQL الناتج: $sql\n";

// ⚠️ لو نفّذناه (لن نفعل!):
// $result = $pdo->query($sql);
// النتيجة: كل البطاقات!

// ═══════════════════════════════════════
// 3. الطريقة الآمنة (Prepared)
// ═══════════════════════════════════════
echo "\n✅ الطريقة الآمنة (Prepared):\n";
echo str_repeat("─", 70) . "\n";

$stmt = $pdo->prepare("SELECT * FROM cards WHERE id = ?");
$stmt->execute([$malicious]);
$result = $stmt->fetch();

echo "   النتيجة: " . ($result === false ? "لا شيء (آمن!)" : "وُجد سجل") . "\n";
echo "   ✅ SQL Injection فشل!\n";


// ═══════════════════════════════════════
// 4. اختبارات شاملة
// ═══════════════════════════════════════
echo "\n📋 اختبار مدخلات ضارة:\n";
echo str_repeat("─", 70) . "\n";

$payloads = [
    "' OR '1'='1",
    "'; DROP TABLE cards; --",
    "' UNION SELECT * FROM users --",
    "1' AND SLEEP(5) --",
    "admin'--",
    "<script>alert(1)</script>",
];

foreach ($payloads as $i => $payload) {
    // ❌ خطر (لا ننفذ)
    echo "\n" . ($i + 1) . ". المدخل: " . substr($payload, 0, 40) . "\n";
    
    // ✅ آمن
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM cards WHERE number = ?");
    $stmt->execute([$payload]);
    $count = $stmt->fetchColumn();
    
    echo "   ✅ Prepared: $count نتيجة (آمن)\n";
}


// ═══════════════════════════════════════
// 5. Validation - طبقة إضافية
// ═══════════════════════════════════════
echo "\n📌 Input Validation:\n";
echo str_repeat("─", 70) . "\n";

function validateCardNumber(string $input): string
{
    // 1. تنظيف
    $cleaned = trim($input);
    
    // 2. تحقق من الصيغة
    if (!preg_match('/^\d{10}$/', $cleaned)) {
        throw new InvalidArgumentException(
            'رقم البطاقة يجب أن يكون 10 أرقام بالضبط'
        );
    }
    
    return $cleaned;
}

// اختبار
$tests = [
    '1234567890' => '✅ صحيح',
    "1234567890' OR '1'='1" => '❌ حقن',
    '123' => '❌ قصير',
    'abcdefghij' => '❌ ليس أرقام',
    '12345 67890' => '❌ مسافات',
];

foreach ($tests as $input => $expected) {
    try {
        $validated = validateCardNumber((string)$input);
        echo "   '$input' → ✅ صحيح: $validated\n";
    } catch (InvalidArgumentException $e) {
        echo "   '$input' → ❌ " . $e->getMessage() . "\n";
    }
}


echo "\n✅ اكتمل العرض - YHM محصن ضد SQLi!\n";
