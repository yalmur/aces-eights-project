<?php

namespace Tests\Feature\Admin;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingsControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    // ── Test 1 ───────────────────────────────────────────────────────────────

    public function test_settings_page_requires_auth(): void
    {
        $this->get('/admin/settings')
            ->assertRedirect('/login');
    }

    // ── Test 2 ───────────────────────────────────────────────────────────────

    public function test_settings_page_returns_200_for_admin(): void
    {
        $this->actingAs($this->admin)
            ->get('/admin/settings')
            ->assertStatus(200);
    }

    // ── Test 3 ───────────────────────────────────────────────────────────────

    public function test_settings_page_blocked_for_customer(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $this->actingAs($customer)
            ->get('/admin/settings')
            ->assertStatus(403);
    }

    // ── Test 4 ───────────────────────────────────────────────────────────────

    public function test_settings_update_persists_values(): void
    {
        $this->actingAs($this->admin)
            ->post('/admin/settings', $this->validPayload())
            ->assertRedirect(route('admin.settings.index'));

        $this->assertDatabaseHas('settings', [
            'key'   => 'store_name',
            'value' => 'Test Pizza',
        ]);
    }

    // ── Test 5 ───────────────────────────────────────────────────────────────

    public function test_settings_update_flashes_success_message(): void
    {
        $this->actingAs($this->admin)
            ->post('/admin/settings', $this->validPayload())
            ->assertSessionHas('success');
    }

    // ── Test 6 ───────────────────────────────────────────────────────────────

    public function test_settings_update_requires_store_name(): void
    {
        $payload = $this->validPayload();
        unset($payload['store_name']);

        $this->actingAs($this->admin)
            ->post('/admin/settings', $payload)
            ->assertSessionHasErrors('store_name');
    }

    // ── Test 7 ───────────────────────────────────────────────────────────────

    public function test_settings_update_validates_email(): void
    {
        $payload = array_merge($this->validPayload(), ['store_email' => 'notanemail']);

        $this->actingAs($this->admin)
            ->post('/admin/settings', $payload)
            ->assertSessionHasErrors('store_email');
    }

    // ── Test 8 ───────────────────────────────────────────────────────────────

    public function test_settings_page_guest_redirected_to_login(): void
    {
        $this->get('/admin/settings')
            ->assertRedirect('/login');
    }

    // ── Test 9 ───────────────────────────────────────────────────────────────

    public function test_settings_update_guest_redirected_to_login(): void
    {
        $this->post('/admin/settings', $this->validPayload())
            ->assertRedirect('/login');
    }

    // ── Test 10 ──────────────────────────────────────────────────────────────

    public function test_settings_page_passes_settings_key_to_view(): void
    {
        $this->actingAs($this->admin)
            ->get('/admin/settings')
            ->assertStatus(200)
            ->assertViewHas('settings')
            ->assertViewHas('settings.store_name');
    }

    // ── Test 11 ──────────────────────────────────────────────────────────────

    public function test_settings_update_persists_all_submitted_fields(): void
    {
        $this->actingAs($this->admin)
            ->post('/admin/settings', $this->validPayload());

        $this->assertDatabaseHas('settings', ['key' => 'store_address', 'value' => '1 Test St, London']);
        $this->assertDatabaseHas('settings', ['key' => 'store_phone',   'value' => '01234 567890']);
        $this->assertDatabaseHas('settings', ['key' => 'store_email',   'value' => 'test@example.com']);
    }

    // ── Test 12 ──────────────────────────────────────────────────────────────

    public function test_settings_update_rejects_non_numeric_size_extra(): void
    {
        $payload = array_merge($this->validPayload(), ['size_large_extra' => 'abc']);

        $this->actingAs($this->admin)
            ->post('/admin/settings', $payload)
            ->assertSessionHasErrors('size_large_extra');
    }

    // ── Test 13 ──────────────────────────────────────────────────────────────

    public function test_settings_update_rejects_non_numeric_crust_extra(): void
    {
        $payload = array_merge($this->validPayload(), ['crust_gluten_free_extra' => 'not-a-number']);

        $this->actingAs($this->admin)
            ->post('/admin/settings', $payload)
            ->assertSessionHasErrors('crust_gluten_free_extra');
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    private function validPayload(): array
    {
        return [
            'store_name'              => 'Test Pizza',
            'store_address'           => '1 Test St, London',
            'store_phone'             => '01234 567890',
            'store_email'             => 'test@example.com',
            'opening_sun_thu'         => '16:00 – 22:45',
            'opening_fri_sat'         => '16:00 – 23:15',
            'hero_text'               => 'Great pizza',
            'story_text'              => 'Our story here',
            'size_large_extra'        => '4.00',
            'crust_gluten_free_extra' => '2.00',
            'crust_cauliflower_extra' => '2.50',
        ];
    }
}
