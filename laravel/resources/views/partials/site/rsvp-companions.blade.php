@php
    /**
     * Displays the "companion names" textarea. One full name per line.
     * Hidden via JS when guests_count < 2, but always rendered so progressive-enhancement keeps the form usable.
     *
     * @var string $companionsDefault
     * @var string|null $rsvpDefault
     * @var int|string|null $countDefault
     */
    $hidden = $rsvpDefault !== 'yes' || ((int) $countDefault) < 2;
@endphp
<div
    class="rsvp-companions"
    data-rsvp-companions
    @if ($hidden) hidden @endif
>
    <label for="companion_names">{{ __('Names of people coming with you') }}</label>
    <textarea
        id="companion_names"
        name="companion_names"
        rows="3"
        maxlength="2000"
        placeholder="{{ __('One name per line') }}"
        aria-describedby="companion_names-help"
    >{{ $companionsDefault }}</textarea>
    <p id="companion_names-help" class="hint" style="margin-top:-0.5rem;">
        {{ __('Optional — helps us prepare place cards and seating.') }}
    </p>
    @error('companion_names')<p class="error">{{ $message }}</p>@enderror
    @error('companion_names.*')<p class="error">{{ $message }}</p>@enderror
</div>
