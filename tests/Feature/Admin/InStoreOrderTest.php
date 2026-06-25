<?php

namespace Tests\Feature\Admin;

use App\Models\MenuItem;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InStoreOrderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    public function test_in_store_page_shows_todays_eat_in_orders(): void
    {
        $admin = $this->admin();
        Order::factory()->create([
            'type'          => 'eat_in',
            'status'        => 'cooking',
            'customer_name' => 'Walk-in Customer',
            'customer_email' => null,
        ]);

        $r = $this->actingAs($admin)->get(route('admin.orders.in-store'));

        $r->assertStatus(200);
        $r->assertSee('Walk-in Customer');
    }

    public function test_admin_can_create_eat_in_order(): void
    {
        $admin = $this->admin();
        $item  = MenuItem::where('is_available', true)->first();

        $r = $this->actingAs($admin)->post(route('admin.orders.in-store.store'), [
            'customer_name'  => 'John Walk-in',
            'customer_phone' => '07700123456',
            'customer_email' => '',
            'order_type'     => 'eat_in',
            'notes'          => 'Table 5',
            'items'          => [
                ['menu_item_id' => $item->id, 'qty' => 2],
            ],
        ]);

        $r->assertRedirect(route('admin.orders.in-store'));
        $this->assertDatabaseHas('orders', [
            'customer_name' => 'John Walk-in',
            'type'          => 'eat_in',
            'status'        => 'accepted',
        ]);
    }

    public function test_create_order_requires_name_and_items(): void
    {
        $admin = $this->admin();

        $r = $this->actingAs($admin)->post(route('admin.orders.in-store.store'), [
            'customer_name' => '',
            'order_type'    => 'eat_in',
            'items'         => [],
        ]);

        $r->assertSessionHasErrors(['customer_name', 'items']);
    }

    public function test_in_store_page_shows_todays_collection_orders(): void
    {
        $admin = $this->admin();
        Order::factory()->create([
            'type'           => 'collection',
            'status'         => 'cooking',
            'customer_name'  => 'Collection Customer',
            'customer_email' => null,
        ]);

        $r = $this->actingAs($admin)->get(route('admin.orders.in-store'));

        $r->assertStatus(200);
        $r->assertSee('Collection Customer');
    }

    public function test_admin_can_create_collection_order(): void
    {
        $admin = $this->admin();
        $item  = MenuItem::where('is_available', true)->first();

        $r = $this->actingAs($admin)->post(route('admin.orders.in-store.store'), [
            'customer_name'  => 'Jane Collection',
            'customer_phone' => '07700999888',
            'customer_email' => '',
            'order_type'     => 'collection',
            'notes'          => '',
            'items'          => [
                ['menu_item_id' => $item->id, 'qty' => 1],
            ],
        ]);

        $r->assertRedirect(route('admin.orders.in-store'));
        $this->assertDatabaseHas('orders', [
            'customer_name' => 'Jane Collection',
            'type'          => 'collection',
            'status'        => 'accepted',
        ]);
    }

    public function test_order_total_is_computed_from_db_price_not_client(): void
    {
        $admin = $this->admin();
        $item  = MenuItem::where('is_available', true)->first();

        $this->actingAs($admin)->post(route('admin.orders.in-store.store'), [
            'customer_name' => 'Price Test',
            'order_type'    => 'eat_in',
            'items'         => [
                ['menu_item_id' => $item->id, 'qty' => 2],
            ],
        ]);

        $order = Order::where('customer_name', 'Price Test')->first();
        $this->assertEquals($item->base_price * 2, $order->total);
    }

    public function test_customer_cannot_access_in_store_page(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $r = $this->actingAs($customer)->get(route('admin.orders.in-store'));

        $r->assertStatus(403);
    }

    public function test_unauthenticated_user_redirected_from_in_store_page(): void
    {
        $r = $this->get(route('admin.orders.in-store'));

        $r->assertRedirect('/login');
    }

    public function test_in_store_page_excludes_yesterdays_orders(): void
    {
        $admin = $this->admin();
        Order::factory()->create([
            'type'           => 'eat_in',
            'status'         => 'cooking',
            'customer_name'  => 'Yesterday Customer',
            'customer_email' => null,
            'created_at'     => now()->subDay(),
        ]);

        $r = $this->actingAs($admin)->get(route('admin.orders.in-store'));

        $r->assertStatus(200);
        $r->assertDontSee('Yesterday Customer');
    }

    public function test_store_in_store_aggregates_total_for_multiple_items(): void
    {
        $admin = $this->admin();
        $items = MenuItem::where('is_available', true)->take(2)->get();
        $this->assertGreaterThanOrEqual(2, $items->count(), 'Seed needs at least 2 available items');

        $this->actingAs($admin)->post(route('admin.orders.in-store.store'), [
            'customer_name' => 'Multi Item Customer',
            'order_type'    => 'eat_in',
            'items'         => [
                ['menu_item_id' => $items[0]->id, 'qty' => 1],
                ['menu_item_id' => $items[1]->id, 'qty' => 2],
            ],
        ]);

        $expected = $items[0]->base_price * 1 + $items[1]->base_price * 2;
        $order    = Order::where('customer_name', 'Multi Item Customer')->first();
        $this->assertNotNull($order);
        $this->assertEquals($expected, $order->total);
    }
}
