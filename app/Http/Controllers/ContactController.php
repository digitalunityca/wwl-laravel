<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactRequest;
use App\Mail\ContactInquiry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function __invoke(StoreContactRequest $request): RedirectResponse
    {
        Mail::to(config('contact.recipient'))->send(new ContactInquiry(
            $request->safe()->only(['name', 'email', 'phone', 'website', 'message'])
        ));

        return redirect()->to(route('home').'#contact')
            ->with('contact_status', 'Thanks. Your message has been received. We’ll be in touch.');
    }
}
