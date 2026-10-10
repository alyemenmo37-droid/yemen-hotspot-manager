<?php
/**
 * مكتبة دوال Hotspot
 * اليوم 2 - المشروع الشامل
 */

declare(strict_types=1);

// ═══════════════════════════════════════
// 1. دوال توليد البطاقات
// ═══════════════════════════════════════

/**
 * توليد رقم بطاقة عشوائي
 */
function generateCardNumber(int $length = 10): string {
    $number = '';
    for ($i = 0; $i < $length; $i++) {
        $number .= random_int(0, 9);
    }
    return $number;
}

/**
 * توليد دفعة بطاقات
 */
function generateBatch(int $count, int $length = 10): array {
    $cards = [];
    for ($i = 0; $i < $count; $i++) {
        $cards[] = generateCardNumber($length);
    }
    return $cards;
}


// ═══════════════════════════════════════
// 2. دوال السرعات
// ═══════════════════════════════════════

/**
 * تنسيق السرعة
 */
function formatSpeed(int $uploadKb, int $downloadKb): string {
    $upload = $uploadKb >= 1024 
        ? round($uploadKb / 1024, 1) . 'M' 
        : $uploadKb . 'k';
    $download = $downloadKb >= 1024 
        ? round($downloadKb / 1024, 1) . 'M' 
        : $downloadKb . 'k';
    
    return "$upload/$download";
}

/**
 * مستويات السرعة
 */
function getSpeedLevels(): array {
    return [
        1 => ['name' => 'بطيء',    'upload' => 512,  'download' => 512],
        2 => ['name' => 'متوسطة',  'upload' => 256,  'download' => 1024],
        3 => ['name' => 'عالية',   'upload' => 512,  'download' => 2048],
        4 => ['name' => 'مفتوح',   'upload' => 2048, 'download' => 15360],
    ];
}

/**
 * الحصول على سرعة بالمعرف
 */
function getSpeedById(int $id): ?string {
    $levels = getSpeedLevels();
    if (!isset($levels[$id])) {
        return null;
    }
    
    $level = $levels[$id];
    return formatSpeed($level['upload'], $level['download']);
}


// ═══════════════════════════════════════
// 3. دوال الوقت والصلاحية
// ═══════════════════════════════════════

/**
 * حساب وقت الانتهاء
 */
function calculateExpiry(int $durationMinutes, ?string $from = null): string {
    $start = $from ? strtotime($from) : time();
    $end = $start + ($durationMinutes * 60);
    return date('Y-m-d H:i:s', $end);
}

/**
 * هل انتهت البطاقة؟
 */
function isExpired(string $expiryDate): bool {
    return strtotime($expiryDate) < time();
}

/**
 * الوقت المتبقي (نص مقروء)
 */
function timeRemaining(string $expiryDate): string {
    $diff = strtotime($expiryDate) - time();
    
    if ($diff <= 0) {
        return 'منتهية';
    }
    
    $days = floor($diff / 86400);
    $hours = floor(($diff % 86400) / 3600);
    $minutes = floor(($diff % 3600) / 60);
    
    $parts = [];
    if ($days > 0) $parts[] = "$days يوم";
    if ($hours > 0) $parts[] = "$hours ساعة";
    if ($minutes > 0) $parts[] = "$minutes دقيقة";
    
    return implode(' و ', $parts);
}


// ═══════════════════════════════════════
// 4. دوال التنسيق
// ═══════════════════════════════════════

/**
 * تنسيق حجم البيانات
 */
function formatBytes(int $bytes, int $precision = 2): string {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    
    $i = 0;
    while ($bytes >= 1024 && $i < count($units) - 1) {
        $bytes /= 1024;
        $i++;
    }
    
    return round($bytes, $precision) . ' ' . $units[$i];
}

/**
 * تنسيق المبلغ بالريال
 */
function formatMoney(float $amount): string {
    return number_format($amount, 0) . ' ريال';
}


// ═══════════════════════════════════════
// 5. دوال التحقق
// ═══════════════════════════════════════

/**
 * التحقق من رقم البطاقة
 */
function isValidCardNumber(string $number): bool {
    return preg_match('/^[0-9]{8,12}$/', $number) === 1;
}

/**
 * التحقق من عنوان IP
 */
function isValidIp(string $ip): bool {
    return filter_var($ip, FILTER_VALIDATE_IP) !== false;
}

/**
 * التحقق من MAC Address
 */
function isValidMac(string $mac): bool {
    return preg_match('/^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/', $mac) === 1;
}


// ═══════════════════════════════════════
// 🎬 الاختبار
// ═══════════════════════════════════════

echo "╔" . str_repeat("═", 50) . "╗\n";
echo "║  🎫 مكتبة دوال Hotspot                          ║\n";
echo "╚" . str_repeat("═", 50) . "╝\n\n";

// 1. توليد بطاقات
echo "📇 توليد 5 بطاقات:\n";
$cards = generateBatch(5);
foreach ($cards as $card) {
    echo "   • $card\n";
}

// 2. السرعات
echo "\n⚡ مستويات السرعة:\n";
foreach (getSpeedLevels() as $id => $level) {
    $speed = formatSpeed($level['upload'], $level['download']);
    printf("   %d. %-10s → %s\n", $id, $level['name'], $speed);
}

// 3. الوقت
echo "\n⏱️ حساب وقت الانتهاء:\n";
$expiry = calculateExpiry(1440); // يوم واحد
echo "   البطاقة تنتهي: $expiry\n";
echo "   الوقت المتبقي: " . timeRemaining($expiry) . "\n";
echo "   منتهية؟ " . (isExpired($expiry) ? "نعم" : "لا") . "\n";

// 4. حجم البيانات
echo "\n💾 تنسيق البيانات:\n";
echo "   1024 bytes → " . formatBytes(1024) . "\n";
echo "   1,048,576 bytes → " . formatBytes(1048576) . "\n";
echo "   404239943 bytes → " . formatBytes(404239943) . "\n";

// 5. المال
echo "\n💰 تنسيق المال:\n";
echo "   " . formatMoney(250) . "\n";
echo "   " . formatMoney(1500) . "\n";

// 6. التحقق
echo "\n✅ التحقق:\n";
var_dump(isValidCardNumber('2672147404'));   // true
var_dump(isValidCardNumber('abc'));           // false
var_dump(isValidIp('192.168.1.1'));          // true
var_dump(isValidMac('76:1A:D5:63:A3:1C'));   // true

// 7. عرض بطاقة كاملة
echo "\n📋 بطاقة كاملة:\n";
$myCard = [
    'number'    => generateCardNumber(),
    'speed'     => getSpeedById(3),
    'amount'    => 250,
    'created'   => date('Y-m-d H:i:s'),
    'expiry'    => calculateExpiry(1440),
    'remaining' => timeRemaining(calculateExpiry(1440)),
];

foreach ($myCard as $key => $value) {
    printf("   %-12s: %s\n", $key, $value);
}

echo "\n🎉 اكتمل التمرين!\n";
