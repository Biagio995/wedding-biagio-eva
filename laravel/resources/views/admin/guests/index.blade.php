<x-layouts.admin
    :page-title="__('Guest list') . ' — ' . config('app.name', 'Wedding')"
    css-page="page-guest-list"
>
    @include('partials.locale-switcher')
    <p class="toolbar">
        <a href="{{ route('admin.rsvp.dashboard') }}">{{ __('RSVP dashboard') }}</a>
        ·
        <a href="{{ route('admin.guests.create') }}">{{ __('Add guest') }}</a>
        ·
        <a href="{{ route('admin.guests.import') }}">{{ __('Import from CSV') }}</a>
        ·
        <a href="{{ route('admin.registry.index') }}">{{ __('Gift list') }}</a>
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

    @if (session('status'))
        <p class="ok" role="status">{{ session('status') }}</p>
    @endif

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
                    <th class="actions-col">{{ __('Actions') }}</th>
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
                        <td class="actions-col">
                            <div class="guest-actions" role="group" aria-label="{{ __('Actions') }}">
                                <a
                                    href="{{ route('admin.guests.edit', ['guest' => $guest, 'rsvp' => $filter]) }}"
                                    class="icon-btn"
                                    title="{{ __('Edit guest') }}"
                                    aria-label="{{ __('Edit guest') }}"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
                                </a>
                                <form
                                    method="post"
                                    action="{{ route('admin.guests.destroy', ['guest' => $guest, 'rsvp' => $filter]) }}"
                                    class="inline-form"
                                    onsubmit="return confirm(@json(__('Delete this guest? This cannot be undone.')));"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        type="submit"
                                        class="icon-btn icon-btn--danger"
                                        title="{{ __('Delete') }}"
                                        aria-label="{{ __('Delete') }}"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">{{ __('No guests match this filter.') }}</td>
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
</x-layouts.admin>
