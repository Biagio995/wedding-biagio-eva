<x-layouts.admin
    :page-title="__('Edit table') . ' — ' . config('app.name', 'Wedding')"
    css-page="page-seating"
>
    @include('partials.locale-switcher')
    <p class="toolbar">
        <a href="{{ route('admin.seating.index') }}">{{ __('Seating chart') }}</a>
        ·
        <a href="{{ route('admin.rsvp.dashboard') }}">{{ __('RSVP dashboard') }}</a>
        ·
        <a href="{{ route('admin.guests.index') }}">{{ __('Guest list') }}</a>
        ·
        <form method="post" action="{{ route('admin.logout') }}" style="display:inline;">
            @csrf
            <button type="submit" class="link">{{ __('Sign out') }}</button>
        </form>
    </p>

    <h1>{{ __('Edit table') }}</h1>
    <p class="sub">{{ $seatingTable->label }}</p>

    <div class="seating-create">
        <form method="post" action="{{ route('admin.seating.update', $seatingTable) }}" class="stack">
            @csrf
            @method('PUT')
            <div>
                <label for="label">{{ __('Label') }}</label>
                <input type="text" id="label" name="label" maxlength="120" required value="{{ old('label', $seatingTable->label) }}">
                @error('label')<p class="err">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="capacity">{{ __('Capacity') }}</label>
                <input type="number" id="capacity" name="capacity" min="1" max="500" value="{{ old('capacity', $seatingTable->capacity) }}">
                @error('capacity')<p class="err">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="sort_order">{{ __('Sort order') }}</label>
                <input type="number" id="sort_order" name="sort_order" min="0" max="65535" required value="{{ old('sort_order', $seatingTable->sort_order) }}">
                @error('sort_order')<p class="err">{{ $message }}</p>@enderror
            </div>
            <div style="grid-column:1/-1;">
                <label for="notes">{{ __('Notes') }}</label>
                <textarea id="notes" name="notes" rows="3" maxlength="2000" style="width:100%;padding:0.5rem;border-radius:8px;border:1px solid rgba(58,50,42,0.12);background:#fffcf9;font-family:inherit;">{{ old('notes', $seatingTable->notes) }}</textarea>
                @error('notes')<p class="err">{{ $message }}</p>@enderror
            </div>
            <button type="submit">{{ __('Save') }}</button>
        </form>
    </div>

    <p style="margin-top:1rem;">
        <a href="{{ route('admin.seating.index') }}">← {{ __('Back to seating chart') }}</a>
    </p>
</x-layouts.admin>
