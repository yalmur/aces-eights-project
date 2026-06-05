<?php

namespace Tests\Feature\Account;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReorderTest extends TestCase
{
    use RefreshDatabase;

    public function test_account_page_shows_reorder_button_for_orders(): void
    {
        $user  = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id, 'status' => 'delivered']);
        $order->items()->create([
            'name'                => 'Classic Margherita',
            'qty'                 => 1,
            'unit_price'          => 9.99,
            'line_total'          => 9.99,
            'size'                => '12" Standard',
            'size_extra'          => 0,
            'crust'               => '48hr Sourdough',
            'crust_extra'         => 0,
            'added_toppings'      => [],
            'removed_ingredients' => [],
        ]);

        $response = $this->actingAs($user)->get(route('account'));

        $response->assertStatus(200);
        $response->assertSee('Reorder');
        $response->assertSee('Classic Margherita');
    }

    public function test_no_reorder_button_when_no_orders(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('account'));

        $response->assertStatus(200);
        $response->assertDontSee('Reorder');
    }
}
