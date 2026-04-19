<x-layouts.admin
    :page-title="__('Gallery QR card') . ' — ' . config('app.name', 'Wedding')"
    css-page="page-gallery-qr"
>
    @include('partials.locale-switcher')
    <p class="toolbar no-print">
        <a href="{{ route('admin.rsvp.dashboard') }}">{{ __('RSVP dashboard') }}</a>
        ·
        <a href="{{ route('admin.photos.index') }}">{{ __('Photo moderation') }}</a>
        ·
        <form method="post" action="{{ route('admin.logout') }}" style="display:inline;">
            @csrf
            <button type="submit" class="link">{{ __('Sign out') }}</button>
        </form>
    </p>

    <h1 class="no-print">{{ __('Gallery QR card') }}</h1>
    <p class="sub no-print">{{ __('Print this card and place it on reception tables so guests can scan and share their photos.') }}</p>

    <p class="no-print cta">
        <button type="button" class="btn" onclick="window.print()">{{ __('Print card') }}</button>
        ·
        <a href="{{ $galleryUrl }}" target="_blank" rel="noopener">{{ __('Open gallery page') }}</a>
    </p>

    <div class="gallery-qr-card" aria-label="{{ __('Scan to share your photos') }}">
        <p class="gallery-qr-card__brand">{{ __($eventTitle) }}</p>
        <h2 class="gallery-qr-card__headline">{{ __('Share your photos with us') }}</h2>
        <p class="gallery-qr-card__lead">{{ __('Scan the QR code with your phone camera and upload the photos you took — no app or sign-up needed.') }}</p>
        <div class="gallery-qr-card__qr-wrap">
            <img class="gallery-qr-card__qr" src="{{ $qrDataUri }}" alt="{{ __('QR code to the gallery page') }}">
        </div>
        <p class="gallery-qr-card__footer">{{ __('Thank you for celebrating with us!') }}</p>
    </div>
</x-layouts.admin>
