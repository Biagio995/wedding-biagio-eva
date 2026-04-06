(function () {
    function initWeddingRsvpSync() {
        var form = document.getElementById('wedding-rsvp-form');
        if (!form || form.dataset.rsvpSyncBound === '1') {
            return;
        }
        form.dataset.rsvpSyncBound = '1';
        var count = document.getElementById('guests_count');
        function syncRsvpGuestsField() {
            var checked = form.querySelector('input[name="rsvp_status"]:checked');
            var isNo = checked && checked.value === 'no';
            if (!count) return;
            count.disabled = isNo;
            if (isNo) {
                count.value = '';
            }
        }
        form.querySelectorAll('input[name="rsvp_status"]').forEach(function (r) {
            r.addEventListener('change', syncRsvpGuestsField);
        });
        syncRsvpGuestsField();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initWeddingRsvpSync);
    } else {
        initWeddingRsvpSync();
    }
    document.addEventListener('turbo:load', initWeddingRsvpSync);
})();
