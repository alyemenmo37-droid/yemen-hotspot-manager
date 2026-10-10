<?php
/**
 * Day 5 - File Basics
 * YHM Project
 */

echo "╔" . str_repeat("═", 50) . "╗\n";
echo "║  📁 أساسيات الملفات في PHP                    ║\n";
echo "╚" . str_repeat("═", 50) . "╝\n\n";

// ═══════════════════════════════════════
// 1. كتابة ملف
// ═══════════════════════════════════════
$content = "مرحباً بك في YHM\n";
$content .= "التاريخ: " . date('Y-m-d H:i:s') . "\n";
$content .= "هذا أول ملف نكتبه في PHP\n";

$result = file_put_contents('test.txt', $content);

if ($result !== false) {
    echo "✅ تم كتابة $result بايت في test.txt\n";
}


// ═══════════════════════════════════════
// 2. قراءة ملف
// ═══════════════════════════════════════
if (file_exists('test.txt')) {
    $readContent = file_get_contents('test.txt');
    echo "\n📖 محتوى الملف:\n";
    echo str_repeat("─", 50) . "\n";
    echo $readContent;
    echo str_repeat("─", 50) . "\n";
}


// ═══════════════════════════════════════
// 3. إضافة (Append) بدل الكتابة
// ═══════════════════════════════════════
file_put_contents('test.txt', "سطر جديد\n", FILE_APPEND);
echo "\n✅ تمت إضافة سطر جديد\n";


// ═══════════════════════════════════════
// 4. قراءة سطراً بسطر
// ═══════════════════════════════════════
echo "\n📖 قراءة سطراً بسطر:\n";
$lines = file('test.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

foreach ($lines as $i => $line) {
    echo "  " . ($i + 1) . ": $line\n";
}


// ═══════════════════════════════════════
// 5. معلومات الملف
// ═══════════════════════════════════════
echo "\n📊 معلومات الملف:\n";
echo "  الحجم: " . filesize('test.txt') . " بايت\n";
echo "  آخر تعديل: " . date('Y-m-d H:i:s', filemtime('test.txt')) . "\n";
echo "  قابل للقراءة: " . (is_readable('test.txt') ? 'نعم' : 'لا') . "\n";
echo "  قابل للكتابة: " . (is_writable('test.txt') ? 'نعم' : 'لا') . "\n";


// ═══════════════════════════════════════
// 6. حذف ملف
// ═══════════════════════════════════════
if (unlink('test.txt')) {
    echo "\n🗑️ تم حذف test.txt\n";
}

echo "\n✅ اكتمل التمرين!\n";
