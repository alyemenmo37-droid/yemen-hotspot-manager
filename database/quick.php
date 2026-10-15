<?php
/**
 * YHM - Quick DB CLI
 * استعلامات سريعة من الطرفية
 */

require_once __DIR__ . '/../src/php-fundamentals/day-10/config.php';
require_once __DIR__ . '/../src/php-fundamentals/day-10/src/Database.php';

$config = require __DIR__ . '/../src/php-fundamentals/day-10/config.php';
$db = new Database($config);

$query = $argv[1] ?? null;

if ($query === null) {
    echo "الاستخدام: php quick.php \"SELECT * FROM cards LIMIT 5\"\n";
    exit(1);
}

try {
    $results = $db->fetchAll($query);
    if (empty($results)) {
        echo "لا نتائج\n";
        exit(0);
    }
    
    // طباعة كجدول
    $headers = array_keys($results[0]);
    
    echo "\n";
    foreach ($headers as $h) {
        printf("%-15s | ", $h);
    }
    echo "\n";
    echo str_repeat("─", 17 * count($headers)) . "\n";
    
    foreach ($results as $row) {
        foreach ($row as $val) {
            printf("%-15s | ", substr((string)$val, 0, 15));
        }
        echo "\n";
    }
    echo "\n";
    
} catch (Exception $e) {
    echo "❌ خطأ: " . $e->getMessage() . "\n";
}
