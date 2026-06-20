@props([
    'guest',
    'qrSvg',
])

<div class="qr-block">
    <p>{{ __('Invitation QR code') }}</p>
    <div class="qr-block__code">{!! $qrSvg !!}</div>
    <p>
        <a href="{{ route('admin.guests.qr', ['guest' => $guest, 'download' => 1]) }}">{{ __('Download QR (PNG)') }}</a>
    </p>
</div>
