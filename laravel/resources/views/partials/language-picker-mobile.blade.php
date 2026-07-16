@php
    $currentLocale = $currentLocale ?? app()->getLocale();
    $locales = config('wedding.locales', []);
    $currentLabel = $locales[$currentLocale] ?? strtoupper($currentLocale);
@endphp
<details class="lang-picker">
    <summary class="lang-picker__trigger">
        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
            <circle cx="12" cy="12" r="10"/>
            <path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"/>
            <path d="M2 12h20"/>
        </svg>
        <span>{{ $currentLabel }}</span>
        <svg class="lang-picker__chevron" xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
            <polyline points="6 9 12 15 18 9"/>
        </svg>
    </summary>
    <ul class="lang-picker__menu" role="menu" aria-label="{{ __('Language') }}">
        @foreach ($locales as $code => $label)
            <li role="none">
                <a
                    role="menuitem"
                    class="lang-picker__item"
                    href="{{ route('locale.switch', ['locale' => $code]) }}"
                    hreflang="{{ $code }}"
                    @if ($currentLocale === $code) aria-current="true" @endif
                >{{ $label }}</a>
            </li>
        @endforeach
    </ul>
</details>
