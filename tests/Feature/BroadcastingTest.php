<?php

namespace Tests\Feature;

use App\Events\OrderStatusUpdated;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class BroadcastingTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    public function test_status_update_dispatches_order_status_updated_event(): void
    {
        Event::fake([OrderStatusUpdated::class]);

        $order = Order::factory()->create(['status' => 'accepted']);

        $this->actingAs($this->admin)->patch("/admin/orders/{$order->id}/status", [
            'status' => 'cooking',
        ]);

        Event::assertDispatched(OrderStatusUpdated::class, function ($event) use ($order) {
            return $event->order->id === $order->id
                && $event->order->status === 'cooking';
        });
    }

    public function test_event_broadcasts_on_admin_channel(): void
    {
        $order    = Order::factory()->create();
        $event    = new OrderStatusUpdated($order);
        $channels = $event->broadcastOn();
        $names    = array_map(fn ($c) => $c->name, $channels);

        $this->assertContains('private-admin.orders', $names);
    }

    public function test_event_broadcasts_on_customer_channel_when_user_exists(): void
    {
        $customer = User::factory()->create();
        $order    = Order::factory()->create(['user_id' => $customer->id]);
        $event    = new OrderStatusUpdated($order);
        $channels = $event->broadcastOn();
        $names    = array_map(fn ($c) => $c->name, $channels);

        $this->assertContains('private-order.' . $order->id, $names);
    }

    public function test_event_does_not_broadcast_customer_channel_for_guest_order(): void
    {
        $order    = Order::factory()->create(['user_id' => null]);
        $event    = new OrderStatusUpdated($order);
        $channels = $event->broadcastOn();
        $names    = array_map(fn ($c) => $c->name, $channels);

        $this->assertNotContains('private-order.' . $order->id, $names);
        $this->assertContains('private-admin.orders', $names);
    }

    public function test_event_broadcast_payload_contains_required_fields(): void
    {
        $order   = Order::factory()->create(['status' => 'cooking']);
        $event   = new OrderStatusUpdated($order);
        $payload = $event->broadcastWith();

        $this->assertArrayHasKey('order_id', $payload);
        $this->assertArrayHasKey('status', $payload);
        $this->assertArrayHasKey('status_label', $payload);
        $this->assertArrayHasKey('status_color', $payload);
        $this->assertSame('cooking', $payload['status']);
        $this->assertSame('Cooking', $payload['status_label']);
    }
}
