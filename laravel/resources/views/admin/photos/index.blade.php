<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#213555">
    <title>{{ __('Photo moderation') }} — {{ config('app.name', 'Wedding') }}</title>
    <style>
        :root {
            --bg: #213555;
            --text: #F5EFE7;
            --muted: #b8aea4;
            --accent: #D8C4B6;
            --radius: 12px;
            --ok: #7ecf9a;
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
        .wrap { max-width: 56rem; margin: 0 auto; }
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
        .filters {
            display: flex;
            flex-wrap: wrap;
            gap: 0.35rem 0.75rem;
            margin-bottom: 1rem;
            font-size: 0.88rem;
        }
        .filters a {
            color: var(--muted);
            text-decoration: none;
            padding: 0.25rem 0.5rem;
            border-radius: 6px;
        }
        .filters a:hover { color: var(--text); }
        .filters a.active {
            color: var(--accent);
            font-weight: 600;
            background: rgba(255,255,255,0.06);
        }
        .ok {
            padding: 0.65rem 0.85rem;
            border-radius: 8px;
            background: rgba(80, 160, 120, 0.2);
            border: 1px solid rgba(120, 200, 160, 0.35);
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }
        .err {
            padding: 0.65rem 0.85rem;
            border-radius: 8px;
            background: rgba(180, 80, 80, 0.15);
            border: 1px solid rgba(220, 120, 120, 0.35);
            margin-bottom: 1rem;
            font-size: 0.9rem;
            color: #f0b0b0;
        }
        .bulk { margin-bottom: 1rem; font-size: 0.9rem; }
        .bulk a { color: var(--accent); font-weight: 600; }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(min(160px, 100%), 1fr));
            gap: 0.75rem;
        }
        .card {
            background: rgba(255,255,255,0.04);
            border-radius: var(--radius);
            border: 1px solid rgba(255,255,255,0.08);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        .card img {
            width: 100%;
            aspect-ratio: 1;
            object-fit: cover;
            display: block;
            background: rgba(0,0,0,0.3);
        }
        .card .meta {
            padding: 0.5rem 0.65rem;
            font-size: 0.78rem;
            color: var(--muted);
            flex: 1;
        }
        .card .actions {
            padding: 0 0.65rem 0.65rem;
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
        }
        .badge {
            display: inline-block;
            padding: 0.15rem 0.45rem;
            border-radius: 6px;
            font-size: 0.72rem;
            font-weight: 600;
            background: rgba(80, 160, 120, 0.25);
            color: var(--ok);
        }
        .btn-approve {
            width: 100%;
            padding: 0.45rem;
            border: none;
            border-radius: 8px;
            background: linear-gradient(145deg, #F5EFE7, #D8C4B6);
            color: #213555;
            font-weight: 600;
            font-size: 0.85rem;
            cursor: pointer;
        }
        .btn-delete {
            width: 100%;
            padding: 0.45rem;
            border: 1px solid rgba(220, 120, 120, 0.55);
            border-radius: 8px;
            background: rgba(180, 60, 60, 0.2);
            color: #f0b0b0;
            font-weight: 600;
            font-size: 0.85rem;
            cursor: pointer;
        }
        .empty {
            text-align: center;
            color: var(--muted);
            padding: 2rem 1rem;
            font-size: 0.9rem;
        }
        .pagination { margin-top: 1.25rem; font-size: 0.88rem; }
        .pagination a { color: var(--accent); }
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
            <a href="{{ route('gallery.album') }}" target="_blank" rel="noopener">{{ __('Public album') }}</a>
            ·
            <form method="post" action="{{ route('admin.logout') }}" style="display:inline;">
                @csrf
                <button type="submit" class="link">{{ __('Sign out') }}</button>
            </form>
        </p>
        <h1>{{ __('Photo moderation') }}</h1>
        <p class="sub">{{ __('Approve or delete uploads. Deleted files are removed from the server.') }}</p>

        @if (session('status'))
            <p class="ok" role="status">{{ session('status') }}</p>
        @endif
        @if (session('error'))
            <p class="err" role="alert">{{ session('error') }}</p>
        @endif

        <p class="bulk">
            <a href="{{ route('admin.photos.archive') }}">{{ __('Download all photos (ZIP)') }}</a>
        </p>

        <nav class="filters" aria-label="{{ __('Filter') }}">
            <a href="{{ route('admin.photos.index', ['status' => 'pending']) }}" class="{{ $filter === 'pending' ? 'active' : '' }}">{{ __('Pending') }}</a>
            <a href="{{ route('admin.photos.index', ['status' => 'approved']) }}" class="{{ $filter === 'approved' ? 'active' : '' }}">{{ __('Approved') }}</a>
            <a href="{{ route('admin.photos.index', ['status' => 'all']) }}" class="{{ $filter === 'all' ? 'active' : '' }}">{{ __('All') }}</a>
        </nav>

        @if ($photos->isEmpty())
            <p class="empty">{{ __('No photos in this view.') }}</p>
        @else
            <div class="grid">
                @foreach ($photos as $photo)
                    <article class="card">
                        <img
                            src="{{ Storage::disk('public')->url($photo->file_path) }}"
                            alt="{{ $photo->original_filename ?: __('Photo') }}"
                            loading="lazy"
                            width="256"
                            height="256"
                        >
                        <div class="meta">
                            @if ($photo->guest)
                                {{ $photo->guest->name }}
                            @else
                                {{ __('Anonymous') }}
                            @endif
                            · {{ $photo->created_at?->timezone(config('app.timezone'))->format('Y-m-d H:i') }}
                            @if ($photo->approved)
                                <span class="badge">{{ __('Approved') }}</span>
                            @endif
                        </div>
                        <div class="actions">
                            @if (! $photo->approved)
                                <form method="post" action="{{ route('admin.photos.approve', ['photo' => $photo->id]) }}">
                                    @csrf
                                    <button type="submit" class="btn-approve">{{ __('Approve') }}</button>
                                </form>
                            @endif
                            <form
                                method="post"
                                action="{{ route('admin.photos.destroy', ['photo' => $photo->id]) }}"
                                onsubmit="return confirm({{ json_encode(__('Remove this photo permanently?')) }});"
                            >
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-delete">{{ __('Delete') }}</button>
                            </form>
                        </div>
                    </article>
                @endforeach
            </div>

            @if ($photos->hasPages())
                <div class="pagination">{{ $photos->links() }}</div>
            @endif
        @endif
    </div>
</body>
</html>
