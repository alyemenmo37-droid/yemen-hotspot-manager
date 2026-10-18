<?php
/**
 * Day 10 - Prepared Statements
 * الحماية من SQL Injection
 */

declare(strict_types=1);

//require_once __DIR__ . '/config.php';
require_once __DIR__ . '/bootstrap.php';
echo "╔" . str_repeat("═", 55) . "╗\n";
echo "║  🛡️ Prepared Statements - الحماية              ║\n";
echo "╚" . str_repeat("═", 55) . "╝\n\n";

$pdo = getPDO();

// ═══════════════════════════════════════
// 1. Positional Parameters (?)
// ═══════════════════════════════════════
echo "📌 1. Positional Parameters:\n";

$stmt = $pdo->prepare("SELECT * FROM cards WHERE status = ? AND amount >= ?");
$stmt->execute(['active', 200]);
$cards = $stmt->fetchAll();

foreach ($cards as $card) {
    echo "   • {$card['number']} | {$card['amount']} | {$card['status']}\n";
}


// ═══════════════════════════════════════
// 2. Named Parameters (:name)
// ═══════════════════════════════════════
echo "\n📌 2. Named Parameters:\n";

$stmt = $pdo->prepare("
    SELECT * FROM cards 
    WHERE status = :status 
    AND amount BETWEEN :min AND :max
");

$stmt->execute([
    ':status' => 'unused',
    ':min' => 200,
    ':max' => 600,
]);

$cards = $stmt->fetchAll();

foreach ($cards as $card) {
    echo "   • {$card['number']} | {$card['amount']} | {$card['status']}\n";
}


// ═══════════════════════════════════════
// 3. الاختبار الخطير - SQL Injection
// ═══════════════════════════════════════
echo "\n🚨 3. اختبار SQL Injection:\n";

// محاولة هجوم
$malicious = "1234567890' OR '1'='1";
echo "   المحاولة: $malicious\n";

// ❌ الطريقة القديمة (خطيرة)
echo "   ❌ بدون Prepared: ";
$sql = "SELECT * FROM cards WHERE number = '$malicious'";
//$sql = "SELECT * FROM cards WHERE number = '1234567890' OR '1'='1'";
$st = $pdo->query($sql);
$sa = $st->fetchAll(PDO::FETCH_ASSOC);
foreach ($sa as $s) {
    echo "  {$s['number']} | {$s['amount']} | {$s['status']}\n";
}
echo "SQL = $sql\n";
echo "   → لو نُفّذ، سيُعيد كل البطاقات!\n\n";

// ✅ الطريقة الآمنة
echo "   ✅ مع Prepared: ";
$stmt = $pdo->prepare("SELECT * FROM cards WHERE number = ?");
//$stmt = $pdo->prepare("SELECT * FROM cards WHERE number = '1234567890' OR '1'='1'");
$stmt->execute([$malicious]);
$result = $stmt->fetchAll();
foreach ($result as $s) {
    echo "hhhhhh  {$s['number']} | {$s['amount']} | {$s['status']}\n";
}/*
if ($result === false) {
    echo "لا نتائج (آمن!)\n";
} else {
    echo "وجدنا: {$result['number']}\n";
}*/


// ═══════════════════════════════════════
// 4. الأخطاء الشائعة
// ═══════════════════════════════════════
echo "\n⚠️ 4. الأخطاء الشائعة:\n";

// ❌ خطأ: بدون prepared
$unsafe = "SELECT * FROM cards WHERE status = 'active'";
echo "   ❌ بدون prepared: $unsafe\n";

// ✅ صحيح: مع prepared
$stmt = $pdo->prepare("SELECT * FROM cards WHERE status = ?");
echo "   ✅ مع prepared: SELECT * FROM cards WHERE status = ?\n";
echo "   (المعامل 'active' يُرسل منفصلاً - آمن)\n";


// ═══════════════════════════════════════
// 5. Fetch Modes
// ═══════════════════════════════════════
echo "\n📌 5. طرق الجلب:\n";

$stmt = $pdo->query("SELECT * FROM cards LIMIT 3");

// FETCH_ASSOC
$row = $stmt->fetch(PDO::FETCH_ASSOC);
echo "   FETCH_ASSOC: " . json_encode($row, JSON_UNESCAPED_UNICODE) . "\n";

$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_OBJ);
echo "   FETCH_OBJ: " . $row->number . "\n";

$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_NUM);
echo "   FETCH_NUM: " . $row[1] . "\n";


// ═══════════════════════════════════════
// 6. Insert آمن
// ═══════════════════════════════════════
echo "\n📌 6. Insert آمن:\n";

$newCard = [
    'number' => '9999888877',
    'amount' => 350,
    'speed' => '256k/1M',
];

try {
    $stmt = $pdo->prepare("
        INSERT INTO cards (number, amount, speed, status)
        VALUES (:number, :amount, :speed, 'unused')
    ");
    
    $stmt->execute($newCard);
    
    $newId = $pdo->lastInsertId();
    echo "   ✅ تم إضافة بطاقة جديدة (ID: $newId)\n";
    
    // حذف للتنظيف
    $pdo->exec("DELETE FROM cards WHERE number = '9999888877'");
    echo "   🗑️ تم حذفها للاختبار\n";
    
} catch (PDOException $e) {
    echo "   ❌ خطأ: " . $e->getMessage() . "\n";
}


echo "\n✅ اكتمل التمرين!\n";
