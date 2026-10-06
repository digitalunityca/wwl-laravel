<?php

namespace Tests\Feature;

use App\Livewire\ContactForm;
use App\Mail\ContactInquiry;
use Illuminate\Mail\PendingMail;
use Illuminate\Mail\Transport\ResendTransport;
use Illuminate\Support\Facades\Exceptions;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;
use RuntimeException;
use Tests\TestCase;

class ContactTest extends TestCase
{
    public function test_resend_mailer_can_be_constructed_with_the_configured_key(): void
    {
        config(['services.resend.key' => 're_local_test_placeholder']);

        $this->assertInstanceOf(
            ResendTransport::class,
            Mail::mailer('resend')->getSymfonyTransport(),
        );
    }

    public function test_valid_inquiry_is_sent_to_the_configured_recipient(): void
    {
        Mail::fake();
        config(['contact.recipient' => 'inbox@example.com']);
        $inquiry = ['name' => 'Alex', 'email' => 'alex@example.com', 'message' => 'I need a website.'];

        Livewire::test(ContactForm::class)
            ->set($inquiry)
            ->call('send')
            ->assertHasNoErrors()
            ->assertSet('submitted', true)
            ->assertSet('message', '')
            ->assertSee('Thank you for reaching out.')
            ->call('send');

        Mail::assertSentCount(1);
        Mail::assertNothingQueued();
        Mail::assertSent(ContactInquiry::class, fn (ContactInquiry $mail) => $mail->hasTo('inbox@example.com') && array_intersect_key($mail->inquiry, $inquiry) === $inquiry
            && $mail->envelope()->replyTo[0]->address === 'alex@example.com'
        );
    }

    public function test_required_fields_show_errors_and_do_not_send_mail(): void
    {
        Mail::fake();
        Livewire::test(ContactForm::class)->call('send')
            ->assertHasErrors(['name' => 'required', 'email' => 'required', 'message' => 'required'])
            ->assertSet('submitted', false);
        Mail::assertNothingOutgoing();
    }

    #[DataProvider('invalidFields')]
    public function test_invalid_details_are_rejected(string $field, mixed $value): void
    {
        Mail::fake();
        $payload = ['name' => 'Alex', 'email' => 'alex@example.com', 'message' => 'A project inquiry.'];
        $payload[$field] = $value;
        Livewire::test(ContactForm::class)->set($payload)->call('send')->assertHasErrors($field);
        Mail::assertNothingOutgoing();
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
            Livewire::test(ContactForm::class)->call('send')->assertHasErrors('name');
        }
        Livewire::test(ContactForm::class)
            ->set(['name' => 'Alex', 'email' => 'alex@example.com', 'message' => 'Please help'])
            ->call('send')->assertHasErrors('delivery')->assertSee('Too many attempts.')
            ->assertSet('submitted', false);
        Mail::assertNothingOutgoing();
    }

    public function test_rate_limit_expires(): void
    {
        Mail::fake();
        RateLimiter::hit('contact:127.0.0.1', 60);
        for ($attempt = 0; $attempt < 4; $attempt++) {
            RateLimiter::hit('contact:127.0.0.1', 60);
        }
        $this->travel(61)->seconds();
        Livewire::test(ContactForm::class)
            ->set(['name' => 'Alex', 'email' => 'alex@example.com', 'message' => 'Please help'])
            ->call('send')->assertHasNoErrors()->assertSet('submitted', true);
        Mail::assertSentCount(1);
    }

    public function test_validation_feedback_preserves_input(): void
    {
        Livewire::test(ContactForm::class)
            ->set(['name' => 'Alex', 'email' => 'invalid', 'message' => 'Help with a website'])
            ->call('send')->assertHasErrors('email')
            ->assertSee('The email field must be a valid email address.')
            ->assertSet('message', 'Help with a website');
    }

    public function test_delivery_failure_preserves_the_message_without_showing_success(): void
    {
        Exceptions::fake();
        $pending = \Mockery::mock(PendingMail::class);
        Mail::shouldReceive('to')->once()->with(config('contact.recipient'))->andReturn($pending);
        $pending->shouldReceive('send')->once()->andThrow(new RuntimeException('Provider unavailable'));

        Livewire::test(ContactForm::class)
            ->set(['name' => 'Alex', 'email' => 'alex@example.com', 'message' => 'Please help'])
            ->call('send')->assertHasErrors('delivery')
            ->assertSet('submitted', false)->assertSet('message', 'Please help')
            ->assertSee('Your message could not be sent.')
            ->assertDontSee('Thank you for reaching out.');

        Exceptions::assertReported(RuntimeException::class);
    }

    public function test_home_contains_the_livewire_form(): void
    {
        $this->get('/')->assertOk()->assertSeeLivewire(ContactForm::class);
    }
}
