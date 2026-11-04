<?php

namespace Database\Seeders;

use App\Models\Tenant;
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
        \App\Models\User::whereNull('tenant_id')->update(['tenant_id' => $yemenNet->id]);
        \App\Models\Card::whereNull('tenant_id')->update(['tenant_id' => $yemenNet->id]);
        \App\Models\Category::whereNull('tenant_id')->update(['tenant_id' => $yemenNet->id]);
        \App\Models\SpeedLevel::whereNull('tenant_id')->update(['tenant_id' => $yemenNet->id]);

        $this->command->info('✅ 2 tenants created + data linked');
    }
}
