<?php
/**
 * Day 11 - One-to-Many Relations
 */

declare(strict_types=1);

require_once __DIR__ . '/../day-10/bootstrap.php';

$pdo = getPDO();

echo "╔" . str_repeat("═", 60) . "╗\n";
echo "║  🔗 العلاقات: One-to-Many                       ║\n";
echo "╚" . str_repeat("═", 60) . "╝\n\n";


// ═══════════════════════════════════════
// 1. Category → Speed Levels (1:N)
// ═══════════════════════════════════════
echo "📌 فئة واحدة → مستويات سرعة متعددة:\n";
echo str_repeat("─", 60) . "\n";

$categories = $pdo->query("
    SELECT id, name FROM categories
")->fetchAll();

foreach ($categories as $cat) {
    echo "\n🎯 {$cat['name']}:\n";
    
    $stmt = $pdo->prepare("
        SELECT id, name, rate_limit, price 
        FROM speed_levels 
        WHERE category_id = ?
    ");
    $stmt->execute([$cat['id']]);
    
    foreach ($stmt->fetchAll() as $speed) {
        printf("   • %-15s | %-12s | %d ريال\n",
            $speed['name'],
            $speed['rate_limit'],
            $speed['price']
        );
    }
}


// ═══════════════════════════════════════
// 2. Speed → Cards (1:N)
// ═══════════════════════════════════════
echo "\n\n📌 مستوى سرعة → بطاقات متعددة:\n";
echo str_repeat("─", 60) . "\n";

$speeds = $pdo->query("
    SELECT id, name, rate_limit FROM speed_levels
")->fetchAll();

foreach ($speeds as $speed) {
    $stmt = $pdo->prepare("
        SELECT COUNT(*) FROM cards WHERE speed_level_id = ?
    ");
    $stmt->execute([$speed['id']]);
    $count = $stmt->fetchColumn();
    
    printf("   %-20s (%s): %d بطاقة\n",
        $speed['name'],
        $speed['rate_limit'],
        $count
    );
}


// ═══════════════════════════════════════
// 3. Card → Sessions (1:N)
// ═══════════════════════════════════════
echo "\n\n📌 بطاقة → جلسات متعددة:\n";
echo str_repeat("─", 60) . "\n";

$stmt = $pdo->query("
    SELECT c.id, c.number, COUNT(s.id) AS sessions_count
    FROM cards c
    LEFT JOIN sessions s ON s.card_id = c.id
    GROUP BY c.id, c.number
    ORDER BY sessions_count DESC
    LIMIT 5
");

foreach ($stmt->fetchAll() as $row) {
    printf("   📇 %s: %d جلسة\n", $row['number'], $row['sessions_count']);
}


// ═══════════════════════════════════════
// 4. N+1 Problem - كيف نتجنبه
// ═══════════════════════════════════════
echo "\n\n📌 N+1 Problem:\n";
echo str_repeat("─", 60) . "\n";

// ❌ الخطأ (N+1 queries)
echo "\n❌ N+1 (بطيء):\n";
$start = microtime(true);

$categories = $pdo->query("SELECT * FROM categories")->fetchAll();
foreach ($categories as $cat) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM speed_levels WHERE category_id = ?");
    $stmt->execute([$cat['id']]);
    $count = $stmt->fetchColumn();
    // query لكل فئة!
}

$n1Time = microtime(true) - $start;
echo "   استعلامات: " . (count($categories) + 1) . "\n";
echo "   الزمن: " . round($n1Time * 1000, 2) . " ms\n";


// ✅ الحل (Eager Loading)
echo "\n✅ Eager Loading (سريع):\n";
$start = microtime(true);

$categories = $pdo->query("
    SELECT 
        c.id, 
        c.name,
        COUNT(s.id) AS speeds_count
    FROM categories c
    LEFT JOIN speed_levels s ON s.category_id = c.id
    GROUP BY c.id, c.name
")->fetchAll();

$eagerTime = microtime(true) - $start;
echo "   استعلامات: 1\n";
echo "   الزمن: " . round($eagerTime * 1000, 2) . " ms\n";


// النتيجة
$improvement = $n1Time > 0 ? round($n1Time / $eagerTime, 1) : 1;
echo "\n⚡ تحسين: {$improvement}x أسرع!\n";


echo "\n✅ اكتمل التمرين!\n";
