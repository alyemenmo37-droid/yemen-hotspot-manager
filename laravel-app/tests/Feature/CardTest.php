<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\SpeedLevel;
use App\Models\User;
use App\Services\MikroTikService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use Tests\TestCase;

class CardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_card()
    {
        // 1. Mock MikroTikService (لكي لا يتصل بـ FreeRADIUS)
        $this->mock(MikroTikService::class, function (MockInterface $mock) {
            $mock->shouldReceive('addUser')
                ->once()
                ->andReturn([
                    'success' => true,
                    'mode' => 'mock',
                    'message' => 'OK',
                ]);
        });

        // 2. إنشاء فئة ومستوى سرعة
        $category = Category::create([
            'name' => 'Test Category',
            'slug' => 'test-category',
            'color' => '#009639',
            'is_active' => true,
        ]);

        $speedLevel = SpeedLevel::create([
            'category_id' => $category->id,
            'name' => 'Test Speed',
            'rate_limit' => '1M/5M',
            'price' => 500,
            'duration_minutes' => 1440,
            'data_limit_mb' => 1024,
            'is_default' => true,
            'is_active' => true,
        ]);

        // 3. إنشاء مستخدم admin
        $admin = User::create([
            'name' => 'Test Admin',
            'email' => 'test-admin-' . uniqid() . '@test.local',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        // 4. محاولة إنشاء بطاقة
        $response = $this->actingAs($admin)->post('/cards', [
            'number' => '1234567891',
            'speed_level_id' => $speedLevel->id,
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('cards', ['number' => '1234567891']);
    }

    public function test_guest_cannot_access_cards()
    {
        $response = $this->get('/cards');
        $response->assertRedirect('/login');
    }
}
