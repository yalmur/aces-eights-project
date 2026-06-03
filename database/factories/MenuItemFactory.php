<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class MenuItemFactory extends Factory
{
    public function definition(): array
    {
        $name = $this->faker->unique()->words(3, true);
        return [
            'category_id'  => Category::factory(),
            'name'         => ucwords($name),
            'slug'         => Str::slug($name),
            'description'  => $this->faker->sentence(),
            'base_price'   => $this->faker->randomFloat(2, 5, 25),
            'image_path'   => null,
            'is_available' => true,
            'is_featured'  => false,
            'sort_order'   => $this->faker->numberBetween(1, 50),
        ];
    }

    public function unavailable(): static
    {
        return $this->state(['is_available' => false]);
    }

    public function featured(): static
    {
        return $this->state(['is_featured' => true]);
    }
}
