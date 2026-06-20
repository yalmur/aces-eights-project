<?php

namespace Tests\Unit;

use App\Models\Order;
use App\Models\OrderItem;
use Tests\TestCase;

class OrderModelTest extends TestCase
{
    // -------------------------------------------------------------------------
    // Order::isPaid()
    // -------------------------------------------------------------------------

    public function test_is_paid_returns_false_for_pending_payment(): void
    {
        $order = new Order(['status' => 'pending_payment']);
        $this->assertSame(false, $order->isPaid());
    }

    public function test_is_paid_returns_false_for_cancelled(): void
    {
        $order = new Order(['status' => 'cancelled']);
        $this->assertSame(false, $order->isPaid());
    }

    public function test_is_paid_returns_false_for_unknown_status(): void
    {
        $order = new Order(['status' => 'mystery']);
        $this->assertSame(false, $order->isPaid());
    }

    public function test_is_paid_returns_true_for_accepted(): void
    {
        $order = new Order(['status' => 'accepted']);
        $this->assertSame(true, $order->isPaid());
    }

    public function test_is_paid_returns_true_for_cooking(): void
    {
        $order = new Order(['status' => 'cooking']);
        $this->assertSame(true, $order->isPaid());
    }

    public function test_is_paid_returns_true_for_ready(): void
    {
        $order = new Order(['status' => 'ready']);
        $this->assertSame(true, $order->isPaid());
    }

    public function test_is_paid_returns_true_for_out_for_delivery(): void
    {
        $order = new Order(['status' => 'out_for_delivery']);
        $this->assertSame(true, $order->isPaid());
    }

    public function test_is_paid_returns_true_for_collected(): void
    {
        $order = new Order(['status' => 'collected']);
        $this->assertSame(true, $order->isPaid());
    }

    public function test_is_paid_returns_true_for_delivered(): void
    {
        $order = new Order(['status' => 'delivered']);
        $this->assertSame(true, $order->isPaid());
    }

    // -------------------------------------------------------------------------
    // Order::statusLabel (attribute)
    // -------------------------------------------------------------------------

    public function test_status_label_for_pending_payment(): void
    {
        $order = new Order(['status' => 'pending_payment']);
        $this->assertSame('Awaiting Payment', $order->status_label);
    }

    public function test_status_label_for_accepted(): void
    {
        $order = new Order(['status' => 'accepted']);
        $this->assertSame('Accepted', $order->status_label);
    }

    public function test_status_label_for_cooking(): void
    {
        $order = new Order(['status' => 'cooking']);
        $this->assertSame('Cooking', $order->status_label);
    }

    public function test_status_label_for_ready(): void
    {
        $order = new Order(['status' => 'ready']);
        $this->assertSame('Ready', $order->status_label);
    }

    public function test_status_label_for_out_for_delivery(): void
    {
        $order = new Order(['status' => 'out_for_delivery']);
        $this->assertSame('Out for Delivery', $order->status_label);
    }

    public function test_status_label_for_collected(): void
    {
        $order = new Order(['status' => 'collected']);
        $this->assertSame('Collected', $order->status_label);
    }

    public function test_status_label_for_delivered(): void
    {
        $order = new Order(['status' => 'delivered']);
        $this->assertSame('Delivered', $order->status_label);
    }

    public function test_status_label_for_cancelled(): void
    {
        $order = new Order(['status' => 'cancelled']);
        $this->assertSame('Cancelled', $order->status_label);
    }

    public function test_status_label_for_unknown_status_uses_ucfirst(): void
    {
        $order = new Order(['status' => 'mystery']);
        $this->assertSame('Mystery', $order->status_label);
    }

    // -------------------------------------------------------------------------
    // Order::isDelivery()
    // -------------------------------------------------------------------------

    public function test_is_delivery_returns_true_for_delivery_type(): void
    {
        $order = new Order(['type' => 'delivery']);
        $this->assertSame(true, $order->isDelivery());
    }

    public function test_is_delivery_returns_false_for_collection_type(): void
    {
        $order = new Order(['type' => 'collection']);
        $this->assertSame(false, $order->isDelivery());
    }

    // -------------------------------------------------------------------------
    // OrderItem::customisationSummary (attribute)
    // -------------------------------------------------------------------------

    public function test_customisation_summary_returns_no_extras_for_defaults(): void
    {
        $item = new OrderItem([
            'size'  => '12" Standard',
            'crust' => '48hr Sourdough',
        ]);
        $this->assertSame('No extras', $item->customisation_summary);
    }

    public function test_customisation_summary_returns_no_extras_when_size_and_crust_are_null(): void
    {
        $item = new OrderItem([]);
        $this->assertSame('No extras', $item->customisation_summary);
    }

    public function test_customisation_summary_includes_non_default_size(): void
    {
        $item = new OrderItem([
            'size'  => '16" Large',
            'crust' => '48hr Sourdough',
        ]);
        $this->assertSame('16" Large', $item->customisation_summary);
    }

    public function test_customisation_summary_includes_non_default_crust(): void
    {
        $item = new OrderItem([
            'size'  => '12" Standard',
            'crust' => 'Thin & Crispy',
        ]);
        $this->assertSame('Thin & Crispy', $item->customisation_summary);
    }

    public function test_customisation_summary_includes_removed_ingredients(): void
    {
        $item = new OrderItem([
            'removed_ingredients' => ['onions', 'peppers'],
        ]);
        $this->assertSame('no onions · no peppers', $item->customisation_summary);
    }

    public function test_customisation_summary_includes_added_toppings(): void
    {
        $item = new OrderItem([
            'added_toppings' => [
                ['name' => 'jalapeños'],
                ['name' => 'extra cheese'],
            ],
        ]);
        $this->assertSame('+jalapeños · +extra cheese', $item->customisation_summary);
    }

    public function test_customisation_summary_includes_instructions(): void
    {
        $item = new OrderItem([
            'instructions' => 'Please cut into squares',
        ]);
        $this->assertSame('Please cut into squares', $item->customisation_summary);
    }

    public function test_customisation_summary_combines_all_parts(): void
    {
        $item = new OrderItem([
            'size'                => '16" Large',
            'crust'               => 'Thin & Crispy',
            'removed_ingredients' => ['onions'],
            'added_toppings'      => [['name' => 'jalapeños']],
            'instructions'        => 'Well done',
        ]);
        $this->assertSame(
            '16" Large · Thin & Crispy · no onions · +jalapeños · Well done',
            $item->customisation_summary
        );
    }
}
