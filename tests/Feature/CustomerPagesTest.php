<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_home_has_industrial_content(): void
    {
        $r = $this->get('/');
        $r->assertStatus(200);
        $r->assertSee('Industrial');
    }

    public function test_menu_has_pizza_category(): void
    {
        $r = $this->get('/menu');
        $r->assertStatus(200);
        $r->assertSee('Pizza');
    }

    public function test_menu_show_returns_200(): void
    {
        $r = $this->get('/menu/margherita');
        $r->assertStatus(200);
    }

    public function test_checkout_has_delivery_option(): void
    {
        $user = User::factory()->create();
        $r = $this->actingAs($user)->get('/checkout');
        $r->assertStatus(200);
        $r->assertSee('Delivery');
    }

    public function test_tracking_page_returns_200(): void
    {
        $order = Order::factory()->create();
        $r = $this->get('/orders/' . $order->id . '/tracking');
        $r->assertStatus(200);
    }

    public function test_confirmation_page_returns_200(): void
    {
        $order = Order::factory()->create();
        $r = $this->get('/orders/' . $order->id . '/confirmation');
        $r->assertStatus(200);
    }

    public function test_booking_has_secure_your_spot(): void
    {
        $r = $this->get('/booking');
        $r->assertStatus(200);
        $r->assertSee('Secure');
    }

    public function test_about_has_brand_content(): void
    {
        $r = $this->get('/about');
        $r->assertStatus(200);
        $r->assertSee('Aces');
    }

    public function test_contact_has_real_address(): void
    {
        $r = $this->get('/contact');
        $r->assertStatus(200);
        $r->assertSee('Fortess Road');
    }

    public function test_login_has_sign_in_heading(): void
    {
        $r = $this->get('/login');
        $r->assertStatus(200);
        $r->assertSee('Sign In');
    }

    public function test_register_has_create_account_heading(): void
    {
        $r = $this->get('/register');
        $r->assertStatus(200);
        $r->assertSee('Create Account');
    }

    public function test_account_redirects_unauthenticated(): void
    {
        $r = $this->get('/account');
        $r->assertRedirect('/login');
    }
}
