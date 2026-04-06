<x-layouts.admin
    :page-title="__('Import guests (CSV)') . ' — ' . config('app.name', 'Wedding')"
    css-page="page-import"
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
        <form method="post" action="{{ route('admin.logout') }}" style="display:inline;">
            @csrf
            <button type="submit" class="link" style="padding:0;font:inherit;">{{ __('Sign out') }}</button>
        </form>
    </p>
    <h1>{{ __('Import guests (CSV)') }}</h1>
    <p class="sub">{{ __('First row must be the header. Columns: name (required), email and token (optional). Use comma or semicolon as separator.') }}</p>

    <pre class="format" aria-label="CSV example">name,email,token
Ada Lovelace,ada@example.com,
Bob,,custom-token-1</pre>

    @if (session('import_result'))
        @php($r = session('import_result'))
        @if (($r['created'] ?? 0) > 0)
            <div class="ok" role="status">
                {{ trans_choice(':count guest created.|:count guests created.', $r['created'], ['count' => $r['created']]) }}
            </div>
        @endif
        @if (!empty($r['errors']))
            <div class="warn" role="alert">
                <strong>{{ __('Some rows were skipped') }}</strong>
                <ul>
                    @foreach ($r['errors'] as $e)
                        <li>{{ __('Line :line: :message', ['line' => $e['line'], 'message' => $e['message']]) }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    @endif

    <div class="card">
        <form method="post" action="{{ route('admin.guests.import.store') }}" enctype="multipart/form-data">
            @csrf
            <label for="file">{{ __('CSV file') }}</label>
            <input type="file" id="file" name="file" accept=".csv,.txt,text/csv,text/plain" required>
            @error('file')
                <p class="err">{{ $message }}</p>
            @enderror
            <button type="submit">{{ __('Import') }}</button>
        </form>
    </div>
</x-layouts.admin>
