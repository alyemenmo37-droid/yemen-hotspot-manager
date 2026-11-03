<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Card;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_card()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        
        $response = $this->actingAs($admin)->post('/cards', [
            'number' => '1234567891',
            'amount' => 500,
            'speed' => '1M/5M',
            'speed_level_id' => 1, // افترض أن الفئة موجودة
        ]);

        $response->assertStatus(302); // إعادة توجيه بعد النجاح
        $this->assertDatabaseHas('cards', ['number' => '1234567891']);
    }

    public function test_guest_cannot_access_cards()
    {
        $response = $this->get('/cards');
        $response->assertRedirect('/login');
    }
}
