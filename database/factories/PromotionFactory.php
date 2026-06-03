<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PromotionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'code'             => strtoupper($this->faker->unique()->lexify('????')),
            'name'             => $this->faker->words(3, true),
            'type'             => $this->faker->randomElement(['percentage', 'fixed_amount', 'free_delivery']),
            'value'            => $this->faker->randomFloat(2, 5, 20),
            'min_order_amount' => null,
            'max_uses'         => null,
            'current_uses'     => 0,
            'is_active'        => true,
            'expires_at'       => null,
        ];
    }

    public function percentage(float $pct = 10): static
    {
        return $this->state(['type' => 'percentage', 'value' => $pct]);
    }

    public function fixed(float $amount = 5): static
    {
        return $this->state(['type' => 'fixed_amount', 'value' => $amount]);
    }

    public function freeDelivery(): static
    {
        return $this->state(['type' => 'free_delivery', 'value' => 0]);
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }

    public function expired(): static
    {
        return $this->state(['expires_at' => now()->subDay()]);
    }
}
