<?php
/**
 * YHM - Security Test
 * اليوم 13
 */

declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/src/Database.php';
require_once __DIR__ . '/src/Security/RateLimiter.php';
require_once __DIR__ . '/src/Security/InputValidator.php';

$config = require __DIR__ . '/config.php';
$db = new Database($config);
$limiter = new RateLimiter($db);

echo "\n";
echo "╔" . str_repeat("═", 70) . "╗\n";
echo "║  🛡️ YHM Security Test (اليوم 13)                        ║\n";
echo "╚" . str_repeat("═", 70) . "╝\n\n";


// ═══════════════════════════════════════
// 1. Input Validation
// ═══════════════════════════════════════
echo "📌 1. Input Validation:\n";
echo str_repeat("─", 70) . "\n";

$tests = [
    ['cardNumber', '1234567890', true],
    ['cardNumber', "123' OR '1'='1", false],
    ['cardNumber', '123', false],
    ['amount', '250', true],
    ['amount', '99999999', false],
    ['amount', 'abc', false],
    ['speed', '256k/1M', true],
    ['speed', '<script>', false],
];

foreach ($tests as [$method, $input, $shouldPass]) {
    try {
        $result = InputValidator::$method($input);
        $icon = $shouldPass ? '✅' : '❌ (لم يرفض!)';
        printf("   %s %-12s('%-20s') → %s\n",
            $icon, $method, $input, $result);
    } catch (Exception $e) {
        $icon = !$shouldPass ? '✅' : '❌ (رفض خاطئ)';
        printf("   %s %-12s('%-20s') → رُفض: %s\n",
            $icon, $method, $input, $e->getMessage());
    }
}


// ═══════════════════════════════════════// 2. Rate Limiting
// ═══════════════════════════════════════
echo "\n📌 2. Rate Limiting:\n";
echo str_repeat("─", 70) . "\n";

// تنظيف
$db->query("DELETE FROM rate_limits");

$ip = '192.168.1.50';

echo "   محاكاة 10 محاولات دخول:\n";
for ($i = 1; $i <= 10; $i++) {
    $result = $limiter->isAllowed($ip, 'login', 5, 60, 300);
    
    if ($result['allowed']) {
        printf("   محاولة %2d: ✅ مسموح (المتبقي: %d)\n",
            $i, $result['remaining'] ?? 0);
    } else {
        printf("   محاولة %2d: ❌ محظور (%d ثانية)\n",
            $i, $result['remaining']);
    }
}


// ═══════════════════════════════════════
// 3. Transactions
// ═══════════════════════════════════════
echo "\n📌 3. Transaction آمنة:\n";
echo str_repeat("─", 70) . "\n";

try {
    $db->transaction(function (Database $db) {
        $db->query("
            INSERT INTO cards (number, amount, speed, status)
            VALUES (?, ?, ?, 'unused')
        ", ['7777666655', 250, '256k/1M']);
        
        $id = $db->fetchColumn("SELECT LAST_INSERT_ID()");
        
        $db->query("
            INSERT INTO activity_log (action, target_type, target_id, details)
            VALUES (?, ?, ?, ?)
        ", ['create', 'card', $id, 'اختبار']);
        
        return $id;
    });
    
    echo "   ✅ المعاملة نجحت\n";
    
    // تنظيف
    $db->query("DELETE FROM cards WHERE number = ?", ['7777666655']);
    $db->query("DELETE FROM rate_limits");
    
} catch (Exception $e) {
    echo "   ❌ " . $e->getMessage() . "\n";
}


echo "\n" . str_repeat("═", 72) . "\n";
echo "🎉 YHM Security مكتمل!\n";
echo str_repeat("═", 72) . "\n\n";
