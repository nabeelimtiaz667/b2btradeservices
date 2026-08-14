/**
 * Lead-capture popup: triggers, copy, and buyer/supplier defaulting rules.
 * Pure config -- edit thresholds/copy/selectors here freely. The engine that
 * reads this (fires triggers, opens the modal, submits the form) lives in
 * lead-popup.js and shouldn't need touching for day-to-day tuning.
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
     * Each trigger fires at most once per page load. No cooldown between
     * DIFFERENT triggers -- dismissing the popup from one trigger doesn't
     * suppress the others, so keep thresholds spaced out here rather than
     * relying on the engine to throttle for you.
     *
     * type: 'exit_intent'      -- mouse leaves the top of the viewport (desktop only)
     * type: 'scroll_percent'   -- page scrolled past `value` percent
     * type: 'time'             -- `value` milliseconds spent on the page
     * type: 'section_visible'  -- any element matching `selector` scrolls into view
     */
    triggers: [
        {
            key: 'exit_intent',
            type: 'exit_intent',
            text: {
                heading: 'Before you go...',
                subtext: "Get matched with verified trade partners — leave your details and we'll do the rest.",
            },
        },
        {
            key: 'scroll_60',
            type: 'scroll_percent',
            value: 60,
            text: {
                heading: 'Still browsing?',
                subtext: 'Create a free profile and start connecting with buyers and suppliers today.',
            },
        },
        {
            key: 'time_25s',
            type: 'time',
            value: 25000,
            text: {
                heading: "Don't miss out",
                subtext: 'Join B2B Trade Services free and get discovered by global trade partners.',
            },
        },
        {
            key: 'results_visible',
            type: 'section_visible',
            selector: '.supplier-product-list-box, .top-products-box',
            text: {
                heading: 'Like what you see?',
                subtext: 'Sign up free to contact suppliers and buyers directly.',
            },
        },
    ],
};
