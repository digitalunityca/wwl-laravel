<?php

namespace App\Livewire;

use App\Mail\ContactInquiry;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Throwable;

class ContactForm extends Component
{
    #[Validate('required|string|max:120')]
    public string $name = '';

    #[Validate('required|email:rfc|max:254')]
    public string $email = '';

    #[Validate('nullable|string|max:50')]
    public string $phone = '';

    #[Validate('nullable|string|max:500')]
    public string $website = '';

    #[Validate('required|string|max:5000')]
    public string $message = '';

    #[Validate('prohibited')]
    public string $company_url = '';

    #[Locked]
    public bool $submitted = false;

    public function send(): void
    {
        if ($this->submitted) {
            return;
        }

        $this->resetErrorBag('delivery');
        $key = 'contact:'.request()->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $this->addError('delivery', 'Too many attempts. Please wait a minute before trying again.');

            return;
        }

        RateLimiter::hit($key, 60);
        $validated = $this->validate();
        unset($validated['company_url']);

        try {
            Mail::to(config('contact.recipient'))->send(new ContactInquiry($validated));
        } catch (Throwable $exception) {
            report($exception);
            $this->addError('delivery', 'Your message could not be sent. Please try again or email us directly.');

            return;
        }

        $this->reset('name', 'email', 'phone', 'website', 'message', 'company_url');
        $this->submitted = true;
    }

    public function render(): View
    {
        return view('livewire.contact-form');
    }
}
