<?php

namespace Tests\Feature\Admin;

use App\Models\DeliveryZone;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeliveryControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    public function test_guest_cannot_access_delivery_index(): void
    {
        $this->get('/admin/delivery')
            ->assertRedirect('/login');
    }

    public function test_non_admin_cannot_access_delivery_index(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/admin/delivery')
            ->assertStatus(403);
    }

    public function test_admin_can_view_delivery_zones(): void
    {
        $zone = DeliveryZone::factory()->create(['name' => 'Central London']);

        $this->actingAs($this->admin)
            ->get('/admin/delivery')
            ->assertStatus(200)
            ->assertSee('Central London');
    }

    public function test_admin_can_create_delivery_zone(): void
    {
        $this->actingAs($this->admin)
            ->post('/admin/delivery', $this->validPayload())
            ->assertRedirect(route('admin.delivery.index'));

        $this->assertDatabaseHas('delivery_zones', ['name' => 'Test Zone']);
    }

    public function test_store_validates_required_fields(): void
    {
        $this->actingAs($this->admin)
            ->post('/admin/delivery', [])
            ->assertSessionHasErrors(['name', 'min_km', 'max_km', 'fee']);
    }

    public function test_admin_can_update_delivery_zone(): void
    {
        $zone = DeliveryZone::factory()->create(['name' => 'Old Name']);

        $this->actingAs($this->admin)
            ->put('/admin/delivery/' . $zone->id, array_merge($this->validPayload(), ['name' => 'New Name']))
            ->assertRedirect(route('admin.delivery.index'));

        $this->assertDatabaseHas('delivery_zones', ['id' => $zone->id, 'name' => 'New Name']);
    }

    public function test_admin_can_delete_delivery_zone(): void
    {
        $zone = DeliveryZone::factory()->create();

        $this->actingAs($this->admin)
            ->delete('/admin/delivery/' . $zone->id)
            ->assertRedirect(route('admin.delivery.index'));

        $this->assertDatabaseMissing('delivery_zones', ['id' => $zone->id]);
    }

    public function test_store_normalizes_postcodes_to_uppercase(): void
    {
        $payload = array_merge($this->validPayload(), ['postcodes' => 'sw1a 1aa, ec1a 1bb']);

        $this->actingAs($this->admin)
            ->post('/admin/delivery', $payload);

        $this->assertDatabaseHas('delivery_zones', ['postcodes' => 'SW1A 1AA, EC1A 1BB']);
    }

    public function test_update_validates_required_fields(): void
    {
        $zone = DeliveryZone::factory()->create();

        $this->actingAs($this->admin)
            ->put('/admin/delivery/' . $zone->id, [])
            ->assertSessionHasErrors(['name', 'min_km', 'max_km', 'fee']);
    }

    public function test_update_normalizes_postcodes_to_uppercase(): void
    {
        $zone = DeliveryZone::factory()->create();

        $this->actingAs($this->admin)
            ->put('/admin/delivery/' . $zone->id, array_merge($this->validPayload(), [
                'postcodes' => 'sw1a 1aa, nw5 2hp',
            ]));

        $this->assertDatabaseHas('delivery_zones', [
            'id'        => $zone->id,
            'postcodes' => 'SW1A 1AA, NW5 2HP',
        ]);
    }

    public function test_update_sets_is_active_false_when_omitted(): void
    {
        $zone    = DeliveryZone::factory()->create(['is_active' => true]);
        $payload = $this->validPayload();
        unset($payload['is_active']);

        $this->actingAs($this->admin)
            ->put('/admin/delivery/' . $zone->id, $payload);

        $this->assertDatabaseHas('delivery_zones', ['id' => $zone->id, 'is_active' => false]);
    }

    public function test_update_returns_404_for_nonexistent_zone(): void
    {
        $this->actingAs($this->admin)
            ->put('/admin/delivery/99999', $this->validPayload())
            ->assertStatus(404);
    }

    public function test_destroy_returns_404_for_nonexistent_zone(): void
    {
        $this->actingAs($this->admin)
            ->delete('/admin/delivery/99999')
            ->assertStatus(404);
    }

    public function test_update_flash_contains_zone_name(): void
    {
        $zone    = DeliveryZone::factory()->create();
        $payload = array_merge($this->validPayload(), ['name' => 'North Zone']);

        $this->actingAs($this->admin)
            ->put('/admin/delivery/' . $zone->id, $payload)
            ->assertSessionHas('success', fn ($msg) => str_contains($msg, 'North Zone'));
    }

    private function validPayload(): array
    {
        return [
            'name'       => 'Test Zone',
            'min_km'     => 0,
            'max_km'     => 5,
            'postcodes'  => 'SW1A 1AA, EC1A 1BB',
            'fee'        => 2.50,
            'is_active'  => 1,
            'sort_order' => 1,
        ];
    }
}
