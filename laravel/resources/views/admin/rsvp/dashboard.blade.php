<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#213555">
    <title>{{ __('RSVP dashboard') }} — {{ config('app.name', 'Wedding') }}</title>
    <style>
        :root {
            --bg: #213555;
            --text: #F5EFE7;
            --muted: #b8aea4;
            --accent: #D8C4B6;
            --radius: 12px;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100dvh;
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
            background: radial-gradient(ellipse 120% 80% at 50% -20%, #3E5879 0%, var(--bg) 55%);
            color: var(--text);
            line-height: 1.45;
            padding: 1rem;
        }
        .wrap { max-width: 52rem; margin: 0 auto; }
        h1 { font-size: 1.25rem; margin: 0 0 0.35rem; font-weight: 600; }
        .sub { color: var(--muted); font-size: 0.9rem; margin-bottom: 1rem; }
        .toolbar { margin-bottom: 1rem; font-size: 0.88rem; }
        .toolbar a { color: var(--accent); }
        .toolbar form { display: inline; }
        button.link {
            background: transparent;
            border: none;
            color: var(--accent);
            font-weight: 500;
            text-decoration: underline;
            font-size: inherit;
            cursor: pointer;
            padding: 0;
            font-family: inherit;
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(9rem, 1fr));
            gap: 0.65rem;
            margin-bottom: 1.25rem;
        }
        .stat {
            padding: 0.75rem 0.85rem;
            background: rgba(255,255,255,0.04);
            border-radius: var(--radius);
            border: 1px solid rgba(255,255,255,0.08);
        }
        .stat .n { font-size: 1.35rem; font-weight: 700; letter-spacing: -0.02em; }
        .stat .l { font-size: 0.78rem; color: var(--muted); margin-top: 0.2rem; }
        .stat.highlight .n { color: var(--accent); }
        .cta { margin-top: 1.25rem; font-size: 0.95rem; }
        .cta a { color: var(--accent); font-weight: 600; }
    </style>
</head>
<body>
    <div class="wrap">
        @include('partials.locale-switcher')
        <p class="toolbar">
            <a href="{{ route('admin.guests.index') }}">{{ __('Guest list') }}</a>
            ·
            <a href="{{ route('admin.photos.index') }}">{{ __('Photo moderation') }}</a>
            ·
            <a href="{{ route('admin.guests.create') }}">{{ __('Add guest') }}</a>
            ·
            <a href="{{ route('admin.guests.import') }}">{{ __('Import from CSV') }}</a>
            ·
            <form method="post" action="{{ route('admin.logout') }}" style="display:inline;">
                @csrf
                <button type="submit" class="link">{{ __('Sign out') }}</button>
            </form>
        </p>
        <h1>{{ __('RSVP dashboard') }}</h1>
        <p class="sub">{{ __('Overview of responses and expected attendance.') }}</p>

        <div class="stats">
            <div class="stat">
                <div class="n">{{ $stats['total_guests'] }}</div>
                <div class="l">{{ __('Invitations') }}</div>
            </div>
            <div class="stat highlight">
                <div class="n">{{ $stats['attending_people'] }}</div>
                <div class="l">{{ __('People attending') }}</div>
            </div>
            <div class="stat">
                <div class="n">{{ $stats['yes'] }}</div>
                <div class="l">{{ __('Attending (invitations)') }}</div>
            </div>
            <div class="stat">
                <div class="n">{{ $stats['no'] }}</div>
                <div class="l">{{ __('Declined') }}</div>
            </div>
            <div class="stat">
                <div class="n">{{ $stats['pending'] }}</div>
                <div class="l">{{ __('Awaiting reply') }}</div>
            </div>
            <div class="stat">
                <div class="n">{{ $stats['responded'] }}</div>
                <div class="l">{{ __('Replied') }}</div>
            </div>
        </div>

        <p class="cta">
            <a href="{{ route('admin.guests.index') }}">{{ __('View guest list with RSVP status') }}</a>
        </p>
    </div>
</body>
</html>
