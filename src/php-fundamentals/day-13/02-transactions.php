<?php
/**
 * Day 13 - Transactions
 * Atomicity: كل شيء أو لا شيء
 */

declare(strict_types=1);

require_once __DIR__ . '/../day-10/bootstrap.php';

$pdo = getPDO();

echo "╔" . str_repeat("═", 70) . "╗\n";
echo "║  🔄 Transactions - الذرية (ACID)                ║\n";
echo "╚" . str_repeat("═", 70) . "╝\n\n";


// ═══════════════════════════════════════
// 1. العملية الآمنة (نجحت)
// ═══════════════════════════════════════
echo "📌 1. Transaction ناجحة:\n";
echo str_repeat("─", 70) . "\n";

try {
    $pdo->beginTransaction();
    
    // 1. إضافة بطاقة
    $stmt = $pdo->prepare("
        INSERT INTO cards (number, amount, speed, status, data_limit_mb)
        VALUES (?, ?, ?, 'unused', ?)
    ");
    $stmt->execute(['5555444433', 250, '256k/1M', 500]);
    $cardId = $pdo->lastInsertId();
    echo "   ✅ 1. أُضيفت البطاقة (ID: $cardId)\n";
    
    // 2. تسجيل في السجل
    $stmt = $pdo->prepare("
        INSERT INTO activity_log (action, target_type, target_id, details)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute(['create', 'card', $cardId, 'بطاقة اختبار']);
    echo "   ✅ 2. أُضيف للسجل\n";
    
    // 3. ربط بمجموعة
    $stmt = $pdo->prepare("
        INSERT INTO card_group_pivot (card_id, group_id)
        VALUES (?, 1)
    ");
    $stmt->execute([$cardId]);
    echo "   ✅ 3. رُبطت بمجموعة\n";
    
    // تأكيد
    $pdo->commit();
    echo "   🎉 COMMIT - كل شيء نجح\n";
    
    // تنظيف
    $pdo->prepare("DELETE FROM cards WHERE id = ?")->execute([$cardId]);
    
} catch (Exception $e) {
    $pdo->rollBack();
    echo "   ❌ ROLLBACK: " . $e->getMessage() . "\n";
}


// ═══════════════════════════════════════
// 2. العملية الفاشلة (Rollback)
// ═══════════════════════════════════════
echo "\n📌 2. Transaction فاشلة:\n";
echo str_repeat("─", 70) . "\n";

try {
    $pdo->beginTransaction();
    
    // 1. إضافة بطاقة
    $stmt = $pdo->prepare("
        INSERT INTO cards (number, amount, speed, status)
        VALUES (?, ?, ?, 'unused')
    ");
    $stmt->execute(['6666555544', 250, '256k/1M']);
    echo "   ✅ 1. أُضيفت البطاقة\n";
    
    // 2. عملية تفشل (رقم مكرر)
    echo "   ⏳ محاولة إضافة بطاقة برقم مكرر...\n";
    $stmt->execute(['6666555544', 250, '256k/1M']);  // سيفشل - UNIQUE
    
} catch (PDOException $e) {
    $pdo->rollBack();
    echo "   ❌ خطأ: " . $e->getMessage() . "\n";
    echo "   🔙 ROLLBACK - تم التراجع عن كل شيء\n";
    
    // تحقق: البطاقة الأولى أيضاً لم تُضف!
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM cards WHERE number = ?");
    $stmt->execute(['6666555544']);
    $count = $stmt->fetchColumn();
    echo "   ✅ البطاقة الأولى لم تُضف (العدد: $count)\n";
}


// ═══════════════════════════════════════
// 3. Row Locking (FOR UPDATE)
// ═══════════════════════════════════════
echo "\n📌 3. Row Locking (منع Race Conditions):\n";
echo str_repeat("─", 70) . "\n";

try {
    $pdo->beginTransaction();
    
    // اقفل السجل
    $stmt = $pdo->prepare("
        SELECT * FROM cards WHERE number = ? FOR UPDATE
    ");
    $stmt->execute(['1234567890']);
    $card = $stmt->fetch();
    
    if ($card) {
        echo "   🔒 السجل مقفل (لا يمكن لغيره تعديله)\n";
        
        // عملية آمنة
        $newBalance = $card['amount'] - 50;
        $pdo->prepare("UPDATE cards SET amount = ? WHERE id = ?")
            ->execute([$newBalance, $card['id']]);
        
        echo "   ✅ تم خصم 50 ريال (الرصيد الجديد: $newBalance)\n";
    }
    
    $pdo->commit();
    
} catch (Exception $e) {
    $pdo->rollBack();
    echo "   ❌ " . $e->getMessage() . "\n";
}


// ═══════════════════════════════════════
// 4. Transaction مع Savepoints
// ═══════════════════════════════════════
echo "\n📌 4. Savepoints:\n";
echo str_repeat("─", 70) . "\n";

try {
    $pdo->beginTransaction();
    
    // عملية 1
    $pdo->exec("INSERT INTO activity_log (action, details) VALUES ('test1', 'عملية 1')");
    echo "   ✅ عملية 1\n";
    
    // حفظ نقطة
    $pdo->exec("SAVEPOINT sp1");
    echo "   💾 حفظ نقطة sp1\n";
    
    // عملية 2
    $pdo->exec("INSERT INTO activity_log (action, details) VALUES ('test2', 'عملية 2')");
    echo "   ✅ عملية 2\n";
    
    // تراجع عن العملية 2 فقط
    $pdo->exec("ROLLBACK TO SAVEPOINT sp1");
    echo "   🔙 تراجع عن عملية 2\n";
    
    // عملية 3
    $pdo->exec("INSERT INTO activity_log (action, details) VALUES ('test3', 'عملية 3')");
    echo "   ✅ عملية 3\n";
    
    $pdo->commit();
    echo "   🎉 COMMIT - عمليات 1 و 3 محفوظة، 2 تراجعت\n";
    
    // عرض
    $logs = $pdo->query("
        SELECT action, details FROM activity_log 
        WHERE action IN ('test1', 'test2', 'test3')
        ORDER BY id DESC LIMIT 5
    ")->fetchAll();
    
    echo "\n   السجل:\n";
    foreach ($logs as $log) {
        echo "      • {$log['action']}: {$log['details']}\n";
    }
    
    // تنظيف
    $pdo->exec("DELETE FROM activity_log WHERE action LIKE 'test%'");
    
} catch (Exception $e) {
    $pdo->rollBack();
    echo "   ❌ " . $e->getMessage() . "\n";
}


echo "\n✅ اكتمل التمرين!\n";
