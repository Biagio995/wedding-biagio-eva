<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="color-scheme" content="dark">
    <meta name="theme-color" content="#1a1a2e">
    <title>{{ $event['title'] }} — {{ config('app.name', 'Wedding') }}</title>
    <style>
        :root {
            --bg: #0f0f14;
            --surface: #1a1a24;
            --text: #f4f4f8;
            --muted: #9898a8;
            --accent: #c9a962;
            --accent-dim: rgba(201, 169, 98, 0.15);
            --radius: 14px;
            --safe-bottom: env(safe-area-inset-bottom, 0px);
            --safe-top: env(safe-area-inset-top, 0px);
            --safe-left: env(safe-area-inset-left, 0px);
            --safe-right: env(safe-area-inset-right, 0px);
            --danger-bg: rgba(244, 67, 54, 0.12);
            --danger-text: #ffcdd2;
            --ok-bg: rgba(76, 175, 80, 0.15);
            --ok-text: #a5d6a7;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100dvh;
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
            background: radial-gradient(ellipse 120% 80% at 50% -20%, #2a2235 0%, var(--bg) 55%);
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
            font-size: 1.5rem;
            font-weight: 600;
            letter-spacing: 0.02em;
            margin: 0 0 0.5rem;
        }
        .sub { color: var(--muted); font-size: 0.95rem; margin-bottom: 1.25rem; }
        .card {
            background: var(--surface);
            border-radius: var(--radius);
            padding: 1.1rem;
            border: 1px solid rgba(255,255,255,0.06);
            margin-bottom: 1rem;
        }
        .card h2 {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--muted);
            margin: 0 0 0.65rem;
            font-weight: 600;
        }
        .when { font-size: 1.05rem; font-weight: 500; margin-bottom: 0.35rem; }
        .where { color: var(--muted); font-size: 0.95rem; }
        .where a { color: var(--accent); }
        .flash {
            padding: 0.65rem 0.85rem;
            border-radius: 10px;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }
        .flash.err { background: var(--danger-bg); color: var(--danger-text); }
        .flash.ok { background: var(--ok-bg); color: var(--ok-text); }
        label { display: block; font-size: 0.85rem; color: var(--muted); margin-bottom: 0.35rem; }
        select, textarea, input[type="number"] {
            width: 100%;
            padding: 0.65rem 0.75rem;
            border-radius: 10px;
            border: 1px solid rgba(255,255,255,0.1);
            background: rgba(0,0,0,0.25);
            color: var(--text);
            font-size: 1rem;
            margin-bottom: 0.85rem;
        }
        textarea { min-height: 5rem; resize: vertical; }
        .row { margin-bottom: 0.85rem; }
        .radios label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            margin-bottom: 0.5rem;
            color: var(--text);
            min-height: 44px;
            padding: 0.35rem 0;
        }
        .radios input { width: auto; min-width: 1.15rem; min-height: 1.15rem; }
        .btn {
            display: block;
            width: 100%;
            padding: 0.95rem 1rem;
            font-size: 1rem;
            font-weight: 600;
            border: none;
            border-radius: var(--radius);
            background: linear-gradient(145deg, #d4b56a, #a8893e);
            color: #1a1508;
            cursor: pointer;
            min-height: 48px;
            -webkit-tap-highlight-color: transparent;
        }
        .btn:focus-visible {
            outline: 2px solid var(--accent);
            outline-offset: 3px;
        }
        .btn:active { transform: scale(0.98); }
        @media (prefers-reduced-motion: reduce) {
            .btn:active { transform: none; }
        }
        .hint { font-size: 0.88rem; color: var(--muted); margin-top: 0.75rem; }
        .hint a { color: var(--accent); }
        .error { color: #ef9a9a; font-size: 0.85rem; margin: -0.5rem 0 0.75rem; }
        .maps-link {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            margin-top: 0.75rem;
            padding: 0.5rem 0.85rem;
            min-height: 44px;
            border-radius: 10px;
            background: var(--accent-dim);
            color: var(--accent) !important;
            font-weight: 600;
            text-decoration: none;
            -webkit-tap-highlight-color: transparent;
        }
        .maps-link:active { opacity: 0.9; }
        .maps-link:focus-visible {
            outline: 2px solid var(--accent);
            outline-offset: 3px;
        }
        .notes-body {
            color: var(--text);
            font-size: 0.95rem;
            white-space: pre-wrap;
            line-height: 1.5;
        }
        .countdown-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 0.5rem;
            text-align: center;
            margin-top: 0.35rem;
        }
        .countdown-unit {
            background: rgba(0,0,0,0.2);
            border-radius: 12px;
            padding: 0.65rem 0.35rem;
            border: 1px solid rgba(255,255,255,0.06);
        }
        .countdown-unit span {
            display: block;
            font-size: 1.35rem;
            font-weight: 700;
            font-variant-numeric: tabular-nums;
            color: var(--accent);
            line-height: 1.2;
        }
        .countdown-unit small {
            display: block;
            margin-top: 0.25rem;
            font-size: 0.68rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--muted);
        }
        .countdown-done-msg {
            text-align: center;
            font-size: 1.05rem;
            font-weight: 600;
            color: var(--accent);
            padding: 0.5rem 0;
        }
        .countdown-done .countdown-grid { display: none; }
    </style>
