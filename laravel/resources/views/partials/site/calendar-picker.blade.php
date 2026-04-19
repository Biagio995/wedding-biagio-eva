@php
    /**
     * Compact "Add to calendar" dropdown.
     *
     * Renders a single pill button that reveals three direct-add providers
     * (Google / Apple / Outlook). Each option opens the provider's event-creation
     * UI with the event pre-filled — no .ics download step.
     *
     * Expected variables (injected from the including view or resolved below):
     *   - $event    array  wedding event config (config('wedding.event'))
     */
    $event = $event ?? config('wedding.event');

    $eventTz = isset($event['timezone']) && $event['timezone'] !== '' && $event['timezone'] !== null
        ? $event['timezone']
        : null;
    $eventStart = $eventTz !== null
        ? \Illuminate\Support\Carbon::parse($event['date'], $eventTz)
        : \Illuminate\Support\Carbon::parse($event['date']);
    $eventStartUtc = $eventStart->copy()->setTimezone('UTC');
    $eventDurationHours = max(1, (int) ($event['duration_hours'] ?? 7));
    $eventEndUtc = $eventStartUtc->copy()->addHours($eventDurationHours);

    $calendarTitle = trim((string) ($event['calendar_title'] ?? '')) !== ''
        ? __($event['calendar_title'])
        : __($event['title']);
    $calendarLocation = trim(implode(', ', array_filter([
        trim((string) ($event['location_name'] ?? '')),
        trim((string) ($event['location_address'] ?? '')),
    ])));
    $calendarMapsUrl = trim((string) ($event['maps_url'] ?? ''));
    $calendarDescription = trim(
        __($event['description'] ?? '')
        .($calendarMapsUrl !== '' ? "\n\n".$calendarMapsUrl : '')
    );

    $googleCalendarUrl = 'https://calendar.google.com/calendar/render?'.http_build_query([
        'action' => 'TEMPLATE',
        'text' => $calendarTitle,
        'dates' => $eventStartUtc->format('Ymd\THis\Z').'/'.$eventEndUtc->format('Ymd\THis\Z'),
        'details' => $calendarDescription,
        'location' => $calendarLocation,
    ]);

    $outlookCalendarUrl = 'https://outlook.live.com/calendar/0/deeplink/compose?'.http_build_query([
        'path' => '/calendar/action/compose',
        'rru' => 'addevent',
        'subject' => $calendarTitle,
        'startdt' => $eventStartUtc->toIso8601String(),
        'enddt' => $eventEndUtc->toIso8601String(),
        'body' => $calendarDescription,
        'location' => $calendarLocation,
    ]);

    /**
     * webcal:// hands the event straight to the system Calendar app on iOS/macOS
     * (and most Windows mail clients with a calendar handler), without saving an .ics
     * to Downloads.
     */
    $appleCalendarUrl = preg_replace(
        '#^https?://#i',
        'webcal://',
        route('wedding.calendar.ics', [], absolute: true)
    );
@endphp

<details class="calendar-picker">
    <summary class="btn btn--ghost calendar-picker__trigger">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
            <rect x="3" y="4" width="18" height="18" rx="2"/>
            <path d="M16 2v4M8 2v4M3 10h18"/>
            <path d="M12 14v5M9.5 16.5h5"/>
        </svg>
        <span>{{ __('Add to calendar') }}</span>
        <svg class="calendar-picker__chevron" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
            <polyline points="6 9 12 15 18 9"></polyline>
        </svg>
    </summary>
    <ul class="calendar-picker__menu" role="menu" aria-label="{{ __('Choose a calendar') }}">
        <li role="none">
            <a role="menuitem" class="calendar-picker__item" href="{{ $googleCalendarUrl }}" target="_blank" rel="noopener">
                {{ __('Google Calendar') }}
            </a>
        </li>
        <li role="none">
            <a role="menuitem" class="calendar-picker__item" href="{{ $appleCalendarUrl }}" rel="nofollow">
                {{ __('Apple Calendar (iOS / macOS)') }}
            </a>
        </li>
        <li role="none">
            <a role="menuitem" class="calendar-picker__item" href="{{ $outlookCalendarUrl }}" target="_blank" rel="noopener">
                {{ __('Outlook') }}
            </a>
        </li>
    </ul>
</details>
