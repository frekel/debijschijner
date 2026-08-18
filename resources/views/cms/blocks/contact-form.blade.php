@php
    $errorMessages = $errors->getBag('default')->all();
@endphp

<div class="cms-form-block-form cms-contact-form-block">
    @include('cms.partials.form-feedback', [
        'successMessage' => (string) session('contact_success', ''),
        'errorMessages' => $errorMessages,
        'errorTitle' => 'Controleer het formulier:',
    ])

    <form method="post" action="{{ route('contact.submit') }}" class="cms-native-form" novalidate>
        @csrf

        <div class="cms-native-form-row cms-native-form-row-split">
            <div class="cms-native-form-field">
                <label for="contact-first-name">Voornaam</label>
                <input id="contact-first-name" type="text" name="bsforms[fields][0][first]" value="{{ old('bsforms.fields.0.first') }}" required>
                @error('bsforms.fields.0.first')<p class="cms-native-form-error">{{ $message }}</p>@enderror
            </div>
            <div class="cms-native-form-field">
                <label for="contact-last-name">Achternaam</label>
                <input id="contact-last-name" type="text" name="bsforms[fields][0][last]" value="{{ old('bsforms.fields.0.last') }}" required>
                @error('bsforms.fields.0.last')<p class="cms-native-form-error">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="cms-native-form-honeypot" aria-hidden="true">
            <label for="contact-honeypot">Telefoonnummer Email Naam</label>
            <input id="contact-honeypot" type="text" name="bsforms[fields][2]" value="{{ old('bsforms.fields.2') }}" tabindex="-1" autocomplete="off">
        </div>

        <div class="cms-native-form-field">
            <label for="contact-email">Email</label>
            <input id="contact-email" type="email" name="bsforms[fields][1]" value="{{ old('bsforms.fields.1') }}" required>
            @error('bsforms.fields.1')<p class="cms-native-form-error">{{ $message }}</p>@enderror
        </div>

        <div class="cms-native-form-field">
            <label for="contact-phone">Telefoonnummer</label>
            <input id="contact-phone" type="text" name="bsforms[fields][9]" value="{{ old('bsforms.fields.9') }}" required>
            @error('bsforms.fields.9')<p class="cms-native-form-error">{{ $message }}</p>@enderror
        </div>

        <div class="cms-native-form-field">
            <label for="contact-message">Bericht</label>
            <textarea id="contact-message" name="bsforms[fields][3]" rows="5" required>{{ old('bsforms.fields.3') }}</textarea>
            @error('bsforms.fields.3')<p class="cms-native-form-error">{{ $message }}</p>@enderror
        </div>

        <div class="cms-native-form-actions">
            <button type="submit" class="cms-native-form-submit">Verstuur</button>
        </div>
    </form>
</div>