</head>
<body>
    @include('partials.site-navbar', ['brandTitle' => $event['title']])
    <div class="wrap">

        @if (session('wedding_error'))
            <div class="flash err" role="alert">{{ session('wedding_error') }}</div>
        @endif
        @if (session('wedding_success'))
            <div class="flash ok" role="status" aria-live="polite">
                <strong>{{ session('wedding_success') }}</strong>
                @if (session('wedding_confirmation_email_sent'))
                    <span style="display:block;margin-top:0.5rem;font-weight:400;opacity:0.95;">{{ __('We also sent a confirmation to your email address.') }}</span>
                @endif
            </div>
        @endif

        <h1>{{ $event['title'] }}</h1>
        <p class="sub">{{ $event['description'] }}</p>
        <p class="hint" style="margin-top:-0.5rem;margin-bottom:1.25rem;">{{ __('No guest login or sign-up — your personal invitation link is enough.') }}</p>

        @php
            $eventTz = isset($event['timezone']) && $event['timezone'] !== '' && $event['timezone'] !== null
                ? $event['timezone']
                : null;
            $startsAt = $eventTz !== null
                ? \Illuminate\Support\Carbon::parse($event['date'], $eventTz)
                : \Illuminate\Support\Carbon::parse($event['date']);
            $countdownTargetIso = $startsAt->toIso8601String();
        @endphp

        <div
            class="card"
            id="wedding-countdown"
            data-countdown-target="{{ $countdownTargetIso }}"
            aria-live="polite"
        >
            <h2>{{ __('Countdown') }}</h2>
            <div class="countdown-grid" role="timer" aria-label="{{ __('Time until the event') }}">
                <div class="countdown-unit">
                    <span data-cd="days">0</span>
                    <small>{{ __('Days') }}</small>
                </div>
                <div class="countdown-unit">
                    <span data-cd="hours">0</span>
                    <small>{{ __('Hours') }}</small>
                </div>
                <div class="countdown-unit">
                    <span data-cd="minutes">0</span>
                    <small>{{ __('Minutes') }}</small>
                </div>
                <div class="countdown-unit">
                    <span data-cd="seconds">0</span>
                    <small>{{ __('Seconds') }}</small>
                </div>
            </div>
            <p class="countdown-done-msg" id="wedding-countdown-done" hidden>{{ __('The celebration has started!') }}</p>
        </div>

        <div class="card">
            <h2>{{ __('When & where') }}</h2>
            <p class="when">{{ $startsAt->translatedFormat('l j F Y, H:i') }}</p>
            @if ($eventTz !== null)
                <p class="where" style="margin-bottom:0.65rem;">{{ __('Time zone: :tz', ['tz' => $eventTz]) }}</p>
            @endif
            <p class="where">
                <strong style="color:var(--text);font-weight:600;">{{ $event['location_name'] }}</strong>
                @if (!empty($event['location_address']))
                    <br>{{ $event['location_address'] }}
                @endif
                @if (!empty($event['maps_url']))
                    <br>
                    <a class="maps-link" href="{{ $event['maps_url'] }}" target="_blank" rel="noopener noreferrer">{{ __('Open in Google Maps') }}</a>
                @endif
            </p>
        </div>

        @if (!empty(trim($event['additional_notes'] ?? '')))
            <div class="card">
                <h2>{{ __('Additional details') }}</h2>
                <div class="notes-body">{{ $event['additional_notes'] }}</div>
            </div>
        @endif

        @if ($guest)
            @php
                $rsvpDefault = old('rsvp_status', $guest->rsvp_status);
                $countDefault = old('guests_count', $guest->guests_count);
                if ($countDefault === null && $rsvpDefault === 'yes') {
                    $countDefault = 1;
                }
            @endphp
            <div class="card">
                <h2>{{ __('RSVP') }}</h2>
                <p class="sub" style="margin-top:0;">{{ __('Hello, :name', ['name' => $guest->name]) }}</p>
                @if ($guest->email)
                    <p class="sub" style="margin-top:-0.5rem;">{{ $guest->email }}</p>
                @endif
                <p class="hint" style="margin-bottom:1rem;">{{ __('You opened this page with your personal link. You can change your answers anytime — only the latest save is kept.') }}</p>
                @if (filled($guest->rsvp_status))
                    <p class="hint" style="margin-bottom:1rem;">{{ __('You already sent a response. Update the form below if something changed.') }}</p>
                @endif
                <form method="post" action="{{ route('wedding.rsvp.store') }}" id="wedding-rsvp-form">
                    @csrf
                    <fieldset class="row" style="border:none;padding:0;margin:0;">
                        <legend class="label-like" style="font-size:0.85rem;color:var(--muted);display:block;margin-bottom:0.35rem;">{{ __('Will you attend?') }}</legend>
                        <div class="radios" role="group" aria-required="true">
                            <label><input type="radio" name="rsvp_status" value="yes" @checked($rsvpDefault === 'yes')> {{ __('Yes') }}</label>
                            <label><input type="radio" name="rsvp_status" value="no" @checked($rsvpDefault === 'no')> {{ __('No') }}</label>
                        </div>
                        @error('rsvp_status')<p class="error">{{ $message }}</p>@enderror
                    </fieldset>
                    <label for="guests_count">{{ __('Number of guests (including you)') }}</label>
                    <input type="number" id="guests_count" name="guests_count" min="1" max="50" inputmode="numeric"
                        value="{{ $countDefault }}"
                        @disabled($rsvpDefault === 'no')
                        aria-describedby="guests_count-help">
                    <p id="guests_count-help" class="hint" style="margin-top:-0.5rem;">{{ __('Required if you answer Yes.') }}</p>
                    @error('guests_count')<p class="error">{{ $message }}</p>@enderror

                    <label for="notes">{{ __('Allergies, dietary needs, and other requests') }}</label>
                    <p id="rsvp-notes-help" class="hint" style="margin-bottom:0.35rem;">{{ __('Free text — saved with your RSVP. You can update it anytime before the event.') }}</p>
                    <textarea
                        id="notes"
                        name="notes"
                        rows="4"
                        maxlength="2000"
                        spellcheck="true"
                        autocomplete="off"
                        placeholder="{{ __('e.g. nut allergy, wheelchair access, plus-one name…') }}"
                        aria-describedby="rsvp-notes-help"
                    >{{ old('notes', $guest->notes) }}</textarea>
                    @error('notes')<p class="error">{{ $message }}</p>@enderror

                    <button type="submit" class="btn">{{ filled($guest->rsvp_status) ? __('Update response') : __('Send response') }}</button>
                </form>
            </div>
        @else
            @php
                $rsvpDefault = old('rsvp_status');
                $countDefault = old('guests_count');
                if ($countDefault === null && $rsvpDefault === 'yes') {
                    $countDefault = 1;
                }
            @endphp
            <div class="card">
                <h2>{{ __('RSVP') }}</h2>
                <p class="hint" style="margin-bottom:1rem;">{{ __('You can respond here without an invitation link — enter your name below. If you received a personal link, open it once so we can match your details automatically.') }}</p>
                <form method="post" action="{{ route('wedding.rsvp.store') }}" id="wedding-rsvp-form">
                    @csrf
                    <label for="rsvp_open_name">{{ __('Your name') }}</label>
                    <input
                        type="text"
                        id="rsvp_open_name"
                        name="name"
                        value="{{ old('name') }}"
                        maxlength="120"
                        required
                        autocomplete="name"
                        aria-required="true"
                    >
                    @error('name')<p class="error">{{ $message }}</p>@enderror

                    <label for="rsvp_open_email">{{ __('Email (optional)') }}</label>
                    <p id="rsvp_open_email-help" class="hint" style="margin-top:-0.35rem;margin-bottom:0.35rem;">{{ __('For confirmation or updates, if you want.') }}</p>
                    <input
                        type="email"
                        id="rsvp_open_email"
                        name="email"
                        value="{{ old('email') }}"
                        maxlength="255"
                        autocomplete="email"
                        aria-describedby="rsvp_open_email-help"
                    >
                    @error('email')<p class="error">{{ $message }}</p>@enderror

                    <fieldset class="row" style="border:none;padding:0;margin:0;">
                        <legend class="label-like" style="font-size:0.85rem;color:var(--muted);display:block;margin-bottom:0.35rem;">{{ __('Will you attend?') }}</legend>
                        <div class="radios" role="group" aria-required="true">
                            <label><input type="radio" name="rsvp_status" value="yes" @checked($rsvpDefault === 'yes')> {{ __('Yes') }}</label>
                            <label><input type="radio" name="rsvp_status" value="no" @checked($rsvpDefault === 'no')> {{ __('No') }}</label>
                        </div>
                        @error('rsvp_status')<p class="error">{{ $message }}</p>@enderror
                    </fieldset>
                    <label for="guests_count">{{ __('Number of guests (including you)') }}</label>
                    <input type="number" id="guests_count" name="guests_count" min="1" max="50" inputmode="numeric"
                        value="{{ $countDefault }}"
                        @disabled($rsvpDefault === 'no' || $rsvpDefault === null)
                        aria-describedby="guests_count-help">
                    <p id="guests_count-help" class="hint" style="margin-top:-0.5rem;">{{ __('Required if you answer Yes.') }}</p>
                    @error('guests_count')<p class="error">{{ $message }}</p>@enderror

                    <label for="notes">{{ __('Allergies, dietary needs, and other requests') }}</label>
                    <p id="rsvp-notes-help" class="hint" style="margin-bottom:0.35rem;">{{ __('Free text — saved with your RSVP.') }}</p>
                    <textarea
                        id="notes"
                        name="notes"
                        rows="4"
                        maxlength="2000"
                        spellcheck="true"
                        autocomplete="off"
                        placeholder="{{ __('e.g. nut allergy, wheelchair access, plus-one name…') }}"
                        aria-describedby="rsvp-notes-help"
                    >{{ old('notes') }}</textarea>
                    @error('notes')<p class="error">{{ $message }}</p>@enderror

                    <button type="submit" class="btn">{{ __('Send response') }}</button>
                </form>
            </div>
        @endif
    </div>
    <script>
        (function () {
            var form = document.getElementById('wedding-rsvp-form');
            if (form) {
                var count = document.getElementById('guests_count');
                function syncRsvpGuestsField() {
                    var checked = form.querySelector('input[name="rsvp_status"]:checked');
                    var isNo = checked && checked.value === 'no';
                    if (!count) return;
                    count.disabled = isNo;
                    if (isNo) {
                        count.value = '';
                    }
                }
                form.querySelectorAll('input[name="rsvp_status"]').forEach(function (r) {
                    r.addEventListener('change', syncRsvpGuestsField);
                });
                syncRsvpGuestsField();
            }
        })();
    </script>
    <script>
        (function () {
            var root = document.getElementById('wedding-countdown');
            if (!root) return;
            var raw = root.getAttribute('data-countdown-target');
            if (!raw) return;
            var targetMs = Date.parse(raw);
            if (Number.isNaN(targetMs)) return;

            var els = {
                days: root.querySelector('[data-cd="days"]'),
                hours: root.querySelector('[data-cd="hours"]'),
                minutes: root.querySelector('[data-cd="minutes"]'),
                seconds: root.querySelector('[data-cd="seconds"]'),
            };
            var doneEl = document.getElementById('wedding-countdown-done');
            var grid = root.querySelector('.countdown-grid');

            function pad(n) {
                return String(n);
            }

            function tick() {
                var diff = targetMs - Date.now();
                if (diff <= 0) {
                    root.classList.add('countdown-done');
                    if (grid) grid.setAttribute('hidden', 'hidden');
                    if (doneEl) doneEl.removeAttribute('hidden');
                    return false;
                }
                var totalSec = Math.floor(diff / 1000);
                var d = Math.floor(totalSec / 86400);
                var h = Math.floor((totalSec % 86400) / 3600);
                var m = Math.floor((totalSec % 3600) / 60);
                var s = totalSec % 60;
                if (els.days) els.days.textContent = pad(d);
                if (els.hours) els.hours.textContent = pad(h);
                if (els.minutes) els.minutes.textContent = pad(m);
                if (els.seconds) els.seconds.textContent = pad(s);
                return true;
            }

            if (!tick()) {
                return;
            }
            var iv = setInterval(function () {
                if (!tick()) {
                    clearInterval(iv);
                }
            }, 1000);
        })();
    </script>
</body>
</html>
