<?php

namespace Tests\Feature\Admin;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KitchenPollTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    public function test_kitchen_poll_returns_json_with_correct_structure(): void
    {
        $this->actingAs($this->admin)
            ->getJson('/admin/kitchen/poll')
            ->assertOk()
            ->assertJsonStructure(['latest_id', 'has_new']);
    }

    public function test_kitchen_poll_has_new_false_when_no_active_orders(): void
    {
        $this->actingAs($this->admin)
            ->getJson('/admin/kitchen/poll?since=999')
            ->assertOk()
            ->assertJson(['has_new' => false]);
    }

    public function test_kitchen_poll_has_new_true_when_active_order_exists(): void
    {
        $order = Order::factory()->create(['status' => 'accepted']);

        $this->actingAs($this->admin)
            ->getJson('/admin/kitchen/poll?since=0')
            ->assertOk()
            ->assertJson(['has_new' => true, 'latest_id' => $order->id]);
    }

    public function test_kitchen_poll_ignores_delivered_and_cancelled_orders(): void
    {
        Order::factory()->create(['status' => 'delivered']);
        Order::factory()->create(['status' => 'cancelled']);

        $this->actingAs($this->admin)
            ->getJson('/admin/kitchen/poll?since=0')
            ->assertOk()
            ->assertJson(['has_new' => false]);
    }

    public function test_kitchen_poll_has_new_false_when_since_matches_latest_id(): void
    {
        $order = Order::factory()->create(['status' => 'cooking']);

        $this->actingAs($this->admin)
            ->getJson("/admin/kitchen/poll?since={$order->id}")
            ->assertOk()
            ->assertJson(['has_new' => false]);
    }

    public function test_unauthenticated_cannot_access_kitchen_poll(): void
    {
        $this->getJson('/admin/kitchen/poll')->assertUnauthorized();
    }

    public function test_non_admin_cannot_access_kitchen_poll(): void
    {
        $user = User::factory()->create(['role' => 'customer']);

        $this->actingAs($user)
            ->getJson('/admin/kitchen/poll')
            ->assertStatus(403);
    }
}
