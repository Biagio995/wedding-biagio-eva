(function () {
    var root = document.getElementById('wedding-countdown');
    if (!root) return;
    var raw = root.getAttribute('data-countdown-target');
    if (!raw) return;
    var targetMs = Date.parse(raw);
    if (Number.isNaN(targetMs)) return;

    var els = {
        days: root.querySelector('[data-cd="days"]'),
        hours: root.querySelector('[data-cd="hours"]'),
        minutes: root.querySelector('[data-cd="minutes"]'),
        seconds: root.querySelector('[data-cd="seconds"]'),
    };
    var doneEl = document.getElementById('wedding-countdown-done');
    var grid = root.querySelector('.countdown-grid');

    function pad(n) {
        return String(n);
    }

    function tick() {
        var diff = targetMs - Date.now();
        if (diff <= 0) {
            root.classList.add('countdown-done');
            if (grid) grid.setAttribute('hidden', 'hidden');
            if (doneEl) doneEl.removeAttribute('hidden');
            return false;
        }
        var totalSec = Math.floor(diff / 1000);
        var d = Math.floor(totalSec / 86400);
        var h = Math.floor((totalSec % 86400) / 3600);
        var m = Math.floor((totalSec % 3600) / 60);
        var s = totalSec % 60;
        if (els.days) els.days.textContent = pad(d);
        if (els.hours) els.hours.textContent = pad(h);
        if (els.minutes) els.minutes.textContent = pad(m);
        if (els.seconds) els.seconds.textContent = pad(s);
        return true;
    }

    if (!tick()) {
        return;
    }
    var iv = setInterval(function () {
        if (!tick()) {
            clearInterval(iv);
        }
    }, 1000);
})();
