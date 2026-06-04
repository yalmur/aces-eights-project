<?php

namespace Tests\Feature;

use App\Models\DeliveryZone;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeliveryZoneTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    public function test_admin_delivery_page_returns_200(): void
    {
        $this->actingAs($this->admin)->get('/admin/delivery')->assertStatus(200);
    }

    public function test_admin_can_create_delivery_zone(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/delivery', [
            'name' => 'Zone 1', 'min_km' => '0', 'max_km' => '2', 'fee' => '2.50', 'is_active' => '1',
        ]);
        $response->assertRedirect('/admin/delivery');
        $this->assertDatabaseHas('delivery_zones', ['name' => 'Zone 1', 'fee' => 2.50]);
    }

    public function test_admin_can_update_delivery_zone(): void
    {
        $zone = DeliveryZone::factory()->create(['fee' => 3.00]);
        $this->actingAs($this->admin)->put("/admin/delivery/{$zone->id}", [
            'name' => $zone->name, 'min_km' => $zone->min_km, 'max_km' => $zone->max_km, 'fee' => '4.50', 'is_active' => '1',
        ]);
        $this->assertDatabaseHas('delivery_zones', ['id' => $zone->id, 'fee' => 4.50]);
    }

    public function test_admin_can_delete_delivery_zone(): void
    {
        $zone = DeliveryZone::factory()->create();
        $this->actingAs($this->admin)->delete("/admin/delivery/{$zone->id}");
        $this->assertDatabaseMissing('delivery_zones', ['id' => $zone->id]);
    }
}
