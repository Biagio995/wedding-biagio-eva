<div
    id="wedding-envelope"
    class="wedding-envelope"
    hidden
    data-wedding-envelope
    role="dialog"
    aria-modal="true"
    aria-label="{{ __('You have been invited to our wedding') }}"
>
    <div class="wedding-envelope__lang-bar">
        @include('partials.site.lang-links')
    </div>
    <div
        class="wedding-envelope__scene"
        data-wedding-envelope-scene
        role="button"
        tabindex="0"
        aria-label="{{ __('Open invitation') }}"
    >
        <div class="wedding-envelope__cp-wrap" aria-hidden="true">
            <div class="wedding-envelope__cp-lid wedding-envelope__cp-lid--one"></div>
            <div class="wedding-envelope__cp-lid wedding-envelope__cp-lid--two"></div>
            <div class="wedding-envelope__cp-pocket"></div>
            <div class="wedding-envelope__letter" aria-hidden="true">
                <p class="wedding-envelope__title">{{ __('You have been invited to our wedding') }}</p>
            </div>
        </div>
        <div class="wedding-envelope__hit" aria-hidden="true"></div>
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
</div>
