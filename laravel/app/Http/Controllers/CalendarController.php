<?php

namespace App\Http\Controllers;

use App\Services\WeddingCalendarIcsGenerator;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class CalendarController extends Controller
{
    /** Serve an .ics file with the wedding event (add-to-calendar). */
    public function ics(WeddingCalendarIcsGenerator $generator): Response
    {
        $event = config('wedding.event');
        $appUrl = (string) config('app.url', 'http://localhost');

        $ics = $generator->build(is_array($event) ? $event : [], $appUrl);

        $filename = $this->buildFilename((string) ($event['title'] ?? 'wedding'));

        return response($ics, 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    private function buildFilename(string $title): string
    {
        $slug = Str::slug($title);
        if ($slug === '') {
            $slug = 'wedding';
        }

        return $slug.'.ics';
    }
}
