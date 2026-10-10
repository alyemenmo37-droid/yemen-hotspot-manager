<?php
/**
 * YHM Project - Card Analyzer
 * محلل بطاقات Yemen Hotspot Manager
 * اليوم 3
 */

declare(strict_types=1);

// ═══════════════════════════════════════
// 1. بيانات تجريبية
// ═══════════════════════════════════════
$cards = [
    ['number' => '1001', 'amount' => 100,  'speed' => '512k/512k', 'status' => 'unused', 'created' => '2026-10-01'],
    ['number' => '1002', 'amount' => 250,  'speed' => '256k/1M',   'status' => 'active', 'created' => '2026-10-02'],
    ['number' => '1003', 'amount' => 500,  'speed' => '512k/2M',   'status' => 'active', 'created' => '2026-10-03'],
    ['number' => '1004', 'amount' => 1000, 'speed' => '2M/15M',    'status' => 'expired', 'created' => '2026-10-04'],
    ['number' => '1005', 'amount' => 250,  'speed' => '256k/1M',   'status' => 'active', 'created' => '2026-10-05'],
    ['number' => '1006', 'amount' => 100,  'speed' => '512k/512k', 'status' => 'unused', 'created' => '2026-10-06'],
    ['number' => '1007', 'amount' => 500,  'speed' => '512k/2M',   'status' => 'active', 'created' => '2026-10-07'],
    ['number' => '1008', 'amount' => 250,  'speed' => '256k/1M',   'status' => 'expired', 'created' => '2026-10-08'],
    ['number' => '1009', 'amount' => 1000, 'speed' => '2M/15M',    'status' => 'active', 'created' => '2026-10-09'],
];


// ═══════════════════════════════════════
// 2. دالة عرض القائمة
// ═══════════════════════════════════════
function displayCards(array $cards, string $title = 'البطاقات'): void {
    echo "\n📇 $title (" . count($cards) . "):\n";
    echo str_repeat("─", 70) . "\n";
    printf("%-8s | %-8s | %-12s | %-10s | %s\n", 
        "الرقم", "السعر", "السرعة", "الحالة", "التاريخ");
    echo str_repeat("─", 70) . "\n";
    
    foreach ($cards as $c) {
        printf("%-8s | %-8d | %-12s | %-10s | %s\n",
            $c['number'],
            $c['amount'],
            $c['speed'],
            $c['status'],
            $c['created']
        );
    }
    echo str_repeat("─", 70) . "\n";
}


// ═══════════════════════════════════════
// 3. إحصائيات شاملة
// ═══════════════════════════════════════
function analyzeCards(array $cards): array {
    $total = count($cards);
    
    // حسب الحالة
    $byStatus = [];
    foreach ($cards as $c) {
        $status = $c['status'];
        $byStatus[$status] = ($byStatus[$status] ?? 0) + 1;
    }
    
    // حسب السرعة
    $bySpeed = [];
    foreach ($cards as $c) {
        $speed = $c['speed'];
        $bySpeed[$speed] = ($bySpeed[$speed] ?? 0) + 1;
    }
    
    // المبيعات (النشطة + المنتهية)
    $soldCards = array_filter($cards, fn($c) => $c['status'] !== 'unused');
    $totalSales = array_reduce($soldCards, fn($s, $c) => $s + $c['amount'], 0);
    
    // متوسط السعر
    $avgPrice = $total > 0 
        ? array_sum(array_column($cards, 'amount')) / $total 
        : 0;
    
    // حسب السعر
    $byAmount = [];
    foreach ($cards as $c) {
        $amt = $c['amount'];
        $byAmount[$amt] = ($byAmount[$amt] ?? 0) + 1;
    }
    ksort($byAmount);
    
    return [
        'total' => $total,
        'byStatus' => $byStatus,
        'bySpeed' => $bySpeed,
        'byAmount' => $byAmount,
        'totalSales' => $totalSales,
        'avgPrice' => $avgPrice,
        'soldCount' => count($soldCards),
    ];
}


// ═══════════════════════════════════════
// 4. عرض التقرير
// ═══════════════════════════════════════
function displayReport(array $stats): void {
    echo "\n╔" . str_repeat("═", 50) . "╗\n";
    echo "║  📊 تقرير YHM الشامل" . str_repeat(" ", 29) . "║\n";
    echo "╚" . str_repeat("═", 50) . "╝\n\n";
    
    // الملخص
    echo "📈 الملخص:\n";
    echo "  • إجمالي البطاقات: {$stats['total']}\n";
    echo "  • المباعة: {$stats['soldCount']}\n";
    echo "  • إجمالي المبيعات: " . number_format($stats['totalSales']) . " ريال\n";
    echo "  • متوسط السعر: " . number_format($stats['avgPrice'], 2) . " ريال\n";
    
    // حسب الحالة
    echo "\n📌 حسب الحالة:\n";
    foreach ($stats['byStatus'] as $status => $count) {
        $percentage = round($count / $stats['total'] * 100, 1);
        printf("  • %-10s: %d (%.1f%%)\n", $status, $count, $percentage);
    }
    
    // حسب السرعة
    echo "\n⚡ حسب السرعة:\n";
    foreach ($stats['bySpeed'] as $speed => $count) {
        printf("  • %-12s: %d\n", $speed, $count);
    }
    
    // حسب السعر
    echo "\n💰 حسب السعر:\n";
    foreach ($stats['byAmount'] as $amount => $count) {
        printf("  • %-6d ريال: %d\n", $amount, $count);
    }
}


// ═══════════════════════════════════════
// 🎬 البرنامج الرئيسي
// ═══════════════════════════════════════

echo "\n";
echo "╔" . str_repeat("═", 50) . "╗\n";
echo "║  📡 Yemen Hotspot Manager (YHM)              ║\n";
echo "║  محلل البطاقات - اليوم 3                     ║\n";
echo "╚" . str_repeat("═", 50) . "╝\n";

// 1. عرض كل البطاقات
displayCards($cards, 'كل البطاقات');

// 2. حسب الحالة
$activeCards = array_filter($cards, fn($c) => $c['status'] === 'active');
displayCards(array_values($activeCards), 'البطاقات النشطة');

$unusedCards = array_filter($cards, fn($c) => $c['status'] === 'unused');
displayCards(array_values($unusedCards), 'البطاقات غير المستخدمة');

// 3. التقرير الشامل
$stats = analyzeCards($cards);
displayReport($stats);

// 4. الخلاصة
echo "\n" . str_repeat("═", 52) . "\n";
echo "🎉 تم التحليل بنجاح — YHM اليوم 3 مكتمل!\n";
echo str_repeat("═", 52) . "\n\n";
