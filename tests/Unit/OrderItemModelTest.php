<?php

namespace Tests\Unit;

use App\Models\OrderItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderItemModelTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------------------------
    // OrderItem::customisationSummary (getCustomisationSummaryAttribute)
    // -------------------------------------------------------------------------

    public function test_all_defaults_returns_no_extras(): void
    {
        $item = new OrderItem([
            'size'                => '12" Standard',
            'crust'               => '48hr Sourdough',
            'removed_ingredients' => [],
            'added_toppings'      => [],
            'instructions'        => null,
        ]);

        $this->assertSame('No extras', $item->customisationSummary);
    }

    public function test_non_default_size_is_included(): void
    {
        $item = new OrderItem(['size' => '9" Small']);

        $this->assertSame('9" Small', $item->customisationSummary);
    }

    public function test_default_size_is_excluded(): void
    {
        $item = new OrderItem([
            'size'  => '12" Standard',
            'crust' => null,
        ]);

        $this->assertStringNotContainsString('12" Standard', $item->customisationSummary);
    }

    public function test_non_default_crust_is_included(): void
    {
        $item = new OrderItem(['crust' => 'Thin & Crispy']);

        $this->assertStringContainsString('Thin & Crispy', $item->customisationSummary);
    }

    public function test_default_crust_is_excluded(): void
    {
        $item = new OrderItem([
            'size'  => null,
            'crust' => '48hr Sourdough',
        ]);

        $this->assertStringNotContainsString('48hr Sourdough', $item->customisationSummary);
    }

    public function test_null_size_is_skipped(): void
    {
        $item = new OrderItem(['size' => null]);

        $this->assertSame('No extras', $item->customisationSummary);
    }

    public function test_removed_ingredients_appear_prefixed_with_no(): void
    {
        $item = new OrderItem([
            'removed_ingredients' => ['Tomato', 'Cheese'],
        ]);

        $summary = $item->customisationSummary;
        $this->assertStringContainsString('no Tomato', $summary);
        $this->assertStringContainsString('no Cheese', $summary);
    }

    public function test_added_toppings_appear_prefixed_with_plus(): void
    {
        $item = new OrderItem([
            'added_toppings' => [['name' => 'Pepperoni'], ['name' => 'Mushrooms']],
        ]);

        $summary = $item->customisationSummary;
        $this->assertStringContainsString('+Pepperoni', $summary);
        $this->assertStringContainsString('+Mushrooms', $summary);
    }

    public function test_instructions_appear_at_end(): void
    {
        $item = new OrderItem(['instructions' => 'Extra crispy please']);

        $summary = $item->customisationSummary;
        $this->assertStringEndsWith('Extra crispy please', $summary);
    }

    public function test_all_customisations_combined_produce_correct_string(): void
    {
        $item = new OrderItem([
            'size'                => '9" Small',
            'crust'               => 'Thin & Crispy',
            'removed_ingredients' => ['Tomato', 'Cheese'],
            'added_toppings'      => [['name' => 'Pepperoni'], ['name' => 'Mushrooms']],
            'instructions'        => 'Extra crispy please',
        ]);

        $expected = '9" Small · Thin & Crispy · no Tomato · no Cheese · +Pepperoni · +Mushrooms · Extra crispy please';
        $this->assertSame($expected, $item->customisationSummary);
    }

    public function test_empty_removed_ingredients_array_is_skipped(): void
    {
        $item = new OrderItem([
            'removed_ingredients' => [],
        ]);

        $this->assertSame('No extras', $item->customisationSummary);
    }

    public function test_empty_added_toppings_array_is_skipped(): void
    {
        $item = new OrderItem([
            'added_toppings' => [],
        ]);

        $this->assertSame('No extras', $item->customisationSummary);
    }

    public function test_unit_price_cast_to_decimal(): void
    {
        $item = OrderItem::factory()->create(['unit_price' => 9.5]);

        $this->assertSame('9.50', (string) $item->fresh()->unit_price);
    }

    public function test_line_total_cast_to_decimal(): void
    {
        $item = OrderItem::factory()->create(['line_total' => 19.1]);

        $this->assertSame('19.10', (string) $item->fresh()->line_total);
    }

    public function test_added_toppings_cast_to_array(): void
    {
        $toppings = [['name' => 'Jalapeños', 'price' => 1.5]];
        $item     = OrderItem::factory()->create(['added_toppings' => $toppings]);

        $this->assertIsArray($item->fresh()->added_toppings);
        $this->assertSame('Jalapeños', $item->fresh()->added_toppings[0]['name']);
    }

    public function test_removed_ingredients_cast_to_array(): void
    {
        $item = OrderItem::factory()->create(['removed_ingredients' => ['Olives']]);

        $this->assertIsArray($item->fresh()->removed_ingredients);
        $this->assertContains('Olives', $item->fresh()->removed_ingredients);
    }

    public function test_order_relationship_returns_parent_order(): void
    {
        $order = \App\Models\Order::factory()->create();
        $item  = OrderItem::factory()->create(['order_id' => $order->id]);

        $this->assertSame($order->id, $item->order->id);
    }

    public function test_menu_item_relationship_returns_linked_menu_item(): void
    {
        $menuItem = \App\Models\MenuItem::factory()->create();
        $item     = OrderItem::factory()->create(['menu_item_id' => $menuItem->id]);

        $this->assertSame($menuItem->id, $item->menuItem->id);
    }
}
