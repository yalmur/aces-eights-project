<?php

namespace Tests\Feature\Admin;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    // --- Auth and access ---

    public function test_admin_can_access_dashboard(): void
    {
        $this->actingAs($this->admin)
            ->get('/admin')
            ->assertStatus(200);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/admin')
            ->assertRedirect('/login');
    }

    public function test_customer_cannot_access_dashboard(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $this->actingAs($customer)
            ->get('/admin')
            ->assertStatus(403);
    }

    // --- todayOrders stat ---

    public function test_today_orders_counts_non_excluded_statuses(): void
    {
        Order::factory()->create(['status' => 'accepted', 'created_at' => today()]);
        Order::factory()->create(['status' => 'cooking', 'created_at' => today()]);
        Order::factory()->create(['status' => 'ready', 'created_at' => today()]);

        $this->actingAs($this->admin)
            ->get('/admin')
            ->assertViewHas('todayOrders', 3);
    }

    public function test_today_orders_excludes_pending_payment(): void
    {
        Order::factory()->create(['status' => 'accepted', 'created_at' => today()]);
        Order::factory()->create(['status' => 'pending_payment', 'created_at' => today()]);

        $this->actingAs($this->admin)
            ->get('/admin')
            ->assertViewHas('todayOrders', 1);
    }

    public function test_today_orders_excludes_cancelled(): void
    {
        Order::factory()->create(['status' => 'accepted', 'created_at' => today()]);
        Order::factory()->create(['status' => 'cancelled', 'created_at' => today()]);

        $this->actingAs($this->admin)
            ->get('/admin')
            ->assertViewHas('todayOrders', 1);
    }

    // --- kitchenLoad stat ---

    public function test_kitchen_load_is_low_with_zero_queue(): void
    {
        $this->actingAs($this->admin)
            ->get('/admin')
            ->assertViewHas('kitchenLoad', 'LOW');
    }

    public function test_kitchen_load_is_moderate_with_four_orders(): void
    {
        Order::factory()->count(4)->create(['status' => 'accepted', 'created_at' => today()]);

        $this->actingAs($this->admin)
            ->get('/admin')
            ->assertViewHas('kitchenLoad', 'MODERATE');
    }

    public function test_kitchen_load_is_high_with_eight_orders(): void
    {
        Order::factory()->count(8)->create(['status' => 'accepted', 'created_at' => today()]);

        $this->actingAs($this->admin)
            ->get('/admin')
            ->assertViewHas('kitchenLoad', 'HIGH');
    }

    // --- orderGrowth stat ---

    public function test_order_growth_is_null_when_no_yesterday_orders(): void
    {
        Order::factory()->count(5)->create(['status' => 'accepted', 'created_at' => today()]);

        $this->actingAs($this->admin)
            ->get('/admin')
            ->assertViewHas('orderGrowth', null);
    }

    public function test_order_growth_calculates_positive_growth(): void
    {
        Order::factory()->count(10)->create(['status' => 'accepted', 'created_at' => today()->subDay()]);
        Order::factory()->count(12)->create(['status' => 'accepted', 'created_at' => today()]);

        $this->actingAs($this->admin)
            ->get('/admin')
            ->assertViewHas('orderGrowth', 20);
    }

    public function test_order_growth_calculates_negative_growth(): void
    {
        Order::factory()->count(10)->create(['status' => 'accepted', 'created_at' => today()->subDay()]);
        Order::factory()->count(5)->create(['status' => 'accepted', 'created_at' => today()]);

        $this->actingAs($this->admin)
            ->get('/admin')
            ->assertViewHas('orderGrowth', -50);
    }
}
