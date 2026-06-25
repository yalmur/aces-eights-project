<?php

namespace Tests\Unit;

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class SettingModelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_get_returns_default_when_key_not_in_db(): void
    {
        $this->assertSame('fallback', Setting::get('missing_key', 'fallback'));
    }

    public function test_get_returns_null_when_key_absent_and_no_default(): void
    {
        $this->assertNull(Setting::get('missing_key'));
    }

    public function test_get_returns_db_value_when_key_exists(): void
    {
        Setting::create(['key' => 'store_name', 'value' => 'Aces & Eights']);
        $this->assertSame('Aces & Eights', Setting::get('store_name', 'Default'));
    }

    public function test_get_caches_value_so_db_delete_does_not_affect_result(): void
    {
        Setting::create(['key' => 'cached_key', 'value' => 'original']);
        Setting::get('cached_key'); // warm cache
        Setting::where('key', 'cached_key')->delete(); // remove from DB

        $this->assertSame('original', Setting::get('cached_key'));
    }

    public function test_set_writes_value_to_db(): void
    {
        Setting::set('new_key', 'new_value');
        $this->assertDatabaseHas('settings', ['key' => 'new_key', 'value' => 'new_value']);
    }

    public function test_set_forgets_cache_so_next_get_reads_fresh(): void
    {
        Setting::create(['key' => 'refresh_key', 'value' => 'old']);
        Setting::get('refresh_key'); // warm cache
        Setting::set('refresh_key', 'new'); // bust cache

        $this->assertSame('new', Setting::get('refresh_key'));
    }

    public function test_set_many_persists_all_given_keys(): void
    {
        Setting::setMany(['key_a' => 'alpha', 'key_b' => 'beta']);

        $this->assertDatabaseHas('settings', ['key' => 'key_a', 'value' => 'alpha']);
        $this->assertDatabaseHas('settings', ['key' => 'key_b', 'value' => 'beta']);
    }
}
