@php
    $wrapperClass = $wrapperClass ?? 'site-header__langs';
    $linkClass = $linkClass ?? 'site-header__lang';
@endphp
<div class="{{ $wrapperClass }}" aria-label="{{ __('Language') }}">
    @foreach (config('wedding.locales', []) as $code => $label)
        <a
            class="{{ $linkClass }}"
            href="{{ route('locale.switch', ['locale' => $code]) }}"
            hreflang="{{ $code }}"
            @if (app()->getLocale() === $code) aria-current="true" @endif
        >{{ $label }}</a>
        @if (! $loop->last)
            <span aria-hidden="true">·</span>
        @endif
    @endforeach
</div>
