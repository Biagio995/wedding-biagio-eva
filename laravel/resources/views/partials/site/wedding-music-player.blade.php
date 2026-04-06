<div
    id="wedding-music-player"
    class="wedding-music-player"
    data-turbo-permanent
    data-play-label="{{ __('Play wedding music') }}"
    data-pause-label="{{ __('Pause wedding music') }}"
>
    <audio preload="metadata" loop playsinline>
        <source src="{{ asset('audio/ordinary-violin-wedding.mp3') }}" type="audio/mpeg">
    </audio>
    <button
        type="button"
        class="wedding-music-player__toggle"
        aria-pressed="false"
        aria-label="{{ __('Play wedding music') }}"
    >
        <span class="wedding-music-player__art" aria-hidden="true">
            <span class="wedding-music-player__disc">
                <span class="wedding-music-player__label">
                    {{-- Violino stilizzato al centro del “disco” --}}
                    <svg class="wedding-music-player__violin" width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" focusable="false">
                        <path d="M9 3c.5 2.2 1.2 4 2.5 5.2C12.8 9.5 14 10 15.5 10.5c1.2.4 2.5.8 3.5 1.8 1.5 1.5 1.8 3.5 1 5.5-.8 2-2.5 3.2-4.5 3.5-2.5.4-5-1-6-3.2-.8-1.8-.5-3.8 1-5.5.6-.7 1.3-1.2 2-1.6" stroke="currentColor" stroke-width="1.35" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M7 21l2.5-2.5M5 19l2-2" stroke="currentColor" stroke-width="1.35" stroke-linecap="round"/>
                        <path d="M11.5 8.5L14 6" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/>
                    </svg>
                </span>
            </span>
            <span class="wedding-music-player__overlay">
                <svg class="wedding-music-player__icon wedding-music-player__icon--play" width="28" height="28" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                    <circle cx="12" cy="12" r="11" fill="rgba(255,252,249,0.92)" stroke="rgba(58,50,42,0.2)" stroke-width="1"/>
                    <path d="M10 8.5v7l6-3.5-6-3.5z" fill="var(--text, #3a322a)"/>
                </svg>
                <svg class="wedding-music-player__icon wedding-music-player__icon--pause" width="28" height="28" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                    <circle cx="12" cy="12" r="11" fill="rgba(255,252,249,0.92)" stroke="rgba(58,50,42,0.2)" stroke-width="1"/>
                    <path d="M9 8.5h2.5v7H9v-7zm5.5 0H17v7h-2.5v-7z" fill="var(--text, #3a322a)"/>
                </svg>
            </span>
        </span>
    </button>
</div>
