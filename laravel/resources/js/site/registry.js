/**
 * Lista nozze: invio moduli tramite checkbox (prenota / annulla) senza pulsanti submit.
 */
function initRegistryGiftCheckboxes() {
    document.querySelectorAll('form.registry-claim:not([data-registry-init])').forEach((form) => {
        form.setAttribute('data-registry-init', '1');
        const toggle = form.querySelector('.registry-claim__toggle');
        const nameInput = form.querySelector('.registry-claim__input');
        if (!toggle || !nameInput) {
            return;
        }

        const trySubmitClaim = () => {
            if (!nameInput.value.trim()) {
                toggle.checked = false;
                nameInput.reportValidity();
                nameInput.focus();

                return;
            }
            form.submit();
        };

        toggle.addEventListener('change', () => {
            if (!toggle.checked) {
                return;
            }
            trySubmitClaim();
        });

        nameInput.addEventListener('keydown', (e) => {
            if (e.key !== 'Enter') {
                return;
            }
            e.preventDefault();
            if (!nameInput.value.trim()) {
                nameInput.reportValidity();

                return;
            }
            toggle.checked = true;
            trySubmitClaim();
        });
    });
}

document.addEventListener('DOMContentLoaded', initRegistryGiftCheckboxes);
document.addEventListener('turbo:load', initRegistryGiftCheckboxes);
