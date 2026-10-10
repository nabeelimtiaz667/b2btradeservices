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

    function openPopup(trigger, force) {
        if (modalEl.classList.contains('show')) return; // already open
        if (!force && alreadyHandledThisSession()) return; // already submitted this session

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

    // --- Timed popups (admin-controlled: Popup Leads > Trigger Settings) ----
    // Case 1: a freshly loaded page shows the popup after firstDelaySeconds.
    // Case 2: a visitor who stays gets it again repeatIntervalSeconds after the
    // previous popup was closed (a countdown from the close, not a fixed clock,
    // so an ignored popup never stacks and a dismissed one never re-opens
    // instantly). 0 switches a case off. Nothing repeats once the visitor has
    // submitted the form this session -- openPopup() refuses and no further
    // countdown is armed.

    var timing = window.LEAD_POPUP_TIMING || config.timing || {};
    var firstDelayMs = secondsToMs(timing.firstDelaySeconds);
    var repeatMs = secondsToMs(timing.repeatIntervalSeconds);
    var repeatTimer = null;

    function secondsToMs(value) {
        var n = parseInt(value, 10);
        return n > 0 ? n * 1000 : 0;
    }

    function timedTrigger(kind) {
        return { key: 'timed_' + kind, text: (config.timedText || {})[kind] || (config.timedText || {}).first };
    }

    // A background tab can't see a popup, so hold it until the tab is visible
    // again instead of opening it unseen.
    function whenVisible(fn) {
        if (!document.hidden) {
            fn();
            return;
        }
        var onVisible = function () {
            if (document.hidden) return;
            document.removeEventListener('visibilitychange', onVisible);
            fn();
        };
        document.addEventListener('visibilitychange', onVisible);
    }

    function scheduleRepeat() {
        if (!repeatMs || alreadyHandledThisSession()) return;
        clearTimeout(repeatTimer);
        repeatTimer = setTimeout(function () {
            whenVisible(function () {
                // If something else opened the popup meanwhile, its own close
                // re-arms the countdown (see the hidden.bs.modal listener).
                if (!modalEl.classList.contains('show')) openPopup(timedTrigger('repeat'));
            });
        }, repeatMs);
    }

    if (firstDelayMs) {
        setTimeout(function () {
            whenVisible(function () { openPopup(timedTrigger('first')); });
        }, firstDelayMs);
    }

    // Any popup closing (whatever opened it) starts the repeat countdown.
    modalEl.addEventListener('hidden.bs.modal', scheduleRepeat);

    // With no case-1 popup there's no close to count from, so start at page load.
    if (!firstDelayMs) scheduleRepeat();

    // --- Manual open (e.g. content-gate "Register" button) -----------------
    // Bypasses the auto-triggers' once-per-session suppression -- a visitor
    // deliberately clicking a button should always see the form, unlike the
    // unprompted nag popups above.

    document.addEventListener('click', function (e) {
        var el = e.target.closest('[data-open-lead-popup]');
        if (!el) return;
        e.preventDefault();
        openPopup({
            key: 'manual',
            text: {
                heading: 'Join B2B Trade Services',
                subtext: "Leave your details and start connecting with trade partners.",
            },
        }, true);
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
            .then(bumpCsrfToken)
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
