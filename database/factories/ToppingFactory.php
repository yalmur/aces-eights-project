<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ToppingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'         => ucwords($this->faker->unique()->words(2, true)),
            'price'        => $this->faker->randomFloat(2, 1, 3),
            'is_available' => true,
            'sort_order'   => $this->faker->numberBetween(1, 30),
        ];
    }
}
