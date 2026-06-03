<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AllergenFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'       => $this->faker->unique()->word(),
            'icon'       => 'warning',
            'is_visible' => true,
            'sort_order' => $this->faker->numberBetween(1, 10),
        ];
    }
}
