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
</x-layouts.admin>
