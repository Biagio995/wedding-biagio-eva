<nav class="locale-switcher" aria-label="{{ __('Language') }}">
    @foreach (config('wedding.locales', []) as $code => $label)
        <a
            href="{{ route('locale.switch', ['locale' => $code]) }}"
            hreflang="{{ $code }}"
            @if (app()->getLocale() === $code) aria-current="true" @endif
        >{{ $label }}</a>
        @if (! $loop->last)
            <span aria-hidden="true">·</span>
        @endif
    @endforeach
</nav>
