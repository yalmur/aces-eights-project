<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_cart_page_is_accessible_to_guests(): void
    {
        $response = $this->get('/cart');

        $response->assertStatus(200);
    }

    public function test_cart_page_has_correct_title(): void
    {
        $response = $this->get('/cart');

        $response->assertStatus(200);
        $response->assertSee('Your Order');
    }
}
