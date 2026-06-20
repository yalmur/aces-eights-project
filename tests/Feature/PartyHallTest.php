<?php

namespace Tests\Feature;

use App\Models\PartyHallInquiry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PartyHallTest extends TestCase
{
    use RefreshDatabase;

    private array $validData = [
        'name'       => 'John Smith',
        'email'      => 'john@example.com',
        'phone'      => '07700900000',
        'event_date' => '2027-01-01',
        'guests'     => 100,
        'event_type' => 'Birthday Party',
        'message'    => 'Looking forward to it.',
    ];

    public function test_party_hall_redirects_to_external_hire_page(): void
    {
        $this->get('/party-hall')->assertRedirect('https://www.acesandeightssaloonbar.com/hire/');
    }

    public function test_admin_can_view_inquiries(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        PartyHallInquiry::create($this->validData);
        $this->actingAs($admin)->get('/admin/party-hall')->assertStatus(200)->assertSee('john@example.com');
    }

    public function test_admin_can_update_inquiry_status(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $inquiry = PartyHallInquiry::create($this->validData);
        $this->actingAs($admin)
            ->patch("/admin/party-hall/{$inquiry->id}", ['status' => 'contacted'])
            ->assertRedirect();
        $this->assertDatabaseHas('party_hall_inquiries', ['id' => $inquiry->id, 'status' => 'contacted']);
    }

    public function test_customer_cannot_access_admin_party_hall(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $this->actingAs($customer)->get('/admin/party-hall')->assertStatus(403);
    }
}
