<?php
/**
 * YHM - Using Packagist Libraries
 */

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use Carbon\Carbon;
use Faker\Factory as Faker;

echo "╔" . str_repeat("═", 60) . "╗\n";
echo "║  📦 YHM - Packagist Libraries Demo             ║\n";
echo "╚" . str_repeat("═", 60) . "╝\n\n";

// ═══════════════════════════════════════
// 1. Carbon - التواريخ
// ═══════════════════════════════════════
echo "📌 Carbon:\n";

$now = Carbon::now();
echo "   الآن: $now\n";
echo "   بعد ساعة: " . $now->copy()->addHour() . "\n";
echo "   بالأمس: " . $now->copy()->subDay()->format('Y-m-d') . "\n";
echo "   بعد أسبوع: " . $now->copy()->addWeek() . "\n";

$expiry = Carbon::now()->addDays(30);
$remaining = $now->diffInDays($expiry);
echo "   تنتهي بعد: $remaining يوم\n";

// ═══════════════════════════════════════
// 2. Faker - بيانات وهمية
// ═══════════════════════════════════════
echo "\n📌 Faker:\n";

$faker = Faker::create('ar_SA');

echo "   اسم: " . $faker->name() . "\n";
echo "   بريد: " . $faker->email() . "\n";
echo "   هاتف: " . $faker->phoneNumber() . "\n";
echo "   مدينة: " . $faker->city() . "\n";
echo "   عنوان: " . $faker->address() . "\n";

// ═══════════════════════════════════════
// 3. توليد بطاقات مع Faker
// ═══════════════════════════════════════
echo "\n📌 توليد 5 بطاقات وهمية:\n";

use Yhm\Models\Card;

for ($i = 0; $i < 5; $i++) {
    $number = '';
    for ($j = 0; $j < 10; $j++) {
        $number .= random_int(0, 9);
    }
    
    $card = new Card(
        id: $i + 1,
        number: $number,
        amount: $faker->randomElement([100, 250, 500, 1000]),
        speed: $faker->randomElement(['512k/512k', '256k/1M', '512k/2M', '2M/15M']),
        status: $faker->randomElement(['unused', 'active']),
    );
    
    echo "   " . $card->label() . "\n";
}

echo "\n✅ كل الحزم تعمل!\n";
