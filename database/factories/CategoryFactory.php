<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CategoryFactory extends Factory
{
    public function definition(): array
    {
        $name = $this->faker->unique()->word();
        return [
            'name'       => ucfirst($name),
            'slug'       => Str::slug($name),
            'sort_order' => $this->faker->numberBetween(1, 10),
        ];
    }
}
