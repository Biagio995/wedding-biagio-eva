@php
    /** @var \Illuminate\Support\Collection $ownSongRecommendations */
    $ownSongRecommendations = $ownSongRecommendations ?? collect();
    $needsName = $guest === null;
@endphp
<div class="card reveal-on-scroll" id="dj-songs">
    <h2>{{ __('Song suggestions for the DJ') }}</h2>
    <p class="sub" style="margin-top:0;">{{ __('Tell us which songs must absolutely play — we will share the list with the DJ.') }}</p>

    @if ($errors->songs->any())
        <div class="flash err" role="alert">
            <ul style="margin:0;padding-left:1rem;">
                @foreach ($errors->songs->all() as $errorMessage)
                    <li>{{ $errorMessage }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="post" action="{{ route('wedding.songs.store') }}" class="song-form">
        @csrf
        @if ($needsName)
            <label for="song_submitted_by">{{ __('Your name') }}</label>
            <input
                type="text"
                id="song_submitted_by"
                name="submitted_by"
                value="{{ old('submitted_by') }}"
                maxlength="120"
                autocomplete="name"
                required
                aria-required="true"
            >
            @error('submitted_by', 'songs')<p class="error">{{ $message }}</p>@enderror
        @endif

        <label for="song_title">{{ __('Song title') }}</label>
        <input
            type="text"
            id="song_title"
            name="title"
            value="{{ old('title') }}"
            maxlength="200"
            required
            aria-required="true"
        >
        @error('title', 'songs')<p class="error">{{ $message }}</p>@enderror

        <label for="song_artist">{{ __('Artist (optional)') }}</label>
        <input
            type="text"
            id="song_artist"
            name="artist"
            value="{{ old('artist') }}"
            maxlength="200"
        >
        @error('artist', 'songs')<p class="error">{{ $message }}</p>@enderror

        <label for="song_notes">{{ __('Note for the DJ (optional)') }}</label>
        <textarea
            id="song_notes"
            name="notes"
            maxlength="500"
            rows="2"
        >{{ old('notes') }}</textarea>
        @error('notes', 'songs')<p class="error">{{ $message }}</p>@enderror

        <button type="submit" class="btn">{{ __('Add song') }}</button>
    </form>

    @if ($ownSongRecommendations->isNotEmpty())
        <h3 class="song-list__title">{{ __('Your suggestions') }}</h3>
        <ul class="song-list">
            @foreach ($ownSongRecommendations as $song)
                <li class="song-list__item">
                    <div class="song-list__text">
                        <strong>{{ $song->title }}</strong>
                        @if (!empty($song->artist))
                            <span class="song-list__artist">— {{ $song->artist }}</span>
                        @endif
                        @if (!empty($song->notes))
                            <p class="song-list__notes">{{ $song->notes }}</p>
                        @endif
                    </div>
                    <form method="post" action="{{ route('wedding.songs.destroy', $song) }}" class="song-list__delete">
                        @csrf
                        @method('delete')
                        <button type="submit" class="btn btn--ghost" aria-label="{{ __('Remove :title', ['title' => $song->title]) }}">
                            {{ __('Remove') }}
                        </button>
                    </form>
                </li>
            @endforeach
        </ul>
    @endif

    @php
        $publicSongRecommendations = $publicSongRecommendations ?? collect();
    @endphp
    @if ($publicSongRecommendations->isNotEmpty())
        <h3 class="song-list__title song-list__title--public">{{ __('Already suggested') }}</h3>
        <p class="song-list__hint">{{ __('A quick look at what other guests have asked for — so you can avoid picking the same song twice.') }}</p>
        <ul class="song-list song-list--public">
            @foreach ($publicSongRecommendations as $entry)
                <li class="song-list__item song-list__item--public">
                    <div class="song-list__text">
                        <strong>{{ $entry['title'] }}</strong>
                        @if (!empty($entry['artist']))
                            <span class="song-list__artist">— {{ $entry['artist'] }}</span>
                        @endif
                        <span class="song-list__author">· {{ $entry['author'] }}</span>
                    </div>
                </li>
            @endforeach
        </ul>
    @endif
</div>
