<?php
/**
 * Day 11 - Many-to-Many Relations
 */

declare(strict_types=1);

require_once __DIR__ . '/../day-10/bootstrap.php';

$pdo = getPDO();

echo "╔" . str_repeat("═", 60) . "╗\n";
echo "║  🔗 العلاقات: Many-to-Many                      ║\n";
echo "╚" . str_repeat("═", 60) . "╝\n\n";


// ═══════════════════════════════════════
// 1. تنظيف بيانات الاختبار
// ═══════════════════════════════════════
$pdo->exec("DELETE FROM card_group_pivot");

// ═══════════════════════════════════════
// 2. جلب بطاقات ومجموعات
// ═══════════════════════════════════════
$cards = $pdo->query("SELECT id, number FROM cards LIMIT 5")->fetchAll();
$groups = $pdo->query("SELECT id, name FROM card_groups")->fetchAll();

if (empty($cards) || empty($groups)) {
    die("لا توجد بيانات للاختبار\n");
}


// ═══════════════════════════════════════
// 3. ربط بطاقات بمجموعات
// ═══════════════════════════════════════
echo "📌 ربط بطاقات بمجموعات:\n";

$stmt = $pdo->prepare("
    INSERT INTO card_group_pivot (card_id, group_id) 
    VALUES (?, ?)
");

// بطاقة 1 → 2 مجموعات
$stmt->execute([$cards[0]['id'], $groups[0]['id']]);
$stmt->execute([$cards[0]['id'], $groups[2]['id']]);
echo "   ✅ {$cards[0]['number']} → {$groups[0]['name']} + {$groups[2]['name']}\n";

// بطاقة 2 → مجموعة 1
$stmt->execute([$cards[1]['id'], $groups[0]['id']]);
echo "   ✅ {$cards[1]['number']} → {$groups[0]['name']}\n";

// بطاقة 3 → مجموعة VIP
$stmt->execute([$cards[2]['id'], $groups[2]['id']]);
echo "   ✅ {$cards[2]['number']} → {$groups[2]['name']}\n";


// ═══════════════════════════════════════
// 4. عرض مجموعات كل بطاقة
// ═══════════════════════════════════════
echo "\n📌 مجموعات كل بطاقة:\n";
echo str_repeat("─", 60) . "\n";

$stmt = $pdo->query("
    SELECT 
        c.number,
        GROUP_CONCAT(g.name SEPARATOR ', ') AS groups_list
    FROM cards c
    LEFT JOIN card_group_pivot p ON p.card_id = c.id
    LEFT JOIN card_groups g ON g.id = p.group_id
    GROUP BY c.id, c.number
    HAVING groups_list IS NOT NULL
");

foreach ($stmt->fetchAll() as $row) {
    printf("   📇 %s → %s\n", $row['number'], $row['groups_list']);
}


// ═══════════════════════════════════════
// 5. عرض بطاقات كل مجموعة
// ═══════════════════════════════════════
echo "\n📌 بطاقات كل مجموعة:\n";
echo str_repeat("─", 60) . "\n";

$stmt = $pdo->query("
    SELECT 
        g.name AS group_name,
        COUNT(p.card_id) AS cards_count,
        GROUP_CONCAT(c.number SEPARATOR ', ') AS cards_list
    FROM card_groups g
    LEFT JOIN card_group_pivot p ON p.group_id = g.id
    LEFT JOIN cards c ON c.id = p.card_id
    GROUP BY g.id, g.name
");

foreach ($stmt->fetchAll() as $row) {
    echo "\n🎯 {$row['group_name']} ({$row['cards_count']}):\n";
    if ($row['cards_list']) {
        echo "   " . $row['cards_list'] . "\n";
    } else {
        echo "   (فارغة)\n";
    }
}


// ═══════════════════════════════════════
// 6. إحصائيات
// ═══════════════════════════════════════
echo "\n📊 إحصائيات Many-to-Many:\n";
echo str_repeat("─", 60) . "\n";

$stats = $pdo->query("
    SELECT 
        (SELECT COUNT(*) FROM cards) AS total_cards,
        (SELECT COUNT(*) FROM card_groups) AS total_groups,
        (SELECT COUNT(*) FROM card_group_pivot) AS total_relations,
        (SELECT ROUND(AVG(cards_per_group), 2) FROM (
            SELECT COUNT(*) AS cards_per_group 
            FROM card_group_pivot 
            GROUP BY group_id
        ) AS x) AS avg_cards_per_group
")->fetch();

echo "   البطاقات: {$stats['total_cards']}\n";
echo "   المجموعات: {$stats['total_groups']}\n";
echo "   العلاقات: {$stats['total_relations']}\n";
echo "   متوسط البطاقات/مجموعة: {$stats['avg_cards_per_group']}\n";


// ═══════════════════════════════════════
// 7. تنظيف
// ═══════════════════════════════════════
$pdo->exec("DELETE FROM card_group_pivot");

echo "\n✅ اكتمل التمرين!\n";
