<?php

namespace Database\Seeders;

use App\Models\Allergen;
use App\Models\BaseIngredient;
use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MenuItemSeeder extends Seeder
{
    public function run(): void
    {
        $cats = Category::pluck('id', 'slug');
        $allergenIds = Allergen::pluck('id', 'name');

        $items = [
            // STARTERS
            ['category' => 'starters', 'name' => 'Olives',             'desc' => 'Sicilian green olives.',                                                    'price' => 4.50, 'allergens' => []],
            ['category' => 'starters', 'name' => 'Basket of Bread',    'desc' => 'Fresh homemade focaccia with olive oil.',                                   'price' => 4.50, 'allergens' => ['Gluten']],
            ['category' => 'starters', 'name' => 'Garlic Bread',       'desc' => 'Tomato sauce and garlic.',                                                  'price' => 5.00, 'allergens' => ['Gluten']],
            ['category' => 'starters', 'name' => 'Antipasto Italiano', 'desc' => 'Premium Italian cheeses, cured meats and bread.',                          'price' => 9.50, 'allergens' => ['Gluten', 'Dairy']],
            ['category' => 'starters', 'name' => 'Caprese',            'desc' => 'Buffalo mozzarella, heritage tomato and fresh basil.',                     'price' => 8.50, 'allergens' => ['Dairy']],
            ['category' => 'starters', 'name' => 'Bresaola',           'desc' => 'Sliced dry beef fillet, rocket, parmesan and lemon.',                      'price' => 9.00, 'allergens' => ['Dairy']],
            ['category' => 'starters', 'name' => 'Tricolore',          'desc' => 'Buffalo mozzarella, tomato, avocado, basil and extra virgin olive oil.',   'price' => 8.00, 'allergens' => ['Dairy']],
            // SALADS
            ['category' => 'salads', 'name' => 'Insalata Verde',        'desc' => 'Mixed leaves, avocado, fennel, cucumber.',                                          'price' => 7.50, 'allergens' => []],
            ['category' => 'salads', 'name' => 'Insalata Mista',        'desc' => 'Mixed leaves, tomato, red onion, peppers.',                                         'price' => 7.00, 'allergens' => []],
            ['category' => 'salads', 'name' => 'Rucola e Parmigiana',   'desc' => 'Rocket, cherry tomatoes, parmesan.',                                                'price' => 8.00, 'allergens' => ['Dairy']],
            ['category' => 'salads', 'name' => 'Chicken Avocado Salad', 'desc' => 'Grilled chicken, avocado, mixed leaves, croutons and parmesan.',                   'price' => 11.00, 'allergens' => ['Gluten', 'Dairy']],
            ['category' => 'salads', 'name' => 'Insalata di Carciofi',  'desc' => 'Artichoke salad, baby spinach, parmesan, balsamic vinegar, hazelnut, pomegranate.','price' => 10.50, 'allergens' => ['Dairy', 'Nuts']],
            ['category' => 'salads', 'name' => 'Insalata Mediterranea', 'desc' => 'Avocado, cherry tomato, baby gem, radicchio lettuce, fennel and lemon dressing.',  'price' => 8.50, 'allergens' => []],
            ['category' => 'salads', 'name' => 'Insalata Cesare',       'desc' => 'Slow roast chicken, baby gem, boiled egg, anchovies, fried capers, Caesar dressing.','price' => 11.50, 'allergens' => ['Gluten', 'Dairy', 'Eggs', 'Shellfish']],
            // PASTA
            ['category' => 'pasta', 'name' => 'Lasagna',                  'desc' => 'Layers of pasta with rich beef ragù, béchamel sauce and cheese.',  'price' => 13.50, 'allergens' => ['Gluten', 'Dairy', 'Eggs']],
            ['category' => 'pasta', 'name' => 'Cannelloni Spinach Ricotta','desc' => 'Pasta tubes filled with spinach and ricotta.',                      'price' => 13.00, 'allergens' => ['Gluten', 'Dairy', 'Eggs']],
            ['category' => 'pasta', 'name' => 'Melanzane Parmigiana',      'desc' => 'Layers of aubergine, tomato sauce, parmesan and mozzarella.',       'price' => 12.50, 'allergens' => ['Dairy']],
            // PIZZA
            ['category' => 'pizza', 'name' => 'Garlic Bread Pizza', 'desc' => 'Tomato sauce and garlic.',                                                                        'price' => 10.50, 'allergens' => ['Gluten'], 'ingredients' => ['Tomato Sauce', 'Garlic', 'Olive Oil']],
            ['category' => 'pizza', 'name' => 'Vegan Pizza',        'desc' => 'Tomato sauce, garlic, avocado, onion, rocket and lemon juice.',                                   'price' => 13.50, 'allergens' => ['Gluten'], 'ingredients' => ['Tomato Sauce', 'Garlic', 'Avocado', 'Red Onion', 'Rocket']],
            ['category' => 'pizza', 'name' => 'Margherita',         'desc' => 'Tomato sauce, mozzarella.',                                                                       'price' => 12.00, 'allergens' => ['Gluten', 'Dairy'], 'ingredients' => ['Tomato Sauce', 'Mozzarella']],
            ['category' => 'pizza', 'name' => 'Honey Mushroom',     'desc' => 'Tomato sauce, mozzarella, mushroom, spinach, blue cheese, honey and olive oil.',                 'price' => 15.00, 'allergens' => ['Gluten', 'Dairy'], 'ingredients' => ['Tomato Sauce', 'Mozzarella', 'Mushrooms', 'Spinach', 'Blue Cheese', 'Honey']],
            ['category' => 'pizza', 'name' => 'Quattro Formaggi',   'desc' => 'Tomato sauce, mozzarella, gorgonzola piccante, dolcelatte and parmesan.',                        'price' => 15.50, 'allergens' => ['Gluten', 'Dairy'], 'ingredients' => ['Tomato Sauce', 'Mozzarella', 'Gorgonzola', 'Dolcelatte', 'Parmesan']],
            ['category' => 'pizza', 'name' => 'Veggie',             'desc' => 'Tomato sauce, mozzarella, aubergine, courgette, onion, provolone cheese and garlic.',            'price' => 14.00, 'allergens' => ['Gluten', 'Dairy'], 'ingredients' => ['Tomato Sauce', 'Mozzarella', 'Aubergine', 'Courgette', 'Red Onion', 'Provolone']],
            ['category' => 'pizza', 'name' => 'Caprino',            'desc' => 'Tomato sauce, mozzarella, goats cheese, asparagus and parmesan.',                                'price' => 15.00, 'allergens' => ['Gluten', 'Dairy'], 'ingredients' => ['Tomato Sauce', 'Mozzarella', 'Goats Cheese', 'Asparagus', 'Parmesan']],
            ['category' => 'pizza', 'name' => 'Squash',             'desc' => 'Tomato sauce, mozzarella, squash, feta cheese and garlic.',                                      'price' => 14.50, 'allergens' => ['Gluten', 'Dairy'], 'ingredients' => ['Tomato Sauce', 'Mozzarella', 'Squash', 'Feta Cheese', 'Garlic']],
            ['category' => 'pizza', 'name' => 'Aces',               'desc' => 'Tomato sauce, mozzarella, goats cheese, peppers and rocket.',                                    'price' => 15.00, 'allergens' => ['Gluten', 'Dairy'], 'ingredients' => ['Tomato Sauce', 'Mozzarella', 'Goats Cheese', 'Mixed Peppers', 'Rocket']],
            ['category' => 'pizza', 'name' => 'Vegetariana',        'desc' => 'Tomato sauce, mozzarella, olives, mushrooms, peppers and spinach.',                              'price' => 14.00, 'allergens' => ['Gluten', 'Dairy'], 'ingredients' => ['Tomato Sauce', 'Mozzarella', 'Olives', 'Mushrooms', 'Mixed Peppers', 'Spinach']],
            ['category' => 'pizza', 'name' => 'Saporita',           'desc' => 'Tomato sauce, mozzarella, buffalo mozzarella, gorgonzola, rocket, pesto, cherry tomatoes.',     'price' => 16.50, 'allergens' => ['Gluten', 'Dairy', 'Nuts'], 'ingredients' => ['Tomato Sauce', 'Mozzarella', 'Buffalo Mozzarella', 'Gorgonzola', 'Rocket', 'Pesto']],
            ['category' => 'pizza', 'name' => 'Ferrari',            'desc' => 'Tomato sauce, mozzarella, pepperoni and mushroom.',                                               'price' => 14.50, 'allergens' => ['Gluten', 'Dairy'], 'ingredients' => ['Tomato Sauce', 'Mozzarella', 'Pepperoni', 'Mushrooms']],
            ['category' => 'pizza', 'name' => 'Napoli',             'desc' => 'Tomato sauce, mozzarella, anchovies, capers and olives.',                                        'price' => 14.50, 'allergens' => ['Gluten', 'Dairy', 'Shellfish'], 'ingredients' => ['Tomato Sauce', 'Mozzarella', 'Anchovies', 'Capers', 'Olives']],
            ['category' => 'pizza', 'name' => 'Burrata',            'desc' => 'Cherry tomatoes, garlic, basil, burrata and balsamic glaze.',                                    'price' => 16.00, 'allergens' => ['Gluten', 'Dairy'], 'ingredients' => ['Cherry Tomatoes', 'Garlic', 'Basil', 'Burrata', 'Balsamic Glaze']],
            ['category' => 'pizza', 'name' => 'Hawaii',             'desc' => 'Tomato sauce, mozzarella, ham and pineapple.',                                                   'price' => 13.50, 'allergens' => ['Gluten', 'Dairy'], 'ingredients' => ['Tomato Sauce', 'Mozzarella', 'Ham', 'Pineapple']],
            ['category' => 'pizza', 'name' => 'Punta Luzzi',        'desc' => 'Tomato sauce, mozzarella, pear, ham and gorgonzola.',                                            'price' => 15.50, 'allergens' => ['Gluten', 'Dairy'], 'ingredients' => ['Tomato Sauce', 'Mozzarella', 'Pear', 'Ham', 'Gorgonzola']],
            ['category' => 'pizza', 'name' => 'Eights',             'desc' => 'Tomato sauce, mozzarella, peppers, onions, chorizo and mushroom.',                               'price' => 15.00, 'allergens' => ['Gluten', 'Dairy'], 'ingredients' => ['Tomato Sauce', 'Mozzarella', 'Mixed Peppers', 'Red Onion', 'Chorizo', 'Mushrooms']],
            ['category' => 'pizza', 'name' => 'Quattro Stagioni',   'desc' => 'Tomato sauce, mozzarella, pepperoni, ham, olives and mushroom.',                                'price' => 15.00, 'allergens' => ['Gluten', 'Dairy'], 'ingredients' => ['Tomato Sauce', 'Mozzarella', 'Pepperoni', 'Ham', 'Olives', 'Mushrooms']],
            ['category' => 'pizza', 'name' => 'Piccante',           'desc' => 'Tomato sauce, mozzarella, pepperoni, jalapeño chillies and ham.',                                'price' => 14.50, 'allergens' => ['Gluten', 'Dairy'], 'ingredients' => ['Tomato Sauce', 'Mozzarella', 'Pepperoni', 'Jalapeño Chillies', 'Ham']],
            ['category' => 'pizza', 'name' => 'Pancetta',           'desc' => 'Tomato sauce, mozzarella, roasted peppers, onion and pancetta.',                                 'price' => 15.00, 'allergens' => ['Gluten', 'Dairy'], 'ingredients' => ['Tomato Sauce', 'Mozzarella', 'Roasted Peppers', 'Red Onion', 'Pancetta']],
            ['category' => 'pizza', 'name' => 'Il Bacio',           'desc' => 'Tomato sauce, mozzarella, Parma ham, asparagus, rocket and parmesan.',                           'price' => 16.50, 'allergens' => ['Gluten', 'Dairy'], 'ingredients' => ['Tomato Sauce', 'Mozzarella', 'Parma Ham', 'Asparagus', 'Rocket', 'Parmesan']],
            ['category' => 'pizza', 'name' => 'Meat Lover',         'desc' => 'Tomato sauce, mozzarella, pepperoni, ham, chorizo, nduja and pancetta.',                         'price' => 17.00, 'allergens' => ['Gluten', 'Dairy'], 'ingredients' => ['Tomato Sauce', 'Mozzarella', 'Pepperoni', 'Ham', 'Chorizo', 'Nduja', 'Pancetta']],
            // TUNA SALADS
            ['category' => 'tuna-salads', 'name' => 'Italian Tuna Salad',       'desc' => 'Tuna, mixed leaves, cherry tomatoes, red onion, olives and cucumber.',     'price' => 10.50, 'allergens' => ['Shellfish']],
            ['category' => 'tuna-salads', 'name' => 'Mediterranean Tuna Salad', 'desc' => 'Tuna, baby gem lettuce, avocado, cherry tomato and fennel.',               'price' => 10.50, 'allergens' => ['Shellfish']],
            ['category' => 'tuna-salads', 'name' => 'Sicilian Tuna Salad',      'desc' => 'Tuna, orange segments, fennel, rocket and olives.',                       'price' => 10.50, 'allergens' => ['Shellfish']],
            ['category' => 'tuna-salads', 'name' => 'Tuna and Mozzarella',      'desc' => 'Tuna, buffalo mozzarella, tomato and basil.',                             'price' => 11.00, 'allergens' => ['Dairy', 'Shellfish']],
            ['category' => 'tuna-salads', 'name' => 'Tuna Caesar',              'desc' => 'Tuna, baby gem lettuce, parmesan, croutons and Caesar dressing.',        'price' => 11.50, 'allergens' => ['Gluten', 'Dairy', 'Eggs', 'Shellfish']],
            // FETA SALADS
            ['category' => 'feta-salads', 'name' => 'Greek Feta Salad',               'desc' => 'Feta cheese, cucumber, cherry tomatoes, red onion and olives.',    'price' => 9.50,  'allergens' => ['Dairy']],
            ['category' => 'feta-salads', 'name' => 'Feta and Avocado Salad',         'desc' => 'Feta cheese, avocado, mixed leaves and cucumber.',                 'price' => 9.50,  'allergens' => ['Dairy']],
            ['category' => 'feta-salads', 'name' => 'Mediterranean Feta Salad',       'desc' => 'Feta cheese, rocket, cherry tomatoes, fennel and olives.',         'price' => 9.50,  'allergens' => ['Dairy']],
            ['category' => 'feta-salads', 'name' => 'Watermelon and Feta Salad',      'desc' => 'Fresh watermelon, feta cheese, mint and rocket.',                  'price' => 10.00, 'allergens' => ['Dairy']],
            ['category' => 'feta-salads', 'name' => 'Roasted Beetroot and Feta Salad','desc' => 'Roasted beetroot, feta cheese, rocket and walnuts.',               'price' => 10.00, 'allergens' => ['Dairy', 'Nuts']],
            // DESSERTS
            ['category' => 'desserts', 'name' => 'Ice Cream 500g',               'desc' => 'Premium Italian ice cream.',                             'price' => 7.00, 'allergens' => ['Dairy', 'Eggs']],
            ['category' => 'desserts', 'name' => 'Neapolitan Baba',              'desc' => 'Traditional rum flavour sponge (no alcohol).',           'price' => 6.50, 'allergens' => ['Gluten', 'Eggs']],
            ['category' => 'desserts', 'name' => 'Sicilian Cannoli',             'desc' => 'Filled pastry with ricotta cream.',                      'price' => 6.50, 'allergens' => ['Gluten', 'Dairy']],
            ['category' => 'desserts', 'name' => 'Tiramisu',                     'desc' => 'Coffee-soaked sponge, mascarpone and cocoa.',            'price' => 7.00, 'allergens' => ['Gluten', 'Dairy', 'Eggs']],
            ['category' => 'desserts', 'name' => 'Gluten Free Tiramisu',         'desc' => 'Gluten free tiramisu.',                                  'price' => 7.50, 'allergens' => ['Dairy', 'Eggs']],
            ['category' => 'desserts', 'name' => 'Torta Della Nonna',            'desc' => 'Traditional custard tart.',                             'price' => 6.50, 'allergens' => ['Gluten', 'Dairy', 'Eggs']],
            ['category' => 'desserts', 'name' => 'Strawberry Cheesecake',        'desc' => 'Gluten free cheesecake.',                               'price' => 6.50, 'allergens' => ['Dairy', 'Eggs']],
            ['category' => 'desserts', 'name' => 'Salted Caramel Cheesecake',    'desc' => 'Gluten free cheesecake.',                               'price' => 6.50, 'allergens' => ['Dairy', 'Eggs']],
            ['category' => 'desserts', 'name' => 'Ricotta and Pistachio Cake',   'desc' => 'Italian ricotta cake.',                                 'price' => 7.00, 'allergens' => ['Dairy', 'Eggs', 'Nuts']],
            ['category' => 'desserts', 'name' => 'Apple and Frangipane Cake',    'desc' => 'Apple almond cake.',                                    'price' => 6.50, 'allergens' => ['Gluten', 'Dairy', 'Eggs', 'Nuts']],
            ['category' => 'desserts', 'name' => 'Sicilian Pistachio Mousse',    'desc' => 'Rich pistachio mousse.',                                'price' => 6.50, 'allergens' => ['Dairy', 'Nuts']],
            ['category' => 'desserts', 'name' => 'Delizia Limone',               'desc' => 'Italian lemon dessert.',                                'price' => 6.50, 'allergens' => ['Gluten', 'Dairy', 'Eggs']],
            ['category' => 'desserts', 'name' => 'Lemon Tart',                   'desc' => 'Gluten free lemon tart.',                               'price' => 6.50, 'allergens' => ['Dairy', 'Eggs']],
            ['category' => 'desserts', 'name' => 'Milk Chocolate Profiteroles',  'desc' => 'Chocolate profiteroles.',                               'price' => 7.00, 'allergens' => ['Gluten', 'Dairy', 'Eggs']],
            // DRINKS
            ['category' => 'drinks', 'name' => 'Coca-Cola',                       'desc' => 'Classic sparkling drink.',                 'price' => 3.00, 'allergens' => []],
            ['category' => 'drinks', 'name' => 'Diet Coca-Cola',                  'desc' => 'Sugar-free cola.',                         'price' => 3.00, 'allergens' => []],
            ['category' => 'drinks', 'name' => 'Coca-Cola Zero',                  'desc' => 'Zero sugar cola.',                         'price' => 3.00, 'allergens' => []],
            ['category' => 'drinks', 'name' => 'San Pellegrino Orange',           'desc' => 'Italian sparkling orange drink.',          'price' => 3.50, 'allergens' => []],
            ['category' => 'drinks', 'name' => 'San Pellegrino Lemon',            'desc' => 'Italian sparkling lemon drink.',           'price' => 3.50, 'allergens' => []],
            ['category' => 'drinks', 'name' => 'Tomarchio Peach Melon Tea',       'desc' => 'Italian iced tea.',                        'price' => 3.50, 'allergens' => []],
            ['category' => 'drinks', 'name' => 'Tomarchio Lemon Tangerine Tea',   'desc' => 'Italian iced tea.',                        'price' => 3.50, 'allergens' => []],
            ['category' => 'drinks', 'name' => 'Zuegg Juice',                     'desc' => 'Orange, peach, pear, apricot, apple or blood orange.', 'price' => 3.50, 'allergens' => []],
        ];

        $sortByCategory = [];
        foreach ($items as $data) {
            $catSlug = $data['category'];
            if (!isset($sortByCategory[$catSlug])) $sortByCategory[$catSlug] = 1;

            $item = MenuItem::updateOrCreate(
                ['slug' => Str::slug($data['name'])],
                [
                    'category_id'  => $cats[$catSlug],
                    'name'         => $data['name'],
                    'slug'         => Str::slug($data['name']),
                    'description'  => $data['desc'],
                    'base_price'   => $data['price'],
                    'image_path'   => 'menu-items/' . Str::slug($data['name']) . '.jpg',
                    'is_available' => true,
                    'sort_order'   => $sortByCategory[$catSlug]++,
                ]
            );

            if (!empty($data['allergens'])) {
                $ids = collect($data['allergens'])
                    ->map(fn ($n) => $allergenIds[$n] ?? null)
                    ->filter()->values()->all();
                $item->allergens()->sync($ids);
            }

            if (!empty($data['ingredients'])) {
                $item->baseIngredients()->delete();
                foreach ($data['ingredients'] as $i => $ingName) {
                    BaseIngredient::create([
                        'menu_item_id' => $item->id,
                        'name'         => $ingName,
                        'sort_order'   => $i + 1,
                    ]);
                }
            }
        }
    }
}
