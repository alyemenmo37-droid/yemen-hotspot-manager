<?php
/**
 * Day 3 - Array Functions
 */

$cards = [
    ['number' => '1111', 'amount' => 250, 'status' => 'active'],
    ['number' => '2222', 'amount' => 500, 'status' => 'unused'],
    ['number' => '3333', 'amount' => 250, 'status' => 'expired'],
    ['number' => '4444', 'amount' => 1000, 'status' => 'active'],
];

// ═══════════════════════════════════════
// 1. array_column - استخراج عمود
// ═══════════════════════════════════════
$numbers = array_column($cards, 'number');
print_r($numbers);
echo "الأرقام: " . implode(', ', $numbers) . "\n";

$amounts = array_column($cards, 'amount');
echo "الأسعار: " . implode(', ', $amounts) . "\n";


// ═══════════════════════════════════════
// 2. array_filter - تصفية
// ═══════════════════════════════════════
$activeCards = array_filter($cards, fn($c) => $c['status'] === 'active');
echo "\nالبطاقات النشطة: " . count($activeCards) . "\n";

$expensive = array_filter($cards, fn($c) => $c['amount'] >= 500);
echo "البطاقات ≥ 500: " . count($expensive) . "\n";


// ═══════════════════════════════════════
// 3. array_map - تحويل
// ═══════════════════════════════════════
$formatted = array_map(fn($c) => strtoupper($c['status']), $cards);
echo "\nالحالات (uppercase): " . implode(', ', $formatted) . "\n";


// ═══════════════════════════════════════
// 4. array_reduce - تجميع
// ═══════════════════════════════════════
$total = array_reduce($cards, fn($sum, $c) => $sum + $c['amount'], 0);
echo "\nإجمالي المبيعات: $total ريال\n";


// ═══════════════════════════════════════
// 5. usort - ترتيب مخصص
// ═══════════════════════════════════════
usort($cards, fn($a, $b) => $b['amount'] <=> $a['amount']);
echo "\nمرتبة حسب السعر (الأعلى أولاً):\n";
foreach ($cards as $c) {
    echo "  {$c['number']} - {$c['amount']}\n";
}


// ═══════════════════════════════════════
// 6. array_sum - جمع
// ═══════════════════════════════════════
$sum = array_sum($amounts);
echo "\nمجموع الأسعار: $sum ريال\n";
$avg = $sum / count($amounts);
echo "المتوسط: " . round($avg, 2) . " ريال\n";
