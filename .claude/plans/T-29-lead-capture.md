# T-29 — Two-step lead capture: popup CTA → email-verified account creation

Full design for the lead-capture feature. Referenced from [TASKS.md](../TASKS.md).
Decisions below came out of a planning conversation with Nabeel on 2026-08-14 —
treat this file as the source of truth for that discussion, not the conversation log.

## Objective

Buyers/suppliers currently only get created through the existing `/register` form,
straight into `users`. This adds a second, lighter-weight path: a popup CTA shown to
logged-out visitors that captures a minimal lead, then converts that lead into a real
`users` row once they've verified their email and filled in the rest.

`users` itself is not altered by this work. `leads` has no FK to `users` — it is a
capture mechanism, not a dependency.

## Schema — new `leads` table (new migration)

```
id                              PK
user_type                       ENUM('supplier','buyer')
name                            VARCHAR(255)
email                           VARCHAR(255) UNIQUE
phone, phone_code               VARCHAR
whatsapp                        TINYINT(1)
status                          ENUM('new','verified','converted') DEFAULT 'new'
verification_token              VARCHAR(64), indexed
verification_token_expires_at   DATETIME   -- UTC, set on token issue, +7 days
verified_at                     DATETIME NULL
created_at / updated_at
```

Only step-1 data lives here: name, type, email, phone, whatsapp. Step 2's fields
(password, company, country, products) are never stored on `leads` — they go
straight into the `users` insert when step 2 completes. `status` is the only
lifecycle field; there is no separate "converted_at" or conversion-tracking layer.

`verification_token_expires_at` and `verified_at` are `DATETIME`, deliberately not
`TIMESTAMP`. See "Timezone & expiry safety" below for why that column type matters
here, not just as a style choice.

## Timezone & expiry safety

Expiry is decided entirely server-side — the token is opaque, the click just carries
it to the server, so the browser's/visitor's local clock never enters the comparison.
The actual risk is a **server-internal** one: PHP's clock and MySQL's clock disagreeing.
Concretely, two independent failure modes, both neutralized the same way:

1. **PHP timezone drift.** CI4 already calls `date_default_timezone_set($appTimezone)`
   on every bootstrap (`system/CodeIgniter.php:192`), and `App::$appTimezone` is `UTC`
   — but that's an app-wide config value something else could change later. Rather than
   depend on it staying `UTC`, every expiry write and comparison in this feature uses
   PHP's `gmdate('Y-m-d H:i:s')` explicitly, not bare `date()`. `gmdate()` always
   returns UTC regardless of what `date_default_timezone_set()` is currently pointed
   at, so this piece is correct even if the app's timezone config is ever changed for
   an unrelated reason.
