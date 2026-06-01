<?php

namespace Tests\Feature;

use Tests\TestCase;

class RouteSmokeTest extends TestCase
{
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
            'menu.show'   => ['/menu/the-blueprint'],
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
}
