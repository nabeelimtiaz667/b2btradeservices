/**
 * Wires every homepage lead-capture form (partials/lead-capture-inline-form.php,
 * reused across b2b-top-form / multiple-quote-form / the BCM popup) to the same
 * LeadCapture::capture() AJAX endpoint the popup CTA uses -- same response
 * shape ({status, message}), same intlTelInput phone setup as the rest of the
 * site's forms. Each instance is wired independently, since there are several
 * on one page.
 */
(function () {
    'use strict';

    document.querySelectorAll('.lead-capture-inline-form').forEach(function (form) {
        var errorBox = form.querySelector('.lead-capture-error');
        var phoneInput = form.querySelector('.lead-capture-phone');
        var phoneCodeInput = form.querySelector('.lead-capture-phone-code');
        var iti = null;

        if (phoneInput && window.intlTelInput) {
            iti = window.intlTelInput(phoneInput, {
                initialCountry: 'us',
                separateDialCode: true,
                preferredCountries: ['us', 'gb', 'in', 'pk'],
                utilsScript: 'https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.2.1/js/utils.js',
            });
        }

        form.addEventListener('submit', function (e) {
            e.preventDefault();

            if (iti && phoneCodeInput) {
                phoneCodeInput.value = '+' + iti.getSelectedCountryData().dialCode;
            }

            var btn = form.querySelector('button[type="submit"]');
            var originalText = btn.textContent;
            if (errorBox) { errorBox.style.display = 'none'; }
            btn.disabled = true;
            btn.textContent = 'Submitting...';

            fetch(form.dataset.action, {
                method: 'POST',
                body: new FormData(form),
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (data.status === 'success') {
                        form.innerHTML = '<p class="lead-capture-success" style="color:inherit;text-align:center;padding:20px 0;">'
                            + (data.message || "We've sent you a verification link to finish setting up your account.")
                            + '</p>';
                        return;
                    }
                    btn.disabled = false;
                    btn.textContent = originalText;
                    if (errorBox) {
                        errorBox.textContent = data.message || 'Something went wrong. Please try again.';
                        errorBox.style.display = 'block';
                    } else {
                        alert(data.message || 'Something went wrong. Please try again.');
                    }
                })
                .catch(function () {
                    btn.disabled = false;
                    btn.textContent = originalText;
                    if (errorBox) {
                        errorBox.textContent = 'Something went wrong. Please try again.';
                        errorBox.style.display = 'block';
                    } else {
                        alert('Something went wrong. Please try again.');
                    }
                });
        });
    });
})();
