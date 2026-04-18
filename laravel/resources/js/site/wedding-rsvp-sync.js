(function () {
    function initWeddingRsvpSync() {
        var form = document.getElementById('wedding-rsvp-form');
        if (!form || form.dataset.rsvpSyncBound === '1') {
            return;
        }
        form.dataset.rsvpSyncBound = '1';
        var count = document.getElementById('guests_count');
        var companions = form.querySelector('[data-rsvp-companions]');
        var companionsField = document.getElementById('companion_names');

        function isAttendingYes() {
            var checked = form.querySelector('input[name="rsvp_status"]:checked');
            return !!(checked && checked.value === 'yes');
        }

        function currentCount() {
            if (!count || !count.value) return 0;
            var n = parseInt(count.value, 10);
            return isFinite(n) ? n : 0;
        }

        function syncCompanionsVisibility() {
            if (!companions) return;
            var show = isAttendingYes() && currentCount() >= 2;
            companions.hidden = !show;
            if (!show && companionsField) {
                companionsField.value = '';
            }
        }

        function syncRsvpGuestsField() {
            var isNo = !isAttendingYes() && !!form.querySelector('input[name="rsvp_status"]:checked');
            if (count) {
                count.disabled = isNo;
                if (isNo) {
                    count.value = '';
                }
            }
            syncCompanionsVisibility();
        }

        form.querySelectorAll('input[name="rsvp_status"]').forEach(function (r) {
            r.addEventListener('change', syncRsvpGuestsField);
        });
        if (count) {
            count.addEventListener('input', syncCompanionsVisibility);
            count.addEventListener('change', syncCompanionsVisibility);
        }
        syncRsvpGuestsField();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initWeddingRsvpSync);
    } else {
        initWeddingRsvpSync();
    }
    document.addEventListener('turbo:load', initWeddingRsvpSync);
})();
