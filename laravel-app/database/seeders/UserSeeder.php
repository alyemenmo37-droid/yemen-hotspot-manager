<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // مسؤول
        User::updateOrCreate(
            ['email' => 'admin@yhm.local'],
            [
                'name' => 'مدير النظام',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // مشرف
        User::updateOrCreate(
            ['email' => 'manager@yhm.local'],
            [
                'name' => 'مشرف',
                'password' => Hash::make('manager123'),
                'role' => 'manager',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // مشغل
        User::updateOrCreate(
            ['email' => 'operator@yhm.local'],
            [
                'name' => 'مشغل',
                'password' => Hash::make('operator123'),
                'role' => 'operator',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('✅ 3 users created:');
        $this->command->info('   👤 admin@yhm.local / admin123');
        $this->command->info('   👤 manager@yhm.local / manager123');
        $this->command->info('   👤 operator@yhm.local / operator123');
    }
}
