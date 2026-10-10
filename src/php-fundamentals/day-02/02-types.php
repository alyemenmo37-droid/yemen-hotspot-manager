<?php
/**
 * التمرين 2: Type Hints
 * تحديد أنواع المدخلات والمخرجات
 */

declare(strict_types=1);  // ⚠️ صرامة الأنواع


// ═══════════════════════════════════════
// 1. دالة بأنواع واضحة
// ═══════════════════════════════════════
function calculateTotal(float $price, int $quantity): float {
    return $price * $quantity;
}

echo "المجموع: " . calculateTotal(250.5, 3) . " ريال\n";

// ⚠️ هذا سيرمي خطأ
// echo calculateTotal("نص", 3);  // TypeError


// ═══════════════════════════════════════
// 2. Nullable types
// ═══════════════════════════════════════
function findUser(?string $username): ?string {
    if ($username === null) {
        return null;
    }
    return "المستخدم: $username";
}

echo findUser("محمد") . "\n";   // المستخدم: محمد
var_dump(findUser(null));       // NULL


// ═══════════════════════════════════════
// 3. Union Types (PHP 8+)
// ═══════════════════════════════════════
function processId(int|string $id): string {
    return "معالج: $id";
}

echo processId(123) . "\n";
echo processId("ABC-456") . "\n";


// ═══════════════════════════════════════
// 4. دالة Hotspot محسّنة
// ═══════════════════════════════════════
function createCard(
    string $number,
    float $amount,
    int $duration = 1440  // يوم كامل بالدقائق
): array {
    return [
        'number' => $number,
        'amount' => $amount,
        'duration' => $duration,
        'created_at' => date('Y-m-d H:i:s'),
        'status' => 'unused',
    ];
}

$card = createCard('2672147404', 250);
print_r($card);
