<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#1a1a2e">
    <title>{{ __('Guest list') }} — {{ config('app.name', 'Wedding') }}</title>
    <style>
        :root {
            --bg: #0f0f14;
            --text: #f4f4f8;
            --muted: #9898a8;
            --accent: #c9a962;
            --radius: 12px;
            --ok: #7ecf9a;
            --no: #e89898;
            --pending: #c9c3a8;
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
        .table-wrap {
            overflow-x: auto;
            border-radius: var(--radius);
            border: 1px solid rgba(255,255,255,0.08);
            background: rgba(255,255,255,0.03);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.88rem;
        }
        th, td {
            padding: 0.5rem 0.65rem;
            text-align: left;
            border-bottom: 1px solid rgba(255,255,255,0.06);
        }
        th { color: var(--muted); font-weight: 600; font-size: 0.8rem; }
        tr:last-child td { border-bottom: none; }
        .badge {
            display: inline-block;
            padding: 0.15rem 0.45rem;
            border-radius: 6px;
            font-size: 0.78rem;
            font-weight: 600;
        }
        .badge-yes { background: rgba(80, 160, 120, 0.25); color: var(--ok); }
        .badge-no { background: rgba(200, 100, 100, 0.25); color: var(--no); }
        .badge-pending { background: rgba(200, 190, 140, 0.15); color: var(--pending); }
        .num { text-align: right; font-variant-numeric: tabular-nums; }
        .notes { color: var(--muted); max-width: 14rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .pagination { margin-top: 1rem; font-size: 0.88rem; }
        .pagination a { color: var(--accent); }
    </style>
</head>
<body>
    <div class="wrap">
        @include('partials.locale-switcher')
        <p class="toolbar">
            <a href="{{ route('admin.rsvp.dashboard') }}">{{ __('RSVP dashboard') }}</a>
            ·
            <a href="{{ route('admin.guests.create') }}">{{ __('Add guest') }}</a>
            ·
            <a href="{{ route('admin.guests.import') }}">{{ __('Import from CSV') }}</a>
            ·
            <a href="{{ route('admin.photos.index') }}">{{ __('Photo moderation') }}</a>
            ·
            <form method="post" action="{{ route('admin.logout') }}" style="display:inline;">
                @csrf
                <button type="submit" class="link">{{ __('Sign out') }}</button>
            </form>
        </p>
        <h1>{{ __('Guest list') }}</h1>
        <p class="sub">{{ __('Who has responded and RSVP status per invitation.') }}</p>

        <nav class="filters" aria-label="{{ __('Filter by RSVP') }}">
            <a href="{{ route('admin.guests.index', ['rsvp' => 'all']) }}" class="{{ $filter === 'all' ? 'active' : '' }}">{{ __('All') }}</a>
            <a href="{{ route('admin.guests.index', ['rsvp' => 'yes']) }}" class="{{ $filter === 'yes' ? 'active' : '' }}">{{ __('Attending') }}</a>
            <a href="{{ route('admin.guests.index', ['rsvp' => 'no']) }}" class="{{ $filter === 'no' ? 'active' : '' }}">{{ __('Declined') }}</a>
            <a href="{{ route('admin.guests.index', ['rsvp' => 'pending']) }}" class="{{ $filter === 'pending' ? 'active' : '' }}">{{ __('Awaiting reply') }}</a>
        </nav>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Email') }}</th>
                        <th>{{ __('RSVP') }}</th>
                        <th class="num">{{ __('Guests') }}</th>
                        <th>{{ __('Notes') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($guests as $guest)
                        <tr>
                            <td>{{ $guest->name }}</td>
                            <td>{{ $guest->email ?? '—' }}</td>
                            <td>
                                @if ($guest->rsvp_status === 'yes')
                                    <span class="badge badge-yes">{{ __('Yes') }}</span>
                                @elseif ($guest->rsvp_status === 'no')
                                    <span class="badge badge-no">{{ __('No') }}</span>
                                @else
                                    <span class="badge badge-pending">{{ __('Pending') }}</span>
                                @endif
                            </td>
                            <td class="num">
                                @if ($guest->rsvp_status === 'yes')
                                    {{ $guest->guests_count ?? '—' }}
                                @else
                                    —
                                @endif
                            </td>
                            <td class="notes" title="{{ $guest->notes }}">{{ $guest->notes ? Str::limit($guest->notes, 48) : '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">{{ __('No guests match this filter.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($guests->hasPages())
            <div class="pagination">
                {{ $guests->links() }}
            </div>
        @endif
    </div>
</body>
</html>
