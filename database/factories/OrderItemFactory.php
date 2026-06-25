<?php

namespace Database\Factories;

use App\Models\MenuItem;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemFactory extends Factory
{
    public function definition(): array
    {
        $price = $this->faker->randomFloat(2, 5, 25);
        return [
            'order_id'            => Order::factory(),
            'menu_item_id'        => MenuItem::factory(),
            'name'                => $this->faker->words(2, true),
            'qty'                 => 1,
            'unit_price'          => $price,
            'size'                => null,
            'crust'               => null,
            'size_extra'          => 0,
            'crust_extra'         => 0,
            'added_toppings'      => null,
            'removed_ingredients' => null,
            'instructions'        => null,
            'line_total'          => $price,
        ];
    }
}
