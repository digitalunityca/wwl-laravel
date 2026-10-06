<?php

namespace Tests\Feature;

use App\Mail\ContactInquiry;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ContactTest extends TestCase
{
    public function test_valid_inquiry_is_queued_to_the_configured_recipient(): void
    {
        Mail::fake();
        config(['contact.recipient' => 'inbox@example.com']);
        $inquiry = ['name' => 'Alex', 'email' => 'alex@example.com', 'message' => 'I need a website.'];

        $this->post(route('contact.store'), [...$inquiry, 'recipient' => 'unwanted@example.com'])
            ->assertRedirect(route('home').'#contact')
            ->assertSessionHas('contact_status');

        Mail::assertQueued(ContactInquiry::class, fn (ContactInquiry $mail) => $mail->hasTo('inbox@example.com') && $mail->inquiry === $inquiry
            && $mail->envelope()->replyTo[0]->address === 'alex@example.com'
        );
    }

    public function test_required_fields_show_errors_and_do_not_queue_mail(): void
    {
        Mail::fake();
        $this->post(route('contact.store'), [])
            ->assertRedirect(route('home').'#contact')
            ->assertSessionHasErrors(['name', 'email', 'message']);
        Mail::assertNothingQueued();
    }

    #[DataProvider('invalidFields')]
    public function test_invalid_details_are_rejected(string $field, mixed $value): void
    {
        Mail::fake();
        $payload = ['name' => 'Alex', 'email' => 'alex@example.com', 'message' => 'A project inquiry.'];
        $payload[$field] = $value;
        $this->post(route('contact.store'), $payload)->assertSessionHasErrors($field);
        Mail::assertNothingQueued();
    }

    public static function invalidFields(): array
    {
        return [
            'invalid email' => ['email', 'invalid'],
            'long name' => ['name', str_repeat('a', 121)],
            'long email' => ['email', str_repeat('a', 245).'@example.com'],
            'long phone' => ['phone', str_repeat('1', 51)],
            'long website' => ['website', str_repeat('a', 501)],
            'long message' => ['message', str_repeat('a', 5001)],
            'invalid structure' => ['message', ['nested']],
            'honeypot' => ['company_url', 'spam.example'],
        ];
    }

    public function test_optional_details_are_included_and_user_content_is_escaped(): void
    {
        $inquiry = ['name' => '<script>name</script>', 'email' => 'alex@example.com',
            'phone' => '<script>phone</script>', 'website' => '<script>site</script>',
            'message' => '<script>message</script>'];
        $mail = new ContactInquiry($inquiry);
        $html = $mail->render();
        foreach (['name', 'phone', 'website', 'message'] as $field) {
            $this->assertStringContainsString(e($inquiry[$field]), $html);
            $this->assertStringNotContainsString($inquiry[$field], $html);
        }
        $mail->assertSeeInText('alex@example.com');
    }

    public function test_repeated_submissions_are_rate_limited(): void
    {
        Mail::fake();
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->post(route('contact.store'), [])->assertRedirect();
        }
        $this->post(route('contact.store'), [])->assertTooManyRequests();
        Mail::assertNothingQueued();
    }

    public function test_validation_feedback_preserves_and_escapes_input(): void
    {
        $this->followingRedirects()->post(route('contact.store'), [
            'name' => '<script>alert(1)</script>', 'email' => 'invalid', 'message' => 'Help with a website',
        ])->assertOk()->assertSee('The email field must be a valid email address.')
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertSee('Help with a website');
    }
}
