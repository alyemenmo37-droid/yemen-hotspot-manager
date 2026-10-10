<?php
/**
 * Day 3 - Arrays Basics
 * YHM Project
 */

// ═══════════════════════════════════════
// 1. Indexed Array
// ═══════════════════════════════════════
$speeds = ['بطيء', 'متوسطة', 'عالية', 'مفتوح'];

echo "سرعة رقم 2: " . $speeds[1] . "\n";  // متوسطة
echo "عدد المستويات: " . count($speeds) . "\n";


// ═══════════════════════════════════════
// 2. Associative Array
// ═══════════════════════════════════════
$card = [
    'number' => '2672147404',
    'amount' => 250,
    'speed'  => '512k/2M',
    'status' => 'active',
];

echo "\nبطاقة: {$card['number']}\n";
echo "السعر: {$card['amount']} ريال\n";


// ═══════════════════════════════════════
// 3. Multidimensional
// ═══════════════════════════════════════
$cards = [
    ['number' => '1111111111', 'amount' => 250, 'status' => 'active'],
    ['number' => '2222222222', 'amount' => 500, 'status' => 'unused'],
    ['number' => '3333333333', 'amount' => 250, 'status' => 'expired'],
    ['number' => '4444444444', 'amount' => 1000, 'status' => 'active'],
];

echo "\n📇 قائمة البطاقات:\n";
foreach ($cards as $c) {
    printf("  %s | %d ريال | %s\n", $c['number'], $c['amount'], $c['status']);
}
