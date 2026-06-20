@php
    $mailLocales = config('wedding.mail_locales', []);
    $selectedLocale = old('locale', $selectedLocale ?? null);
@endphp

<label for="locale">{{ __('Email language') }}</label>
<select id="locale" name="locale">
    <option value="" @selected($selectedLocale === null || $selectedLocale === '')>{{ __('Default (:lang)', ['lang' => $mailLocales[config('wedding.mail.default_locale', 'en')] ?? 'English']) }}</option>
    @foreach ($mailLocales as $code => $label)
        <option value="{{ $code }}" @selected($selectedLocale === $code)>{{ $label }}</option>
    @endforeach
</select>
<p class="hint" style="margin-top:-0.5rem;">{{ __('Invitation and RSVP emails are sent in this language, regardless of the site language.') }}</p>
@error('locale')
    <p class="err">{{ $message }}</p>
@enderror
