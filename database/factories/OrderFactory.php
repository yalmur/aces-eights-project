<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    public function definition(): array
    {
        $subtotal = $this->faker->randomFloat(2, 10, 60);
        $fee = 3.50;
        return [
            'user_id'           => User::factory(),
            'type'              => 'delivery',
            'status'            => 'accepted',
            'subtotal'          => $subtotal,
            'delivery_fee'      => $fee,
            'total'             => round($subtotal + $fee, 2),
            'customer_name'     => $this->faker->name(),
            'customer_email'    => $this->faker->email(),
            'customer_phone'    => $this->faker->phoneNumber(),
            'delivery_address'  => $this->faker->streetAddress(),
            'delivery_city'     => 'London',
            'delivery_postcode' => 'NW5 2HP',
        ];
    }

    public function pendingPayment(): static
    {
        return $this->state(['status' => 'pending_payment', 'stripe_session_id' => 'cs_test_' . fake()->uuid()]);
    }

    public function collection(): static
    {
        return $this->state(['type' => 'collection', 'delivery_fee' => 0]);
    }
}
