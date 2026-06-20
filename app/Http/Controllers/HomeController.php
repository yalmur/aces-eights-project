<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use App\Models\Setting;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $featuredItems = MenuItem::where('is_featured', true)
            ->where('is_available', true)
            ->with('category', 'allergens')
            ->limit(6)
            ->get();

        $openingSunThu = Setting::get('opening_sun_thu', '16:00 – 22:45');
        $openingFriSat = Setting::get('opening_fri_sat', '16:00 – 23:15');

        return view('home', [
            'title'         => 'Aces & Eights Pizza — London\'s Finest Italian Pizza',
            'featuredItems' => $featuredItems,
            'heroText'      => Setting::get('hero_text', ''),
            'storyText'     => Setting::get('story_text', ''),
            'openingSunThu' => $openingSunThu,
            'openingFriSat' => $openingFriSat,
            'isOpenNow'     => self::checkIsOpen($openingSunThu, $openingFriSat),
        ]);
    }

    private static function checkIsOpen(string $sunThu, string $friSat): bool
    {
        try {
            $now     = now()->setTimezone('Europe/London');
            $dow     = (int) $now->format('w'); // 0=Sun, 6=Sat
            $hours   = in_array($dow, [5, 6]) ? $friSat : $sunThu;
            $parts   = preg_split('/\s*[–—-]\s*/', $hours);
            if (count($parts) < 2) return false;
            $open    = \Carbon\Carbon::createFromTimeString(trim($parts[0]), 'Europe/London')->setDate($now->year, $now->month, $now->day);
            $close   = \Carbon\Carbon::createFromTimeString(trim($parts[1]), 'Europe/London')->setDate($now->year, $now->month, $now->day);
            if ($close->lt($open)) {
                $close->addDay();
            }
            return $now->between($open, $close);
        } catch (\Throwable) {
            return false;
        }
    }
}
