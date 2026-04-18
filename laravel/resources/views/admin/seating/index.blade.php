<x-layouts.admin
    :page-title="__('Seating chart') . ' — ' . config('app.name', 'Wedding')"
    css-page="page-seating"
>
    @include('partials.locale-switcher')
    <p class="toolbar">
        <a href="{{ route('admin.rsvp.dashboard') }}">{{ __('RSVP dashboard') }}</a>
        ·
        <a href="{{ route('admin.guests.index') }}">{{ __('Guest list') }}</a>
        ·
        <a href="{{ route('admin.guests.create') }}">{{ __('Add guest') }}</a>
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

    <h1>{{ __('Seating chart') }}</h1>
    <p class="sub">{{ __('Create tables and assign guests. Capacity counts main guest + their companions.') }}</p>

    @if (session('status'))
        <p class="ok" role="status">{{ session('status') }}</p>
    @endif

    <div class="seating-create">
        <h2 class="h2" style="margin-bottom:0.75rem;">{{ __('Add table') }}</h2>
        <form method="post" action="{{ route('admin.seating.store') }}">
            @csrf
            <div>
                <label for="label">{{ __('Label') }}</label>
                <input type="text" id="label" name="label" maxlength="120" required value="{{ old('label') }}">
                @error('label')<p class="err">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="capacity">{{ __('Capacity') }}</label>
                <input type="number" id="capacity" name="capacity" min="1" max="500" value="{{ old('capacity') }}">
                @error('capacity')<p class="err">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="sort_order">{{ __('Sort order') }}</label>
                <input type="number" id="sort_order" name="sort_order" min="0" max="65535" value="{{ old('sort_order') }}">
                @error('sort_order')<p class="err">{{ $message }}</p>@enderror
            </div>
            <button type="submit">{{ __('Add table') }}</button>
        </form>
    </div>

    @if ($tables->isEmpty())
        <p class="empty">{{ __('No tables yet. Add the first one above.') }}</p>
    @else
        <div class="seating-grid">
            @foreach ($tables as $table)
                @php
                    $occupied = $table->occupiedSeats();
                    $over = $table->capacity !== null && $occupied > $table->capacity;
                @endphp
                <div class="seating-table">
                    <div class="seating-table__head">
                        <h3 class="seating-table__title">{{ $table->label }}</h3>
                        <span class="seating-table__meta {{ $over ? 'seating-table__capacity--over' : '' }}">
                            @if ($table->capacity !== null)
                                {{ __(':occupied / :cap seats', ['occupied' => $occupied, 'cap' => $table->capacity]) }}
                            @else
                                {{ __(':occupied seats', ['occupied' => $occupied]) }}
                            @endif
                        </span>
                    </div>
                    @if ($table->notes)
                        <p class="seating-table__notes">{{ $table->notes }}</p>
                    @endif

                    <ul class="seating-table__guests">
                        @forelse ($table->guests as $guest)
                            <li class="seating-table__guest">
                                <span class="seating-table__guest-name">
                                    <span>{{ $guest->name }}</span>
                                    @php
                                        $count = $guest->rsvp_status === 'yes' && is_int($guest->guests_count) && $guest->guests_count > 0
                                            ? $guest->guests_count : 1;
                                    @endphp
                                    <span class="seating-table__guest-count">
                                        @if ($guest->rsvp_status === 'no')
                                            {{ __('Declined') }}
                                        @elseif ($guest->rsvp_status === 'yes')
                                            {{ __(':count seats', ['count' => $count]) }}
                                        @else
                                            {{ __('Pending') }}
                                        @endif
                                    </span>
                                </span>
                                <form method="post" action="{{ route('admin.seating.unassign', $guest) }}">
                                    @csrf
                                    <button type="submit" class="link" aria-label="{{ __('Remove from table') }}" title="{{ __('Remove from table') }}">×</button>
                                </form>
                            </li>
                        @empty
                            <li class="empty">{{ __('No guests assigned yet.') }}</li>
                        @endforelse
                    </ul>

                    @if ($unassigned->isNotEmpty())
                        <form class="seating-table__assign" method="post" action="{{ route('admin.seating.assign', $table) }}">
                            @csrf
                            <label for="guest-for-{{ $table->id }}" hidden>{{ __('Assign guest') }}</label>
                            <select id="guest-for-{{ $table->id }}" name="guest_id" required>
                                <option value="">{{ __('— pick a guest —') }}</option>
                                @foreach ($unassigned as $g)
                                    <option value="{{ $g->id }}">{{ $g->name }}</option>
                                @endforeach
                            </select>
                            <button type="submit">{{ __('Assign') }}</button>
                        </form>
                    @endif

                    <div class="seating-table__footer">
                        <a href="{{ route('admin.seating.edit', $table) }}">{{ __('Edit') }}</a>
                        <form method="post" action="{{ route('admin.seating.destroy', $table) }}"
                            onsubmit="return confirm(@json(__('Delete this table? Guests will become unassigned.')));"
                            style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="link" style="color:#b4593f;">{{ __('Delete') }}</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <section class="unassigned">
        <h2 class="h2" style="margin-bottom:0.75rem;">{{ __('Unassigned guests') }} ({{ $unassigned->count() }})</h2>
        @if ($unassigned->isEmpty())
            <p class="empty">{{ __('Everyone has a table.') }}</p>
        @else
            <ul>
                @foreach ($unassigned as $guest)
                    <li>
                        {{ $guest->name }}
                        @if ($guest->rsvp_status === 'yes' && $guest->guests_count)
                            <span class="seating-table__guest-count">— {{ __(':count seats', ['count' => (int) $guest->guests_count]) }}</span>
                        @elseif ($guest->rsvp_status === null)
                            <span class="seating-table__guest-count">— {{ __('Pending') }}</span>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif
    </section>
</x-layouts.admin>
