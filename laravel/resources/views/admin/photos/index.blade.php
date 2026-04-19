<x-layouts.admin
    :page-title="__('Photo moderation') . ' — ' . config('app.name', 'Wedding')"
    css-page="page-photos"
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
        <a href="{{ route('gallery.album') }}" target="_blank" rel="noopener">{{ __('Public album') }}</a>
        ·
        <a href="{{ route('admin.songs.index') }}">{{ __('DJ song suggestions') }}</a>
        ·
        <form method="post" action="{{ route('admin.logout') }}" style="display:inline;">
            @csrf
            <button type="submit" class="link">{{ __('Sign out') }}</button>
        </form>
    </p>
    <h1>{{ __('Photo moderation') }}</h1>
    <p class="sub">{{ __('Approve or delete uploads. Deleted files are removed from the server.') }}</p>

    @if (session('status'))
        <p class="ok" role="status">{{ session('status') }}</p>
    @endif
    @if (session('error'))
        <p class="err" role="alert">{{ session('error') }}</p>
    @endif

    <p class="bulk">
        <a href="{{ route('admin.photos.archive') }}">{{ __('Download all photos (ZIP)') }}</a>
    </p>

    <nav class="filters" aria-label="{{ __('Filter') }}">
        <a href="{{ route('admin.photos.index', ['status' => 'pending']) }}" class="{{ $filter === 'pending' ? 'active' : '' }}">{{ __('Pending') }}</a>
        <a href="{{ route('admin.photos.index', ['status' => 'approved']) }}" class="{{ $filter === 'approved' ? 'active' : '' }}">{{ __('Approved') }}</a>
        <a href="{{ route('admin.photos.index', ['status' => 'all']) }}" class="{{ $filter === 'all' ? 'active' : '' }}">{{ __('All') }}</a>
    </nav>

    @if ($photos->isEmpty())
        <p class="empty">{{ __('No photos in this view.') }}</p>
    @else
        <div class="grid">
            @foreach ($photos as $photo)
                <article class="card">
                    <img
                        src="{{ Storage::disk('public')->url($photo->file_path) }}"
                        alt="{{ $photo->original_filename ?: __('Photo') }}"
                        loading="lazy"
                        width="256"
                        height="256"
                    >
                    <div class="meta">
                        @if ($photo->guest)
                            {{ $photo->guest->name }}
                        @else
                            {{ __('Anonymous') }}
                        @endif
                        · {{ $photo->created_at?->timezone(config('app.timezone'))->format('Y-m-d H:i') }}
                        @if ($photo->approved)
                            <span class="badge">{{ __('Approved') }}</span>
                        @endif
                    </div>
                    <div class="actions">
                        @if (! $photo->approved)
                            <form method="post" action="{{ route('admin.photos.approve', ['photo' => $photo->id]) }}">
                                @csrf
                                <button type="submit" class="btn-approve">{{ __('Approve') }}</button>
                            </form>
                        @endif
                        <form
                            method="post"
                            action="{{ route('admin.photos.destroy', ['photo' => $photo->id]) }}"
                            onsubmit="return confirm({{ json_encode(__('Remove this photo permanently?')) }});"
                        >
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-delete">{{ __('Delete') }}</button>
                        </form>
                    </div>
                </article>
            @endforeach
        </div>

        @if ($photos->hasPages())
            <div class="pagination">{{ $photos->links() }}</div>
        @endif
    @endif
</x-layouts.admin>
