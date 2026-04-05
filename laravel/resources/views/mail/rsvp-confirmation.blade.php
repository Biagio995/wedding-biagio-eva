<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('RSVP confirmation') }}</title>
</head>
<body style="font-family: system-ui, -apple-system, Segoe UI, Roboto, sans-serif; line-height: 1.5; color: #1a1a1a; max-width: 32rem; margin: 0; padding: 1.25rem;">
    <p>{{ __('Hello :name,', ['name' => $guest->name]) }}</p>
    <p>{{ __('This email confirms that we have saved your RSVP for :event.', ['event' => __(config('wedding.event.title'))]) }}</p>
    <p>
        <strong>{{ __('Attendance') }}:</strong>
        {{ $guest->rsvp_status === 'yes' ? __('Yes') : __('No') }}
    </p>
    @if ($guest->rsvp_status === 'yes' && $guest->guests_count)
        <p><strong>{{ __('Number of guests') }}:</strong> {{ $guest->guests_count }}</p>
    @endif
    @if (filled($guest->notes))
        <p><strong>{{ __('Your notes') }}:</strong><br>{{ $guest->notes }}</p>
    @endif
    <p style="margin-top: 1.5rem;">{{ __('Thank you!') }}</p>
</body>
</html>
