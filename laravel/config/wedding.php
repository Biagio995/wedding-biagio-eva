<?php

return [

    /*
    | UI languages (session key `locale`). Names are shown in the language switcher.
    */
    'locales' => [
        'it' => 'Italiano',
        'el' => 'Ελληνικά',
        'de' => 'Deutsch',
    ],

    /*
    | US-29: Guest access does not use Laravel user accounts or registration.
    | Guests are recognized via opaque token (URL/QR) and session keys only; RSVP and
    | gallery uploads never require sign-up. Admin routes under /admin are separate.
    */

    /*
    | Event details for the public wedding page (US-03: date, place, Maps, notes).
    | Override via .env; you can also edit values here for multi-line notes.
    */
    'event' => [
        'title' => env('WEDDING_EVENT_TITLE', 'Our wedding'),
        /** Local wall-clock start: interpreted in `timezone` (or app timezone). */
        'date' => env('WEDDING_EVENT_DATE', '2027-06-26 19:00'),
        'timezone' => env('WEDDING_EVENT_TIMEZONE', 'Europe/Athens'),
        'location_name' => env('WEDDING_LOCATION_NAME', 'Venue TBD'),
        'location_address' => env('WEDDING_LOCATION_ADDRESS', ''),
        /** Full Google Maps URL (place or directions). Shown only if non-empty. */
        'maps_url' => env('WEDDING_MAPS_URL', ''),
        /**
         * Optional embeddable map URL (e.g. Google Maps share → Embed a map → copy the `src` URL,
         * or OpenStreetMap → Share → HTML → copy the `src`). Only URLs starting with https:// are
         * rendered inside an <iframe>. Leave empty to fall back to the plain "Open in Maps" link.
         */
        'maps_embed_url' => env('WEDDING_MAPS_EMBED_URL', ''),
        'description' => env('WEDDING_DESCRIPTION', 'We would love to celebrate with you.'),
        /**
         * Extra info for guests (dress code, schedule, parking, gifts, etc.).
         * Use newlines in .env with quoted strings, or set this key in this file as a heredoc.
         */
        'additional_notes' => env('WEDDING_ADDITIONAL_NOTES', ''),
    ],

    /*
    | Frequently Asked Questions shown on the public wedding page.
    | Each item: ['question' => '...', 'answer' => '...'].
    | Strings are rendered with __(), so you can either put the final text here in your
    | preferred language, or use translation keys and add them to lang/{locale}.json.
    */
    'faqs' => [
        // [
        //     'question' => 'Are children welcome?',
        //     'answer' => 'Yes, little ones are welcome. Please tell us their names and ages in the notes.',
        // ],
        // [
        //     'question' => 'Is there parking at the venue?',
        //     'answer' => 'Free parking is available on-site, and there is overflow parking 3 minutes on foot.',
        // ],
    ],

    /*
    | US-08: optional confirmation email after RSVP (requires guest email and working mail config).
    */
    'rsvp' => [
        'send_confirmation_email' => env('WEDDING_SEND_RSVP_CONFIRMATION_EMAIL', false),
        /** US-24: optional admin inbox when any guest submits or updates an RSVP (requires MAIL_*). */
        'notify_admin_email' => env('WEDDING_ADMIN_RSVP_NOTIFY_EMAIL'),
    ],

    /*
    | US-25: optional scheduled reminders for guests who have not RSVP’d yet (requires email).
    */
    'rsvp_reminders' => [
        'enabled' => env('WEDDING_RSVP_REMINDERS_ENABLED', false),
        /** Minimum days between reminders per guest (after first send). */
        'cooldown_days' => max(1, (int) env('WEDDING_RSVP_REMINDER_COOLDOWN_DAYS', 14)),
    ],

    /*
    | US-16: admin UI for guest management. Set `password_hash` to a bcrypt string, e.g.
    | `php artisan tinker` → `\Illuminate\Support\Facades\Hash::make('your-secret')`
    | Leave empty to disable admin routes (login returns 503).
    */
    'admin' => [
        'password_hash' => env('WEDDING_ADMIN_PASSWORD_HASH'),
        /** US-18: CSV guest import limits. */
        'csv_max_rows' => max(1, min(2000, (int) env('WEDDING_ADMIN_CSV_MAX_ROWS', 500))),
        'csv_max_file_kilobytes' => max(64, min(8192, (int) env('WEDDING_ADMIN_CSV_MAX_KB', 1024))),
    ],

];
