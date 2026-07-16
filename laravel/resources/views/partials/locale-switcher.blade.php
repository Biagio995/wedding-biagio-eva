@php
    $currentLocale = app()->getLocale();
@endphp
<nav class="locale-switcher" aria-label="{{ __('Language') }}">
    <div class="locale-switcher__links">
        @foreach (config('wedding.locales', []) as $code => $label)
            <a
                href="{{ route('locale.switch', ['locale' => $code]) }}"
                hreflang="{{ $code }}"
                @if ($currentLocale === $code) aria-current="true" @endif
            >{{ $label }}</a>
            @if (! $loop->last)
                <span aria-hidden="true">·</span>
            @endif
        @endforeach
    </div>

    <label class="sr-only" for="mobile-admin-language">{{ __('Language') }}</label>
    <select
        id="mobile-admin-language"
        class="locale-switcher__select"
        onchange="if (this.value) window.location.href = this.value;"
    >
        @foreach (config('wedding.locales', []) as $code => $label)
            <option
                value="{{ route('locale.switch', ['locale' => $code]) }}"
                @selected($currentLocale === $code)
            >{{ $label }}</option>
        @endforeach
    </select>
</nav>
