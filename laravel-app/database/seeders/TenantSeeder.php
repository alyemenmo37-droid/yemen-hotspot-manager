<?php

namespace Database\Seeders;

use App\Models\Card;
use App\Models\Category;
use App\Models\SpeedLevel;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    public function run(): void
    {
        // 1. إنشاء شبكتين تجريبيتين
        $yemenNet = Tenant::create([
            'name' => 'Yemen Net',
            'domain' => 'yemen.local',
        ]);

        $adenNet = Tenant::create([
            'name' => 'Aden Net',
            'domain' => 'aden.local',
        ]);

        // 2. ربط البيانات الحالية بالشبكة الأولى
        User::whereNull('tenant_id')->update(['tenant_id' => $yemenNet->id]);
        Card::whereNull('tenant_id')->update(['tenant_id' => $yemenNet->id]);
        Category::whereNull('tenant_id')->update(['tenant_id' => $yemenNet->id]);
        SpeedLevel::whereNull('tenant_id')->update(['tenant_id' => $yemenNet->id]);

        $this->command->info('✅ 2 tenants created + data linked');
    }
}
