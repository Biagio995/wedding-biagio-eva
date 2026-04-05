<x-layouts.site-public
    :page-title="__($event['title']) . ' — ' . config('app.name', 'Wedding')"
    :brand-title="$event['title']"
    page="wedding"
>
    <div class="wrap wrap--wedding">

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

        <h1>{{ __($event['title']) }}</h1>
        <p class="sub">{{ __('It will be a great joy for us and for our parents to have you with us at the beginning of our new life.') }}</p>

        @include('partials.site.wedding-monogram')

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
                    <input
                        type="email"
                        id="rsvp_open_email"
                        name="email"
                        value="{{ old('email') }}"
                        maxlength="255"
                        autocomplete="email"
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

                    <button type="submit" class="btn">{{ __('Send response') }}</button>
                </form>
            </div>
        @endif
    </div>
</x-layouts.site-public>
