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
        \App\Models\DeliveryZone::factory()->create(['postcodes' => 'NW5,N7,N19', 'is_active' => true]);

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

    public function test_valid_promo_code_applies_discount(): void
    {
        \App\Models\Promotion::factory()->fixed(5)->create(['code' => 'FIVE']);
        $cartItems = [[
            'id' => 'margherita', 'name' => 'Margherita', 'category' => 'pizza',
            'basePrice' => 99.00, 'qty' => 1, 'size' => '12" Standard', 'sizeExtra' => 0,
            'crust' => '48hr Sourdough', 'crustExtra' => 0, 'toppings' => [],
            'removedIngredients' => [], 'chips' => [], 'instructions' => '',
            'lineTotal' => 99.00,
        ]];
        $this->actingAs($this->customer)->post('/checkout', [
            'order_type' => 'collection', 'cart_items' => json_encode($cartItems), 'promo_code' => 'FIVE',
        ]);
        // DB price is 12.00 regardless of client-sent basePrice=99
        $this->assertDatabaseHas('orders', ['promo_code' => 'FIVE', 'discount_amount' => 5.00, 'total' => 7.00]);
    }

    public function test_client_cannot_manipulate_item_price(): void
    {
        $cartItems = [[
            'id' => 'margherita', 'name' => 'Margherita', 'category' => 'pizza',
            'basePrice' => 0.01, 'qty' => 1, 'size' => '12" Standard', 'sizeExtra' => 0,
            'crust' => '48hr Sourdough', 'crustExtra' => 0, 'toppings' => [],
            'removedIngredients' => [], 'chips' => [], 'instructions' => '',
            'lineTotal' => 0.01,
        ]];
        $this->actingAs($this->customer)->post('/checkout', [
            'order_type' => 'collection', 'cart_items' => json_encode($cartItems),
        ]);
        $this->assertDatabaseHas('order_items', ['name' => 'Margherita', 'unit_price' => 12.00, 'line_total' => 12.00]);
        $this->assertDatabaseMissing('order_items', ['unit_price' => 0.01]);
    }

    public function test_stripe_failure_deletes_order_and_redirects_with_error(): void
    {
        $cartItems = [[
            'id' => 'margherita', 'name' => 'Margherita', 'category' => 'pizza',
            'basePrice' => 12.00, 'qty' => 1, 'size' => '12" Standard', 'sizeExtra' => 0,
            'crust' => '48hr Sourdough', 'crustExtra' => 0, 'toppings' => [],
            'removedIngredients' => [], 'chips' => [], 'instructions' => '',
            'lineTotal' => 12.00,
        ]];

        // No Stripe secret → StripeClient will throw
        config(['services.stripe.secret' => null]);

        $this->actingAs($this->customer)->post('/checkout', [
            'order_type' => 'collection', 'cart_items' => json_encode($cartItems),
        ])->assertSessionHasErrors(['cart_items']);

        $this->assertDatabaseMissing('orders', ['user_id' => $this->customer->id]);
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

    public function test_unavailable_topping_rejected_at_checkout(): void
    {
        \App\Models\Topping::factory()->create(['name' => 'Pepperoni', 'is_available' => false]);

        $cartItems = [[
            'id' => 'margherita', 'name' => 'Margherita', 'category' => 'pizza',
            'basePrice' => 12.00, 'qty' => 1, 'size' => '12" Standard', 'sizeExtra' => 0,
            'crust' => '48hr Sourdough', 'crustExtra' => 0,
            'toppings' => [['name' => 'Pepperoni']],
            'removedIngredients' => [], 'chips' => [], 'instructions' => '',
            'lineTotal' => 12.00,
        ]];

        $response = $this->actingAs($this->customer)->post('/checkout', [
            'order_type' => 'collection',
            'cart_items' => json_encode($cartItems),
        ]);

        $response->assertSessionHasErrors(['cart_items']);
        $this->assertDatabaseMissing('orders', ['user_id' => $this->customer->id]);
    }

    public function test_large_size_adds_size_extra_to_line_total(): void
    {
        $pizza = MenuItem::factory()->create([
            'category_id' => \App\Models\Category::factory()->create(['slug' => 'pizza2', 'name' => 'Pizza2'])->id,
            'name'        => 'Pepperoni Special',
            'slug'        => 'pepperoni-special',
            'base_price'  => 12.00,
        ]);

        $cartItems = [[
            'id'                 => 'pepperoni-special',
            'name'               => 'Pepperoni Special',
            'category'           => 'pizza',
            'basePrice'          => 12.00,
            'qty'                => 1,
            'size'               => '15" Large',
            'sizeExtra'          => 4.00,
            'crust'              => '48hr Sourdough',
            'crustExtra'         => 0,
            'toppings'           => [],
            'removedIngredients' => [],
            'chips'              => [],
            'instructions'       => '',
            'lineTotal'          => 16.00,
        ]];

        $this->actingAs($this->customer)->post('/checkout', [
            'order_type' => 'collection',
            'cart_items' => json_encode($cartItems),
        ]);

        $this->assertDatabaseHas('order_items', [
            'menu_item_id' => $pizza->id,
            'size'         => '15" Large',
            'size_extra'   => 4.00,
            'line_total'   => 16.00,
        ]);
    }

    public function test_gluten_free_crust_adds_crust_extra_to_line_total(): void
    {
        $pizza = MenuItem::factory()->create([
            'category_id' => \App\Models\Category::factory()->create(['slug' => 'pizza3', 'name' => 'Pizza3'])->id,
            'name'        => 'BBQ Chicken',
            'slug'        => 'bbq-chicken',
            'base_price'  => 12.00,
        ]);

        $cartItems = [[
            'id'                 => 'bbq-chicken',
            'name'               => 'BBQ Chicken',
            'category'           => 'pizza',
            'basePrice'          => 12.00,
            'qty'                => 1,
            'size'               => '12" Standard',
            'sizeExtra'          => 0,
            'crust'              => 'Gluten-Free',
            'crustExtra'         => 2.00,
            'toppings'           => [],
            'removedIngredients' => [],
            'chips'              => [],
            'instructions'       => '',
            'lineTotal'          => 14.00,
        ]];

        $this->actingAs($this->customer)->post('/checkout', [
            'order_type' => 'collection',
            'cart_items' => json_encode($cartItems),
        ]);

        $this->assertDatabaseHas('order_items', [
            'menu_item_id' => $pizza->id,
            'crust'        => 'Gluten-Free',
            'crust_extra'  => 2.00,
            'line_total'   => 14.00,
        ]);
    }

    public function test_topping_price_adds_to_line_total(): void
    {
        $pizza = MenuItem::factory()->create([
            'category_id' => \App\Models\Category::factory()->create(['slug' => 'pizza4', 'name' => 'Pizza4'])->id,
            'name'        => 'Veggie Supreme',
            'slug'        => 'veggie-supreme',
            'base_price'  => 12.00,
        ]);
        \App\Models\Topping::factory()->create(['name' => 'Pepperoni', 'price' => 1.50, 'is_available' => true]);

        $cartItems = [[
            'id'                 => 'veggie-supreme',
            'name'               => 'Veggie Supreme',
            'category'           => 'pizza',
            'basePrice'          => 12.00,
            'qty'                => 1,
            'size'               => '12" Standard',
            'sizeExtra'          => 0,
            'crust'              => '48hr Sourdough',
            'crustExtra'         => 0,
            'toppings'           => [['name' => 'Pepperoni']],
            'removedIngredients' => [],
            'chips'              => [],
            'instructions'       => '',
            'lineTotal'          => 13.50,
        ]];

        $this->actingAs($this->customer)->post('/checkout', [
            'order_type' => 'collection',
            'cart_items' => json_encode($cartItems),
        ]);

        $this->assertDatabaseHas('order_items', [
            'menu_item_id' => $pizza->id,
            'line_total'   => 13.50,
        ]);
    }

    public function test_out_of_zone_delivery_is_rejected(): void
    {
        // No DeliveryZone created — every postcode is out of zone
        $cartItems = [[
            'id'                 => 'margherita',
            'name'               => 'Margherita',
            'category'           => 'pizza',
            'basePrice'          => 12.00,
            'qty'                => 1,
            'size'               => '12" Standard',
            'sizeExtra'          => 0,
            'crust'              => '48hr Sourdough',
            'crustExtra'         => 0,
            'toppings'           => [],
            'removedIngredients' => [],
            'chips'              => [],
            'instructions'       => '',
            'lineTotal'          => 12.00,
        ]];

        $response = $this->actingAs($this->customer)->post('/checkout', [
            'order_type'     => 'delivery',
            'street_address' => '99 Nowhere Lane',
            'city'           => 'London',
            'postal_code'    => 'ZZ99 9ZZ',
            'cart_items'     => json_encode($cartItems),
        ]);

        $response->assertSessionHasErrors('postal_code');
        $this->assertDatabaseMissing('orders', ['user_id' => $this->customer->id]);
    }

    public function test_unavailable_menu_item_rejected_at_checkout(): void
    {
        $cartItems = [[
            'id'                 => 'nonexistent-slug',
            'name'               => 'Ghost Pizza',
            'category'           => 'pizza',
            'basePrice'          => 12.00,
            'qty'                => 1,
            'size'               => '12" Standard',
            'sizeExtra'          => 0,
            'crust'              => '48hr Sourdough',
            'crustExtra'         => 0,
            'toppings'           => [],
            'removedIngredients' => [],
            'chips'              => [],
            'instructions'       => '',
            'lineTotal'          => 12.00,
        ]];

        $response = $this->actingAs($this->customer)->post('/checkout', [
            'order_type' => 'collection',
            'cart_items' => json_encode($cartItems),
        ]);

        $response->assertSessionHasErrors('cart_items');
        $this->assertDatabaseMissing('orders', ['user_id' => $this->customer->id]);
    }
}
