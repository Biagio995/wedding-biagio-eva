@php
    $eventTitle = $eventTitle ?? '';
@endphp
<div
    id="wedding-envelope"
    class="wedding-envelope"
    hidden
    data-wedding-envelope
    role="dialog"
    aria-modal="true"
    aria-labelledby="wedding-envelope-title"
>
    <div class="wedding-envelope__lang-bar">
        @include('partials.site.lang-links')
    </div>
    <div class="wedding-envelope__scene" data-wedding-envelope-scene>
        <div class="wedding-envelope__back" aria-hidden="true"></div>
        {{-- Area click su tutta la busta (sotto lettera/tasche/lembo); lembo/sigillo hanno pointer-events gestiti a parte --}}
        <div class="wedding-envelope__hit" aria-hidden="true"></div>
        <div class="wedding-envelope__pocket" aria-hidden="true"></div>
        <div class="wedding-envelope__letter">
            <span class="wedding-envelope__flourish" aria-hidden="true">❦</span>
            <p id="wedding-envelope-title" class="wedding-envelope__title">{{ $eventTitle }}</p>
            <p class="wedding-envelope__hint">{{ __('Tap or click the envelope, the seal, or the button below') }}</p>
        </div>
        <div class="wedding-envelope__flap" aria-hidden="true">
            <span class="wedding-envelope__flap-face"></span>
        </div>
        <div class="wedding-envelope__wax" aria-hidden="true">
            <img
                class="wedding-envelope__wax-body"
                src="{{ asset('images/wedding-texture-initials.png') }}"
                width="1024"
                height="1024"
                alt=""
                decoding="async"
                fetchpriority="high"
            />
        </div>
    </div>
    <button type="button" class="wedding-envelope__open-btn" data-wedding-envelope-open>
        {{ __('Open invitation') }}
    </button>
</div>
