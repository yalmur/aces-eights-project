<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Starters',    'slug' => 'starters',    'sort_order' => 1],
            ['name' => 'Salads',      'slug' => 'salads',      'sort_order' => 2],
            ['name' => 'Pasta',       'slug' => 'pasta',       'sort_order' => 3],
            ['name' => 'Pizza',       'slug' => 'pizza',       'sort_order' => 4],
            ['name' => 'Tuna Salads', 'slug' => 'tuna-salads', 'sort_order' => 5],
            ['name' => 'Feta Salads', 'slug' => 'feta-salads', 'sort_order' => 6],
            ['name' => 'Desserts',    'slug' => 'desserts',    'sort_order' => 7],
            ['name' => 'Drinks',      'slug' => 'drinks',      'sort_order' => 8],
        ];
        foreach ($categories as $cat) {
            Category::updateOrCreate(['slug' => $cat['slug']], $cat);
        }
    }
}
