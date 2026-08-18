@php
    $errorMessages = $errors->getBag('default')->all();
@endphp

<div class="cms-form-block-form cms-apply-form-block">
    @include('cms.partials.form-feedback', [
        'successMessage' => (string) session('apply_success', ''),
        'errorMessages' => $errorMessages,
        'errorTitle' => 'Controleer het formulier:',
    ])

    <form method="post" action="{{ route('apply.submit') }}" class="cms-native-form" novalidate>
        @csrf

        <div class="cms-native-form-row cms-native-form-row-split">
            <div class="cms-native-form-field">
                <label for="apply-first-name">Voornaam</label>
                <input id="apply-first-name" type="text" name="wpforms[fields][0][first]" value="{{ old('wpforms.fields.0.first') }}" required>
                @error('wpforms.fields.0.first')<p class="cms-native-form-error">{{ $message }}</p>@enderror
            </div>
            <div class="cms-native-form-field">
                <label for="apply-last-name">Achternaam</label>
                <input id="apply-last-name" type="text" name="wpforms[fields][0][last]" value="{{ old('wpforms.fields.0.last') }}" required>
                @error('wpforms.fields.0.last')<p class="cms-native-form-error">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="cms-native-form-honeypot" aria-hidden="true">
            <label for="apply-honeypot">Bericht Traject Telefoonnummer</label>
            <input id="apply-honeypot" type="text" name="wpforms[fields][2]" value="{{ old('wpforms.fields.2') }}" tabindex="-1" autocomplete="off">
        </div>

        <div class="cms-native-form-field">
            <label for="apply-email">Email</label>
            <input id="apply-email" type="email" name="wpforms[fields][1]" value="{{ old('wpforms.fields.1') }}" required>
            @error('wpforms.fields.1')<p class="cms-native-form-error">{{ $message }}</p>@enderror
        </div>

        <div class="cms-native-form-field">
            <label for="apply-phone">Telefoonnummer</label>
            <input id="apply-phone" type="text" name="wpforms[fields][9]" value="{{ old('wpforms.fields.9') }}" required>
            @error('wpforms.fields.9')<p class="cms-native-form-error">{{ $message }}</p>@enderror
        </div>

        <div class="cms-native-form-field">
            <label for="apply-trajectory">Traject</label>
            <select id="apply-trajectory" name="wpforms[fields][10]" required>
                @foreach(['Intakegesprek', 'Kort traject', 'Regulier traject', 'Anders, namelijk'] as $option)
                    <option value="{{ $option }}" @selected(old('wpforms.fields.10') === $option)>{{ $option }}</option>
                @endforeach
            </select>
            @error('wpforms.fields.10')<p class="cms-native-form-error">{{ $message }}</p>@enderror
        </div>

        <div class="cms-native-form-field">
            <label for="apply-message">Bericht</label>
            <textarea id="apply-message" name="wpforms[fields][3]" rows="7">{{ old('wpforms.fields.3') }}</textarea>
            @error('wpforms.fields.3')<p class="cms-native-form-error">{{ $message }}</p>@enderror
        </div>

        <div class="cms-native-form-actions">
            <button type="submit" class="cms-native-form-submit">Vraag aan</button>
        </div>
    </form>
</div>