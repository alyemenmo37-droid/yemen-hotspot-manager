<?php
/**
 * YHM - Full with Relations
 * اليوم 11
 */

declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/src/Database.php';
require_once __DIR__ . '/src/Models/Card.php';
require_once __DIR__ . '/src/Repositories/CardRepository.php';

$config = require __DIR__ . '/config.php';
$db = new Database($config);
$repo = new CardRepository($db);

echo "\n";
echo "╔" . str_repeat("═", 70) . "╗\n";
echo "║  📡 YHM - Relations (اليوم 11)                              ║\n";
echo "╚" . str_repeat("═", 70) . "╝\n\n";


// ═══════════════════════════════════════
// 1. البطاقات مع العلاقات
// ═══════════════════════════════════════
$cards = $repo->allWithRelations();

//echo "📋 البطاقات مع التفاصيل ({$count = count($cards)}):\n";
echo "📋 البطاقات مع التفاصيل (" . count($cards) . "):\n";
echo str_repeat("─", 70) . "\n";

foreach (array_slice($cards, 0, 10) as $card) {
    printf("   %s | %d ريال\n", $card['number'], $card['amount']);
    printf("      📁 %s | ⚡ %s | 📊 %d/%d MB\n",
        $card['category_name'] ?? '-',
        $card['rate_limit'] ?? $card['speed'],
        $card['data_used_mb'],
        $card['data_limit_mb']
    );
    if ($card['groups_list']) {
        printf("      🏷️ المجموعات: %s\n", $card['groups_list']);
    }
    echo "\n";
}


// ═══════════════════════════════════════
// 2. الإحصائيات الشاملة
// ═══════════════════════════════════════
$stats = $repo->comprehensiveStats();

echo "\n📊 الإحصائيات الشاملة:\n";
echo str_repeat("─", 70) . "\n";

$g = $stats['general'];
echo "   إجمالي: {$g['total']}\n";
echo "   نشطة: {$g['active']}\n";
echo "   غير مستخدمة: {$g['unused']}\n";
echo "   المبيعات: " . number_format((float)$g['sales']) . " ريال\n";

echo "\n   حسب الفئة:\n";
foreach ($stats['byCategory'] as $cat) {
    printf("      • %-15s: %d بطاقة (%d نشطة) = %s ريال\n",
        $cat['category_name'],
        $cat['cards_count'],
        $cat['active_count'],
        number_format((float)$cat['total_value'])
    );
}

$data = $stats['dataUsage'];
echo "\n   استخدام البيانات:\n";
printf("      الحد الكلي: %.2f GB\n", $data['total_limit'] / 1024);
printf("      المستخدم: %.2f GB\n", $data['total_used'] / 1024);
printf("      المتوسط: %.1f%%\n", $data['avg_percentage'] ?? 0);


echo "\n" . str_repeat("═", 72) . "\n";
echo "🎉 YHM Relations مكتمل!\n";
echo str_repeat("═", 72) . "\n\n";
