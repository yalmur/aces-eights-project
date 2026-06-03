<?php

namespace Database\Seeders;

use App\Models\Topping;
use Illuminate\Database\Seeder;

class ToppingSeeder extends Seeder
{
    public function run(): void
    {
        $toppings = [
            ['name' => 'Aubergines',        'price' => 2.00, 'sort_order' => 1],
            ['name' => 'Mixed Peppers',      'price' => 1.50, 'sort_order' => 2],
            ['name' => 'Mushrooms',          'price' => 1.50, 'sort_order' => 3],
            ['name' => 'Regular Pepperoni',  'price' => 2.00, 'sort_order' => 4],
            ['name' => 'Nduja',              'price' => 2.00, 'sort_order' => 5],
            ['name' => 'Spicy Ground Beef',  'price' => 2.00, 'sort_order' => 6],
            ['name' => 'Broccoli',           'price' => 2.00, 'sort_order' => 7],
            ['name' => 'Parmesan',           'price' => 2.00, 'sort_order' => 8],
            ['name' => 'Pine Nuts',          'price' => 1.50, 'sort_order' => 9],
            ['name' => 'Garlic Oil',         'price' => 1.50, 'sort_order' => 10],
            ['name' => 'Mozzarella',         'price' => 2.00, 'sort_order' => 11],
            ['name' => 'Olive Oil',          'price' => 1.50, 'sort_order' => 12],
            ['name' => 'Smoky Pancetta',     'price' => 2.00, 'sort_order' => 13],
            ['name' => 'Tomato Sauce',       'price' => 1.00, 'sort_order' => 14],
            ['name' => 'Basil',              'price' => 0.50, 'sort_order' => 15],
            ['name' => 'Red Onion',          'price' => 1.50, 'sort_order' => 16],
            ['name' => 'Anchovies',          'price' => 2.00, 'sort_order' => 17],
            ['name' => 'Chilli Flakes',      'price' => 1.00, 'sort_order' => 18],
            ['name' => 'Whole Black Olives', 'price' => 1.50, 'sort_order' => 19],
            ['name' => 'Oregano',            'price' => 0.50, 'sort_order' => 20],
            ['name' => 'Vegan Mozzarella',   'price' => 2.50, 'sort_order' => 21],
            ['name' => 'Sicilian Sausage',   'price' => 2.00, 'sort_order' => 22],
            ['name' => 'Hot Honey',          'price' => 2.00, 'sort_order' => 23],
            ['name' => 'Speck Ham',          'price' => 2.00, 'sort_order' => 24],
            ['name' => 'Provolone Picante',  'price' => 1.50, 'sort_order' => 25],
        ];
        foreach ($toppings as $t) {
            Topping::updateOrCreate(['name' => $t['name']], $t);
        }
    }
}
