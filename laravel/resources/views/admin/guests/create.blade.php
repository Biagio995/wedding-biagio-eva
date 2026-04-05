<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#213555">
    <title>{{ __('Add guest') }} — {{ config('app.name', 'Wedding') }}</title>
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
        .wrap { max-width: 28rem; margin: 0 auto; }
        h1 { font-size: 1.25rem; margin: 0 0 0.5rem; font-weight: 600; }
        .sub { color: var(--muted); font-size: 0.9rem; margin-bottom: 1.25rem; }
        .card {
            padding: 1.25rem;
            background: rgba(255,255,255,0.04);
            border-radius: var(--radius);
            border: 1px solid rgba(255,255,255,0.08);
        }
        label { display: block; font-size: 0.85rem; color: var(--muted); margin-bottom: 0.35rem; }
        input[type="text"], input[type="email"] {
            width: 100%;
            padding: 0.5rem 0.65rem;
            border-radius: 8px;
            border: 1px solid rgba(255,255,255,0.12);
            background: rgba(0,0,0,0.3);
            color: var(--text);
            font-size: 0.95rem;
            margin-bottom: 1rem;
        }
        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            align-items: center;
        }
        button[type="submit"] {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 8px;
            background: linear-gradient(145deg, #F5EFE7, #D8C4B6);
            color: #213555;
            font-weight: 600;
            cursor: pointer;
        }
        button.link {
            background: transparent;
            color: var(--accent);
            font-weight: 500;
            text-decoration: underline;
        }
        .ok {
            padding: 0.75rem;
            border-radius: 8px;
            background: rgba(80, 160, 120, 0.2);
            border: 1px solid rgba(120, 200, 160, 0.35);
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }
        .ok a { color: var(--accent); word-break: break-all; }
        .err { color: #f0a0a0; font-size: 0.85rem; margin-top: -0.5rem; margin-bottom: 0.75rem; }
        .hint { font-size: 0.8rem; color: var(--muted); margin-top: -0.5rem; margin-bottom: 1rem; }
        .toolbar { margin-bottom: 1rem; font-size: 0.88rem; }
        .toolbar form { display: inline; }
        .qr-block { margin-top: 0.75rem; }
        .qr-block img {
            display: block;
            width: 160px;
            height: auto;
            max-width: 100%;
            background: #fff;
            padding: 0.5rem;
            border-radius: 8px;
        }
        .qr-block a { font-size: 0.88rem; }
    </style>
</head>
<body>
    <div class="wrap">
        @include('partials.locale-switcher')
        <p class="toolbar">
            <a href="{{ route('admin.rsvp.dashboard') }}">{{ __('RSVP dashboard') }}</a>
            ·
            <a href="{{ route('admin.guests.index') }}">{{ __('Guest list') }}</a>
            ·
            <a href="{{ route('admin.guests.import') }}">{{ __('Import from CSV') }}</a>
            ·
            <a href="{{ route('admin.photos.index') }}">{{ __('Photo moderation') }}</a>
            ·
            <form method="post" action="{{ route('admin.logout') }}" style="display:inline;">
                @csrf
                <button type="submit" class="link" style="padding:0;font:inherit;">{{ __('Sign out') }}</button>
            </form>
        </p>
        <h1>{{ __('Add guest') }}</h1>
        <p class="sub">{{ __('Create an invitation record. A personal link is generated automatically.') }}</p>

        @if (session('status'))
            <div class="ok" role="status">
                <p>{{ session('status') }}</p>
                @if (session('created_guest'))
                    @php($g = session('created_guest'))
                    <p><strong>{{ e($g['name']) }}</strong></p>
                    <p><a href="{{ $g['invite_url'] }}">{{ $g['invite_url'] }}</a></p>
                    @if (!empty($g['id']))
                        <div class="qr-block">
                            <p>{{ __('Invitation QR code') }}</p>
                            <img
                                src="{{ route('admin.guests.qr', ['guest' => $g['id']]) }}"
                                width="160"
                                height="160"
                                alt=""
                            >
                            <p>
                                <a href="{{ route('admin.guests.qr', ['guest' => $g['id'], 'download' => 1]) }}">{{ __('Download QR (PNG)') }}</a>
                            </p>
                        </div>
                    @endif
                @endif
            </div>
        @endif

        <div class="card">
            <form method="post" action="{{ route('admin.guests.store') }}">
                @csrf
                <label for="name">{{ __('Name') }}</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required maxlength="255" autocomplete="name" autofocus>
                @error('name')
                    <p class="err">{{ $message }}</p>
                @enderror

                <label for="email">{{ __('Email') }} <span class="hint">({{ __('optional') }})</span></label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" maxlength="255" autocomplete="email">
                @error('email')
                    <p class="err">{{ $message }}</p>
                @enderror

                <label for="token">{{ __('Invitation token') }} <span class="hint">({{ __('optional; leave empty for a random link') }})</span></label>
                <input type="text" id="token" name="token" value="{{ old('token') }}" maxlength="64" pattern="[A-Za-z0-9_-]*" inputmode="text" autocomplete="off">
                @error('token')
                    <p class="err">{{ $message }}</p>
                @enderror

                <div class="actions">
                    <button type="submit">{{ __('Create guest') }}</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
