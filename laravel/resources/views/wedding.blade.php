<x-layouts.site-public
    :page-title="__($event['title']) . ' — ' . config('app.name', 'Wedding')"
    :brand-title="$event['title']"
    page="wedding"
>
    @if (request()->routeIs('home'))
        @include('partials.site.envelope-intro')
    @endif
    <div class="wrap wrap--wedding">
        @php
            $eventTz = isset($event['timezone']) && $event['timezone'] !== '' && $event['timezone'] !== null
                ? $event['timezone']
                : null;
            $startsAt = $eventTz !== null
                ? \Illuminate\Support\Carbon::parse($event['date'], $eventTz)
                : \Illuminate\Support\Carbon::parse($event['date']);
            $countdownTargetIso = $startsAt->toIso8601String();

            $locale = app()->getLocale();
            $c = $startsAt->copy()->locale($locale);
            $weekday = \Illuminate\Support\Str::ucfirst($c->dayName);
            $month = \Illuminate\Support\Str::ucfirst($c->translatedFormat('F'));
            $day = $locale === 'de' ? $c->format('j.') : (string) $c->day;
            $year = (string) $c->year;
            $time = $c->format('H:i');
            $eventDateTimeLine = __('Wedding event datetime', [
                'weekday' => $weekday,
                'day' => $day,
                'month' => $month,
                'year' => $year,
                'time' => $time,
            ]);
        @endphp

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

        <div class="reveal-on-scroll">
            <h1>{{ __($event['title']) }}</h1>
            <p class="sub">{{ __('It will be a great joy for us and for our parents to have you with us at the beginning of our new life.') }}</p>
        </div>

        @include('partials.site.wedding-monogram')

        @include('partials.site.save-the-date')

        <div
            class="card reveal-on-scroll"
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
            <p class="countdown-actions">
                <a class="btn btn--ghost" href="{{ route('wedding.calendar.ics') }}" download rel="nofollow">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
                        <rect x="3" y="4" width="18" height="18" rx="2"/>
                        <path d="M16 2v4M8 2v4M3 10h18"/>
                        <path d="M12 14v5M9.5 16.5h5"/>
                    </svg>
                    <span>{{ __('Add to calendar') }}</span>
                </a>
            </p>
        </div>

        <section
            class="wedding-hero-serif reveal-on-scroll"
            aria-label="{{ __('When & where') }} — {{ $eventDateTimeLine }}. {{ __('Wedding church venue line') }} {{ __('Wedding reception venue line') }}"
        >
            <p class="wedding-hero-serif__line wedding-hero-serif__line--label">{{ __('When & where') }}</p>
            <p class="wedding-hero-serif__line wedding-hero-serif__line--date">
                <span class="wedding-hero-serif__icons" aria-hidden="true">
                    <svg class="wedding-hero-serif__icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" focusable="false">
                        <rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="1.75" />
                        <path d="M16 2v4M8 2v4M3 10h18" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" />
                    </svg>
                    <svg class="wedding-hero-serif__icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" focusable="false">
                        <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.75" />
                        <path d="M12 7v5l3 2" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </span>
                <span>{{ $eventDateTimeLine }}</span>
            </p>
            <div class="wedding-hero-serif__church">
                <svg class="wedding-hero-serif__icon wedding-hero-serif__icon--church" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" focusable="false" aria-hidden="true">
                    <path d="M12 2v2M10 3h4" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" />
                    <path d="M6 21h12V10l-6-5-6 5v11z" stroke="currentColor" stroke-width="1.75" stroke-linejoin="round" />
                    <path d="M12 12v5" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" />
                </svg>
                <p class="wedding-hero-serif__church-caption">{{ __('Wedding church venue line') }}</p>
            </div>
            <div class="wedding-hero-serif__reception">
                <svg class="wedding-hero-serif__icon wedding-hero-serif__icon--reception" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" focusable="false" aria-hidden="true">
                    <path d="M2 21h20" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" />
                    <path d="M4 21V11h16v10" stroke="currentColor" stroke-width="1.75" stroke-linejoin="round" />
                    <path d="M4 11V7l8-4 8 4v4" stroke="currentColor" stroke-width="1.75" stroke-linejoin="round" />
                    <path d="M8 15v3M12 15v3M16 15v3" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" />
                    <path d="M10 21v-5h4v5" stroke="currentColor" stroke-width="1.75" stroke-linejoin="round" />
                </svg>
                <p class="wedding-hero-serif__church-caption">{{ __('Wedding reception venue line') }}</p>
            </div>
        </section>

        @php
            $churchEmbed = trim((string) ($event['maps_church_embed_url'] ?? ''));
            $receptionEmbed = trim((string) ($event['maps_embed_url'] ?? ''));
            $churchValid = $churchEmbed !== '' && str_starts_with($churchEmbed, 'https://');
            $receptionValid = $receptionEmbed !== '' && str_starts_with($receptionEmbed, 'https://');
            $churchOpenUrl = trim((string) ($event['maps_church_url'] ?? ''));
            $receptionOpenUrl = trim((string) ($event['maps_url'] ?? ''));
        @endphp
        @if ($churchValid || $receptionValid)
            <div class="card reveal-on-scroll">
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

        @if (!empty(trim($event['additional_notes'] ?? '')))
            <div class="card reveal-on-scroll">
                <h2>{{ __('Additional details') }}</h2>
                <div class="notes-body">{{ $event['additional_notes'] }}</div>
            </div>
        @endif

        @if (!empty($faqs))
            <div class="card reveal-on-scroll faqs">
                <h2>{{ __('Frequently asked questions') }}</h2>
                @foreach ($faqs as $faq)
                    <details class="faq">
                        <summary>
                            <span>{{ __($faq['question']) }}</span>
                            <svg class="faq__chevron" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
                                <polyline points="6 9 12 15 18 9"></polyline>
                            </svg>
                        </summary>
                        <div class="faq__body">{!! nl2br(e(__($faq['answer']))) !!}</div>
                    </details>
                @endforeach
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
            <div class="card reveal-on-scroll">
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
            <div class="card reveal-on-scroll">
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
    </div>
</x-layouts.site-public>
