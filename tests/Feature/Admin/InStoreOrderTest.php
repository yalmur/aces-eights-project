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
}
