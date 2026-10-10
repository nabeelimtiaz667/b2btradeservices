/**
 * Lead-capture popup: timing fallbacks, copy, and buyer/supplier defaulting
 * rules. The popup is shown only by the two timers below (first delay, repeat
 * interval) -- there are no scroll, exit-intent or other event triggers.
 * Pure config -- edit copy/fallbacks here freely. The engine that reads this
 * (runs the timers, opens the modal, submits the form) lives in lead-popup.js
 * and shouldn't need touching for day-to-day tuning.
 *
 * See .claude/plans/T-29-lead-capture.md for the full design.
 */
window.LeadPopupConfig = {
    /**
     * Which radio (buyer/supplier) is pre-selected when the popup opens, based
     * on the current page. First matching pattern wins. Rationale: a page about
     * buyers/inquiries is being browsed by someone looking FOR buyers, i.e. a
     * prospective supplier, so it defaults the opposite of what the page is
     * about -- and vice versa for supplier/product pages.
     */
    defaultTypeRules: [
        { pattern: /^\/$/, type: 'supplier' },
        { pattern: /^\/buyer-detail(\/|$)/, type: 'supplier' },
        { pattern: /^\/buyer-inquiry(\/|$)/, type: 'supplier' },
        { pattern: /^\/buyers?\/search(\/|$)/, type: 'supplier' },
        { pattern: /^\/buyers?(\/|$)/, type: 'supplier' },
        { pattern: /^\/buyer-category(\/|$)/, type: 'supplier' },

        { pattern: /^\/supplier(\/|$)/, type: 'buyer' },
        { pattern: /^\/supplier-profile(\/|$)/, type: 'buyer' },
        { pattern: /^\/supplier-country(\/|$)/, type: 'buyer' },
        { pattern: /^\/supplier-category(\/|$)/, type: 'buyer' },
        { pattern: /^\/product(\/|$)/, type: 'buyer' },
    ],
    // Falls back here when no rule above matches (the site's original default).
    defaultType: 'buyer',

    /**
     * Timed popups -- the two cases an admin controls under Popup Leads >
     * Trigger Settings. The admin's values arrive as window.LEAD_POPUP_TIMING
     * (printed by partials/lead-popup-modal.php); the numbers below are only
     * the fallback if that is missing. 0 switches a case off.
     *
     *   firstDelaySeconds     -- case 1: a freshly loaded page shows the popup
     *                            after this many seconds.
     *   repeatIntervalSeconds -- case 2: a visitor who stays on the page gets
     *                            the popup again this many seconds after the
     *                            previous one was closed.
     */
    timing: {
        firstDelaySeconds: 25,
        repeatIntervalSeconds: 0,
    },

    timedText: {
        first: {
            heading: "Don't miss out",
            subtext: 'Join B2B Trade Services free and get discovered by global trade partners.',
        },
        repeat: {
            heading: 'Still browsing?',
            subtext: 'Create a free profile and start connecting with buyers and suppliers today.',
        },
    },
};
