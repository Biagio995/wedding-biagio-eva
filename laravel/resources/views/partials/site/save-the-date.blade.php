{{-- Save the date: giugno 2027 (1° = martedì), testi tradotti (it/el/de) --}}
@php
    $stdYear = 2027;
    $stdMonth = 6;
    $stdHighlight = 26;
    $firstOfMonth = \Illuminate\Support\Carbon::create($stdYear, $stdMonth, 1);
    $leadingBlank = $firstOfMonth->dayOfWeekIso - 1;
    $daysInMonth = $firstOfMonth->daysInMonth;
    $weekdayKeys = ['MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT', 'SUN'];
@endphp
<section class="save-the-date" aria-label="{{ __('Save the date') }}">
    <div class="save-the-date__panel">
        <div class="save-the-date__month-row">
            <span class="save-the-date__arrow save-the-date__arrow--left" aria-hidden="true"></span>
            <span class="save-the-date__month">{{ __('June') }}</span>
            <span class="save-the-date__arrow save-the-date__arrow--right" aria-hidden="true"></span>
        </div>
        <div class="save-the-date__weekdays" aria-hidden="true">
            @foreach ($weekdayKeys as $key)
                <span>{{ __($key) }}</span>
            @endforeach
        </div>
        <div class="save-the-date__grid" role="grid" aria-label="{{ __('June 2027') }}">
            @for ($i = 0; $i < $leadingBlank; $i++)
                <div class="save-the-date__cell save-the-date__cell--pad" aria-hidden="true"></div>
            @endfor
            @foreach (range(1, $daysInMonth) as $d)
                @if ($d === $stdHighlight)
                    <div class="save-the-date__cell save-the-date__cell--heart" role="gridcell">
                        <span class="save-the-date__heart" aria-hidden="true">
                            <svg viewBox="0 0 24 24" width="40" height="40" focusable="false">
                                <path fill="currentColor" d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                            </svg>
                        </span>
                        <span class="save-the-date__day-num">{{ $d }}</span>
                    </div>
                @else
                    <div class="save-the-date__cell" role="gridcell">
                        <span class="save-the-date__day-num save-the-date__day-num--muted">{{ $d }}</span>
                    </div>
                @endif
            @endforeach
        </div>
        <p class="save-the-date__line save-the-date__line--caps">{{ __('26 JUNE 2027') }}</p>
        <p class="save-the-date__line save-the-date__line--caps">BIAGIO &amp; EVA</p>
        <div class="save-the-date__rule" aria-hidden="true"></div>
    </div>
</section>

<section class="wedding-hero-serif" aria-label="{{ __('Biagio & Eva — 26 june 2027') }}">
    <p class="wedding-hero-serif__line wedding-hero-serif__line--names">biagio &amp; eva</p>
    <p class="wedding-hero-serif__line wedding-hero-serif__line--date">{{ __('26 june 2027') }}</p>
</section>
