<?php

namespace Database\Factories;

use App\Models\Card;
use Illuminate\Database\Eloquent\Factories\Factory;

class CardFactory extends Factory
{
    protected $model = Card::class;

    public function definition(): array
    {
        return [
            'number' => $this->faker->unique()->numerify('##########'),
            'amount' => $this->faker->randomElement([100, 250, 500, 1000]),
            'speed' => $this->faker->randomElement(['512k/512k', '256k/1M', '512k/2M', '2M/15M']),
            'status' => $this->faker->randomElement(['unused', 'active', 'expired', 'stopped']),
            'data_limit_mb' => null,
            'data_used_mb' => 0,
        ];
    }
}
