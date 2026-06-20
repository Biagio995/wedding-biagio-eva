<div
    id="gallery-upload-i18n"
    hidden
    data-msg-failed="{{ __('Upload failed. Please try again.') }}"
    data-msg-rate-limit="{{ __('Too many uploads. Wait a moment, then try again.') }}"
    data-msg-network="{{ __('Network error. Check your connection.') }}"
    data-msg-too-large="{{ __('The photo is too large for the server (max :mb MB). Try a smaller image or take a new photo.', ['mb' => (int) round(config('gallery.upload.max_kilobytes') / 1024)]) }}"
    data-msg-preview-one="{{ __('1 photo selected — tap Upload photos when ready.') }}"
    data-msg-preview-other="{{ __(':count photos selected — tap Upload photos when ready.') }}"
    data-msg-remove-photo="{{ __('Remove from selection') }}"
    data-msg-cta-ready="{{ __('Upload now') }}"
></div>

<div class="album-upload">
    <div class="album-toolbar">
        @if($guest)
            <div class="badge badge--inline" role="status">
                {{ __('Welcome') }}, {{ $guest->name }}
            </div>
        @endif

        <form action="{{ route('gallery.store') }}" method="post" enctype="multipart/form-data" id="gallery-form" class="album-upload-form">
            @csrf
            <input
                id="photos-input"
                name="photos[]"
                type="file"
                accept="image/jpeg,image/png,image/webp,image/gif,image/heic,image/heif"
                multiple
                hidden
            >
            <button type="submit" class="album-upload-cta" id="submit-btn">
                <span class="album-upload-cta__icon-wrap" aria-hidden="true">
                    <svg class="album-upload-cta__icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3z"/>
                        <circle cx="12" cy="13" r="3"/>
                    </svg>
                </span>
                <span class="album-upload-cta__label">{{ __('Upload photos') }}</span>
            </button>
        </form>
    </div>

    <div id="gallery-upload-errors" class="album-upload-errors" role="alert" hidden></div>
    @error('photos')
        <p class="album-upload-errors">{{ $message }}</p>
    @enderror
    @error('photos.*')
        <p class="album-upload-errors">{{ $message }}</p>
    @enderror

    <div id="gallery-preview" class="gallery-preview" hidden>
        <p id="gallery-preview-summary" class="gallery-preview__summary" aria-live="polite"></p>
        <div id="gallery-preview-grid" class="gallery-preview__grid" role="list"></div>
    </div>

    <div id="upload-progress" class="progress-wrap" hidden>
        <div class="progress-track" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" aria-label="{{ __('Upload progress') }}">
            <div class="progress-bar-fill"></div>
        </div>
        <p class="progress-label" id="progress-pct">{{ __('Uploading…') }} <span>0%</span></p>
    </div>
</div>
