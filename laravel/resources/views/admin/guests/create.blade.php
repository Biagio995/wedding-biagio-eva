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
            <button type="submit" class="link">{{ __('Sign out') }}</button>
        </form>
    </p>
    <h1>{{ __('Add guest') }}</h1>
    <p class="sub">{{ __('Create an invitation record. A personal link is generated automatically.') }}</p>

    @if (session('status'))
        <div class="ok" role="status">
            <p>{{ session('status') }}</p>
            @php($g = $createdGuest ?? session('created_guest'))
            @if (is_array($g))
                <p><strong>{{ e($g['name']) }}</strong></p>
                <p><a href="{{ $g['invite_url'] }}">{{ $g['invite_url'] }}</a></p>
                @if (!empty($g['email']))
                    @if (!empty($g['invitation_email_sent']))
                        <p>{{ __('Invitation email sent to :email.', ['email' => $g['email']]) }}</p>
                    @else
                        <p class="err">{{ __('Invitation email could not be sent.') }}</p>
                    @endif
                @endif
                @if (!empty($createdGuestQrSvg))
                    @include('admin.guests.partials.qr-block', [
                        'guest' => $g['id'],
                        'qrSvg' => $createdGuestQrSvg,
                    ])
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

            @include('admin.guests.partials.locale-field')

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
