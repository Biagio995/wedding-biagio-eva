<x-layouts.admin
    :page-title="__('Add guest') . ' — ' . config('app.name', 'Wedding')"
    css-page="page-create"
>
    @include('partials.locale-switcher')
    <p class="toolbar">
        <a href="{{ route('admin.rsvp.dashboard') }}">{{ __('RSVP dashboard') }}</a>
        ·
        <a href="{{ route('admin.guests.index') }}">{{ __('Guest list') }}</a>
        ·
        <a href="{{ route('admin.guests.import') }}">{{ __('Import from CSV') }}</a>
        ·
        <a href="{{ route('admin.registry.index') }}">{{ __('Gift list') }}</a>
        ·
        <a href="{{ route('admin.photos.index') }}">{{ __('Photo moderation') }}</a>
        ·
        <form method="post" action="{{ route('admin.logout') }}" style="display:inline;">
            @csrf
            <button type="submit" class="link" style="padding:0;font:inherit;">{{ __('Sign out') }}</button>
        </form>
    </p>
    <h1>{{ __('Add guest') }}</h1>
    <p class="sub">{{ __('Create an invitation record. A personal link is generated automatically.') }}</p>

    @if (session('status'))
        <div class="ok" role="status">
            <p>{{ session('status') }}</p>
            @if (session('created_guest'))
                @php($g = session('created_guest'))
                <p><strong>{{ e($g['name']) }}</strong></p>
                <p><a href="{{ $g['invite_url'] }}">{{ $g['invite_url'] }}</a></p>
                @if (!empty($g['id']))
                    <div class="qr-block">
                        <p>{{ __('Invitation QR code') }}</p>
                        <img
                            src="{{ route('admin.guests.qr', ['guest' => $g['id']]) }}"
                            width="160"
                            height="160"
                            alt=""
                        >
                        <p>
                            <a href="{{ route('admin.guests.qr', ['guest' => $g['id'], 'download' => 1]) }}">{{ __('Download QR (PNG)') }}</a>
                        </p>
                    </div>
                @endif
            @endif
        </div>
    @endif

    <div class="card">
        <form method="post" action="{{ route('admin.guests.store') }}">
            @csrf
            <label for="name">{{ __('Name') }}</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required maxlength="255" autocomplete="name" autofocus>
            @error('name')
                <p class="err">{{ $message }}</p>
            @enderror

            <label for="email">{{ __('Email') }} <span class="hint">({{ __('optional') }})</span></label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" maxlength="255" autocomplete="email">
            @error('email')
                <p class="err">{{ $message }}</p>
            @enderror

            <label for="token">{{ __('Invitation token') }} <span class="hint">({{ __('optional; leave empty for a random link') }})</span></label>
            <input type="text" id="token" name="token" value="{{ old('token') }}" maxlength="64" pattern="[A-Za-z0-9_-]*" inputmode="text" autocomplete="off">
            @error('token')
                <p class="err">{{ $message }}</p>
            @enderror

            <div class="actions">
                <button type="submit">{{ __('Create guest') }}</button>
            </div>
        </form>
    </div>
</x-layouts.admin>
