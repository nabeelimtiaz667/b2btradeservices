/**
 * Lead-capture popup engine. Reads config/copy from window.LeadPopupConfig
 * (lead-popup-triggers.js) -- this file is the "how", that file is the "what".
 * Only included on pages where the visitor is logged out (server-side gate in
 * partials/footer.php); nothing here re-checks that.
 */
(function () {
    'use strict';

    var config = window.LeadPopupConfig;
    if (!config) return;

    var modalEl = document.getElementById('leadPopupModal');
    var form = document.getElementById('leadPopupForm');
    if (!modalEl || !form) return;

    var CAPTURE_URL = form.dataset.action;
    var SESSION_DONE_KEY = 'leadPopupDone';
    var fired = {};
    var bsModal = null;

    function getModal() {
        if (!bsModal && window.bootstrap) {
            bsModal = new bootstrap.Modal(modalEl);
        }
        return bsModal;
    }

    function relativePath() {
        var base = window.LEAD_POPUP_BASE_PATH || '';
        var path = window.location.pathname;
        if (base && path.indexOf(base) === 0) {
            path = path.slice(base.length);
        }
        return path || '/';
    }

    function resolveDefaultType() {
        var path = relativePath();
        var rules = config.defaultTypeRules || [];
        for (var i = 0; i < rules.length; i++) {
            if (rules[i].pattern.test(path)) return rules[i].type;
        }
        return config.defaultType || 'buyer';
    }

    function showStep(name) {
        modalEl.querySelectorAll('.lead-popup-step').forEach(function (el) {
            el.classList.toggle('active', el.dataset.step === name);
        });
    }

    function alreadyHandledThisSession() {
        try {
            return sessionStorage.getItem(SESSION_DONE_KEY) === '1';
        } catch (e) {
            return false; // storage unavailable (private mode etc.) -- fail open
        }
    }

    function markHandledThisSession() {
        try {
            sessionStorage.setItem(SESSION_DONE_KEY, '1');
        } catch (e) { /* ignore */ }
    }

    function openPopup(trigger) {
        if (modalEl.classList.contains('show')) return; // already open
        if (alreadyHandledThisSession()) return; // already submitted this session

        document.getElementById('leadPopupHeading').textContent = trigger.text.heading;
        document.getElementById('leadPopupSubtext').textContent = trigger.text.subtext;

        var defaultType = resolveDefaultType();
        document.getElementById('leadPopupTypeSupplier').checked = defaultType === 'supplier';
        document.getElementById('leadPopupTypeBuyer').checked = defaultType === 'buyer';

        document.getElementById('leadPopupError').style.display = 'none';
        form.reset();
        // form.reset() re-applies the checked= HTML attribute set above, but
        // set it again defensively since reset() timing across browsers isn't
        // perfectly consistent with dynamically-changed defaultChecked state.
        document.getElementById('leadPopupTypeSupplier').checked = defaultType === 'supplier';
        document.getElementById('leadPopupTypeBuyer').checked = defaultType === 'buyer';
        showStep('capture');

        var modal = getModal();
        if (modal) modal.show();
    }

    function fire(trigger) {
        if (fired[trigger.key]) return;
        fired[trigger.key] = true;
        openPopup(trigger);
    }

    // --- Trigger listeners -------------------------------------------------

    var isCoarsePointer = window.matchMedia && window.matchMedia('(pointer: coarse)').matches;

    (config.triggers || []).forEach(function (trigger) {
        switch (trigger.type) {
            case 'exit_intent':
                if (isCoarsePointer) break; // no mouse to leave the viewport on touch
                document.addEventListener('mouseout', function (e) {
                    if (e.clientY <= 0 && !e.relatedTarget) fire(trigger);
                });
                break;

            case 'scroll_percent':
                document.addEventListener('scroll', function () {
                    var scrollable = document.documentElement.scrollHeight - window.innerHeight;
                    if (scrollable <= 0) return;
                    var pct = (window.scrollY / scrollable) * 100;
                    if (pct >= trigger.value) fire(trigger);
                }, { passive: true });
                break;

            case 'time':
                setTimeout(function () { fire(trigger); }, trigger.value);
                break;

            case 'section_visible':
                var targets = document.querySelectorAll(trigger.selector);
                if (!targets.length || !window.IntersectionObserver) break;
                var observer = new IntersectionObserver(function (entries) {
                    entries.forEach(function (entry) {
                        if (entry.isIntersecting) {
                            fire(trigger);
                            observer.disconnect();
                        }
                    });
                }, { threshold: 0.4 });
                targets.forEach(function (el) { observer.observe(el); });
                break;
        }
    });

    // --- Phone input (intlTelInput, same pattern as register.php) ---------

    var iti = null;
    var phoneInput = document.getElementById('leadPopupPhone');
    if (phoneInput && window.intlTelInput) {
        iti = window.intlTelInput(phoneInput, {
            initialCountry: 'us',
            separateDialCode: true,
            preferredCountries: ['us', 'gb', 'in', 'pk'],
            utilsScript: 'https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.2.1/js/utils.js',
        });
    }

    // --- Form submit ---------------------------------------------------

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        if (iti) {
            document.getElementById('leadPopupPhoneCode').value = '+' + iti.getSelectedCountryData().dialCode;
        }

        var btn = document.getElementById('leadPopupSubmitBtn');
        var errorBox = document.getElementById('leadPopupError');
        errorBox.style.display = 'none';
        btn.disabled = true;
        btn.textContent = 'Submitting...';

        fetch(CAPTURE_URL, {
            method: 'POST',
            body: new FormData(form),
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
        })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                btn.disabled = false;
                btn.textContent = 'Get Started';

                if (data.status === 'success') {
                    markHandledThisSession();
                    document.getElementById('leadPopupResultHeading').textContent =
                        data.step === 'already_verified' ? 'Almost there' : 'Check your email';
                    document.getElementById('leadPopupResultMessage').textContent =
                        data.message || "We've sent you a verification link to finish setting up your account.";
                    showStep('result');
                    return;
                }

                errorBox.textContent = data.message || 'Something went wrong. Please try again.';
                errorBox.style.display = 'block';
            })
            .catch(function () {
                btn.disabled = false;
                btn.textContent = 'Get Started';
                errorBox.textContent = 'Something went wrong. Please try again.';
                errorBox.style.display = 'block';
            });
    });
})();
