<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class OurMenuController extends Controller
{
    public function index(): View
    {
        // In Phase 4+, these sections are fetched from DB (menu_categories + menu_items tables).
        // Admin edits via /admin/menu.
        $sections = [
            [
                'slug'    => 'antipasti',
                'heading' => 'Antipasti',
                'italian' => 'Starters',
                'items'   => [
                    ['name' => 'Olives',            'desc' => 'Sicilian green olives.',                                                  'price' => '3.50'],
                    ['name' => 'Basket of Bread',   'desc' => 'Fresh homemade focaccia with olive oil.',                                 'price' => '4.50'],
                    ['name' => 'Garlic Bread',       'desc' => 'Tomato sauce and garlic.',                                                'price' => '4.50'],
                    ['name' => 'Antipasto Italiano', 'desc' => 'Premium Italian cheeses, cured meats and bread.',                        'price' => '11.50'],
                    ['name' => 'Caprese',            'desc' => 'Buffalo mozzarella, heritage tomato and fresh basil.',                    'price' => '9.50'],
                    ['name' => 'Bresaola',           'desc' => 'Sliced dry beef fillet, rocket, parmesan and lemon.',                    'price' => '10.50'],
                    ['name' => 'Tricolore',          'desc' => 'Buffalo mozzarella, tomato, avocado, basil and extra virgin olive oil.', 'price' => '9.50'],
                ],
            ],
            [
                'slug'    => 'insalate',
                'heading' => 'Insalate',
                'italian' => 'Salads',
                'items'   => [
                    ['name' => 'Insalata Verde',         'desc' => 'Mixed leaves, avocado, fennel, cucumber.',                                      'price' => '7.50'],
                    ['name' => 'Insalata Mista',         'desc' => 'Mixed leaves, tomato, red onion, peppers.',                                     'price' => '7.50'],
                    ['name' => 'Rucola e Parmigiana',    'desc' => 'Rocket, cherry tomatoes, parmesan.',                                            'price' => '8.50'],
                    ['name' => 'Chicken Avocado Salad',  'desc' => 'Grilled chicken, avocado, mixed leaves, croutons and parmesan.',                'price' => '12.50'],
                    ['name' => 'Insalata di Carciofi',   'desc' => 'Artichoke salad, baby spinach, parmesan, balsamic vinegar, hazelnut and pomegranate.', 'price' => '11.50'],
                    ['name' => 'Insalata Mediterranea',  'desc' => 'Avocado, cherry tomato, baby gem, radicchio lettuce, fennel shavings and lemon dressing.', 'price' => '10.50'],
                    ['name' => 'Insalata Cesare',        'desc' => 'Slow roast chicken, baby gem leaves, boiled egg, anchovies, fried capers and Caesar dressing.', 'price' => '13.50'],
                ],
            ],
            [
                'slug'    => 'pasta',
                'heading' => 'Pasta',
                'italian' => 'Pasta',
                'items'   => [
                    ['name' => 'Lasagna (Beef)',          'desc' => 'Layers of pasta with rich beef ragù, béchamel sauce and cheese.',  'price' => '13.50'],
                    ['name' => 'Cannelloni (Spinach & Ricotta)', 'desc' => 'Pasta tubes filled with spinach and ricotta.',              'price' => '12.50'],
                    ['name' => 'Melanzane Parmigiana',   'desc' => 'Layers of aubergine, tomato sauce, parmesan and mozzarella.',      'price' => '12.50'],
                ],
            ],
            [
                'slug'    => 'pizza',
                'heading' => 'Pizza',
                'italian' => '22 Varieties',
                'items'   => [
                    ['name' => 'Garlic Bread',      'desc' => 'Tomato sauce and garlic.',                                                                    'price' => '8.50'],
                    ['name' => 'Vegan Pizza',       'desc' => 'Tomato sauce, garlic, avocado, onion, rocket and lemon juice.',                               'price' => '12.00'],
                    ['name' => 'Margherita',        'desc' => 'Tomato sauce, mozzarella.',                                                                   'price' => '11.00'],
                    ['name' => 'Honey Mushroom',    'desc' => 'Tomato sauce, mozzarella, mushroom, spinach, blue cheese, honey and olive oil.',              'price' => '14.50'],
                    ['name' => 'Quattro Formaggi',  'desc' => 'Tomato sauce, mozzarella, gorgonzola piccante, dolcelatte and parmesan.',                     'price' => '15.50'],
                    ['name' => 'Veggie',            'desc' => 'Tomato sauce, mozzarella, aubergine, courgette, onion, provolone cheese and garlic.',         'price' => '13.50'],
                    ['name' => 'Caprino',           'desc' => 'Tomato sauce, mozzarella, goats cheese, asparagus and parmesan.',                             'price' => '14.50'],
                    ['name' => 'Squash',            'desc' => 'Tomato sauce, mozzarella, squash, feta cheese and garlic.',                                   'price' => '13.50'],
                    ['name' => 'Aces',              'desc' => 'Tomato sauce, mozzarella, goats cheese, peppers and rocket.',                                 'price' => '14.50'],
                    ['name' => 'Vegetariana',       'desc' => 'Tomato sauce, mozzarella, olives, mushrooms, peppers and spinach.',                           'price' => '13.50'],
                    ['name' => 'Saporita',          'desc' => 'Tomato sauce, mozzarella, buffalo mozzarella, gorgonzola, rocket, pesto and cherry tomatoes.','price' => '15.50'],
                    ['name' => 'Ferrari',           'desc' => 'Tomato sauce, mozzarella, pepperoni and mushroom.',                                           'price' => '13.50'],
                    ['name' => 'Napoli',            'desc' => 'Tomato sauce, mozzarella, anchovies, capers and olives.',                                     'price' => '13.50'],
                    ['name' => 'Burrata',           'desc' => 'Cherry tomatoes, garlic, basil, burrata and balsamic glaze.',                                 'price' => '15.50'],
                    ['name' => 'Hawaii',            'desc' => 'Tomato sauce, mozzarella, ham and pineapple.',                                                'price' => '13.50'],
                    ['name' => 'Punta Luzzi',       'desc' => 'Tomato sauce, mozzarella, pear, ham and gorgonzola.',                                        'price' => '14.50'],
                    ['name' => 'Eights',            'desc' => 'Tomato sauce, mozzarella, peppers, onions, chorizo and mushroom.',                            'price' => '14.50'],
                    ['name' => 'Quattro Stagioni',  'desc' => 'Tomato sauce, mozzarella, pepperoni, ham, olives and mushroom.',                              'price' => '14.50'],
                    ['name' => 'Piccante',          'desc' => 'Tomato sauce, mozzarella, pepperoni, jalapeño chillies and ham.',                            'price' => '14.50'],
                    ['name' => 'Pancetta',          'desc' => 'Tomato sauce, mozzarella, roasted peppers, onion and pancetta.',                              'price' => '14.50'],
                    ['name' => 'Il Bacio',          'desc' => 'Tomato sauce, mozzarella, Parma ham, asparagus, rocket and parmesan.',                        'price' => '16.50'],
                    ['name' => 'Meat Lover',        'desc' => 'Tomato sauce, mozzarella, pepperoni, ham, chorizo, nduja and pancetta.',                      'price' => '17.50'],
                ],
            ],
            [
                'slug'    => 'tuna-salads',
                'heading' => 'Insalate di Tonno',
                'italian' => 'Tuna Salads',
                'items'   => [
                    ['name' => 'Italian Tuna Salad',      'desc' => 'Tuna, mixed leaves, cherry tomatoes, red onion, olives and cucumber.',  'price' => '11.50'],
                    ['name' => 'Mediterranean Tuna Salad','desc' => 'Tuna, baby gem lettuce, avocado, cherry tomato and fennel.',             'price' => '11.50'],
                    ['name' => 'Sicilian Tuna Salad',     'desc' => 'Tuna, orange segments, fennel, rocket and olives.',                     'price' => '11.50'],
                    ['name' => 'Tuna & Mozzarella',       'desc' => 'Tuna, buffalo mozzarella, tomato and basil.',                          'price' => '12.50'],
                    ['name' => 'Tuna Caesar',             'desc' => 'Tuna, baby gem lettuce, parmesan, croutons and Caesar dressing.',       'price' => '12.50'],
                ],
            ],
            [
                'slug'    => 'feta-salads',
                'heading' => 'Insalate di Feta',
                'italian' => 'Feta Salads',
                'items'   => [
                    ['name' => 'Greek Feta Salad',          'desc' => 'Feta cheese, cucumber, cherry tomatoes, red onion and olives.',   'price' => '10.50'],
                    ['name' => 'Feta & Avocado Salad',      'desc' => 'Feta cheese, avocado, mixed leaves and cucumber.',               'price' => '10.50'],
                    ['name' => 'Mediterranean Feta Salad',  'desc' => 'Feta cheese, rocket, cherry tomatoes, fennel and olives.',       'price' => '10.50'],
                    ['name' => 'Watermelon & Feta Salad',   'desc' => 'Fresh watermelon, feta cheese, mint and rocket.',               'price' => '10.50'],
                    ['name' => 'Roasted Beetroot & Feta',   'desc' => 'Roasted beetroot, feta cheese, rocket and walnuts.',            'price' => '10.50'],
                ],
            ],
            [
                'slug'    => 'dolci',
                'heading' => 'Dolci',
                'italian' => 'Desserts',
                'items'   => [
                    ['name' => 'Tiramisù',                      'desc' => 'Coffee-soaked sponge, mascarpone and cocoa.',           'price' => '6.50'],
                    ['name' => 'Gluten Free Tiramisù',          'desc' => 'Quadrifoglio gluten free tiramisù.',                    'price' => '6.50'],
                    ['name' => 'Traditional Sicilian Cannoli',  'desc' => 'Filled pastry with ricotta cream.',                    'price' => '5.50'],
                    ['name' => 'Neapolitan Babà Rum',           'desc' => 'Traditional rum flavour sponge (no alcohol).',         'price' => '5.50'],
                    ['name' => 'Torta Della Nonna',             'desc' => 'Traditional custard tart.',                            'price' => '5.50'],
                    ['name' => 'Strawberry Cheesecake',         'desc' => 'Gluten free cheesecake.',                             'price' => '6.50'],
                    ['name' => 'Salted Caramel Cheesecake',     'desc' => 'Gluten free cheesecake.',                             'price' => '6.50'],
                    ['name' => 'Ricotta & Pistachio Cake',      'desc' => 'Italian ricotta cake.',                               'price' => '6.50'],
                    ['name' => 'Apple & Frangipane Cake',       'desc' => 'Apple almond cake.',                                  'price' => '5.50'],
                    ['name' => 'Sicilian Pistachio Mousse',     'desc' => 'Rich pistachio mousse.',                              'price' => '6.50'],
                    ['name' => 'Delizia Limone',                'desc' => 'Classic Italian lemon dessert.',                      'price' => '5.50'],
                    ['name' => 'Lemon Tart Dessert',            'desc' => 'Gluten free lemon tart.',                            'price' => '5.50'],
                    ['name' => 'Milk Chocolate Profiteroles',   'desc' => 'Chocolate profiteroles.',                             'price' => '5.50'],
                    ['name' => 'Ice Cream',                     'desc' => 'Sammontana Barattolino premium Italian (500g).',      'price' => '7.50'],
                ],
            ],
            [
                'slug'    => 'bevande',
                'heading' => 'Bevande',
                'italian' => 'Drinks',
                'items'   => [
                    ['name' => 'Coca-Cola',                     'desc' => 'Classic sparkling drink.',                                     'price' => '2.50'],
                    ['name' => 'Diet Coca-Cola',                'desc' => 'Sugar-free cola.',                                             'price' => '2.50'],
                    ['name' => 'Coca-Cola Zero',                'desc' => 'Zero sugar cola.',                                            'price' => '2.50'],
                    ['name' => 'San Pellegrino Orange',         'desc' => 'Italian sparkling orange drink.',                              'price' => '3.00'],
                    ['name' => 'San Pellegrino Lemon',          'desc' => 'Italian sparkling lemon drink.',                              'price' => '3.00'],
                    ['name' => 'Tomarchio Peach & Melon Tea',  'desc' => 'Italian iced tea.',                                           'price' => '3.00'],
                    ['name' => 'Tomarchio Lemon & Tangerine',  'desc' => 'Italian iced tea.',                                           'price' => '3.00'],
                    ['name' => 'Zuegg Juices',                  'desc' => 'Orange, peach, pear, apricot, apple and blood orange.',       'price' => '2.50'],
                    ['name' => 'Still Mineral Water',           'desc' => 'Ferrarelle Italian still mineral water (glass bottle).',     'price' => '3.50'],
                    ['name' => 'Sparkling Mineral Water',       'desc' => 'Ferrarelle Italian sparkling mineral water (glass bottle).', 'price' => '3.50'],
                ],
            ],
        ];

        return view('our-menu', ['title' => 'Menu', 'sections' => $sections]);
    }
}
