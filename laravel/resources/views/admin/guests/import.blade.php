<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#1a1a2e">
    <title>{{ __('Import guests (CSV)') }} — {{ config('app.name', 'Wedding') }}</title>
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
            line-height: 1.45;
            padding: 1rem;
        }
        .wrap { max-width: 32rem; margin: 0 auto; }
        h1 { font-size: 1.25rem; margin: 0 0 0.5rem; font-weight: 600; }
        .sub { color: var(--muted); font-size: 0.9rem; margin-bottom: 1rem; }
        .card {
            padding: 1.25rem;
            background: rgba(255,255,255,0.04);
            border-radius: var(--radius);
            border: 1px solid rgba(255,255,255,0.08);
        }
        label { display: block; font-size: 0.85rem; color: var(--muted); margin-bottom: 0.35rem; }
        input[type="file"] {
            width: 100%;
            font-size: 0.9rem;
            margin-bottom: 1rem;
            color: var(--text);
        }
        button[type="submit"] {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 8px;
            background: linear-gradient(145deg, #d4b56a, #a8893e);
            color: #1a1508;
            font-weight: 600;
            cursor: pointer;
        }
        button.link {
            background: transparent;
            color: var(--accent);
            font-weight: 500;
            text-decoration: underline;
        }
        .toolbar { margin-bottom: 1rem; font-size: 0.88rem; }
        .toolbar a { color: var(--accent); }
        .toolbar form { display: inline; }
        pre.format {
            font-size: 0.78rem;
            background: rgba(0,0,0,0.35);
            padding: 0.65rem;
            border-radius: 8px;
            overflow-x: auto;
            color: var(--muted);
            margin: 0 0 1rem;
        }
        .ok {
            padding: 0.75rem;
            border-radius: 8px;
            background: rgba(80, 160, 120, 0.2);
            border: 1px solid rgba(120, 200, 160, 0.35);
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }
        .warn {
            padding: 0.75rem;
            border-radius: 8px;
            background: rgba(200, 160, 80, 0.15);
            border: 1px solid rgba(200, 180, 120, 0.35);
            margin-bottom: 1rem;
            font-size: 0.88rem;
        }
        .warn ul { margin: 0.5rem 0 0; padding-left: 1.1rem; }
        .err { color: #f0a0a0; font-size: 0.85rem; margin-bottom: 0.75rem; }
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
            <a href="{{ route('admin.guests.create') }}">{{ __('Add guest') }}</a>
            ·
            <a href="{{ route('admin.photos.index') }}">{{ __('Photo moderation') }}</a>
            ·
            <form method="post" action="{{ route('admin.logout') }}" style="display:inline;">
                @csrf
                <button type="submit" class="link" style="padding:0;font:inherit;">{{ __('Sign out') }}</button>
            </form>
        </p>
        <h1>{{ __('Import guests (CSV)') }}</h1>
        <p class="sub">{{ __('First row must be the header. Columns: name (required), email and token (optional). Use comma or semicolon as separator.') }}</p>

        <pre class="format" aria-label="CSV example">name,email,token
Ada Lovelace,ada@example.com,
Bob,,custom-token-1</pre>

        @if (session('import_result'))
            @php($r = session('import_result'))
            @if (($r['created'] ?? 0) > 0)
                <div class="ok" role="status">
                    {{ trans_choice(':count guest created.|:count guests created.', $r['created'], ['count' => $r['created']]) }}
                </div>
            @endif
            @if (!empty($r['errors']))
                <div class="warn" role="alert">
                    <strong>{{ __('Some rows were skipped') }}</strong>
                    <ul>
                        @foreach ($r['errors'] as $e)
                            <li>{{ __('Line :line: :message', ['line' => $e['line'], 'message' => $e['message']]) }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        @endif

        <div class="card">
            <form method="post" action="{{ route('admin.guests.import.store') }}" enctype="multipart/form-data">
                @csrf
                <label for="file">{{ __('CSV file') }}</label>
                <input type="file" id="file" name="file" accept=".csv,.txt,text/csv,text/plain" required>
                @error('file')
                    <p class="err">{{ $message }}</p>
                @enderror
                <button type="submit">{{ __('Import') }}</button>
            </form>
        </div>
    </div>
</body>
</html>
