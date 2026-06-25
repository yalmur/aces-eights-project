<?php

namespace Tests\Feature;

use App\Mail\ContactMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ContactFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_valid_contact_submission_queues_email(): void
    {
        Mail::fake();

        $this->post('/contact', [
            'name'    => 'John Doe',
            'email'   => 'john@example.com',
            'subject' => 'Question about delivery',
            'message' => 'When does the kitchen close?',
        ])->assertRedirect();

        Mail::assertQueued(ContactMessage::class);
    }

    public function test_contact_form_returns_success_flash(): void
    {
        Mail::fake();

        $this->post('/contact', [
            'name'    => 'John',
            'email'   => 'john@example.com',
            'subject' => 'Test',
            'message' => 'Test message',
        ])->assertSessionHas('success');
    }

    public function test_contact_form_requires_all_fields(): void
    {
        Mail::fake();

        $this->post('/contact', [])->assertSessionHasErrors(['name', 'email', 'subject', 'message']);

        Mail::assertNothingQueued();
    }

    public function test_contact_form_rejects_invalid_email(): void
    {
        Mail::fake();

        $this->post('/contact', [
            'name'    => 'John',
            'email'   => 'not-an-email',
            'subject' => 'Test',
            'message' => 'Test message',
        ])->assertSessionHasErrors(['email']);

        Mail::assertNothingQueued();
    }

    public function test_contact_form_rejects_overlong_message(): void
    {
        Mail::fake();

        $this->post('/contact', [
            'name'    => 'John',
            'email'   => 'john@example.com',
            'subject' => 'Test',
            'message' => str_repeat('x', 2001),
        ])->assertSessionHasErrors(['message']);

        Mail::assertNothingQueued();
    }

    public function test_contact_form_strips_crlf_injection_from_name_and_subject(): void
    {
        Mail::fake();

        $this->post('/contact', [
            'name'    => "John\r\nBcc: evil@example.com",
            'email'   => 'john@example.com',
            'subject' => "Legit\nSubject",
            'message' => 'Normal message',
        ])->assertRedirect();

        Mail::assertQueued(ContactMessage::class);
    }

    public function test_contact_email_queued_to_store_email_setting(): void
    {
        Mail::fake();
        \App\Models\Setting::set('store_email', 'store@example.com');

        $this->post('/contact', [
            'name'    => 'Jane',
            'email'   => 'jane@example.com',
            'subject' => 'Question',
            'message' => 'Hello there',
        ]);

        Mail::assertQueued(ContactMessage::class, fn ($mail) => $mail->hasTo('store@example.com'));
    }

    public function test_contact_form_rejects_overlong_name(): void
    {
        Mail::fake();

        $this->post('/contact', [
            'name'    => str_repeat('a', 101),
            'email'   => 'john@example.com',
            'subject' => 'Test',
            'message' => 'Test message',
        ])->assertSessionHasErrors(['name']);

        Mail::assertNothingQueued();
    }

    public function test_contact_form_rejects_overlong_subject(): void
    {
        Mail::fake();

        $this->post('/contact', [
            'name'    => 'John',
            'email'   => 'john@example.com',
            'subject' => str_repeat('s', 151),
            'message' => 'Test message',
        ])->assertSessionHasErrors(['subject']);

        Mail::assertNothingQueued();
    }
}
