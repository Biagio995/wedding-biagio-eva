<x-layouts.site-public
    :page-title="__('Gallery') . ' — ' . config('app.name', 'Wedding')"
    page="gallery"
>
    <div class="wrap">
        <div
            id="gallery-upload-i18n"
            hidden
            data-msg-failed="{{ __('Upload failed. Please try again.') }}"
            data-msg-rate-limit="{{ __('Too many uploads. Wait a moment, then try again.') }}"
            data-msg-network="{{ __('Network error. Check your connection.') }}"
            data-msg-preview-one="{{ __('1 photo selected — tap Upload photos when ready.') }}"
            data-msg-preview-other="{{ __(':count photos selected — tap Upload photos when ready.') }}"
            data-msg-remove-photo="{{ __('Remove from selection') }}"
        ></div>
        <h1>{{ __('Wedding gallery') }}</h1>
        <p class="sub">{{ __('Share your photos from the day — quick upload from your phone.') }}</p>

        @if($guest)
            <div class="badge" role="status">
                {{ __('Welcome') }}, {{ $guest->name }}
            </div>
        @else
            <p class="sub" style="margin-top:-0.5rem;">{{ __('Open this page using your invitation link or QR code to be recognized.') }}</p>
        @endif

        @if(session('upload_success'))
            <div class="flash" role="status" aria-live="polite">{{ __('Photos uploaded. Thank you!') }}</div>
        @endif

        <div class="card">
            <form action="{{ route('gallery.store') }}" method="post" enctype="multipart/form-data" id="gallery-form">
                @csrf
                <label class="upload-zone" for="photos-input">
                    <span>
                        {{ __('Tap to choose photos') }}
                        <strong>{{ __('Camera or gallery — multi-select') }}</strong>
                    </span>
                </label>
                <input
                    id="photos-input"
                    name="photos[]"
                    type="file"
                    accept="image/jpeg,image/png,image/webp,image/gif,image/heic,image/heif"
                    multiple
                >
                <div id="gallery-preview" class="gallery-preview" hidden>
                    <p id="gallery-preview-summary" class="gallery-preview__summary" aria-live="polite"></p>
                    <div id="gallery-preview-grid" class="gallery-preview__grid" role="list"></div>
                </div>
                <div id="gallery-upload-errors" style="color:#ef9a9a;font-size:0.85rem;margin:0 0 0.75rem;" role="alert" hidden></div>
                @error('photos')
                    <p style="color:#ef9a9a;font-size:0.85rem;margin:0 0 0.75rem;">{{ $message }}</p>
                @enderror
                @error('photos.*')
                    <p style="color:#ef9a9a;font-size:0.85rem;margin:0 0 0.75rem;">{{ $message }}</p>
                @enderror
                <div id="upload-progress" class="progress-wrap" hidden>
                    <div class="progress-track" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" aria-label="{{ __('Upload progress') }}">
                        <div class="progress-bar-fill"></div>
                    </div>
                    <p class="progress-label" id="progress-pct">{{ __('Uploading…') }} <span>0%</span></p>
                </div>
                <button type="submit" class="btn" id="submit-btn">{{ __('Upload photos') }}</button>
                <p class="hint">{{ __('Up to :count images at once — JPEG, PNG, WebP, GIF, HEIC — max :mb MB each. Progress is shown while uploading.', ['count' => config('gallery.upload.max_files_per_request'), 'mb' => (int) round(config('gallery.upload.max_kilobytes') / 1024)]) }}</p>
            </form>
        </div>
    </div>
</x-layouts.site-public>
