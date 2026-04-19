<x-layouts.site-public
    :page-title="__('Attend') . ' — ' . __($event['title']) . ' — ' . config('app.name', 'Wedding')"
    :brand-title="$event['title']"
    page="wedding"
>
    <div class="wrap wrap--wedding">
        @if (session('wedding_error'))
            <div class="flash err" role="alert">{{ session('wedding_error') }}</div>
        @endif

        @php
            $rsvpConfirmation = $rsvpConfirmation ?? null;
        @endphp
        @if ($rsvpConfirmation)
            <div class="rsvp-confirmation card reveal-on-scroll"
                 id="rsvp-confirmation"
                 role="status"
                 aria-live="polite">
                <div class="rsvp-confirmation__badge" aria-hidden="true">
                    @if ($rsvpConfirmation['attending'])
                        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" focusable="false"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                    @else
                        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" focusable="false"><circle cx="12" cy="12" r="9"/><path d="M8 12h8"/></svg>
                    @endif
                </div>
                <h2 class="rsvp-confirmation__title">
                    @if ($rsvpConfirmation['is_update'])
                        {{ __('Your RSVP has been updated.') }}
                    @else
                        {{ __('Thank you — your response has been saved.') }}
                    @endif
                </h2>
                <dl class="rsvp-confirmation__facts">
                    <div>
                        <dt>{{ __('Name') }}</dt>
                        <dd>{{ $rsvpConfirmation['name'] }}</dd>
                    </div>
                    <div>
                        <dt>{{ __('Attending') }}</dt>
                        <dd>{{ $rsvpConfirmation['attending'] ? __('Yes') : __('No') }}</dd>
                    </div>
                    @if ($rsvpConfirmation['attending'] && $rsvpConfirmation['guests_count'])
                        <div>
                            <dt>{{ __('Number of guests') }}</dt>
                            <dd>{{ $rsvpConfirmation['guests_count'] }}</dd>
                        </div>
                    @endif
                    @if ($rsvpConfirmation['attending'] && !empty($rsvpConfirmation['companion_names']))
                        <div>
                            <dt>{{ __('Companion names') }}</dt>
                            <dd>{{ implode(', ', $rsvpConfirmation['companion_names']) }}</dd>
                        </div>
                    @endif
                </dl>
                @if (session('wedding_confirmation_email_sent'))
                    <p class="rsvp-confirmation__email-note">{{ __('We also sent a confirmation to your email address.') }}</p>
                @endif
                <div class="rsvp-confirmation__actions">
                    @if ($rsvpConfirmation['attending'])
                        @include('partials.site.calendar-picker', ['event' => $event])
                    @endif
                    <a class="btn btn--ghost" href="{{ route('gallery.show') }}">{{ __('Share photos') }}</a>
                    @if (!empty(config('wedding.song_recommendations.enabled', true)))
                        <a class="btn btn--ghost" href="{{ route('wedding.show') }}#dj-songs">{{ __('Suggest a song') }}</a>
                    @endif
                    <a class="rsvp-confirmation__change" href="#attend-rsvp">{{ __('Change my answer') }}</a>
                </div>
            </div>
        @elseif (session('wedding_success'))
            <div class="flash ok" role="status" aria-live="polite">
                <strong>{{ session('wedding_success') }}</strong>
                @if (session('wedding_confirmation_email_sent'))
                    <span style="display:block;margin-top:0.5rem;font-weight:400;opacity:0.95;">{{ __('We also sent a confirmation to your email address.') }}</span>
                @endif
            </div>
        @endif

        <div class="reveal-on-scroll">
            <h1>{{ __('Attend') }}</h1>
            <p class="sub">{{ __('Everything you need to join us: how to reach the venues and your RSVP.') }}</p>
        </div>

        @php
            $deadlineInfo = $rsvpDeadline ?? ['date' => null, 'formatted' => null, 'passed' => false];
        @endphp
        @if (! empty($deadlineInfo['formatted']))
            <div class="rsvp-deadline @if ($deadlineInfo['passed']) rsvp-deadline--passed @endif reveal-on-scroll"
                 role="note">
                @if ($deadlineInfo['passed'])
                    <strong>{{ __('The RSVP deadline has passed.') }}</strong>
                    <span>{{ __('You can still reply, but please let us know directly so we can update the plans.') }}</span>
                @else
                    <strong>{{ __('Please respond by :date.', ['date' => $deadlineInfo['formatted']]) }}</strong>
                    <span>{{ __('It helps us confirm final numbers with the venue and caterer.') }}</span>
                @endif
            </div>
        @endif

        @php
            $companionsToText = static function ($names): string {
                if (! is_array($names)) {
                    return '';
                }
                return implode("\n", array_filter(array_map(
                    static fn ($n): string => is_string($n) ? trim($n) : '',
                    $names,
                ), static fn (string $n): bool => $n !== ''));
            };
        @endphp
        @if ($guest)
            @php
                $rsvpDefault = old('rsvp_status', $guest->rsvp_status);
                $countDefault = old('guests_count', $guest->guests_count);
                if ($countDefault === null && $rsvpDefault === 'yes') {
                    $countDefault = 1;
                }
                $companionsOld = old('companion_names');
                if (is_array($companionsOld)) {
                    $companionsDefault = implode("\n", array_map(
                        static fn ($n): string => is_string($n) ? $n : '',
                        $companionsOld,
                    ));
                } elseif (is_string($companionsOld)) {
                    $companionsDefault = $companionsOld;
                } else {
                    $companionsDefault = $companionsToText($guest->companion_names);
                }
            @endphp
            <div class="card reveal-on-scroll" id="attend-rsvp">
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

                    @include('partials.site.rsvp-companions', [
                        'companionsDefault' => $companionsDefault,
                        'rsvpDefault' => $rsvpDefault,
                        'countDefault' => $countDefault,
                    ])

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
                $companionsOld = old('companion_names');
                if (is_array($companionsOld)) {
                    $companionsDefault = implode("\n", array_map(
                        static fn ($n): string => is_string($n) ? $n : '',
                        $companionsOld,
                    ));
                } elseif (is_string($companionsOld)) {
                    $companionsDefault = $companionsOld;
                } else {
                    $companionsDefault = '';
                }
            @endphp
            <div class="card reveal-on-scroll" id="attend-rsvp">
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

                    @include('partials.site.rsvp-companions', [
                        'companionsDefault' => $companionsDefault,
                        'rsvpDefault' => $rsvpDefault,
                        'countDefault' => $countDefault,
                    ])

                    <button type="submit" class="btn">{{ __('Send response') }}</button>
                </form>
            </div>
        @endif

        @php
            $churchEmbed = trim((string) ($event['maps_church_embed_url'] ?? ''));
            $receptionEmbed = trim((string) ($event['maps_embed_url'] ?? ''));
            $churchValid = $churchEmbed !== '' && str_starts_with($churchEmbed, 'https://');
            $receptionValid = $receptionEmbed !== '' && str_starts_with($receptionEmbed, 'https://');
            $churchOpenUrl = trim((string) ($event['maps_church_url'] ?? ''));
            $receptionOpenUrl = trim((string) ($event['maps_url'] ?? ''));
        @endphp
        @if ($churchValid || $receptionValid)
            <div class="card reveal-on-scroll card--wide" id="attend-map">
                <h2>{{ __('How to get there') }}</h2>
                <div class="map-embed-grid" @class(['map-embed-grid--single' => ! ($churchValid && $receptionValid)])>
                    @if ($churchValid)
                        <div class="map-embed-item">
                            <h3 class="map-embed-item__title">{{ __('Church') }}</h3>
                            <div class="map-embed">
                                <iframe
                                    src="{{ $churchEmbed }}"
                                    title="{{ __('Map of the church') }}"
                                    loading="lazy"
                                    referrerpolicy="no-referrer-when-downgrade"
                                    allowfullscreen
                                ></iframe>
                            </div>
                            @if ($churchOpenUrl !== '')
                                <p class="map-embed__link">
                                    <a href="{{ $churchOpenUrl }}" target="_blank" rel="noopener noreferrer">
                                        {{ __('Open in Google Maps') }}
                                    </a>
                                </p>
                            @endif
                        </div>
                    @endif
                    @if ($receptionValid)
                        <div class="map-embed-item">
                            <h3 class="map-embed-item__title">{{ __('Reception') }}</h3>
                            <div class="map-embed">
                                <iframe
                                    src="{{ $receptionEmbed }}"
                                    title="{{ __('Map of the reception') }}"
                                    loading="lazy"
                                    referrerpolicy="no-referrer-when-downgrade"
                                    allowfullscreen
                                ></iframe>
                            </div>
                            @if ($receptionOpenUrl !== '')
                                <p class="map-embed__link">
                                    <a href="{{ $receptionOpenUrl }}" target="_blank" rel="noopener noreferrer">
                                        {{ __('Open in Google Maps') }}
                                    </a>
                                </p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</x-layouts.site-public>
