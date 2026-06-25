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

    // --- activeDeliveries stat ---

    public function test_active_deliveries_counts_out_for_delivery_orders(): void
    {
        Order::factory()->create(['status' => 'out_for_delivery']);
        Order::factory()->create(['status' => 'out_for_delivery']);
        Order::factory()->create(['status' => 'cooking']); // should not count

        $this->actingAs($this->admin)
            ->get('/admin')
            ->assertViewHas('activeDeliveries', 2);
    }

    public function test_active_deliveries_is_zero_when_no_deliveries_out(): void
    {
        Order::factory()->create(['status' => 'accepted']);

        $this->actingAs($this->admin)
            ->get('/admin')
            ->assertViewHas('activeDeliveries', 0);
    }

    // --- todayRevenue stat ---

    public function test_today_revenue_sums_non_excluded_order_totals(): void
    {
        Order::factory()->create(['status' => 'accepted',  'total' => 25.00, 'created_at' => today()]);
        Order::factory()->create(['status' => 'delivered', 'total' => 15.00, 'created_at' => today()]);

        $this->actingAs($this->admin)
            ->get('/admin')
            ->assertViewHas('todayRevenue', 40.0);
    }

    public function test_today_revenue_excludes_pending_payment_orders(): void
    {
        Order::factory()->create(['status' => 'accepted',        'total' => 20.00, 'created_at' => today()]);
        Order::factory()->create(['status' => 'pending_payment', 'total' => 99.00, 'created_at' => today()]);

        $this->actingAs($this->admin)
            ->get('/admin')
            ->assertViewHas('todayRevenue', 20.0);
    }

    public function test_today_revenue_is_zero_when_no_orders_today(): void
    {
        $this->actingAs($this->admin)
            ->get('/admin')
            ->assertViewHas('todayRevenue', 0.0);
    }

    // --- recentOrders stat ---

    public function test_recent_orders_excludes_pending_payment_and_cancelled(): void
    {
        $visible = Order::factory()->create(['status' => 'accepted']);
        Order::factory()->create(['status' => 'pending_payment']);
        Order::factory()->create(['status' => 'cancelled']);

        $response = $this->actingAs($this->admin)->get('/admin');

        $orders = $response->viewData('recentOrders');
        $this->assertTrue($orders->contains('id', $visible->id));
        $this->assertCount(1, $orders);
    }

    public function test_recent_orders_excludes_delivered_and_collected(): void
    {
        $visible = Order::factory()->create(['status' => 'cooking']);
        Order::factory()->create(['status' => 'delivered']);
        Order::factory()->create(['status' => 'collected']);

        $response = $this->actingAs($this->admin)->get('/admin');

        $orders = $response->viewData('recentOrders');
        $this->assertTrue($orders->contains('id', $visible->id));
        $this->assertCount(1, $orders);
    }

    public function test_recent_orders_limited_to_five(): void
    {
        Order::factory()->count(8)->create(['status' => 'accepted']);

        $response = $this->actingAs($this->admin)->get('/admin');

        $this->assertCount(5, $response->viewData('recentOrders'));
    }
}
