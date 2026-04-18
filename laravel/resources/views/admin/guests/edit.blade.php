<x-layouts.admin
    :page-title="__('Edit guest') . ' — ' . config('app.name', 'Wedding')"
    css-page="page-create"
>
    @include('partials.locale-switcher')
    <p class="toolbar">
        <a href="{{ route('admin.rsvp.dashboard') }}">{{ __('RSVP dashboard') }}</a>
        ·
        <a href="{{ route('admin.guests.index', ['rsvp' => $filter]) }}">{{ __('Guest list') }}</a>
        ·
        <a href="{{ route('admin.guests.create') }}">{{ __('Add guest') }}</a>
        ·
        <a href="{{ route('admin.registry.index') }}">{{ __('Gift list') }}</a>
        ·
        <form method="post" action="{{ route('admin.logout') }}" style="display:inline;">
            @csrf
            <button type="submit" class="link" style="padding:0;font:inherit;">{{ __('Sign out') }}</button>
        </form>
    </p>
    <h1>{{ __('Edit guest') }}</h1>
    <p class="sub">{{ __('Update invitation details and RSVP.') }}</p>

    <div class="card">
        <form method="post" action="{{ route('admin.guests.update', $guest) }}">
            @csrf
            @method('PUT')
            <input type="hidden" name="return_rsvp" value="{{ $filter }}">

            <label for="name">{{ __('Name') }}</label>
            <input type="text" id="name" name="name" value="{{ old('name', $guest->name) }}" required maxlength="255" autocomplete="name" autofocus>
            @error('name')
                <p class="err">{{ $message }}</p>
            @enderror

            <label for="email">{{ __('Email') }} <span class="hint">({{ __('optional') }})</span></label>
            <input type="email" id="email" name="email" value="{{ old('email', $guest->email) }}" maxlength="255" autocomplete="email">
            @error('email')
                <p class="err">{{ $message }}</p>
            @enderror

            <label for="token">{{ __('Invitation token') }}</label>
            <input type="text" id="token" name="token" value="{{ old('token', $guest->token) }}" maxlength="64" pattern="[A-Za-z0-9_-]+" inputmode="text" autocomplete="off" required>
            @error('token')
                <p class="err">{{ $message }}</p>
            @enderror

            <h2 style="margin-top:1.25rem;font-size:1.05rem;font-weight:600;">{{ __('RSVP') }}</h2>

            <label for="rsvp_status">{{ __('Status') }}</label>
            <select id="rsvp_status" name="rsvp_status">
                <option value="" @selected(old('rsvp_status', $guest->rsvp_status) === null || old('rsvp_status', $guest->rsvp_status) === '')>{{ __('Awaiting reply') }}</option>
                <option value="yes" @selected(old('rsvp_status', $guest->rsvp_status) === 'yes')>{{ __('Yes') }}</option>
                <option value="no" @selected(old('rsvp_status', $guest->rsvp_status) === 'no')>{{ __('No') }}</option>
            </select>
            @error('rsvp_status')
                <p class="err">{{ $message }}</p>
            @enderror

            <label for="guests_count">{{ __('Guests') }}</label>
            <input
                type="number"
                id="guests_count"
                name="guests_count"
                value="{{ old('guests_count', $guest->guests_count) }}"
                min="1"
                max="500"
            >
            @error('guests_count')
                <p class="err">{{ $message }}</p>
            @enderror

            @php
                $companionsOld = old('companion_names');
                if (is_array($companionsOld)) {
                    $companionsValue = implode("\n", array_map(static fn ($n) => is_string($n) ? $n : '', $companionsOld));
                } elseif (is_string($companionsOld)) {
                    $companionsValue = $companionsOld;
                } else {
                    $storedCompanions = is_array($guest->companion_names) ? $guest->companion_names : [];
                    $companionsValue = implode("\n", array_filter(array_map(
                        static fn ($n) => is_string($n) ? trim($n) : '',
                        $storedCompanions,
                    ), static fn (string $n): bool => $n !== ''));
                }
            @endphp
            <label for="companion_names">{{ __('Companion names') }}</label>
            <textarea
                id="companion_names"
                name="companion_names"
                rows="3"
                placeholder="{{ __('One name per line') }}"
            >{{ $companionsValue }}</textarea>
            <p class="hint" style="margin-top:-0.5rem;">{{ __('One name per line. Used for place cards and seating.') }}</p>
            @error('companion_names')
                <p class="err">{{ $message }}</p>
            @enderror
            @error('companion_names.*')
                <p class="err">{{ $message }}</p>
            @enderror

            <label for="notes">{{ __('Your notes') }}</label>
            <textarea id="notes" name="notes" rows="3" maxlength="5000">{{ old('notes', $guest->notes) }}</textarea>
            @error('notes')
                <p class="err">{{ $message }}</p>
            @enderror

            <div class="actions">
                <button type="submit">{{ __('Save') }}</button>
                <a href="{{ route('admin.guests.index', ['rsvp' => $filter]) }}">{{ __('Cancel') }}</a>
            </div>
        </form>
    </div>

    <div class="card" style="margin-top:1rem;">
        <p class="sub" style="margin-bottom:0.75rem;">{{ __('Invitation link') }}</p>
        <p><a href="{{ route('wedding.enter', ['token' => $guest->token]) }}">{{ route('wedding.enter', ['token' => $guest->token]) }}</a></p>
        <p style="margin-top:0.75rem;">
            <a href="{{ route('admin.guests.qr', $guest) }}" target="_blank" rel="noopener">{{ __('View QR') }}</a>
            ·
            <a href="{{ route('admin.guests.qr', ['guest' => $guest, 'download' => 1]) }}">{{ __('Download QR (PNG)') }}</a>
        </p>
    </div>

    <script>
        (function () {
            var sel = document.getElementById('rsvp_status');
            var count = document.getElementById('guests_count');
            var companions = document.getElementById('companion_names');
            if (!sel || !count) return;
            function sync() {
                var y = sel.value === 'yes';
                count.disabled = !y;
                if (y && (!count.value || count.value === '0')) {
                    count.value = '1';
                }
                if (!y) {
                    count.value = '';
                    if (companions) {
                        companions.value = '';
                        companions.disabled = true;
                    }
                } else if (companions) {
                    companions.disabled = false;
                }
            }
            sel.addEventListener('change', sync);
            sync();
        })();
    </script>
</x-layouts.admin>
