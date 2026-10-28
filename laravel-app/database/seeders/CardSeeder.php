<?php

namespace Database\Seeders;

use App\Models\Card;
use Illuminate\Database\Seeder;

class CardSeeder extends Seeder
{
    public function run(): void
    {
        $cards = [
            ['number' => '1111111111', 'amount' => 100, 'speed' => '512k/512k', 'status' => 'active'],
            ['number' => '2222222222', 'amount' => 250, 'speed' => '256k/1M', 'status' => 'active'],
            ['number' => '3333333333', 'amount' => 500, 'speed' => '512k/2M', 'status' => 'unused'],
            ['number' => '4444444444', 'amount' => 1000, 'speed' => '2M/15M', 'status' => 'unused'],
            ['number' => '5555555555', 'amount' => 250, 'speed' => '256k/1M', 'status' => 'expired'],
        ];

        foreach ($cards as $card) {
            Card::create($card);
        }

        // إضافة بطاقات عشوائية
        for ($i = 0; $i < 20; $i++) {
            do {
                $number = '';
                for ($j = 0; $j < 10; $j++) {
                    $number .= random_int(0, 9);
                }
            } while (Card::where('number', $number)->exists());

            Card::create([
                'number' => $number,
                'amount' => [100, 250, 500, 1000][array_rand([100, 250, 500, 1000])],
                'speed' => ['512k/512k', '256k/1M', '512k/2M', '2M/15M'][array_rand(['512k/512k', '256k/1M', '512k/2M', '2M/15M'])],
                'status' => ['unused', 'active', 'expired'][array_rand(['unused', 'active', 'expired'])],
            ]);
        }

        $this->command->info('✅ Cards seeded: '.Card::count());
    }
}
