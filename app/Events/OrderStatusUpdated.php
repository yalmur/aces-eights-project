<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public readonly Order $order) {}

    public function broadcastOn(): array
    {
        $channels = [new PrivateChannel('admin.orders')];

        if ($this->order->user_id) {
            $channels[] = new PrivateChannel('order.' . $this->order->id);
        }

        return $channels;
    }

    public function broadcastWith(): array
    {
        return [
            'order_id'     => $this->order->id,
            'status'       => $this->order->status,
            'status_label' => $this->order->status_label,
            'status_color' => $this->order->status_color,
            'type'         => $this->order->type,
        ];
    }

    public function broadcastAs(): string
    {
        return 'OrderStatusUpdated';
    }
}
