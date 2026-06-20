<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingsTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    public function test_admin_settings_page_returns_200(): void
    {
        $this->actingAs($this->admin)->get('/admin/settings')->assertStatus(200);
    }

    public function test_admin_can_save_settings(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/settings', [
            'store_name'      => 'Test Pizza',
            'store_address'   => '1 Test Street, London',
            'store_phone'     => '+44 123 456 7890',
            'store_email'     => 'test@test.com',
            'opening_sun_thu'          => '17:00 – 22:00',
            'opening_fri_sat'          => '17:00 – 23:00',
            'size_large_extra'         => '4.00',
            'crust_gluten_free_extra'  => '2.00',
            'crust_cauliflower_extra'  => '2.50',
        ]);
        $response->assertRedirect('/admin/settings');
        $this->assertDatabaseHas('settings', ['key' => 'store_name', 'value' => 'Test Pizza']);
        $this->assertSame('1 Test Street, London', Setting::get('store_address'));
    }

    public function test_setting_get_returns_default_when_not_set(): void
    {
        $this->assertSame('default_val', Setting::get('nonexistent_key', 'default_val'));
    }

    public function test_setting_set_persists_value(): void
    {
        Setting::set('test_key', 'test_value');
        $this->assertDatabaseHas('settings', ['key' => 'test_key', 'value' => 'test_value']);
        $this->assertSame('test_value', Setting::get('test_key'));
    }
}
