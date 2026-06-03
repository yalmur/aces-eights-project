<?php

namespace Database\Seeders;

use App\Models\Allergen;
use Illuminate\Database\Seeder;

class AllergenSeeder extends Seeder
{
    public function run(): void
    {
        $allergens = [
            ['name' => 'Gluten',    'icon' => 'bakery_dining', 'sort_order' => 1],
            ['name' => 'Dairy',     'icon' => 'egg',           'sort_order' => 2],
            ['name' => 'Eggs',      'icon' => 'egg',           'sort_order' => 3],
            ['name' => 'Nuts',      'icon' => 'nutrition',     'sort_order' => 4],
            ['name' => 'Soy',       'icon' => 'grass',         'sort_order' => 5],
            ['name' => 'Shellfish', 'icon' => 'set_meal',      'sort_order' => 6],
            ['name' => 'Celery',    'icon' => 'eco',           'sort_order' => 7],
            ['name' => 'Sulphites', 'icon' => 'science',       'sort_order' => 8],
        ];
        foreach ($allergens as $a) {
            Allergen::updateOrCreate(['name' => $a['name']], $a);
        }
    }
}
