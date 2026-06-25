<?php

namespace Tests\Feature\Admin;

use App\Events\OrderStatusUpdated;
use App\Mail\OrderStatusUpdate;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class OrderStatusTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    public function test_admin_can_update_order_status(): void
    {
        Event::fake();
        Mail::fake();

        $order = Order::factory()->create(['status' => 'accepted']);

        $this->actingAs($this->admin)
            ->patch("/admin/orders/{$order->id}/status", ['status' => 'cooking'])
            ->assertRedirect();

        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'cooking']);
    }

    public function test_order_status_update_dispatches_event(): void
    {
        Event::fake();
        Mail::fake();

        $order = Order::factory()->create(['status' => 'accepted']);

        $this->actingAs($this->admin)
            ->patch("/admin/orders/{$order->id}/status", ['status' => 'cooking']);

        Event::assertDispatched(OrderStatusUpdated::class, fn ($e) => $e->order->id === $order->id);
    }

    public function test_order_status_update_queues_email_when_customer_email_present(): void
    {
        Event::fake();
        Mail::fake();

        $order = Order::factory()->create([
            'status'         => 'accepted',
            'customer_email' => 'customer@example.com',
        ]);

        $this->actingAs($this->admin)
            ->patch("/admin/orders/{$order->id}/status", ['status' => 'cooking']);

        Mail::assertQueued(OrderStatusUpdate::class);
    }

    public function test_order_status_update_skips_email_without_customer_email(): void
    {
        Event::fake();
        Mail::fake();

        $order = Order::factory()->create([
            'status'         => 'accepted',
            'customer_email' => null,
        ]);

        $this->actingAs($this->admin)
            ->patch("/admin/orders/{$order->id}/status", ['status' => 'cooking']);

        Mail::assertNothingQueued();
    }

    public function test_invalid_status_rejected(): void
    {
        $order = Order::factory()->create(['status' => 'accepted']);

        $this->actingAs($this->admin)
            ->patch("/admin/orders/{$order->id}/status", ['status' => 'flying'])
            ->assertSessionHasErrors(['status']);
    }

    public function test_unauthenticated_cannot_update_order_status(): void
    {
        $order = Order::factory()->create();

        $this->patch("/admin/orders/{$order->id}/status", ['status' => 'cooking'])
            ->assertRedirect('/login');
    }

    public function test_non_admin_cannot_update_order_status(): void
    {
        $user  = User::factory()->create(['role' => 'customer']);
        $order = Order::factory()->create();

        $this->actingAs($user)
            ->patch("/admin/orders/{$order->id}/status", ['status' => 'cooking'])
            ->assertStatus(403);
    }

    public function test_status_update_returns_success_flash(): void
    {
        Event::fake();
        Mail::fake();

        $order = Order::factory()->create(['status' => 'accepted']);

        $this->actingAs($this->admin)
            ->patch("/admin/orders/{$order->id}/status", ['status' => 'cooking'])
            ->assertSessionHas('success');
    }

    public function test_update_status_returns_404_for_nonexistent_order(): void
    {
        $this->actingAs($this->admin)
            ->patch('/admin/orders/99999/status', ['status' => 'cooking'])
            ->assertStatus(404);
    }
}
