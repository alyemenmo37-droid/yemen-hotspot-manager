<?php
/**
 * Day 13 - Rate Limiting
 * الحماية من Brute Force
 */

declare(strict_types=1);

require_once __DIR__ . '/../day-10/bootstrap.php';

$pdo = getPDO();

echo "╔" . str_repeat("═", 70) . "╗\n";
echo "║  🛡️ Rate Limiting - الحماية من الهجمات        ║\n";
echo "╚" . str_repeat("═", 70) . "╝\n\n";


// ═══════════════════════════════════════
// 1. جدول Rate Limits
// ═══════════════════════════════════════
$pdo->exec("
    CREATE TABLE IF NOT EXISTS rate_limits (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        identifier VARCHAR(100) NOT NULL,
        action VARCHAR(50) NOT NULL,
        attempts INT UNSIGNED NOT NULL DEFAULT 1,
        first_attempt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        last_attempt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        blocked_until DATETIME NULL,
        
        UNIQUE KEY uniq_identifier_action (identifier, action),
        INDEX idx_blocked (blocked_until)
    ) ENGINE=InnoDB
");

// تنظيف
$pdo->exec("DELETE FROM rate_limits");


// ═══════════════════════════════════════
// 2. RateLimiter Class
// ═══════════════════════════════════════
class RateLimiter
{
    public function __construct(
        private PDO $pdo,
    ) {}
    
    /**
     * هل مسموح؟
     * 
     * @param string $identifier IP أو User ID
     * @param string $action نوع العملية
     * @param int $maxAttempts الحد الأقصى
     * @param int $windowSeconds النافذة الزمنية
     * @param int $blockSeconds مدة الحظر
     */
    public function isAllowed(
        string $identifier,
        string $action,
        int $maxAttempts = 5,
        int $windowSeconds = 60,
        int $blockSeconds = 300
    ): array {
        // تحقق من الحظر
        $stmt = $this->pdo->prepare("
            SELECT * FROM rate_limits 
            WHERE identifier = ? AND action = ?
            AND blocked_until IS NOT NULL AND blocked_until > NOW()
        ");
        $stmt->execute([$identifier, $action]);
        
        if ($row = $stmt->fetch()) {
            $remaining = strtotime($row['blocked_until']) - time();
            return [
                'allowed' => false,
                'reason' => 'blocked',
                'remaining' => $remaining,
                'attempts' => $row['attempts'],
            ];
        }
        
        // جلب/إنشاء السجل
        $stmt = $this->pdo->prepare("
            SELECT * FROM rate_limits 
            WHERE identifier = ? AND action = ?
        ");
        $stmt->execute([$identifier, $action]);
        $row = $stmt->fetch();
        
        if ($row) {
            // هل النافذة انتهت؟
            $windowAge = time() - strtotime($row['first_attempt']);
            
            if ($windowAge > $windowSeconds) {
                // ابدأ نافذة جديدة
                $this->pdo->prepare("
                    UPDATE rate_limits 
                    SET attempts = 1, first_attempt = NOW(), 
                        last_attempt = NOW(), blocked_until = NULL
                    WHERE id = ?
                ")->execute([$row['id']]);
                return ['allowed' => true, 'attempts' => 1, 'max' => $maxAttempts];
            }
            
            // تجاوز الحد؟
            if ($row['attempts'] >= $maxAttempts) {
                // احظر
                $this->pdo->prepare("
                    UPDATE rate_limits 
                    SET blocked_until = DATE_ADD(NOW(), INTERVAL ? SECOND)
                    WHERE id = ?
                ")->execute([$blockSeconds, $row['id']]);
                
                return [
                    'allowed' => false,
                    'reason' => 'max_attempts',
                    'remaining' => $blockSeconds,
                    'attempts' => $row['attempts'],
                ];
            }
            
            // زيادة المحاولات
            $this->pdo->prepare("
                UPDATE rate_limits 
                SET attempts = attempts + 1, last_attempt = NOW()
                WHERE id = ?
            ")->execute([$row['id']]);
            
            return [
                'allowed' => true,
                'attempts' => $row['attempts'] + 1,
                'max' => $maxAttempts,
                'remaining' => $maxAttempts - $row['attempts'] - 1,
            ];
        }
        
        // إنشاء سجل جديد
        $this->pdo->prepare("
            INSERT INTO rate_limits (identifier, action, attempts)
            VALUES (?, ?, 1)
        ")->execute([$identifier, $action]);
        
        return ['allowed' => true, 'attempts' => 1, 'max' => $maxAttempts];
    }
    
    /**
     * إعادة تعيين (بعد نجاح)
     */
    public function reset(string $identifier, string $action): void
    {
        $this->pdo->prepare("
            DELETE FROM rate_limits 
            WHERE identifier = ? AND action = ?
        ")->execute([$identifier, $action]);
    }
}


// ═══════════════════════════════════════
// 3. الاختبار
// ═══════════════════════════════════════
$limiter = new RateLimiter($pdo);

echo "📌 محاكاة تسجيل دخول خاطئ (5 محاولات):\n";
echo str_repeat("─", 70) . "\n";

$ip = '192.168.1.100';

for ($i = 1; $i <= 7; $i++) {
    $result = $limiter->isAllowed($ip, 'login', 5, 60, 300);
    
    if ($result['allowed']) {
        printf("   محاولة %d: ✅ مسموح (المتبقي: %d)\n",
            $i, $result['remaining'] ?? 0);
    } else {
        printf("   محاولة %d: ❌ محظور (%d ثانية)\n",
            $i, $result['remaining']);
    }
}


// ═══════════════════════════════════════
// 4. تجربة IP آخر (مستقل)
// ═══════════════════════════════════════
echo "\n📌 IP آخر (مستقل):\n";
echo str_repeat("─", 70) . "\n";

$ip2 = '192.168.1.101';

for ($i = 1; $i <= 3; $i++) {
    $result = $limiter->isAllowed($ip2, 'login', 5, 60, 300);
    printf("   محاولة %d: %s\n",
        $i,
        $result['allowed'] ? '✅ مسموح' : '❌ محظور'
    );
}


// ═══════════════════════════════════════
// 5. بعد النجاح (reset)
// ═══════════════════════════════════════
echo "\n📌 بعد تسجيل دخول ناجح - إعادة تعيين:\n";
echo str_repeat("─", 70) . "\n";

$limiter->reset($ip, 'login');
echo "   ✅ تم إعادة تعيين $ip\n";

$result = $limiter->isAllowed($ip, 'login', 5, 60, 300);
echo "   محاولة جديدة: " . ($result['allowed'] ? '✅ مسموح' : '❌ محظور') . "\n";


// تنظيف
$pdo->exec("DELETE FROM rate_limits");

echo "\n✅ اكتمل التمرين!\n";
