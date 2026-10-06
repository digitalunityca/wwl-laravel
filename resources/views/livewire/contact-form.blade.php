<div class="contact-form">
    @if ($submitted)
        <div class="contact-thanks" role="status" tabindex="-1" x-init="$el.focus()">
            <svg width="48" height="48" class="success-mark" viewBox="0 0 48 48" fill="none" aria-hidden="true"><circle cx="24" cy="24" r="22" stroke="currentColor" stroke-width="2"/><path d="m14 24 7 7 14-15" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            <h3>Thank you for reaching out.</h3>
            <p>Your message is on its way to our team. We’ll be in touch to talk about what’s next.</p>
        </div>
    @else
        <form wire:submit="send" novalidate>
            <div hidden aria-hidden="true"><label for="contact-company-url">Leave this empty</label><input id="contact-company-url" wire:model="company_url" tabindex="-1" autocomplete="off"></div>
            <div class="form-row">
                <div class="field">
                    <label for="contact-name">Name <span>(required)</span></label>
                    <input id="contact-name" name="name" wire:model.live.blur="name" type="text" autocomplete="name" maxlength="120" required aria-invalid="{{ $errors->has('name') ? 'true' : 'false' }}" @error('name') aria-describedby="name-error" @enderror>
                    @error('name') <p class="field-error" id="name-error" role="alert">{{ $message }}</p> @enderror
                </div>
                <div class="field">
                    <label for="contact-email">Email <span>(required)</span></label>
                    <input id="contact-email" name="email" wire:model.live.blur="email" type="email" autocomplete="email" maxlength="254" required aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}" @error('email') aria-describedby="email-error" @enderror>
                    @error('email') <p class="field-error" id="email-error" role="alert">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="form-row">
                <div class="field">
                    <label for="contact-phone">Phone <span>(optional)</span></label>
                    <input id="contact-phone" name="phone" wire:model.live.blur="phone" type="tel" autocomplete="tel" maxlength="50"  aria-invalid="{{ $errors->has('phone') ? 'true' : 'false' }}" @error('phone') aria-describedby="phone-error" @enderror>
                    @error('phone') <p class="field-error" id="phone-error" role="alert">{{ $message }}</p> @enderror
                </div>
                <div class="field">
                    <label for="contact-website">Website <span>(optional)</span></label>
                    <input id="contact-website" name="website" wire:model.live.blur="website" type="text" autocomplete="url" maxlength="500"  aria-invalid="{{ $errors->has('website') ? 'true' : 'false' }}" @error('website') aria-describedby="website-error" @enderror>
                    @error('website') <p class="field-error" id="website-error" role="alert">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="field">
                <label for="contact-message">How can we help? <span>(required)</span></label>
                <textarea id="contact-message" name="message" wire:model.live.blur="message" rows="5" required maxlength="5000" placeholder="A little about your project and what you’d like to achieve…" aria-invalid="{{ $errors->has('message') ? 'true' : 'false' }}" @error('message') aria-describedby="message-error" @enderror></textarea>
                @error('message') <p class="field-error" id="message-error" role="alert">{{ $message }}</p> @enderror
            </div>
            @error('company_url') <p class="field-error" role="alert">We could not accept this submission. Please email us directly.</p> @enderror
            @error('delivery') <p class="form-errors" role="alert">{{ $message }}</p> @enderror
            <div class="form-actions">
                <button class="button primary" type="submit" wire:loading.attr="disabled" wire:target="send">
                    <span wire:loading.remove wire:target="send">Send message</span>
                    <span wire:loading wire:target="send" role="status">Sending…</span>
                </button>
                <p>We’ll use these details to respond to your inquiry.</p>
            </div>
            <noscript><p>Please enable JavaScript to use this form, or email <a href="mailto:info@webworkslab.dev">info@webworkslab.dev</a>.</p></noscript>
        </form>
    @endif
</div>
