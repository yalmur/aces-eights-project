<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RouteSmokeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('publicRouteProvider')]
    public function test_public_routes_return_200(string $uri): void
    {
        $response = $this->get($uri);

        $response->assertStatus(200);
    }

    public static function publicRouteProvider(): array
    {
        return [
            'home'        => ['/'],
            'menu'        => ['/menu'],
            'cart'        => ['/cart'],
            'checkout'    => ['/checkout'],
            'booking'     => ['/booking'],
            'about'       => ['/about'],
            'contact'     => ['/contact'],
            'login'       => ['/login'],
            'register'    => ['/register'],
            'menu.show'   => ['/menu/margherita'],
        ];
    }

    public function test_order_confirmation_route_returns_200(): void
    {
        $response = $this->get('/orders/TEST123/confirmation');

        $response->assertStatus(200);
    }

    public function test_order_tracking_route_returns_200(): void
    {
        $response = $this->get('/orders/TEST123/tracking');

        $response->assertStatus(200);
    }

    public function test_admin_dashboard_redirects_unauthenticated(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect('/login');
    }

    public function test_account_redirects_unauthenticated(): void
    {
        $response = $this->get('/account');

        $response->assertRedirect('/login');
    }

    public function test_home_page_contains_brand_name(): void
    {
        $response = $this->get('/');

        $response->assertSee('Aces');
    }

    public function test_header_renders_nav_links(): void
    {
        $response = $this->get('/');

        $response->assertSee('Menu');
        $response->assertSee('Book a Table');
    }

    public function test_footer_renders_opening_hours(): void
    {
        $response = $this->get('/');

        $response->assertSee('16:00');
        $response->assertSee('Fortess Road');
    }

    /**
     * Admin routes return 200 for authenticated admin user.
     *
     * @dataProvider adminRouteProvider
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('adminRouteProvider')]
    public function test_admin_routes_return_200_for_admin(string $uri): void
    {
        $admin = \App\Models\User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get($uri);

        $response->assertStatus(200);
    }

    public static function adminRouteProvider(): array
    {
        return [
            'admin dashboard'    => ['/admin'],
            'admin orders'       => ['/admin/orders'],
            'admin in-store'     => ['/admin/orders/in-store'],
            'admin order detail' => ['/admin/orders/preview'],
            'admin kitchen'      => ['/admin/kitchen'],
            'admin menu'         => ['/admin/menu'],
            'admin menu create'  => ['/admin/menu/create'],
            'admin allergy'      => ['/admin/allergy'],
            'admin delivery'     => ['/admin/delivery'],
            'admin promotions'   => ['/admin/promotions'],
            'admin settings'     => ['/admin/settings'],
        ];
    }

    public function test_admin_routes_return_403_for_customer(): void
    {
        $customer = \App\Models\User::factory()->create(['role' => 'customer']);

        $response = $this->actingAs($customer)->get('/admin');

        $response->assertStatus(403);
    }
}
