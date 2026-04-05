<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('RSVP reminder') }}</title>
</head>
<body style="font-family: system-ui, -apple-system, Segoe UI, Roboto, sans-serif; line-height: 1.5; color: #1a1a1a; max-width: 32rem; margin: 0; padding: 1.25rem;">
    <p>{{ __('Hello :name,', ['name' => $guest->name]) }}</p>
    <p>{{ __('We have not yet received your reply for :event.', ['event' => config('wedding.event.title')]) }}</p>
    <p>{{ __('Please open your personal invitation link and let us know if you can join us:') }}</p>
    <p style="word-break: break-all;">
        <a href="{{ route('wedding.enter', ['token' => $guest->token], absolute: true) }}">{{ route('wedding.enter', ['token' => $guest->token], absolute: true) }}</a>
    </p>
    <p style="margin-top: 1.5rem; color: #555; font-size: 0.9rem;">{{ __('If you already responded, you can ignore this message.') }}</p>
</body>
</html>
