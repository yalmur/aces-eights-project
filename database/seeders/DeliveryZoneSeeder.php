<?php

namespace Database\Seeders;

use App\Models\DeliveryZone;
use Illuminate\Database\Seeder;

class DeliveryZoneSeeder extends Seeder
{
    public function run(): void
    {
        $zones = [
            ['name' => 'Zone 1 — Local (0–1.5km)',  'min_km' => 0.00, 'max_km' => 1.50, 'fee' => 2.50, 'is_active' => true,  'sort_order' => 1],
            ['name' => 'Zone 2 — Near (1.5–3km)',   'min_km' => 1.50, 'max_km' => 3.00, 'fee' => 3.50, 'is_active' => true,  'sort_order' => 2],
            ['name' => 'Zone 3 — Extended (3–5km)', 'min_km' => 3.00, 'max_km' => 5.00, 'fee' => 4.50, 'is_active' => true,  'sort_order' => 3],
            ['name' => 'Zone 4 — Far (5–8km)',      'min_km' => 5.00, 'max_km' => 8.00, 'fee' => 5.50, 'is_active' => false, 'sort_order' => 4],
        ];
        foreach ($zones as $z) {
            DeliveryZone::updateOrCreate(['name' => $z['name']], $z);
        }
    }
}
