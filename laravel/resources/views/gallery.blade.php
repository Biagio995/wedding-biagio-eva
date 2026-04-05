<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="color-scheme" content="dark">
    <meta name="theme-color" content="#213555">
    <title>{{ __('Gallery') }} — {{ config('app.name', 'Wedding') }}</title>
    <style>
        :root {
            --bg: #213555;
            --surface: #3E5879;
            --text: #F5EFE7;
            --muted: #b8aea4;
            --accent: #D8C4B6;
            --accent-dim: rgba(216, 196, 182, 0.18);
            --radius: 14px;
            --safe-bottom: env(safe-area-inset-bottom, 0px);
            --safe-top: env(safe-area-inset-top, 0px);
            --safe-left: env(safe-area-inset-left, 0px);
            --safe-right: env(safe-area-inset-right, 0px);
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100dvh;
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
            background: radial-gradient(ellipse 120% 80% at 50% -20%, #3E5879 0%, var(--bg) 55%);
            color: var(--text);
            line-height: 1.45;
            padding: calc(1rem + var(--safe-top)) calc(1rem + var(--safe-right)) calc(1.5rem + var(--safe-bottom)) calc(1rem + var(--safe-left));
            overflow-x: hidden;
            -webkit-text-size-adjust: 100%;
            text-size-adjust: 100%;
            touch-action: manipulation;
        }
        .wrap { max-width: 28rem; margin: 0 auto; }
        h1 {
            font-size: 1.35rem;
            font-weight: 600;
            letter-spacing: 0.02em;
            margin: 0 0 0.35rem;
        }
        .sub { color: var(--muted); font-size: 0.9rem; margin-bottom: 1.25rem; }
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            background: var(--accent-dim);
            color: var(--accent);
            padding: 0.35rem 0.65rem;
            border-radius: 999px;
            font-size: 0.8rem;
            margin-bottom: 1rem;
        }
        .card {
            background: var(--surface);
            border-radius: var(--radius);
            padding: 1.1rem;
            border: 1px solid rgba(255,255,255,0.06);
        }
        label.upload-zone {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: clamp(9rem, 36vh, 14rem);
            border: 2px dashed rgba(216, 196, 182, 0.4);
            border-radius: var(--radius);
            cursor: pointer;
            transition: border-color 0.2s, background 0.2s;
            margin-bottom: 0.85rem;
            -webkit-tap-highlight-color: transparent;
        }
        @media (prefers-reduced-motion: reduce) {
            label.upload-zone { transition: none; }
        }
        label.upload-zone:active, label.upload-zone:focus-within {
            border-color: var(--accent);
            background: rgba(216, 196, 182, 0.08);
        }
        .upload-zone span { font-size: 0.95rem; color: var(--muted); text-align: center; padding: 0 0.5rem; }
        .upload-zone strong { color: var(--accent); display: block; margin-top: 0.35rem; }
        input[type="file"] {
            position: absolute;
            width: 0.1px;
            height: 0.1px;
            opacity: 0;
            overflow: hidden;
            z-index: -1;
        }
        .btn {
            display: block;
            width: 100%;
            padding: 0.95rem 1rem;
            font-size: 1rem;
            font-weight: 600;
            border: none;
            border-radius: var(--radius);
            background: linear-gradient(145deg, #F5EFE7, #D8C4B6);
            color: #213555;
            cursor: pointer;
            min-height: 48px;
            -webkit-tap-highlight-color: transparent;
        }
        .btn:disabled { opacity: 0.45; cursor: not-allowed; }
        .btn:not(:disabled):active { transform: scale(0.98); }
        @media (prefers-reduced-motion: reduce) {
            .btn:not(:disabled):active { transform: none; }
            .progress-bar-fill { transition: none; }
        }
        .btn:focus-visible {
            outline: 2px solid var(--accent);
            outline-offset: 3px;
        }
        .flash {
            background: rgba(76, 175, 80, 0.15);
            color: #a5d6a7;
            padding: 0.65rem 0.85rem;
            border-radius: 10px;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }
        .hint { font-size: 0.78rem; color: var(--muted); margin-top: 0.65rem; text-align: center; }
        .progress-wrap {
            margin-bottom: 0.85rem;
        }
        .progress-wrap[hidden] { display: none !important; }
        .progress-track {
            height: 8px;
            border-radius: 999px;
            background: rgba(255,255,255,0.08);
            overflow: hidden;
        }
        .progress-bar-fill {
            height: 100%;
            width: 0%;
            border-radius: 999px;
            background: linear-gradient(90deg, #3E5879, #D8C4B6);
            transition: width 0.1s ease-out;
        }
        .progress-label {
            font-size: 0.75rem;
            color: var(--muted);
            margin-top: 0.35rem;
            text-align: center;
        }
    </style>
</head>
<body>
    @include('partials.site-navbar')
    <div class="wrap">
        <h1>{{ __('Wedding gallery') }}</h1>
        <p class="sub">{{ __('Share your photos from the day — quick upload from your phone.') }}</p>
        <p class="hint" style="text-align:left;margin-top:-0.5rem;margin-bottom:1rem;">{{ __('No account or password — use your invitation link or QR code to upload as yourself.') }}</p>

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
    <script>
        (function () {
            var form = document.getElementById('gallery-form');
            var input = document.getElementById('photos-input');
            var btn = document.getElementById('submit-btn');
            var progressWrap = document.getElementById('upload-progress');
            var progressBar = progressWrap ? progressWrap.querySelector('.progress-bar-fill') : null;
            var progressTrack = progressWrap ? progressWrap.querySelector('.progress-track') : null;
            var progressPct = document.getElementById('progress-pct');
            var errBox = document.getElementById('gallery-upload-errors');
            if (!form || !input) return;

            var uploading = false;

            function showErrors(payload) {
                if (!errBox) return;
                var errors = payload.errors || {};
                var parts = [];
                Object.keys(errors).forEach(function (k) {
                    (errors[k] || []).forEach(function (m) { parts.push(m); });
                });
                if (payload.message && parts.length === 0) parts.push(payload.message);
                errBox.textContent = parts.join(' ');
                errBox.hidden = parts.length === 0;
            }

            function setProgress(pct) {
                if (!progressBar || !progressTrack) return;
                progressBar.style.width = pct + '%';
                progressTrack.setAttribute('aria-valuenow', String(Math.round(pct)));
                var span = progressPct && progressPct.querySelector('span');
                if (span) span.textContent = Math.round(pct) + '%';
            }

            function uploadWithProgress() {
                if (uploading) return;
                if (!input.files || !input.files.length) return;
                uploading = true;
                if (errBox) { errBox.hidden = true; errBox.textContent = ''; }
                if (btn) btn.disabled = true;
                if (progressWrap) progressWrap.hidden = false;
                setProgress(0);

                var xhr = new XMLHttpRequest();
                var data = new FormData(form);
                var token = form.querySelector('[name="_token"]');
                xhr.open('POST', form.action);
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                xhr.setRequestHeader('Accept', 'application/json');
                if (token) xhr.setRequestHeader('X-CSRF-TOKEN', token.value);
                xhr.upload.onprogress = function (e) {
                    if (e.lengthComputable && e.total > 0) {
                        setProgress((e.loaded / e.total) * 100);
                    }
                };
                xhr.onload = function () {
                    uploading = false;
                    if (btn) btn.disabled = false;
                    if (progressWrap) progressWrap.hidden = true;
                    setProgress(0);
                    if (xhr.status === 200) {
                        try {
                            var json = JSON.parse(xhr.responseText);
                            if (json.redirect) {
                                window.location.href = json.redirect;
                                return;
                            }
                        } catch (ignore) {}
                        window.location.reload();
                        return;
                    }
                    if (xhr.status === 422) {
                        try {
                            showErrors(JSON.parse(xhr.responseText));
                        } catch (e2) {
                            showErrors({ message: '{{ __("Upload failed. Please try again.") }}' });
                        }
                        return;
                    }
                    if (xhr.status === 429) {
                        showErrors({ message: '{{ __("Too many uploads. Wait a moment, then try again.") }}' });
                        return;
                    }
                    showErrors({ message: '{{ __("Upload failed. Please try again.") }}' });
                };
                xhr.onerror = function () {
                    uploading = false;
                    if (btn) btn.disabled = false;
                    if (progressWrap) progressWrap.hidden = true;
                    setProgress(0);
                    showErrors({ message: '{{ __("Network error. Check your connection.") }}' });
                };
                xhr.send(data);
            }

            form.addEventListener('submit', function (e) {
                e.preventDefault();
                if (!input.files || !input.files.length) {
                    input.click();
                    return;
                }
                uploadWithProgress();
            });

            btn.addEventListener('click', function (e) {
                if (!input.files || !input.files.length) {
                    e.preventDefault();
                    input.click();
                }
            });
        })();
    </script>
</body>
</html>
