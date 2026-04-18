<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * Builds an RFC 5545 VCALENDAR/VEVENT for the wedding event (US: add-to-calendar).
 *
 * We intentionally generate a timed event in UTC (DTSTART:Z / DTEND:Z) so that clients
 * across time zones display the same absolute instant without depending on VTIMEZONE.
 */
class WeddingCalendarIcsGenerator
{
    /** Default duration when no explicit end is configured. */
    private const DEFAULT_DURATION_HOURS = 5;

    /** @param array<string, mixed> $event */
    public function build(array $event, string $appUrl): string
    {
        [$start, $end] = $this->resolveStartAndEnd($event);

        $calendarTitle = trim((string) ($event['calendar_title'] ?? ''));
        $title = $calendarTitle !== ''
            ? $calendarTitle
            : (string) ($event['title'] ?? 'Our wedding');
        $description = (string) ($event['description'] ?? '');
        $locationParts = array_filter([
            (string) ($event['location_name'] ?? ''),
            (string) ($event['location_address'] ?? ''),
        ], static fn (string $s): bool => $s !== '');
        $location = implode(', ', $locationParts);
        $mapsUrl = (string) ($event['maps_url'] ?? '');

        $uid = $this->buildUid($appUrl, $start);
        $dtstamp = Carbon::now('UTC')->format('Ymd\THis\Z');

        $lines = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//Wedding//Invitation//EN',
            'CALSCALE:GREGORIAN',
            'METHOD:PUBLISH',
            'BEGIN:VEVENT',
            'UID:'.$uid,
            'DTSTAMP:'.$dtstamp,
            'DTSTART:'.$start->copy()->setTimezone('UTC')->format('Ymd\THis\Z'),
            'DTEND:'.$end->copy()->setTimezone('UTC')->format('Ymd\THis\Z'),
            'SUMMARY:'.$this->escape($title),
        ];

        if ($description !== '' || $mapsUrl !== '') {
            $fullDescription = trim($description.($mapsUrl !== '' ? "\n".$mapsUrl : ''));
            $lines[] = 'DESCRIPTION:'.$this->escape($fullDescription);
        }

        if ($location !== '') {
            $lines[] = 'LOCATION:'.$this->escape($location);
        }

        if ($mapsUrl !== '') {
            $lines[] = 'URL:'.$this->escape($mapsUrl);
        }

        $lines[] = 'END:VEVENT';
        $lines[] = 'END:VCALENDAR';

        $folded = array_map([$this, 'foldLine'], $lines);

        return implode("\r\n", $folded)."\r\n";
    }

    /**
     * @param array<string, mixed> $event
     * @return array{0: Carbon, 1: Carbon}
     */
    private function resolveStartAndEnd(array $event): array
    {
        $timezone = $event['timezone'] ?? null;
        $timezone = is_string($timezone) && $timezone !== '' ? $timezone : config('app.timezone', 'UTC');

        $dateRaw = (string) ($event['date'] ?? 'now');
        $start = Carbon::parse($dateRaw, $timezone);

        $end = $start->copy()->addHours(self::DEFAULT_DURATION_HOURS);

        return [$start, $end];
    }

    private function buildUid(string $appUrl, Carbon $start): string
    {
        $host = parse_url($appUrl, PHP_URL_HOST) ?: 'wedding.local';
        $slug = Str::slug($host) ?: 'wedding';

        return $start->copy()->setTimezone('UTC')->format('Ymd\THis\Z').'-'.$slug;
    }

    /**
     * Escape per RFC 5545: backslash, comma, semicolon, and newlines.
     */
    private function escape(string $value): string
    {
        $value = str_replace(["\\", "\r\n", "\r", "\n", ',', ';'], ['\\\\', '\\n', '\\n', '\\n', '\\,', '\\;'], $value);

        return $value;
    }

    /**
     * RFC 5545 line folding: no line may exceed 75 octets; continuation lines start with a space.
     */
    private function foldLine(string $line): string
    {
        if (strlen($line) <= 75) {
            return $line;
        }

        $out = '';
        $remaining = $line;
        $first = true;

        while ($remaining !== '') {
            $chunkSize = $first ? 75 : 74;
            $chunk = substr($remaining, 0, $chunkSize);
            $remaining = substr($remaining, $chunkSize);
            $out .= ($first ? '' : "\r\n ").$chunk;
            $first = false;
        }

        return $out;
    }
}
