<?php

namespace Tests\Feature\Account;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PasswordChangeTest extends TestCase
{
    use RefreshDatabase;

    private User $customer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->customer = User::factory()->create([
            'role'     => 'customer',
            'password' => Hash::make('oldpassword'),
        ]);
    }

    public function test_customer_can_change_password(): void
    {
        $response = $this->actingAs($this->customer)->post('/account/password', [
            'current_password' => 'oldpassword', 'password' => 'newpassword123', 'password_confirmation' => 'newpassword123',
        ]);
        $response->assertRedirect('/account');
        $this->assertTrue(Hash::check('newpassword123', $this->customer->fresh()->password));
    }

    public function test_wrong_current_password_rejected(): void
    {
        $response = $this->actingAs($this->customer)->post('/account/password', [
            'current_password' => 'wrongpassword', 'password' => 'newpassword123', 'password_confirmation' => 'newpassword123',
        ]);
        $response->assertSessionHasErrors(['current_password']);
    }

    public function test_new_password_must_be_confirmed(): void
    {
        $response = $this->actingAs($this->customer)->post('/account/password', [
            'current_password' => 'oldpassword', 'password' => 'newpassword123', 'password_confirmation' => 'mismatch',
        ]);
        $response->assertSessionHasErrors(['password']);
    }
}
