<x-layouts.admin
    :page-title="__('RSVP dashboard') . ' — ' . config('app.name', 'Wedding')"
    css-page="page-dashboard"
>
    @include('partials.locale-switcher')
    <p class="toolbar">
        <a href="{{ route('admin.guests.index') }}">{{ __('Guest list') }}</a>
        ·
        <a href="{{ route('admin.seating.index') }}">{{ __('Seating chart') }}</a>
        ·
        <a href="{{ route('admin.registry.index') }}">{{ __('Gift list') }}</a>
        ·
        <a href="{{ route('admin.photos.index') }}">{{ __('Photo moderation') }}</a>
        ·
        <a href="{{ route('admin.songs.index') }}">{{ __('DJ song suggestions') }}</a>
        ·
        <a href="{{ route('admin.guests.create') }}">{{ __('Add guest') }}</a>
        ·
        <a href="{{ route('admin.guests.import') }}">{{ __('Import from CSV') }}</a>
        ·
        <a href="{{ route('admin.audit.index') }}">{{ __('Audit log') }}</a>
        ·
        <form method="post" action="{{ route('admin.logout') }}" style="display:inline;">
            @csrf
            <button type="submit" class="link">{{ __('Sign out') }}</button>
        </form>
    </p>
    <h1>{{ __('RSVP dashboard') }}</h1>
    <p class="sub">{{ __('Overview of responses and expected attendance.') }}</p>

    <div class="stats">
        <div class="stat">
            <div class="n">{{ $stats['total_guests'] }}</div>
            <div class="l">{{ __('Invitations') }}</div>
        </div>
        <div class="stat highlight">
            <div class="n">{{ $stats['attending_people'] }}</div>
            <div class="l">{{ __('People attending') }}</div>
        </div>
        <div class="stat">
            <div class="n">{{ $stats['yes'] }}</div>
            <div class="l">{{ __('Attending (invitations)') }}</div>
        </div>
        <div class="stat">
            <div class="n">{{ $stats['no'] }}</div>
            <div class="l">{{ __('Declined') }}</div>
        </div>
        <div class="stat">
            <div class="n">{{ $stats['pending'] }}</div>
            <div class="l">{{ __('Awaiting reply') }}</div>
        </div>
        <div class="stat">
            <div class="n">{{ $stats['responded'] }}</div>
            <div class="l">{{ __('Replied') }}</div>
        </div>
    </div>

    <p class="cta">
        <a href="{{ route('admin.guests.index') }}">{{ __('View guest list with RSVP status') }}</a>
        ·
        <a href="{{ route('admin.guests.export') }}">{{ __('Export RSVP as CSV') }}</a>
    </p>
</x-layouts.admin>
