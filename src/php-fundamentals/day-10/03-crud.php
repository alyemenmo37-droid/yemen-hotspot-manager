<?php
/**
 * Day 10 - Full CRUD with PDO
 */

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

$pdo = getPDO();

echo "╔" . str_repeat("═", 60) . "╗\n";
echo "║  📝 CRUD كامل مع PDO                            ║\n";
echo "╚" . str_repeat("═", 60) . "╝\n\n";

// ═══════════════════════════════════════
// 1. CREATE - إنشاء
// ═══════════════════════════════════════
echo "1️⃣ CREATE (إضافة):\n";

$testNumber = '7777666655';

try {
    $stmt = $pdo->prepare("
        INSERT INTO cards (number, amount, speed, status) 
        VALUES (:number, :amount, :speed, :status)
    ");
    
    $stmt->execute([
        ':number' => $testNumber,
        ':amount' => 750,
        ':speed' => '512k/2M',
        ':status' => 'unused',
    ]);
    
    $newId = $pdo->lastInsertId();
    echo "   ✅ أُضيفت بطاقة بمعرف: $newId\n";
    
} catch (PDOException $e) {
    echo "   ❌ خطأ: " . $e->getMessage() . "\n";
}


// ═══════════════════════════════════════
// 2. READ - قراءة
// ═══════════════════════════════════════
echo "\n2️⃣ READ (قراءة):\n";

// قراءة بطاقة محددة
$stmt = $pdo->prepare("SELECT * FROM cards WHERE number = ?");
$stmt->execute([$testNumber]);
$card = $stmt->fetch();

if ($card) {
    echo "   📇 البطاقة:\n";
    echo "      الرقم: {$card['number']}\n";
    echo "      السعر: {$card['amount']}\n";
    echo "      السرعة: {$card['speed']}\n";
    echo "      الحالة: {$card['status']}\n";
}

// قراءة متعددة
$stmt = $pdo->query("SELECT number, amount, status FROM cards ORDER BY id DESC LIMIT 3");
echo "\n   📋 آخر 3 بطاقات:\n";
foreach ($stmt->fetchAll() as $c) {
    echo "      • {$c['number']} | {$c['amount']} | {$c['status']}\n";
}


// ═══════════════════════════════════════
// 3. UPDATE - تحديث
// ═══════════════════════════════════════
echo "\n3️⃣ UPDATE (تحديث):\n";

$stmt = $pdo->prepare("
    UPDATE cards 
    SET status = :status, activated_at = NOW()
    WHERE number = :number
");

$stmt->execute([
    ':status' => 'active',
    ':number' => $testNumber,
]);

$affected = $stmt->rowCount();
echo "   ✅ تم تعديل $affected سجل\n";

// تحقق
$stmt = $pdo->prepare("SELECT status, activated_at FROM cards WHERE number = ?");
$stmt->execute([$testNumber]);
$updated = $stmt->fetch();
echo "   الحالة الجديدة: {$updated['status']}\n";
echo "   وقت التفعيل: {$updated['activated_at']}\n";


// ═══════════════════════════════════════
// 4. UPDATE - زيادة السعر
// ═══════════════════════════════════════
echo "\n4️⃣ UPDATE (زيادة الأسعار):\n";

$stmt = $pdo->prepare("
    UPDATE cards 
    SET amount = amount + :increase 
    WHERE speed = :speed
");

$stmt->execute([
    ':increase' => 50,
    ':speed' => '256k/1M',
]);

echo "   ✅ تم زيادة سعر " . $stmt->rowCount() . " بطاقة\n";


// ═══════════════════════════════════════
// 5. DELETE - حذف
// ═══════════════════════════════════════
echo "\n5️⃣ DELETE (حذف):\n";

$stmt = $pdo->prepare("DELETE FROM cards WHERE number = ?");
$stmt->execute([$testNumber]);

echo "   ✅ تم حذف " . $stmt->rowCount() . " بطاقة\n";

// تحقق
$stmt = $pdo->prepare("SELECT COUNT(*) FROM cards WHERE number = ?");
$stmt->execute([$testNumber]);
$count = $stmt->fetchColumn();

echo "   عدد البطاقات المتبقية بنفس الرقم: $count\n";


// ═══════════════════════════════════════
// 6. Transaction - معالجة متعددة
// ═══════════════════════════════════════
echo "\n6️⃣ TRANSACTION (معاملة آمنة):\n";

try {
    $pdo->beginTransaction();
    
    // عملية 1: إضافة بطاقة
    $stmt = $pdo->prepare("
        INSERT INTO cards (number, amount, speed, status)
        VALUES (?, ?, ?, 'unused')
    ");
    $stmt->execute(['5555444433', 500, '512k/2M']);
    $cardId = $pdo->lastInsertId();
    
    // عملية 2: تسجيل في السجل
    $stmt = $pdo->prepare("
        INSERT INTO activity_log (action, target_type, target_id, details)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute(['create', 'card', $cardId, 'بطاقة تجريبية']);
    
    // كل شيء نجح - تأكيد
    $pdo->commit();
    echo "   ✅ تمت المعاملة بنجاح\n";
    
    // حذف للتنظيف
    $pdo->exec("DELETE FROM cards WHERE number = '5555444433'");
    $pdo->exec("DELETE FROM activity_log WHERE target_id = $cardId");
    echo "   🗑️ تم التنظيف\n";
    
} catch (Exception $e) {
    $pdo->rollBack();
    echo "   ❌ فشلت المعاملة: " . $e->getMessage() . "\n";
}


// ═══════════════════════════════════════
// 7. الإحصائيات
// ═══════════════════════════════════════
echo "\n📊 الإحصائيات النهائية:\n";

$stats = $pdo->query("
    SELECT 
        COUNT(*) AS total,
        SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) AS active,
        SUM(CASE WHEN status = 'unused' THEN 1 ELSE 0 END) AS unused,
        COALESCE(SUM(CASE WHEN status != 'unused' THEN amount ELSE 0 END), 0) AS sales
    FROM cards
")->fetch();

echo "   إجمالي: {$stats['total']}\n";
echo "   نشطة: {$stats['active']}\n";
echo "   غير مستخدمة: {$stats['unused']}\n";
echo "   المبيعات: {$stats['sales']} ريال\n";


echo "\n✅ اكتمل CRUD بنجاح!\n";
