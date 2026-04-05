<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('RSVP notification') }}</title>
</head>
<body style="font-family: system-ui, -apple-system, Segoe UI, Roboto, sans-serif; line-height: 1.5; color: #1a1a1a; max-width: 32rem; margin: 0; padding: 1.25rem;">
    <p><strong>{{ $isUpdate ? __('RSVP updated') : __('New RSVP') }}</strong> — {{ config('wedding.event.title') }}</p>
    <p><strong>{{ __('Guest') }}:</strong> {{ $guest->name }}</p>
    @if (filled($guest->email))
        <p><strong>{{ __('Email') }}:</strong> {{ $guest->email }}</p>
    @endif
    <p>
        <strong>{{ __('Attendance') }}:</strong>
        {{ $guest->rsvp_status === 'yes' ? __('Yes') : __('No') }}
    </p>
    @if ($guest->rsvp_status === 'yes' && $guest->guests_count)
        <p><strong>{{ __('Number of guests') }}:</strong> {{ $guest->guests_count }}</p>
    @endif
    @if (filled($guest->notes))
        <p><strong>{{ __('Notes') }}:</strong><br>{{ $guest->notes }}</p>
    @endif
</body>
</html>
