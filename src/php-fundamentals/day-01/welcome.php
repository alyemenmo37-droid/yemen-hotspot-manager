<?php
/**
 * برنامج ترحيب ذكي
 * اليوم 1 - المشروع الأساسي
 */

// إعدادات
$siteName = "شبكة فورجي نت";
$currentTime = date('H');
$greeting = '';

// تحديد التحية حسب الوقت
if ($currentTime < 12) {
    $greeting = "صباح الخير";
} elseif ($currentTime < 17) {
    $greeting = "مساء الخير";
} else {
    $greeting = "مساء النور";
}

// بطاقات ترحيب
$users = [
    ['name' => 'محمد', 'balance' => 500],
    ['name' => 'أحمد', 'balance' => 250],
    ['name' => 'علي',   'balance' => 1000],
];

// العرض
echo "╔" . str_repeat("═", 40) . "╗\n";
echo "║  $greeting - $siteName  ║\n";
echo "╚" . str_repeat("═", 40) . "╝\n\n";

echo "التاريخ: " . date('Y-m-d H:i') . "\n";
echo "عدد المستخدمين: " . count($users) . "\n\n";

echo "المستخدمون:\n";
echo str_repeat("-", 40) . "\n";

foreach ($users as $user) {
    $status = $user['balance'] > 0 ? "✅ نشط" : "❌ معطل";
    printf("%-10s | %5d ريال | %s\n", 
        $user['name'], 
        $user['balance'], 
        $status
    );
}

echo str_repeat("-", 40) . "\n";

$total = array_sum(array_column($users, 'balance'));
echo "الإجمالي: $total ريال\n";
echo "\nشكراً لاستخدامك $siteName!\n";
?>
