<?php

namespace Tests\Feature\Api;

use App\Models\Card;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CardApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_get_cards()
    {
        // 1. أنشئ مستخدم
        $user = User::factory()->create();

        // 2. أنشئ بطاقة
        Card::factory()->create(['number' => '1234567890']);

        // 3. سجل الدخول باستخدام Sanctum
        Sanctum::actingAs($user);

        // 4. أرسل طلب API
        $response = $this->getJson('/api/cards');

        $response->assertStatus(200);
        $response->assertJsonFragment(['number' => '1234567890']);
    }

    public function test_guest_cannot_access_api_cards()
    {
        $response = $this->getJson('/api/cards');
        $response->assertStatus(401);
    }
}
