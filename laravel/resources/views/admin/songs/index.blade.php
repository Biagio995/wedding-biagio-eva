<x-layouts.admin
    :page-title="__('DJ song suggestions') . ' — ' . config('app.name', 'Wedding')"
    css-page="page-songs"
>
    @include('partials.locale-switcher')
    <p class="toolbar">
        <a href="{{ route('admin.rsvp.dashboard') }}">{{ __('RSVP dashboard') }}</a>
        ·
        <a href="{{ route('admin.guests.index') }}">{{ __('Guest list') }}</a>
        ·
        <a href="{{ route('admin.seating.index') }}">{{ __('Seating chart') }}</a>
        ·
        <a href="{{ route('admin.registry.index') }}">{{ __('Gift list') }}</a>
        ·
        <a href="{{ route('admin.photos.index') }}">{{ __('Photo moderation') }}</a>
        ·
        <a href="{{ route('admin.audit.index') }}">{{ __('Audit log') }}</a>
        ·
        <form method="post" action="{{ route('admin.logout') }}" style="display:inline;">
            @csrf
            <button type="submit" class="link">{{ __('Sign out') }}</button>
        </form>
    </p>

    <h1>{{ __('DJ song suggestions') }}</h1>
    <p class="sub">{{ __('Songs guests want to hear at the reception.') }}</p>

    @if (session('admin_success'))
        <div class="flash ok" role="status" aria-live="polite">{{ session('admin_success') }}</div>
    @endif

    <p class="cta">
        <strong>{{ $songs->count() }}</strong> {{ trans_choice('{1} suggestion|[2,*] suggestions', $songs->count()) }}
        @if ($songs->isNotEmpty())
            ·
            <a href="{{ route('admin.songs.export') }}">{{ __('Export as CSV') }}</a>
        @endif
    </p>

    @if ($songs->isEmpty())
        <p class="song-empty">{{ __('No song suggestions yet.') }}</p>
    @else
        <div class="table-wrap songs-table">
            <table>
                <thead>
                    <tr>
                        <th>{{ __('When') }}</th>
                        <th>{{ __('Submitted by') }}</th>
                        <th>{{ __('Song title') }}</th>
                        <th>{{ __('Artist') }}</th>
                        <th>{{ __('Note') }}</th>
                        <th class="song-actions-col">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($songs as $song)
                        <tr>
                            <td>
                                <time datetime="{{ $song->created_at?->toIso8601String() }}">
                                    {{ $song->created_at?->format('Y-m-d H:i') }}
                                </time>
                            </td>
                            <td>{{ $song->displayAuthor() }}</td>
                            <td><strong>{{ $song->title }}</strong></td>
                            <td>{{ $song->artist ?? '—' }}</td>
                            <td class="song-note">{{ $song->notes ?? '—' }}</td>
                            <td>
                                <form method="post" action="{{ route('admin.songs.destroy', $song) }}"
                                    onsubmit="return confirm(@json(__('Delete this song suggestion?')));">
                                    @csrf
                                    @method('delete')
                                    <button type="submit" class="btn btn--ghost">{{ __('Delete') }}</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</x-layouts.admin>
