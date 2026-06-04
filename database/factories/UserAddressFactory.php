<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserAddressFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'        => User::factory(),
            'label'          => $this->faker->randomElement(['Home', 'Work', 'Other']),
            'street_address' => $this->faker->streetAddress(),
            'city'           => 'London',
            'postcode'       => 'NW' . $this->faker->numberBetween(1, 9) . ' ' . $this->faker->numberBetween(1, 9) . 'HP',
            'is_default'     => false,
        ];
    }
}
