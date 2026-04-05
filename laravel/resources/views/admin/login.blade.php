<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#1a1a2e">
    <title>{{ __('Admin login') }} — {{ config('app.name', 'Wedding') }}</title>
    <style>
        :root {
            --bg: #0f0f14;
            --text: #f4f4f8;
            --muted: #9898a8;
            --accent: #c9a962;
            --radius: 12px;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100dvh;
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
            background: radial-gradient(ellipse 120% 80% at 50% -20%, #2a2235 0%, var(--bg) 55%);
            color: var(--text);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        .card {
            width: 100%;
            max-width: 22rem;
            padding: 1.5rem;
            background: rgba(255,255,255,0.04);
            border-radius: var(--radius);
            border: 1px solid rgba(255,255,255,0.08);
        }
        h1 { font-size: 1.15rem; margin: 0 0 1rem; font-weight: 600; }
        label { display: block; font-size: 0.85rem; color: var(--muted); margin-bottom: 0.35rem; }
        input[type="password"] {
            width: 100%;
            padding: 0.55rem 0.65rem;
            border-radius: 8px;
            border: 1px solid rgba(255,255,255,0.12);
            background: rgba(0,0,0,0.3);
            color: var(--text);
            font-size: 1rem;
            margin-bottom: 0.75rem;
        }
        button {
            width: 100%;
            padding: 0.55rem;
            border: none;
            border-radius: 8px;
            background: linear-gradient(145deg, #d4b56a, #a8893e);
            color: #1a1508;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
        }
        .err {
            color: #f0a0a0;
            font-size: 0.85rem;
            margin-bottom: 0.75rem;
        }
    </style>
</head>
<body>
    <div class="card">
        @include('partials.locale-switcher')
        <h1>{{ __('Admin login') }}</h1>
        @if ($errors->has('password'))
            <p class="err">{{ $errors->first('password') }}</p>
        @endif
        <form method="post" action="{{ route('admin.login') }}">
            @csrf
            <label for="password">{{ __('Password') }}</label>
            <input type="password" id="password" name="password" required autocomplete="current-password" autofocus>
            <button type="submit">{{ __('Sign in') }}</button>
        </form>
    </div>
</body>
</html>
