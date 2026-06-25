<?php

namespace Tests\Feature\Admin;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KitchenIndexTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    public function test_kitchen_index_returns_200(): void
    {
        $this->actingAs($this->admin)
            ->get('/admin/kitchen')
            ->assertOk();
    }

    public function test_accepted_orders_appear_in_preparing_column(): void
    {
        $order = Order::factory()->create(['status' => 'accepted']);

        $this->actingAs($this->admin)
            ->get('/admin/kitchen')
            ->assertViewHas('preparing', fn ($p) => $p->contains('id', $order->id));
    }

    public function test_cooking_orders_appear_in_preparing_column(): void
    {
        $order = Order::factory()->create(['status' => 'cooking']);

        $this->actingAs($this->admin)
            ->get('/admin/kitchen')
            ->assertViewHas('preparing', fn ($p) => $p->contains('id', $order->id));
    }

    public function test_ready_orders_appear_in_ready_column(): void
    {
        $order = Order::factory()->create(['status' => 'ready']);

        $this->actingAs($this->admin)
            ->get('/admin/kitchen')
            ->assertViewHas('ready', fn ($r) => $r->contains('id', $order->id));
    }

    public function test_out_for_delivery_orders_appear_in_dispatched_column(): void
    {
        $order = Order::factory()->create(['status' => 'out_for_delivery']);

        $this->actingAs($this->admin)
            ->get('/admin/kitchen')
            ->assertViewHas('dispatched', fn ($d) => $d->contains('id', $order->id));
    }

    public function test_delivered_and_cancelled_orders_excluded_from_kitchen(): void
    {
        Order::factory()->create(['status' => 'delivered']);
        Order::factory()->create(['status' => 'cancelled']);

        $this->actingAs($this->admin)
            ->get('/admin/kitchen')
            ->assertViewHas('preparing', fn ($p) => $p->isEmpty())
            ->assertViewHas('ready', fn ($r) => $r->isEmpty())
            ->assertViewHas('dispatched', fn ($d) => $d->isEmpty());
    }

    public function test_guest_cannot_access_kitchen_index(): void
    {
        $this->get('/admin/kitchen')
            ->assertRedirect('/login');
    }

    public function test_customer_cannot_access_kitchen_index(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $this->actingAs($customer)
            ->get('/admin/kitchen')
            ->assertForbidden();
    }
}
