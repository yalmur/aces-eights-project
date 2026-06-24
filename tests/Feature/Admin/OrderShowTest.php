<?php

namespace Tests\Feature\Admin;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderShowTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    public function test_admin_can_view_order_detail(): void
    {
        $order = Order::factory()->create();

        $this->actingAs($this->admin)
            ->get("/admin/orders/{$order->id}")
            ->assertOk();
    }

    public function test_order_detail_passes_order_and_title_to_view(): void
    {
        $order = Order::factory()->create();

        $this->actingAs($this->admin)
            ->get("/admin/orders/{$order->id}")
            ->assertViewHas('order', fn ($o) => $o->id === $order->id)
            ->assertViewHas('title', "Order #{$order->id}");
    }

    public function test_order_detail_returns_404_for_nonexistent_order(): void
    {
        $this->actingAs($this->admin)
            ->get('/admin/orders/999999')
            ->assertNotFound();
    }

    public function test_guest_cannot_access_order_detail(): void
    {
        $order = Order::factory()->create();

        $this->get("/admin/orders/{$order->id}")
            ->assertRedirect('/login');
    }

    public function test_non_admin_cannot_access_order_detail(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $order = Order::factory()->create();

        $this->actingAs($customer)
            ->get("/admin/orders/{$order->id}")
            ->assertForbidden();
    }
}
