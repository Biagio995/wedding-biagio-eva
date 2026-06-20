<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('RSVP notification') }}</title>
</head>
<body style="font-family: system-ui, -apple-system, Segoe UI, Roboto, sans-serif; line-height: 1.5; color: #1a1a1a; max-width: 32rem; margin: 0; padding: 1.25rem;">
    <p><strong>{{ $isUpdate ? __('RSVP updated') : __('New RSVP') }}</strong> — {{ __(config('wedding.event.title')) }}</p>
    <p><strong>{{ __('Guest') }}:</strong> {{ $guest->name }}</p>
    @if (filled($guest->email))
        <p><strong>{{ __('Email') }}:</strong> {{ $guest->email }}</p>
    @endif
    <p>
        <strong>{{ __('Attendance') }}:</strong>
        {{ $guest->rsvp_status === 'yes' ? __('Yes') : __('No') }}
    </p>
    @if ($guest->rsvp_status === 'yes')
        @php
            $companions = is_array($guest->companion_names) ? $guest->companion_names : [];
            $companions = array_values(array_filter(array_map(
                static fn ($n): string => is_string($n) ? trim($n) : '',
                $companions,
            ), static fn (string $n): bool => $n !== ''));
            $attendees = array_merge([(string) $guest->name], $companions);
        @endphp
        @if ($guest->guests_count)
            <p><strong>{{ __('Number of guests') }}:</strong> {{ $guest->guests_count }}</p>
        @endif
        <p><strong>{{ __('Attendees') }}:</strong></p>
        <ul style="margin: 0.25rem 0 0; padding-left: 1.25rem;">
            @foreach ($attendees as $attendee)
                <li>{{ $attendee }}</li>
            @endforeach
        </ul>
    @endif
    @if (filled($guest->notes))
        <p><strong>{{ __('Notes') }}:</strong><br>{{ $guest->notes }}</p>
    @endif
</body>
</html>
