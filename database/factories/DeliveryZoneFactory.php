<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class DeliveryZoneFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'       => $this->faker->words(3, true),
            'min_km'     => 0.00,
            'max_km'     => $this->faker->randomFloat(2, 1, 5),
            'fee'        => $this->faker->randomFloat(2, 2, 6),
            'is_active'  => true,
            'sort_order' => $this->faker->numberBetween(1, 10),
        ];
    }
}
