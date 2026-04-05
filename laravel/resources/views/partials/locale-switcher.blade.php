@once
    <style>
        .locale-switcher {
            display: flex;
            flex-wrap: wrap;
            gap: 0.35rem 0.65rem;
            align-items: center;
            justify-content: flex-end;
            font-size: 0.82rem;
            margin-bottom: 0.85rem;
        }
        .locale-switcher span { color: var(--muted, #9898a8); }
        .locale-switcher a {
            color: var(--accent, #c9a962);
            text-decoration: none;
            padding: 0.35rem 0.45rem;
            border-radius: 6px;
            min-height: 44px;
            display: inline-flex;
            align-items: center;
            -webkit-tap-highlight-color: transparent;
        }
        .locale-switcher a[aria-current="true"] {
            font-weight: 600;
            text-decoration: underline;
        }
        .locale-switcher a:focus-visible {
            outline: 2px solid var(--accent, #c9a962);
            outline-offset: 2px;
        }
    </style>
@endonce
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
