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

    @include('partials.language-picker-mobile', ['currentLocale' => $currentLocale])
</nav>
