<?php

namespace Tests\Feature\Admin;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderIndexTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    // ── Basic access ────────────────────────────────────────────────────────────

    public function test_admin_can_access_orders_index(): void
    {
        $this->actingAs($this->admin)
            ->get('/admin/orders')
            ->assertOk();
    }

    public function test_non_admin_gets_403(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $this->actingAs($customer)
            ->get('/admin/orders')
            ->assertStatus(403);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/admin/orders')
            ->assertRedirect('/login');
    }

    // ── Default 'all' tab ───────────────────────────────────────────────────────

    public function test_default_tab_shows_all_active_statuses(): void
    {
        $visible = ['accepted', 'cooking', 'ready', 'out_for_delivery', 'delivered', 'collected'];

        foreach ($visible as $status) {
            Order::factory()->create(['status' => $status]);
        }

        $response = $this->actingAs($this->admin)->get('/admin/orders');

        $response->assertOk()
            ->assertViewHas('activeTab', 'all')
            ->assertViewHas('orders', fn ($orders) => $orders->total() === count($visible));
    }

    public function test_default_tab_excludes_pending_payment_orders(): void
    {
        Order::factory()->create(['status' => 'accepted']);
        Order::factory()->pendingPayment()->create();

        $response = $this->actingAs($this->admin)->get('/admin/orders');

        $response->assertViewHas('orders', fn ($orders) => $orders->total() === 1);
    }

    public function test_default_tab_excludes_cancelled_orders(): void
    {
        Order::factory()->create(['status' => 'accepted']);
        Order::factory()->create(['status' => 'cancelled']);

        $response = $this->actingAs($this->admin)->get('/admin/orders');

        $response->assertViewHas('orders', fn ($orders) => $orders->total() === 1);
    }

    // ── Tab filtering ───────────────────────────────────────────────────────────

    public function test_pending_tab_shows_only_accepted_orders(): void
    {
        Order::factory()->create(['status' => 'accepted']);
        Order::factory()->create(['status' => 'cooking']);

        $response = $this->actingAs($this->admin)->get('/admin/orders?status=pending');

        $response->assertOk()
            ->assertViewHas('activeTab', 'pending')
            ->assertViewHas('orders', fn ($orders) => $orders->total() === 1);
    }

    public function test_cooking_tab_shows_only_cooking_orders(): void
    {
        Order::factory()->create(['status' => 'cooking']);
        Order::factory()->create(['status' => 'accepted']);

        $response = $this->actingAs($this->admin)->get('/admin/orders?status=cooking');

        $response->assertOk()
            ->assertViewHas('activeTab', 'cooking')
            ->assertViewHas('orders', fn ($orders) => $orders->total() === 1);
    }

    public function test_ready_tab_shows_ready_and_out_for_delivery(): void
    {
        Order::factory()->create(['status' => 'ready']);
        Order::factory()->create(['status' => 'out_for_delivery']);
        Order::factory()->create(['status' => 'cooking']);

        $response = $this->actingAs($this->admin)->get('/admin/orders?status=ready');

        $response->assertOk()
            ->assertViewHas('activeTab', 'ready')
            ->assertViewHas('orders', fn ($orders) => $orders->total() === 2);
    }

    public function test_delivered_tab_shows_delivered_and_collected(): void
    {
        Order::factory()->create(['status' => 'delivered']);
        Order::factory()->create(['status' => 'collected']);
        Order::factory()->create(['status' => 'accepted']);

        $response = $this->actingAs($this->admin)->get('/admin/orders?status=delivered');

        $response->assertOk()
            ->assertViewHas('activeTab', 'delivered')
            ->assertViewHas('orders', fn ($orders) => $orders->total() === 2);
    }

    public function test_unknown_tab_falls_back_to_all_active_orders(): void
    {
        Order::factory()->create(['status' => 'accepted']);
        Order::factory()->create(['status' => 'cooking']);
        Order::factory()->create(['status' => 'cancelled']);

        $response = $this->actingAs($this->admin)->get('/admin/orders?status=bogus');

        $response->assertOk()
            ->assertViewHas('activeTab', 'bogus')
            ->assertViewHas('orders', fn ($orders) => $orders->total() === 2);
    }
}
