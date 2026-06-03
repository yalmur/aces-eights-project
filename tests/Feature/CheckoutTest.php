<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\MenuItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    private User $customer;
    private MenuItem $pizza;

    protected function setUp(): void
    {
        parent::setUp();
        $this->customer = User::factory()->create(['role' => 'customer']);
        $category = Category::factory()->create(['slug' => 'pizza', 'name' => 'Pizza']);
        $this->pizza = MenuItem::factory()->create([
            'category_id' => $category->id,
            'name'        => 'Margherita',
            'slug'        => 'margherita',
            'base_price'  => 12.00,
        ]);
    }

    public function test_checkout_page_requires_auth(): void
    {
        $response = $this->get('/checkout');
        $response->assertRedirect('/login');
    }

    public function test_checkout_page_accessible_when_logged_in(): void
    {
        $response = $this->actingAs($this->customer)->get('/checkout');
        $response->assertStatus(200);
    }

    public function test_post_checkout_creates_order(): void
    {
        $cartItems = [[
            'id'                 => 'margherita',
            'name'               => 'Margherita',
            'category'           => 'pizza',
            'basePrice'          => 12.00,
            'qty'                => 2,
            'size'               => '12" Standard',
            'sizeExtra'          => 0,
            'crust'              => '48hr Sourdough',
            'crustExtra'         => 0,
            'toppings'           => [],
            'removedIngredients' => [],
            'chips'              => [],
            'instructions'       => '',
            'lineTotal'          => 24.00,
        ]];

        $response = $this->actingAs($this->customer)->post('/checkout', [
            'order_type'     => 'delivery',
            'street_address' => '10 Test Street',
            'city'           => 'London',
            'postal_code'    => 'NW5 2HP',
            'cart_items'     => json_encode($cartItems),
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('orders', [
            'user_id'          => $this->customer->id,
            'type'             => 'delivery',
            'status'           => 'pending_payment',
            'customer_name'    => $this->customer->name,
            'delivery_address' => '10 Test Street',
        ]);
        $this->assertDatabaseHas('order_items', [
            'name'       => 'Margherita',
            'qty'        => 2,
            'unit_price' => 12.00,
            'line_total' => 24.00,
        ]);
    }

    public function test_checkout_requires_cart_items(): void
    {
        $response = $this->actingAs($this->customer)->post('/checkout', [
            'order_type'     => 'delivery',
            'street_address' => '10 Test St',
            'city'           => 'London',
            'postal_code'    => 'NW5 2HP',
            'cart_items'     => '[]',
        ]);
        $response->assertSessionHasErrors(['cart_items']);
    }

    public function test_collection_order_has_no_delivery_fee(): void
    {
        $cartItems = [[
            'id' => 'margherita', 'name' => 'Margherita', 'category' => 'pizza',
            'basePrice' => 12.00, 'qty' => 1, 'size' => '12" Standard', 'sizeExtra' => 0,
            'crust' => '48hr Sourdough', 'crustExtra' => 0, 'toppings' => [],
            'removedIngredients' => [], 'chips' => [], 'instructions' => '',
            'lineTotal' => 12.00,
        ]];

        $this->actingAs($this->customer)->post('/checkout', [
            'order_type'  => 'collection',
            'cart_items'  => json_encode($cartItems),
        ]);

        $this->assertDatabaseHas('orders', [
            'type'         => 'collection',
            'delivery_fee' => 0,
        ]);
    }
}
