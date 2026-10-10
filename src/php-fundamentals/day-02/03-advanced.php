<?php
/**
 * التمرين 3: دوال متقدمة
 */

// ═══════════════════════════════════════
// 1. Variadic Functions (...args)
// ═══════════════════════════════════════
function sum(...$numbers): float {
    return array_sum($numbers);
}

echo "المجموع: " . sum(1, 2, 3, 4, 5) . "\n";        // 15
echo "المجموع: " . sum(10, 20) . "\n";                // 30
echo "المجموع: " . sum(1.5, 2.5, 3) . "\n";           // 7


// ═══════════════════════════════════════
// 2. Arrow Functions (PHP 7.4+)
// ═══════════════════════════════════════
$multiply = fn($x, $y) => $x * $y;
echo "5 × 6 = " . $multiply(5, 6) . "\n";


// ═══════════════════════════════════════
// 3. Closures (الدوال المجهولة)
// ═══════════════════════════════════════
$greet = function($name) {
    return "أهلاً $name";
};
echo $greet("علي") . "\n";


// ═══════════════════════════════════════
// 4. Named Arguments (PHP 8+)
// ═══════════════════════════════════════
function createUser($name, $email, $role = 'user', $active = true) {
    return compact('name', 'email', 'role', 'active');
}

// بدون named arguments
$u1 = createUser('محمد', 'm@test.com', 'user', true);

// مع named arguments (أوضح)
$u2 = createUser(
    name: 'أحمد',
    email: 'a@test.com',
    role: 'admin'
);

print_r($u2);


// ═══════════════════════════════════════
// 5. Return Types المتعددة
// ═══════════════════════════════════════
function divideSafely(float $a, float $b): array {
    if ($b == 0) {
        return ['success' => false, 'error' => 'قسمة على صفر'];
    }
    return ['success' => true, 'result' => $a / $b];
}

$result = divideSafely(10, 2);
if ($result['success']) {
    echo "10 ÷ 2 = " . $result['result'] . "\n";
}

$result = divideSafely(10, 0);
if (!$result['success']) {
    echo "خطأ: " . $result['error'] . "\n";
}
