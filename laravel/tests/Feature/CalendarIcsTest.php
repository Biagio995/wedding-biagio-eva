<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class CalendarIcsTest extends TestCase
{
    use RefreshDatabase;

    public function test_ics_endpoint_returns_calendar_payload(): void
    {
        Config::set('wedding.event.title', 'Anna & Marco wedding');
        Config::set('wedding.event.calendar_title', '');
        Config::set('wedding.event.date', '2027-06-26 19:00');
        Config::set('wedding.event.timezone', 'Europe/Athens');
        Config::set('wedding.event.location_name', 'Villa Rosa');
        Config::set('wedding.event.location_address', 'Via dei Ciliegi 1, Roma');
        Config::set('wedding.event.description', 'We would love to celebrate with you.');
        Config::set('wedding.event.maps_url', 'https://maps.example/villa');

        $response = $this->get('/w/calendar.ics');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/calendar; charset=utf-8');

        $disposition = $response->headers->get('Content-Disposition');
        $this->assertIsString($disposition);
        $this->assertStringContainsString('attachment', $disposition);
        $this->assertStringContainsString('.ics', $disposition);

        $body = (string) $response->getContent();
        $this->assertStringContainsString('BEGIN:VCALENDAR', $body);
        $this->assertStringContainsString('BEGIN:VEVENT', $body);
        $this->assertStringContainsString('END:VEVENT', $body);
        $this->assertStringContainsString('END:VCALENDAR', $body);
        $this->assertStringContainsString('SUMMARY:Anna & Marco wedding', $body);
        $this->assertStringContainsString('LOCATION:Villa Rosa\\, Via dei Ciliegi 1\\, Roma', $body);
        $this->assertStringContainsString('URL:https://maps.example/villa', $body);
        $this->assertMatchesRegularExpression('/DTSTART:\d{8}T\d{6}Z/', $body);
        $this->assertMatchesRegularExpression('/DTEND:\d{8}T\d{6}Z/', $body);
    }

    public function test_calendar_title_overrides_summary_and_filename(): void
    {
        Config::set('wedding.event.title', 'Our wedding');
        Config::set('wedding.event.calendar_title', 'Biagio&Eva Wedding');
        Config::set('wedding.event.date', '2027-06-26 19:00');
        Config::set('wedding.event.timezone', 'Europe/Athens');

        $response = $this->get('/w/calendar.ics');
        $response->assertOk();

        $body = (string) $response->getContent();
        $this->assertStringContainsString('SUMMARY:Biagio&Eva Wedding', $body);
        $this->assertStringNotContainsString('SUMMARY:Our wedding', $body);

        $disposition = (string) $response->headers->get('Content-Disposition');
        // Str::slug() drops the ampersand, collapsing "Biagio&Eva" into "biagioeva".
        $this->assertStringContainsString('biagioeva-wedding.ics', $disposition);
    }

    public function test_event_duration_hours_controls_dtend(): void
    {
        Config::set('wedding.event.title', 'Late-night wedding');
        Config::set('wedding.event.calendar_title', '');
        Config::set('wedding.event.date', '2027-06-26 19:00');
        Config::set('wedding.event.timezone', 'Europe/Athens');
        Config::set('wedding.event.duration_hours', 7);

        $body = (string) $this->get('/w/calendar.ics')->assertOk()->getContent();

        /**
         * 2027-06-26 19:00 Athens (UTC+3) = 2027-06-26 16:00 UTC
         * + 7h = 2027-06-26 23:00 UTC → 2027-06-27 02:00 Athens.
         */
        $this->assertStringContainsString('DTSTART:20270626T160000Z', $body);
        $this->assertStringContainsString('DTEND:20270626T230000Z', $body);
    }

    public function test_ics_endpoint_tolerates_missing_optional_fields(): void
    {
        Config::set('wedding.event.title', 'Simple wedding');
        Config::set('wedding.event.calendar_title', '');
        Config::set('wedding.event.date', '2030-09-12 17:00');
        Config::set('wedding.event.timezone', 'Europe/Rome');
        Config::set('wedding.event.location_name', '');
        Config::set('wedding.event.location_address', '');
        Config::set('wedding.event.description', '');
        Config::set('wedding.event.maps_url', '');

        $response = $this->get('/w/calendar.ics');
        $response->assertOk();

        $body = (string) $response->getContent();
        $this->assertStringNotContainsString('LOCATION:', $body);
        $this->assertStringNotContainsString('URL:', $body);
        $this->assertStringNotContainsString('DESCRIPTION:', $body);
    }
}
