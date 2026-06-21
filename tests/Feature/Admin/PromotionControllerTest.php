<?php

namespace Tests\Feature\Admin;

use App\Models\Promotion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PromotionControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    public function test_guest_cannot_access_promotions_index(): void
    {
        $this->get('/admin/promotions')->assertRedirect('/login');
    }

    public function test_non_admin_cannot_access_promotions_index(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/admin/promotions')->assertStatus(403);
    }

    public function test_admin_can_view_promotions_index(): void
    {
        $promo = Promotion::factory()->create(['code' => 'WELCOME20']);

        $this->actingAs($this->admin)
            ->get('/admin/promotions')
            ->assertStatus(200)
            ->assertSee('WELCOME20');
    }

    public function test_admin_can_view_create_form(): void
    {
        $this->actingAs($this->admin)
            ->get('/admin/promotions/create')
            ->assertStatus(200);
    }

    public function test_admin_can_create_promotion(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/promotions', [
            'code'             => 'save10',
            'name'             => 'Save 10%',
            'type'             => 'percentage',
            'value'            => 10,
            'min_order_amount' => 15,
            'max_uses'         => null,
            'is_active'        => 1,
            'expires_at'       => null,
        ]);

        $response->assertRedirect(route('admin.promotions.index'));
        $this->assertDatabaseHas('promotions', ['code' => 'SAVE10', 'name' => 'Save 10%']);
    }

    public function test_store_rejects_duplicate_promotion_code(): void
    {
        Promotion::factory()->create(['code' => 'SAVE10']);

        $this->actingAs($this->admin)
            ->post('/admin/promotions', [
                'code'  => 'SAVE10',
                'name'  => 'Duplicate',
                'type'  => 'percentage',
                'value' => 10,
            ])
            ->assertSessionHasErrors('code');
    }

    public function test_admin_can_view_edit_form(): void
    {
        $promo = Promotion::factory()->create();

        $this->actingAs($this->admin)
            ->get("/admin/promotions/{$promo->id}/edit")
            ->assertStatus(200);
    }

    public function test_admin_can_update_promotion(): void
    {
        $promo = Promotion::factory()->create(['code' => 'OLD10', 'name' => 'Old Name']);

        $response = $this->actingAs($this->admin)->put("/admin/promotions/{$promo->id}", [
            'code'  => 'OLD10',
            'name'  => 'New Name',
            'type'  => 'percentage',
            'value' => 10,
        ]);

        $response->assertRedirect(route('admin.promotions.index'));
        $this->assertDatabaseHas('promotions', ['id' => $promo->id, 'name' => 'New Name']);
    }

    public function test_admin_can_delete_promotion(): void
    {
        $promo = Promotion::factory()->create();

        $this->actingAs($this->admin)
            ->delete("/admin/promotions/{$promo->id}")
            ->assertRedirect(route('admin.promotions.index'));

        $this->assertDatabaseMissing('promotions', ['id' => $promo->id]);
    }
}
