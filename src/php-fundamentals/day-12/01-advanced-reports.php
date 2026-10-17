<?php
/**
 * Day 12 - Advanced Reports
 */

declare(strict_types=1);

require_once __DIR__ . '/../day-10/bootstrap.php';

$pdo = getPDO();

echo "╔" . str_repeat("═", 70) . "╗\n";
echo "║  📊 تقارير احترافية متقدمة                      ║\n";
echo "╚" . str_repeat("═", 70) . "╝\n\n";


// ═══════════════════════════════════════
// 1. تقرير شامل بـ Multiple JOINs
// ═══════════════════════════════════════
echo "📌 تقرير مبيعات شامل:\n";
echo str_repeat("─", 70) . "\n";

$sql = "
    SELECT 
        cat.name AS category,
        s.name AS speed_name,
        s.rate_limit,
        COUNT(c.id) AS cards_count,
        COALESCE(SUM(c.amount), 0) AS revenue,
        COALESCE(AVG(c.amount), 0) AS avg_price,
        SUM(CASE WHEN c.status = 'active' THEN 1 ELSE 0 END) AS active_cards
    FROM categories cat
    LEFT JOIN speed_levels s ON s.category_id = cat.id
    LEFT JOIN cards c ON c.speed_level_id = s.id
    GROUP BY cat.id, cat.name, s.id, s.name, s.rate_limit
    ORDER BY revenue DESC
";

$rows = $pdo->query($sql)->fetchAll();

printf("%-15s | %-15s | %8s | %12s | %8s\n",
    'الفئة', 'السرعة', 'البطاقات', 'الإيرادات', 'نشطة'
);
echo str_repeat("─", 70) . "\n";

foreach ($rows as $row) {
    printf("%-15s | %-15s | %8d | %10s ريال | %8d\n",
        $row['category'],
        $row['speed_name'],
        $row['cards_count'],
        number_format((float)$row['revenue']),
        $row['active_cards']
    );
}


// ═══════════════════════════════════════
// 2. CASE WHEN - تصنيف
// ═══════════════════════════════════════
echo "\n\n📌 تصنيف البطاقات حسب السعر:\n";
echo str_repeat("─", 70) . "\n";

$sql = "
    SELECT 
        CASE 
            WHEN amount <= 100 THEN 'رخيصة'
            WHEN amount <= 250 THEN 'متوسطة'
            WHEN amount <= 500 THEN 'مرتفعة'
            WHEN amount <= 1000 THEN 'عالية'
            ELSE 'مميزة'
        END AS price_range,
        COUNT(*) AS count,
        SUM(amount) AS total
    FROM cards
    GROUP BY price_range
    ORDER BY total DESC
";

foreach ($pdo->query($sql)->fetchAll() as $row) {
    printf("%-15s: %3d بطاقة = %s ريال\n",
        $row['price_range'],
        $row['count'],
        number_format((float)$row['total'])
    );
}


// ═══════════════════════════════════════
// 3. Window Functions
// ═══════════════════════════════════════
echo "\n\n📌 ترتيب البطاقات بقيم متراكمة:\n";
echo str_repeat("─", 70) . "\n";

$sql = "
    SELECT 
        number,
        amount,
        ROW_NUMBER() OVER (ORDER BY amount DESC) AS rank_num,
        RANK() OVER (ORDER BY amount DESC) AS rank_val,
        SUM(amount) OVER (ORDER BY amount DESC) AS cumulative_sum
    FROM cards
    ORDER BY amount DESC
    LIMIT 10
";

printf("%-15s | %8s | %5s | %5s | %12s\n",
    'الرقم', 'السعر', '#', 'الترتيب', 'المتراكم'
);
echo str_repeat("─", 70) . "\n";

foreach ($pdo->query($sql)->fetchAll() as $row) {
    printf("%-15s | %8d | %5d | %5d | %10s ريال\n",
        $row['number'],
        $row['amount'],
        $row['rank_num'],
        $row['rank_val'],
        number_format((float)$row['cumulative_sum'])
    );
}


// ═══════════════════════════════════════
// 4. CTE (WITH)
// ═══════════════════════════════════════
echo "\n\n📌 CTE - تقرير مبني على تقرير:\n";
echo str_repeat("─", 70) . "\n";

$sql = "
    WITH card_stats AS (
        SELECT 
            speed_level_id,
            COUNT(*) AS count,
            SUM(amount) AS revenue
        FROM cards
        GROUP BY speed_level_id
    )
    SELECT 
        s.name AS speed_name,
        s.rate_limit,
        COALESCE(cs.count, 0) AS cards_count,
        COALESCE(cs.revenue, 0) AS revenue
    FROM speed_levels s
    LEFT JOIN card_stats cs ON cs.speed_level_id = s.id
    ORDER BY revenue DESC
";

foreach ($pdo->query($sql)->fetchAll() as $row) {
    printf("%-20s (%s): %d بطاقة = %s ريال\n",
        $row['speed_name'],
        $row['rate_limit'],
        $row['cards_count'],
        number_format((float)$row['revenue'])
    );
}


// ═══════════════════════════════════════
// 5. ROLLUP - مجموعات فرعية
// ═══════════════════════════════════════
echo "\n\n📌 ROLLUP - مجموع كلي:\n";
echo str_repeat("─", 70) . "\n";

$sql = "
    SELECT 
        COALESCE(status, 'الإجمالي') AS status,
        COALESCE(speed, 'الكل') AS speed,
        COUNT(*) AS count,
        SUM(amount) AS total
    FROM cards
    GROUP BY status, speed WITH ROLLUP
";

foreach ($pdo->query($sql)->fetchAll() as $row) {
    printf("%-12s | %-12s | %3d | %s\n",
        $row['status'],
        $row['speed'],
        $row['count'],
        number_format((float)$row['total'])
    );
}


echo "\n✅ اكتملت التقارير!\n";
