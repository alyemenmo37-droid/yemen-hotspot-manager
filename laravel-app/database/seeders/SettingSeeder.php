<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // General
            ['site_name', 'Yemen Hotspot Manager', 'string', 'general'],
            ['site_description', 'نظام احترافي لإدارة شبكات الواي فاي', 'string', 'general'],
            ['company_phone', '', 'string', 'general'],
            ['company_address', 'اليمن', 'string', 'general'],
            
            // MikroTik
            ['mikrotik_enabled', true, 'bool', 'mikrotik'],
            ['mikrotik_default_profile', 'default', 'string', 'mikrotik'],
            
            // Cards
            ['card_default_duration', 1440, 'int', 'cards'], // minutes
            ['card_auto_activate', false, 'bool', 'cards'],
            
            // Notifications
            ['notify_on_new_user', true, 'bool', 'notifications'],
            ['notify_on_low_stock', true, 'bool', 'notifications'],
            ['low_stock_threshold', 10, 'int', 'notifications'],
        ];
        
        foreach ($settings as [$key, $value, $type, $group]) {
            Setting::set($key, $value, $type, $group);
        }
        
        $this->command->info('✅ ' . count($settings) . ' settings created');
    }
}