2. **MySQL timezone conversion.** MySQL's `TIMESTAMP` type silently converts values
   between UTC storage and the session's `time_zone` setting on every read/write —
   exactly the kind of mismatch being worried about here, especially since this
   machine's OS clock and the app's configured timezone already disagree (Windows
   local time runs ahead of the app's UTC, per [CONTEXT.md](../CONTEXT.md) point 8).
   `DATETIME` has no such conversion — it stores and returns the literal string it was
   given, full stop. Using `DATETIME` plus PHP-generated UTC strings on both sides
   means MySQL's own session timezone becomes irrelevant to this feature entirely.

This is the same technique the codebase already uses for password-reset tokens
(`Auth.php`'s `reset_token_expires`, compared with `date('Y-m-d H:i:s')`) — extended
here with the `gmdate()` tightening so it's correct independent of app config, and
called out explicitly rather than left implicit.

Practically: `LeadModel` issues expiry as `gmdate('Y-m-d H:i:s', strtotime('+7 days'))`
at write time, and the verify-link expiry check compares against `gmdate('Y-m-d H:i:s')`
at read time — never against MySQL's own `NOW()`/`CURRENT_TIMESTAMP`, so there is
exactly one clock in play (PHP's, forced to UTC) for the entire comparison.

## Status lifecycle

**Step 1 submit (popup), by email:**
- Email already exists in `users` → reject: "you already have an account, please log in."
- No existing `leads` row → insert new row, `status='new'`, issue token (+7 days), send verification email.
- Existing `leads` row, `status='new'` → **update** the row's name/type/phone/whatsapp,
  **reissue** a fresh token and expiry, resend the verification email. This is what
  makes an expired, never-verified link self-healing — no separate regeneration UI needed.
- Existing `leads` row, `status='verified'` → update contact fields only; token/status
  untouched (Q&A: email itself is never editable once captured).
- Existing `leads` row, `status='converted'` → same as "already in `users`" above.

**Verification link click (`GET lead/verify/{token}`):** token identifies one lead row.

| Lead status when clicked | Expiry checked? | Result |
|---|---|---|
| token not found | — | invalid-link message |
| `new`, not expired | yes | mark `verified` (+`verified_at`), show step 2 |
| `new`, expired | yes | "this link has expired" — recovery is resubmitting the step-1 popup |
| `verified` | **no** | same link always re-opens step 2, no matter how old |
| `converted` | — | "you already have an account, please log in" |

The key rule: expiry only gates the *first* click (new→verified). Once verified, the
link is a durable "resume where you left off" pointer with no time limit.

**Resolved in phase 2 (2026-08-14):** see "Reactivation (phase 2)" below — a
`verified` lead who loses that link now gets it re-sent by resubmitting the popup.

## Step 2 — the actual account creation

`GET/POST lead/complete/{token}` — only reachable once a lead is `verified` (see table
above). New view extending `layouts/auth`, styled like
[register.php](../../app/Views/pages/register.php):

- Prefilled from the lead row: `user_type` (radio), `name`, `phone` — all editable.
- `email` shown read-only — it's the identity the link already proved.
- New fields asked here: `password`, `company_name`, `country_id`,
  `selling_products` (supplier) / `buying_products` (buyer), same conditional
  show/hide as the existing signup form.

On submit: re-check `users.email` uniqueness (race guard), then a **single insert**
into `users` combining the lead's carried fields with the new ones — same shape as
`Auth::register()`'s `$userData` array. Then run the exact same post-creation sequence
`Auth::register()` already does: activity log, admin notification email, welcome
email, session login, redirect to dashboard (or pending-approval message, same as
today). `leads.status` flips to `converted` as a side effect of that insert
succeeding — nothing else changes on the lead row.

## Reactivation (phase 2)

Resolves the question originally logged as an open question, then as a deliberately
deferred gap: **a lead who verified their email but never finished step 2 — how do
they get back in if they lose the link?**

The answer doesn't need a new token type or a new page — it reuses the fact that a
`verified` lead's link never expires (see the lifecycle table above). Resubmitting
the step-1 popup with that same, already-verified email now:

1. Updates contact fields, same as before (unchanged from phase 1).
2. **Re-sends the same link** (same token — not a new one, since it's still valid) via
   a new email, `sendLeadResumeEmail()` in `email_helper.php`. Different copy from the
   initial verification email — no "confirm your email" framing (they already did
   that), no expiry mention (there isn't one at this point).
3. Response message tells them a link was re-sent, distinct from the "you're already
   verified, check your original email" message a *cooled-down* resubmission gets
   (see next point) — so the popup UI doesn't need to change, it already just displays
   whatever message the server sends back.

**Anti-spam cooldown:** re-sending on every popup resubmission would let anyone spam
repeat emails at an address they don't own just by knowing it and resubmitting the
form. Guarded with a 5-minute cooldown (`LeadCapture::RESEND_COOLDOWN_MINUTES`) based
on the lead row's `updated_at`, captured *before* the contact-fields update touches
it. Within the cooldown window, the response reverts to the original "you're already
verified" message with no email sent. This is a narrow, purpose-built throttle for
this one resend action — not a substitute for the general rate-limiting gap on
`lead/capture` tracked in [BLOCKERS #7](../BLOCKERS.md).

## Email verification mail

New `sendLeadVerificationEmail()` in
[email_helper.php](../../app/Helpers/email_helper.php), built from the same template
already used by `sendPasswordResetEmail()`/`sendWelcomeEmail()` — logo header,
gradient CTA button, same SMTP → PHP `mail()` fallback chain via `SiteSettingModel`.
States the 7-day expiry explicitly in the copy. Token: `bin2hex(random_bytes(32))`,
identical generation to the existing password-reset token in `Auth.php` — no new
dependency.

Cannot be tested end-to-end locally — [CLAUDE.md](../CLAUDE.md) forbids a working
local SMTP server. Locally: verify token/expiry/status-transition logic directly and
confirm the rendered email body/link. Nabeel tests the real click-through on production.

## Popups

- Rendered once, injected into the shared layouts (`main.php`/`inner.php`), gated
  server-side by `!session()->get('logged_in')`.
- Trigger definitions **and** their per-trigger popup copy live together in one JS
  config file (`public/assets/js/lead-popup-triggers.js`) — an array of
  `{key, type, value/selector, text: {...}}` — editable in one place without touching
  PHP. Starting set: exit-intent, 60% scroll depth, ~25s time-on-page, one
  section-visibility trigger (IntersectionObserver). No cooldown after dismissal —
  other triggers can still fire — just spaced apart so it isn't a barrage.
- Buyer/supplier default radio computed client-side from `location.pathname` against
  a pattern list in the same config file: `buyer-detail`/`buyer-inquiry` pages and
  buyer-type search results default to **supplier**; supplier/product pages and
  searches default to **buyer**; everything else defaults to buyer.

## Explicitly out of scope for this pass

- Admin-side lead list/pipeline view.
- Rate limiting on `lead/capture` — same exposure as every other POST route on the
  site; grouped into [BLOCKERS #7](../BLOCKERS.md) rather than a new entry, per
  owner's instruction, since CSRF being off site-wide is the root cause either way.

## Build order

1. Migration + `LeadModel` (schema, status transitions, `reconcile`-against-`users` check)
2. `Lead` controller: `capture()`, `verify($token)`, `completeSignup($token)`
3. Routes
4. Views: popup partial (step 1 + "check your email" step), `lead-complete-signup.php` (step 2)
5. `sendLeadVerificationEmail()` in `email_helper.php`
6. JS: popup rendering + `lead-popup-triggers.js` config
7. Browser-verify the full flow up to the email step; verify email content/link by hand
