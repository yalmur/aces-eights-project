<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'store_name'       => 'Aces & Eights Pizza',
            'store_address'    => '156 & 158 Fortess Road, Tufnell Park, London, NW5 2HP',
            'store_phone'      => '+44 020 7485 4033',
            'store_email'      => 'nw5pizza@gmail.com',
            'opening_sun_thu'  => '16:00 – 22:45',
            'opening_fri_sat'  => '16:00 – 23:15',
            'hero_text'        => 'FORGED IN THE FIRE OF TRADITION. SERVED WITH INDUSTRIAL PRECISION. ACES & EIGHTS PIZZA — ESTABLISHED 2012.',
            'story_text'       => 'Born from the hum of machinery and the heat of the forge, Aces & Eights was founded on the premise that the best food is made by hand, with tools that have stood the test of time.',
            'maintenance_mode' => '0',
        ];

        foreach ($defaults as $key => $value) {
            Setting::firstOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
