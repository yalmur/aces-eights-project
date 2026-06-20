<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryZone extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'min_km', 'max_km', 'postcodes', 'fee', 'is_active', 'sort_order'];

    protected $casts = [
        'min_km'    => 'decimal:2',
        'max_km'    => 'decimal:2',
        'fee'       => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    /**
     * Extract the postcode district from a full UK postcode.
     * "NW5 2HP" → "NW5", "EC1A 1BB" → "EC1A", "SW1W 0NY" → "SW1W"
     */
    public static function extractDistrict(string $postcode): string
    {
        $clean = strtoupper(trim(preg_replace('/\s+/', ' ', $postcode)));
        // UK postcode format: district is everything before the last 3 chars (digit+2 letters)
        // e.g. "NW5 2HP" → "NW5", "EC1A 1BB" → "EC1A"
        if (preg_match('/^([A-Z]{1,2}[0-9][0-9A-Z]?)\s*[0-9][A-Z]{2}$/i', $clean, $m)) {
            return strtoupper($m[1]);
        }
        // Fallback: return first "word"
        return explode(' ', $clean)[0];
    }

    /**
     * Find the active zone covering a given postcode, or null if not covered.
     */
    public static function findByPostcode(string $postcode): ?self
    {
        $district = self::extractDistrict($postcode);
        $zones    = \Illuminate\Support\Facades\Cache::remember('delivery_zones_active', 300, fn () => self::active()->get());
        return $zones->first(function ($zone) use ($district) {
            if (!$zone->postcodes) return false;
            $covered = array_map('trim', explode(',', strtoupper($zone->postcodes)));
            return in_array($district, $covered, true);
        });
    }

    /** Postcode districts as a cleaned array. */
    public function getPostcodeListAttribute(): array
    {
        if (!$this->postcodes) return [];
        return array_map('trim', array_filter(explode(',', strtoupper($this->postcodes))));
    }
}
