<?php
/**
 * YHM - Reports
 * اليوم 12
 */

declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/src/Database.php';
require_once __DIR__ . '/src/Repositories/ReportRepository.php';

$config = require __DIR__ . '/config.php';
$db = new Database($config);
$reports = new ReportRepository($db);

echo "\n";
echo "╔" . str_repeat("═", 70) . "╗\n";
echo "║  📊 YHM Reports (اليوم 12)                                 ║\n";
echo "╚" . str_repeat("═", 70) . "╝\n\n";


// 1. المبيعات حسب الفئة
echo "📌 المبيعات حسب الفئة:\n";
echo str_repeat("─", 70) . "\n";

foreach ($reports->salesByCategory() as $row) {
    printf("   %-15s: %d بطاقة = %s ريال (%d نشطة)\n",
        $row['category'],
        $row['cards_count'],
        number_format((float)$row['revenue']),
        $row['active']
    );
}


// 2. تصنيف الأسعار
echo "\n📌 تصنيف الأسعار:\n";
echo str_repeat("─", 70) . "\n";

foreach ($reports->priceRanges() as $row) {
    printf("   %-15s: %d بطاقة = %s ريال\n",
        $row['price_range'],
        $row['count'],
        number_format((float)$row['total'])
    );
}


// 3. أفضل البطاقات
echo "\n📌 أفضل 5 بطاقات:\n";
echo str_repeat("─", 70) . "\n";

foreach ($reports->topCards(5) as $row) {
    printf("   #%d: %s | %d ريال (%s)\n",
        $row['rank_num'],
        $row['number'],
        $row['amount'],
        $row['status']
    );
}


// 4. استخدام البيانات
$data = $reports->dataUsage();

echo "\n📌 استخدام البيانات:\n";
echo str_repeat("─", 70) . "\n";
printf("   الحد الكلي: %.2f GB\n", $data['total_limit'] / 1024);
printf("   المستخدم: %.2f GB\n", $data['total_used'] / 1024);
printf("   تجاوزت الحد: %d بطاقة\n", $data['exceeded_count']);

$percent = $data['total_limit'] > 0 
    ? round($data['total_used'] / $data['total_limit'] * 100, 1)
    : 0;
printf("   النسبة: %.1f%%\n", $percent);


// 5. النشاط اليومي
echo "\n📌 النشاط اليومي (آخر 7 أيام):\n";
echo str_repeat("─", 70) . "\n";

foreach ($reports->dailyActivity(7) as $row) {
    printf("   %s: %d بطاقة = %s ريال\n",
        $row['day'],
        $row['cards_created'],
        number_format((float)$row['revenue'])
    );
}

echo "\n" . str_repeat("═", 72) . "\n";
echo "🎉 YHM Reports مكتمل!\n";
echo str_repeat("═", 72) . "\n\n";
