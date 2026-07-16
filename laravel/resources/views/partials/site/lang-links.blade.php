@php
    $wrapperClass = $wrapperClass ?? 'site-header__langs';
    $linkClass = $linkClass ?? 'site-header__lang';
    $currentLocale = app()->getLocale();
@endphp
<div class="{{ $wrapperClass }}" aria-label="{{ __('Language') }}">
    <div class="site-header__langs-links">
        @foreach (config('wedding.locales', []) as $code => $label)
            <a
                class="{{ $linkClass }}"
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
</div>
