<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\SpeedLevel;
use Illuminate\Database\Seeder;

class CategorySpeedSeeder extends Seeder
{
    public function run(): void
    {
        // ⚠️ حذف البيانات الموجودة أولاً
        SpeedLevel::query()->delete();
        Category::query()->delete();

        $data = [
            [
                'name' => 'بطيء',
                'slug' => 'slow',
                'color' => '#8B7355',
                'description' => 'بطاقات بسرعة منخفضة',
                'speeds' => [
                    ['name' => 'بطيء - أسبوعي', 'rate_limit' => '512k/512k', 'price' => 100, 'duration_minutes' => 10080, 'data_limit_mb' => 500],
                ],
            ],
            [
                'name' => 'متوسط',
                'slug' => 'medium',
                'color' => '#f57c00',
                'description' => 'بطاقات متوسطة',
                'speeds' => [
                    ['name' => 'متوسط - يومي', 'rate_limit' => '256k/1M', 'price' => 250, 'duration_minutes' => 1440, 'data_limit_mb' => 1024, 'is_default' => true],
                    ['name' => 'متوسط - أسبوعي', 'rate_limit' => '256k/1M', 'price' => 800, 'duration_minutes' => 10080, 'data_limit_mb' => 5120],
                ],
            ],
            [
                'name' => 'عالي',
                'slug' => 'high',
                'color' => '#c62828',
                'description' => 'بطاقات عالية السرعة',
                'speeds' => [
                    ['name' => 'عالي - 3 أيام', 'rate_limit' => '512k/2M', 'price' => 500, 'duration_minutes' => 4320, 'data_limit_mb' => 3072],
                    ['name' => 'عالي - أسبوعي', 'rate_limit' => '512k/2M', 'price' => 1500, 'duration_minutes' => 10080, 'data_limit_mb' => 10240],
                ],
            ],
            [
                'name' => 'مفتوح',
                'slug' => 'unlimited',
                'color' => '#009639',
                'description' => 'بطاقات غير محدودة',
                'speeds' => [
                    ['name' => 'مفتوح - شهري', 'rate_limit' => '2M/15M', 'price' => 2000, 'duration_minutes' => 43200, 'data_limit_mb' => 102400],
                ],
            ],
        ];

        foreach ($data as $catData) {
            $speeds = $catData['speeds'];
            unset($catData['speeds']);

            $category = Category::create($catData);

            foreach ($speeds as $speed) {
                $category->speedLevels()->create($speed);
            }

            $this->command->info("✅ {$category->name}: ".count($speeds).' مستوى');
        }

        $this->command->info('🎉 تم إنشاء '.Category::count().' فئة و '.SpeedLevel::count().' مستوى');
    }
}
