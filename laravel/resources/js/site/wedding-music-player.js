function initWeddingMusicPlayer() {
    const root = document.getElementById('wedding-music-player');
    if (!root || root.dataset.musicPlayerBound === '1') {
        return;
    }

    const audio = root.querySelector('audio');
    const btn = root.querySelector('.wedding-music-player__toggle');
    if (!audio || !btn) {
        return;
    }

    root.dataset.musicPlayerBound = '1';

    const playLabel = root.dataset.playLabel || 'Play';
    const pauseLabel = root.dataset.pauseLabel || 'Pause';

    const setUi = (playing) => {
        root.classList.toggle('is-playing', playing);
        btn.setAttribute('aria-pressed', playing ? 'true' : 'false');
        btn.setAttribute('aria-label', playing ? pauseLabel : playLabel);
    };

    btn.addEventListener('click', async () => {
        try {
            if (audio.paused) {
                await audio.play();
            } else {
                audio.pause();
            }
        } catch {
            setUi(false);
        }
    });

    audio.addEventListener('play', () => setUi(true));
    audio.addEventListener('pause', () => setUi(false));
}

document.addEventListener('turbo:load', initWeddingMusicPlayer);
initWeddingMusicPlayer();
