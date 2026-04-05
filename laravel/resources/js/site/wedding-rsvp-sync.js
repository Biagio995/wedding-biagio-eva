(function () {
    var form = document.getElementById('wedding-rsvp-form');
    if (form) {
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
})();
