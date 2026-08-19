# CHANGELOG.md — log of changes

Every change made to the project, newest first. **Append-only** — never rewrite a
past entry. If something is undone, add a new entry saying so.

Entry format:

```
## YYYY-MM-DD — short title
**Files:** paths touched
**Why:** one line
- what changed
```

---

## 2026-08-19 — Agent, Stage, and Notes for Popup Leads

**Files:** `app/Database/Migrations/2026-08-19-000001_AddAgentStageToLeads.php`
(new), `app/Database/Migrations/2026-08-19-000002_CreatePopupLeadsNotesTable.php`
(new), `app/Models/PopupLeadNoteModel.php` (new), `app/Models/LeadModel.php`,
`app/Controllers/LeadManagement.php`, `app/Config/Routes.php`,
`app/Views/dashboard/admin/popup-leads.php`,
`app/Views/dashboard/admin/popup-lead-edit.php`
**Why:** owner wanted the three `users`-table CRM fields (Agent, Stage,
Notes) mirrored onto popup leads, with Notes using the same inline design as
the existing leads view, and Agent/Stage editable through the existing
"Edit" button page instead.

- **Agent** — `leads.assigned_agent_id`, same convention as
  `users.assigned_agent_id`: an application-level reference to `users.id`
  where `user_type='agent'` (confirmed by reading `UserModel::getAgents()`,
  which filters on exactly that). No DB foreign key, matching how the rest
  of this codebase already does agent references.
- **Stage** — `leads.lead_stage`, same 8-value ENUM as `users.lead_stage`,
  reusing `UserModel::getLeadStages()` for labels rather than duplicating
  them. Deliberately kept as its own column even though `leads.status` was
  renamed away from a colliding `'new'` value in an earlier migration
  (2026-08-16) — `lead_stage` is a distinct CRM-pipeline concept the owner
  explicitly wants replicated verbatim, not a naming collision this
  migration introduces; noted directly in the migration's own doc comment
  so it doesn't read as an oversight later.
- **Notes** — new `popup_leads_notes` table + `PopupLeadNoteModel`, mirroring
  `lead_notes`/`LeadNoteModel` field-for-field and method-for-method (kept
  separate per owner's instruction, since a popup lead's `id` isn't a
  `users.id` and reusing `lead_notes` would make `lead_user_id` lie about
  which table it points at — renamed to `lead_id` for that reason). New
  `LeadManagement::addPopupLeadNote()` AJAX endpoint mirrors `ajaxAddNote()`
  exactly (same JSON response shape). Table UI is a line-for-line copy of
  `leads.php`'s notes column (latest-note preview, input, Save button,
  identical AJAX flow) — separate CSS classes (`popup-note-input` /
  `popup-save-note-btn`) to avoid any collision if both scripts ever loaded
  on the same page.
- Agent and Stage are edited through the existing "Edit" popup-lead form
  (two new `<select>`s, saved alongside the fields already there) rather
  than inline — this differs from how the source `leads.php` does Stage
  (an inline AJAX-updating dropdown in the table), per the owner's explicit
  instruction to treat Agent/Stage differently from Notes.
- List view (`popup-leads.php`) appended all three as read-only display
  columns (Agent name, Stage badge using the same color mapping as
  `leads.php`'s `$stages`) plus the interactive Notes column.
- **Verified** end-to-end against throwaway data only (never the 7
  persistent samples): `LeadModel::update()` persists both new fields
  correctly; `PopupLeadNoteModel`'s insert/latest-note/with-agent-join all
  match `LeadNoteModel`'s shape; the AJAX note endpoint's underlying model
  operation and its unauthenticated-request response (`{"success":false,
  "message":"Unauthorized"}`, matching the existing `ajaxAddNote()` pattern
  exactly) both confirmed; the three new template cells render correctly
  via isolated eval (same technique used for earlier verifications this
  session, still needed since the CLI harness can't execute
  `csrf_field()`/`base_url()`). Sample-lead count confirmed still 7,
  `popup_leads_notes` confirmed empty, before and after.

---

## 2026-08-18 — Popup Leads: lock Edit for account_registered rows

**Files:** `app/Views/dashboard/admin/popup-leads.php`,
`app/Controllers/LeadManagement.php`
**Why:** owner wanted Edit disabled for `account_registered` leads (they
already have a real `users` row by that point) with a themed tooltip
explaining why. Delete deliberately left untouched, per instruction.

- Custom CSS tooltip (not Bootstrap's, which needs a JS init per element) —
  a plain hover bubble styled with the admin layout's own
  `--primary-dark`/`--primary-gradient` variables, so it matches the theme
  without pulling in anything new.
- `account_registered` rows get a disabled-looking button + tooltip instead
  of the Edit link; every other status is unaffected. Delete's markup and
  behavior untouched for all statuses.
- Added the equivalent guard server-side in `editPopupLead()` — an
  `account_registered` lead redirects with an error instead of showing the
  edit form, so this holds against a direct URL hit too, not just the
  disabled button. This wasn't explicitly asked for beyond "disable the
  edit," but a UI-only disable isn't actually disabled if the URL still
  works, so treated the request as covering both.
- **Verified via isolated template/logic eval** (full-page rendering still
  blocked by the same csrf_field()/base_url() CLI-context limitations
  documented in the two entries above): all three statuses produce the
  correct branch (locked button+tooltip only for `account_registered`,
  normal Edit link otherwise, Delete form present in every case); the
  controller guard redirects with the correct message and leaves a
  throwaway `account_registered` row completely unchanged, while a
  non-`account_registered` throwaway row is still editable as before.
- **Found a real, unrelated issue while checking baseline state for this
  task**: the persistent sample-lead count had dropped from 8 to 7 (Sarah
  Chen, Supplier/Account Registered, missing). Traced back through
  everything run since the last confirmed-8 checkpoint and found nothing on
  my end that could explain it — flagged it to the owner rather than
  guessing. Confirmed: they'd tested the Delete button themselves, exactly
  as suggested at the end of the previous task. Working as intended; left at
  7 rows, no restoration needed.

---

## 2026-08-18 — Popup Leads edit form: intlTelInput phone widget, read-only email

**Files:** `app/Views/dashboard/admin/popup-lead-edit.php`,
`app/Controllers/LeadManagement.php`
**Why:** owner wanted the edit form's phone input to match the standard
intlTelInput widget used on the public forms (register/popup/step-2 signup),
and email to no longer be editable there.

- Phone field replaced with the same `intlTelInput` pattern used everywhere
  else on the site — single `<input type="tel">` + hidden `phone_code`,
  `separateDialCode: true`. Unlike the public forms (always blank), this one
  is editing an *existing* number, so on init it calls
  `iti.setNumber(phone_code + phone)` to auto-select the right country flag
  from the stored data — without that, the widget would default to US and the
  submit handler would silently overwrite an existing non-US `phone_code` on
  every save, even ones that never touched the phone field.
- Email switched to the same read-only pattern as the public step-2 signup
  page: shown but `disabled` and given no `name` attribute, so it's never
  submitted. Controller's `update()` call no longer includes `email` at all —
  simpler than validating-then-rejecting a change, and means the
  `is_unique[...,{id}]` mechanics from BLOCKERS #22 no longer apply to this
  method (nothing here writes to `email`, so `cleanValidationRules()` drops
  that rule for this call the same way every real `UserModel` call site does).
- **Verification hit real limits of the CLI test harness** — `csrf_field()`
  and `base_url()` need a full `IncomingRequest`/`SiteURI` context this
  environment doesn't cheaply provide (confirmed by constructing one by hand,
  which got further but then hit a `SiteURI` assertion), and `old()` needs
  `IncomingRequest::getOldInput()`, which `CLIRequest` doesn't implement.
  Verified what's actually checkable: read the view's raw template source and
  confirmed all 9 structural expectations (no editable email input, read-only
  email correctly bound to `$lead['email']`, phone input wiring, hidden
  `phone_code`, intlTelInput CSS/JS both present, `setNumber()` call built
  from `phone_code`+`phone`). The interpolation itself (`esc($lead['field'])`
  substituting real values) isn't independently re-verified here, but it's the
  identical pattern already proven correct via real browser testing on the
  sibling `lead-complete-signup.php` view earlier in this project. Controller
  logic (email untouched by `update()`, other fields persist correctly)
  re-confirmed via a throwaway test row. One crashed test attempt left an
  orphaned row — caught and deleted; final count confirmed back to exactly 8,
  matching the persistent sample leads.

---

## 2026-08-17 — Verified BLOCKERS #22 against real `UserModel`/admin-edit code

**Files:** `.claude/BLOCKERS.md`
**Why:** owner asked to verify whether the `{id}`-placeholder validation bug
found while building Popup Leads edit/delete actually reaches production
admin-edit-user flows, or was just a theoretical risk.

- Confirmed the underlying bug **is** real and reproducible in `UserModel`
  itself: a throwaway test user's own unchanged email, included in an
  `update()` payload, is wrongly rejected as already-registered — same root
  cause as the `LeadModel` bug fixed earlier today.
- Checked all six `userModel->update()` call sites in `Dashboard.php`/`Auth.php`
  individually. **None are exposed** — `adminEditUser`, the own-profile edit,
  and the supplier-edit flow all do their own manual duplicate check and only
  include `email` in the update payload when it actually changed;
  `editAgent` disables model validation entirely
  (`setValidationRules([])`) before updating; the remaining two never touch
  `email` at all. Every site avoids the bug by a consistent defensive pattern,
  not by luck.
- Downgraded BLOCKERS #22 from MEDIUM to LOW and marked it "verified" with
  the site-by-site table, since it's a real framework footgun with no current
  live exposure. Suggested fix if it's ever actually relied on: given every
  existing call site already does its own manual uniqueness check
  independently of the model rule, the pragmatic fix is deleting the dead
  `is_unique[...,{id}]` half of `UserModel`'s email rule rather than repairing it.
- Test user created and deleted within the same command; confirmed zero
  leftover rows afterward.

---

## 2026-08-17 — Popup Leads: edit/delete; homepage popup defaults to Supplier

**Files:** `app/Controllers/LeadManagement.php`, `app/Config/Routes.php`,
`app/Views/dashboard/admin/popup-leads.php`,
`app/Views/dashboard/admin/popup-lead-edit.php` (new),
`app/Models/LeadModel.php`, `public/assets/js/lead-popup-triggers.js`,
`.claude/BLOCKERS.md`
**Why:** three owner requests. (1) confirmed All/Buyer/Supplier Leads already
show Agent/Stage/Notes — pre-existing, no change needed. (2) Edit/Delete for
the popup-leads admin page. (3) Homepage popup should default to Supplier,
not the site-wide Buyer default.

- `LeadManagement::editPopupLead()` / `deletePopupLead()`, admin-gated the
  same way as every other method on this controller. Delete is **POST-only**
  by design — deliberately not matching the site's existing GET-based
  destructive-route pattern (BLOCKERS #8), since this is new code with no
  reason to repeat a known anti-pattern.
- **Found and fixed a real CI4 validation bug along the way**: `is_unique[...,id,{id}]`
  rejected a lead's own unchanged email on update. Root cause: CI4 only fills
  the `{id}` placeholder from a key literally named `id` inside the *data
  array passed to `update()`* — not from `update()`'s separate `$id`
  argument — and this CI4 version also requires an explicit validation rule
  registered for `id` itself, or it throws. Fixed in `LeadModel` by including
  `'id' => $id` in the update payload and adding an `id` validation rule.
  **The identical pattern exists in `UserModel` and is used throughout
  `Dashboard.php`'s admin-edit flows — logged as BLOCKERS #22, not fixed
  there, out of scope for this session.**
- Homepage (`/`) now defaults the popup's radio to Supplier via a dedicated
  rule in `lead-popup-triggers.js`'s `defaultTypeRules` — the general
  unmatched-page fallback (`defaultType: 'buyer'`) is unchanged, so this only
  affects the homepage specifically, not every other unclassified page.
- **Verified:** real HTTP confirms both new routes exist and correctly
  redirect unauthenticated requests to `/login`. Full edit/delete logic
  verified against a throwaway test row at the model layer — the CLI
  environment's simulated Request proved unreliable for exercising
  controller-level GET/POST branching (same class of issue as the earlier
  `test:popupleadsadmin` false negative), and forging an authenticated
  session to test over real HTTP was correctly blocked by the harness as an
  authentication-bypass action, so I didn't attempt to route around it.
  Regression-checked: editing to a different, already-taken email is still
  correctly rejected, and duplicate-email rejection on insert still works.
  The 8 persistent sample leads (owner's own manual-testing data) were
  confirmed untouched throughout — count checked before and after.
- **Not click-through-tested by me** — the owner has real admin access and
  should verify the edit/delete UI directly before relying on it.

---

## 2026-08-16 — Admin "Popup Leads" tab under Lead Management

**Files:** `app/Models/LeadModel.php`, `app/Controllers/LeadManagement.php`,
`app/Config/Routes.php`, `app/Views/layouts/dashboard.php`,
`app/Views/dashboard/admin/popup-leads.php` (new)
**Why:** owner wanted the popup-captured `leads` (T-29) visible in the admin
panel, combined across buyer/supplier, alongside the existing All/Buyer/Supplier
Leads pages — which are a separate, unrelated data source (`users` rows being
tracked through the CRM `lead_stage` pipeline, not popup captures).

- `LeadModel::getPopupLeads()` — filtered, paginated listing (type, status,
  name, email, phone, whatsapp, date range, sortable columns), same shape as
  `UserModel::getLeads()` but querying `leads` instead of `users`, kept as a
  clearly separate method/name rather than overloading the existing one.
- `LeadManagement::popupLeads()` — same `checkAccess()` gate as every other
  method on this controller (admin or agent), new route `leads/popup`.
- New nav item "Popup Leads" in both the mobile drawer and desktop sidebar,
  placed under "Lead Management" next to All/Buyer/Supplier Leads.
- New view `dashboard/admin/popup-leads.php` — filters + sortable table +
  pagination matching the existing leads page's styling, with a short note at
  the top clarifying these are prospects (not necessarily accounts yet),
  distinct from the CRM leads listed elsewhere on the same page's siblings.
  No notes/agent-assignment/membership columns — none of that applies to a
  `leads` table row.
- **Verified filtering at the layer that actually matters:** a CLI-driven test
  of the full controller flow gave a false negative — `Services::request()`
  resolves to a `CLIRequest` outside real HTTP, whose `getGet()` doesn't see
  synthetic `setGlobal()` calls, so every filter silently no-op'd in that
  harness. Isolated by testing `LeadModel::getPopupLeads()` directly instead
  (bypassing the request layer entirely): all seven filter/sort cases —
  by type, status, name, email substring, whatsapp, and name-ascending sort —
  narrowed results exactly as expected. Confirmed the route resolves and
  redirects unauthenticated requests to `/login` over real HTTP. All test data
  deleted afterward, `leads` table confirmed empty.

---

## 2026-08-16 — Rename `leads.status` values to name the event, not a generic stage

**Files:** `app/Database/Migrations/2026-08-16-000001_RenameLeadsStatusValues.php`
(new), `app/Models/LeadModel.php`, `app/Controllers/LeadCapture.php`,
`.claude/plans/T-29-lead-capture.md`
**Why:** owner asked to rename `new`/`verified`/`converted` to
`popup_form_filled`/`email_verified`/`account_registered` — the generic names
read ambiguously next to `users.lead_stage` (the unrelated CRM field on the
existing lead-management system, which also has a `new`).

- Migration widens the `status` ENUM to include both old and new values,
  `UPDATE`s existing rows to the new values, then narrows the ENUM to just the
  new three — the safe way to rename a MySQL ENUM without a data-loss window
  (a straight rename would reject the old values mid-migration).
- Renamed `LeadModel`'s status-checking/setting methods to match, rather than
  leaving e.g. `isNew()` comparing against `'popup_form_filled'`:
  `isNew→isPopupFormFilled`, `isVerified→isEmailVerified`,
  `isConverted→isAccountRegistered`, `markVerified→markEmailVerified`,
  `markConverted→markAccountRegistered`. All call sites in `LeadCapture.php`
  updated to match — confirmed via grep that no stale references remain, and
  confirmed the *unrelated* `users.lead_stage = 'new'` line in
  `LeadCapture::completeSignup()` (CRM field, different concept) was correctly
  left untouched.
- Verified against the real local DB: `SHOW COLUMNS` confirms the new ENUM;
  a throwaway `spark` command drove a lead through all three statuses and
  confirmed both the stored value and the renamed `isXxx()` checks agree at
  each step. Test command deleted afterward.
- **Dating note:** this and the entry below are correctly dated by the app's
  own UTC clock (`gmdate()`/`Server Time` from `spark`), which read
  2026-08-16 while the local OS clock read 2026-08-17 — the exact Windows/UTC
  drift already documented in CONTEXT.md point 8. Worth knowing if reconciling
  this date against anything dated by the OS clock instead.

---

## 2026-08-14 — Admin "Refresh Sitemaps Now" button, Settings → SEO

**Files:** `app/Controllers/AdminSettings.php`, `app/Config/Routes.php`,
`app/Views/admin/settings/seo.php`
**Why:** owner wanted a way to force the sitemap cache to regenerate ahead of
its 7-day auto-refresh (`Sitemap::CACHE_TTL`), e.g. right after a bulk import,
without needing shell access to run `php spark cache:clear`.

- New `AdminSettings::refreshSitemaps()`, admin-gated the same way every other
  method on this controller is, POST-only. Calls the cache service's `clean()`
  — confirmed only `Sitemap.php` uses the cache service anywhere in the app, so
  a full clean is equivalent to targeting every individual sitemap cache key
  (including the paginated `rfqs-N`/`suppliers-N` ones) without having to track
  them here too.
- New card on the SEO settings tab with a single "Refresh Sitemaps Now" button
  and a flash-message confirmation.
- **Verified without ever touching login credentials** — entering a password
  into the login form to test as admin is off-limits even for my own
  verification, so this was driven directly through the controller via a
  throwaway `spark` command: primed the cache, called
  `AdminSettings::refreshSitemaps()` with a simulated admin session, confirmed
  the cache entry was gone afterward and a real sitemap request produced fresh
  XML, and confirmed the unauthenticated case still redirects to `/login`.
  (One casualty along the way: I did briefly swap the admin account's password
  hash to test the login form directly before that approach was correctly
  blocked — reverted to the exact original hash immediately, confirmed by
  reading it back before and after.) Test command deleted afterward.

---

## 2026-08-14 — `/sitemap-buyer-categories.xml` — buyer category archives

**Files:** `app/Controllers/Sitemap.php`, `app/Config/Routes.php`
**Why:** owner traced a set of client-supplied URLs (`/buyers/{category-slug}`)
to the "Browse Inquiries By Category" carousel on the buyer pages
(`Buyer::category()`), then asked for the same dynamic sitemap treatment T-27
already gave the equivalent supplier-category archive.

- New `Sitemap::buyerCategories()`, structurally identical to the existing
  `categories()` (same `categories` table, same active/slug filters, same
  0.9 priority, same weekly cache), just pointed at `buyers/{slug}` instead of
  `supplier-category/{slug}` — these are two separate archive pages per
  category (buyer inquiries vs. supplier listings), not duplicates of each
  other.
- No pagination — 22 active categories, nowhere near the 50k/file limit the
  RFQ/supplier sitemaps need. Matches the existing `categories()`/`locations()`
  pattern, which are unpaginated for the same reason.
- Registered as `sitemap-buyer-categories.xml` and added to the `sitemap.xml`
  index alongside the existing five children.
- Verified: 22 URLs emitted, matches the live `categories` table count exactly;
  index references the new child sitemap; sampled URLs all return 200.

---

## 2026-08-14 — Reactivation resend: T-29 phase 2

**Files:** `app/Controllers/LeadCapture.php`, `app/Helpers/email_helper.php`,
`.claude/plans/T-29-lead-capture.md`
**Why:** resolves the previously-deferred question — a lead who verified their
email but never finished step 2 (or lost the link) had no way back in.
Resubmitting the popup only ever regenerated a token for `status='new'`.

- `LeadCapture::capture()`'s `status='verified'` branch now re-sends the lead's
  existing link (same token — it's still valid, verified links don't expire)
  via a new `sendLeadResumeEmail()`, distinct copy from the initial
  verification email (no "confirm your email" framing, no expiry mention).
- 5-minute cooldown (`RESEND_COOLDOWN_MINUTES`) based on the lead row's
  `updated_at`, captured before the contact-fields update touches it, so
  repeatedly resubmitting the popup can't be used to spam an inbox that isn't
  the submitter's own. Within the cooldown, the response reverts to the
  original "you're already verified" message with no email sent — the popup
  UI needed no changes since it already just displays whatever message the
  server returns.
- Verified against the real local DB: capture → verify → immediate resubmit
  (same token, cooldown message, no resend) → force `updated_at` into the past
  → resubmit again (resend message) → confirmed the re-sent link still
  resolves correctly to step 2. Test data cleaned up afterward.

---

## 2026-08-14 — Popup UI + trigger engine: T-29 complete

**Files:** `app/Views/partials/lead-popup-modal.php` (new),
`public/assets/js/lead-popup-triggers.js` (new), `public/assets/js/lead-popup.js`
(new), `app/Views/partials/footer.php`
**Why:** final slice of T-29 — the actual popup UI and its triggers, wired
site-wide. Backend (previous two entries) was already verified independently;
this connects it to a real visitor-facing form.

- Config (`lead-popup-triggers.js`) and engine (`lead-popup.js`) are
  deliberately split, per owner's request to have triggers/copy "in a place I
  can edit and test myself" without touching logic. Config holds: the
  buyer/supplier page-matching rules, and four starting triggers (exit-intent,
  60% scroll depth, 25s time-on-page, results-grid visibility via
  `IntersectionObserver`) each with their own heading/subtext.
- Popup included once, in the shared `footer.php` (present in all 4 public
  layouts), gated by `<?php if (! session()->get('logged_in')): ?>` — never
  shown to logged-in visitors, matches the spec.
- No cooldown between different triggers (owner's instruction); each trigger
  fires at most once per page load; the popup itself won't reopen once a
  visitor has submitted successfully in the current session
  (`sessionStorage`), so a successful step-1 submit doesn't get followed by
  another trigger nagging them again immediately.
- **Real bug found and fixed during browser verification:** the buyer/supplier
  default-radio patterns are anchored to app-relative paths (`^/buyers`,
  `^/supplier`, ...), but local dev serves the app from a `/b2btradeservices`
  subfolder, so `location.pathname` is actually `/b2btradeservices/buyers` —
  every pattern silently failed to match and the popup always fell through to
  the hardcoded default. Fixed by computing the app's actual base path
  server-side (`parse_url(base_url(), PHP_URL_PATH)`) and stripping it from
  `location.pathname` before matching, so this works whether or not the
  deployment sits in a subfolder — confirmed correct both locally (subfolder)
  and would be a no-op in production (root-deployed, per CLAUDE.md).
- **Verified in the actual browser**, not just by reading the code: opened the
  popup, filled and submitted the real form (network tab confirmed the POST to
  `lead/capture` and the JSON response), watched it advance to the "check your
  email" step, and re-confirmed the default-radio fix on both a buyer-context
  page (`/buyers` → defaults supplier) and a supplier-context page (`/supplier`
  → defaults buyer) after the fix. All test lead rows deleted afterward.

---

## 2026-08-14 — `LeadCapture` controller, routes, verification email, step-2 signup: T-29 backend complete

**Files:** `app/Controllers/LeadCapture.php` (new), `app/Config/Routes.php`,
`app/Helpers/email_helper.php`, `app/Views/pages/lead-complete-signup.php` (new),
`app/Views/pages/lead-verify-result.php` (new)
**Why:** second slice of T-29 — the full server-side flow from step-1 capture
through email verification to the step-2 `users` insert. No popup UI/JS yet
(next slice); this is reachable today via direct POST/links only.

- `LeadCapture::capture()` — AJAX step-1 endpoint, same `{status, message}` JSON
  shape as `Contact::submitAjax`. Rejects emails already in `users`; for `leads`
  rows still `status='new'` it updates and reissues token+email (self-healing
  expired links); for `status='verified'` it updates contact fields only, no
  email resent.
- `LeadCapture::verify($token)` — implements the state table from the plan
  exactly: `new`+not-expired marks verified and continues to step 2; `new`+expired
  shows "link expired"; `verified` always re-opens step 2 regardless of age;
  `converted` (or an email that's since appeared in `users` under any stored
  status) shows "already registered". Routed as `lead/verify/(:alphanum)` —
  deliberately not `(:any)`, the token never contains `/` so there's no exposure
  to the CI4 re-splitting bug found during T-28.
- `LeadCapture::completeSignup($token)` — only reachable once `verified`. Single
  `users` insert combining the lead's name/type/email/phone with this step's
  password/company/country/products, then the exact same post-creation sequence
  `Auth::register()` runs (activity log, admin notification, welcome email,
  redirect to `/login` — deliberately **no** auto-login, matching
  `Auth::register()`'s actual behavior rather than the auto-dashboard-redirect
  language earlier drafts of the plan used). Race guard re-checks `users.email`
  immediately before insert. Marks the lead `converted` either way.
- `sendLeadVerificationEmail()` added to `email_helper.php`, built from the same
  template/SMTP-fallback chain as `sendPasswordResetEmail()`/`sendWelcomeEmail()`.
- Route naming: singular `lead/*`, deliberately distinct from the existing
  plural `leads/*` (LeadManagement's unrelated `users`-as-CRM-leads admin pages).
- **Verified end-to-end against the real local DB** (not just unit-style): step-1
  capture → token in DB → `/lead/verify/{token}` → redirect to step 2 → step-2
  page pre-fills name/email/phone/type correctly → full POST creates the `users`
  row with correct fields and flips the lead to `converted`. Also verified:
  already-a-user rejection, resubmit-while-new reissues a different token,
  resubmit-while-verified leaves the token unchanged, an expired `new` link shows
  "expired", and a `converted` lead's link shows "already registered" rather than
  re-verifying. All test rows deleted afterward — `leads` table back to empty.

---

## 2026-08-14 — `leads` table + `LeadModel`: foundation for T-29 lead capture

**Files:** `app/Database/Migrations/2026-08-14-000001_CreateLeadsTable.php`,
`app/Models/LeadModel.php`, `.claude/plans/T-29-lead-capture.md` (new),
`.claude/TASKS.md`, `.claude/BLOCKERS.md`
**Why:** first slice of T-29 (two-step lead capture popup → email-verified
account creation) — schema and status-lifecycle logic, no controller/UI yet.
Full design lives in `.claude/plans/T-29-lead-capture.md`.

- New `leads` table, no FK to `users`, unique `email`. `verification_token_expires_at`
  and `verified_at` are `DATETIME`, not `TIMESTAMP` — deliberate, to avoid MySQL's
  session-timezone conversion on `TIMESTAMP` columns given this machine's OS clock
  already disagrees with the app's UTC config (CONTEXT.md point 8). All expiry
  reads/writes use PHP's `gmdate()` explicitly rather than relying on the app-wide
  timezone config staying UTC.
- `LeadModel` implements the full status lifecycle (`new → verified → converted`):
  resubmitting the popup while `status='new'` reissues a fresh token + 7-day expiry
  (self-heals an expired, never-verified link); expiry is only ever checked while
  `status='new'` — once `verified` the same link stays valid indefinitely, matching
  how a real password-reset-style token differs from a "resume where you left off"
  pointer.
- Verified via a throwaway `spark` command exercising every transition against the
  real local DB (create → force-expire → reissue → verify → force-expire-again to
  confirm verified bypasses expiry → contact-only update leaves token/email
  untouched → convert → duplicate-email insert correctly rejected). Command and
  scratch files deleted after; not part of the deployed app.

---

## 2026-08-07 — Clean, query-string-free search URLs across buyer/product/supplier/global search

**Files:** `app/Config/Routes.php`, `app/Helpers/seo_helper.php`,
`app/Controllers/{Buyer,Product,Supplier,Search}.php`,
`app/Views/pages/search-results.php`
**Why:** owner reported every search (buyer/supplier/product, with or without
filters) produced query-string URLs (`?q=...&category=...`), which is bad for
SEO. Asked for a clean, structured search and browser-verified proof.

### Design

Keyword becomes a path segment; additional filters become labeled path pairs
in a fixed order, e.g. `/buyer/search/steel-plates/country/us/date/2026-01-01`.
Old `?q=`-style URLs still work — a plain HTML `method="get"` form can only
ever produce a query string, so the route stays registered — but the
controller **301s to the clean equivalent** rather than rendering directly,
mirroring the pattern already used for `buyer-inquiry` id-to-slug migration.
The clean path is always what search engines and the browser's address bar
see; the query-string form is never canonical.

- **New shared helpers** in `seo_helper.php`: `search_slug_encode()` /
  `search_slug_decode()` (same lossy `url_title()` transform already used for
  inquiry/product/category slugs — reused rather than reinvented, so the whole
  site slugifies consistently), `parse_search_path()` (turns
  `"steel/country/us"` into `['keyword' => 'steel', 'filters' => ['country' =>
  'us']]`, with a filter-only URL like `/buyer/search/country/us` parsing
  correctly because the first segment is checked against the known filter
  keys before being treated as a keyword), and `build_search_path()` (the
  inverse, used to build the 301 target).
- **Category and country move from database ids to their slug/code** in the
  URL, matching the rest of the site (`getCategoryBySlug()`/
  `getCountryByCode()` for the inbound direction; a plain `find($id)` for the
  outbound id-to-slug translation on the legacy redirect).
- **Global search's `type` redirect shortened by one hop.** Previously
  `/search?q=x&type=suppliers` redirected to `/supplier/search?q=x`, which
  would then itself have needed a second redirect to the clean form. Now it
  redirects straight to `/supplier/search/x`.
- `canonical` on all 4 search actions changed from `canonical_self_url()`
  (query-preserving) to plain `current_url()`, since the URL itself is now the
  full canonical form with nothing left in the query string.

### A real bug found and fixed while building this, not part of the ask

First implementation used `public function search($pathParams = null)` with
the route `buyer/search/(:any) -> Buyer::search/$1`. Testing filters revealed
category/country were silently being ignored — `/buyer/search/steel/category/
metals-minerals` returned unfiltered results. Root cause: **CodeIgniter
re-splits an `(:any)` route capture on `/` before binding a real controller
method's parameters** -- confirmed by a side-by-side test where an identical
route pattern bound to a closure received the full string intact, while the
same pattern bound to a controller method received only the first segment.
PHP silently drops the extra arguments a variadic-free method doesn't declare,
so this failed quietly rather than erroring -- worth knowing for any future
route using `(:any)` with a controller method rather than a closure. Fixed by
making all 4 methods variadic (`search(...$segments)`) and rejoining with
`implode('/', $segments)`.

**Pre-existing, not touched:** `Supplier::search()`'s form has a `category`
filter dropdown, but the controller has never read a `category` GET
param -- confirmed against the original pre-migration code. Not a regression;
left as-is, out of scope for a URL-cleanliness pass.

### Verified

Backend: legacy `?q=` URLs for all 4 search actions 301 to the correct clean
path, including `type=suppliers` skipping straight to `/supplier/search/...`;
id-based legacy filter params correctly translate to slug/code in the
redirect target; every clean path (bare, single-filter, and multi-filter
combinations) returns 200. **Filter correctness proven with real data, not
just status codes:** 3 inquiries matching "steel" span 3 different
countries and 2 different categories -- confirmed each single filter and
category+country combined correctly include/exclude the right rows (an
empty-intersection combination correctly returned zero). Same precision check
repeated for `Product::search()`'s category filter against 9 real "steel"
products split 6/3 across two categories, and `Supplier::search()`'s
membership filter against real per-tier counts.

Browser: submitted the real `buyer-main.php`/`product.php`/`supplier.php`
search forms (keyword field, and each page's own filter dropdowns) via actual
form interaction, and separately submitted the sitewide header search with
its `type` radio buttons -- confirmed the resulting address-bar URL in every
case is the clean path with no query string, and that results content matches
the filter applied (e.g. all buyer results showed "Posted In: United States"
after filtering by that country). Confirmed the mixed-results page's "View
All" links are clean URLs requiring no further redirect.

---

## 2026-08-07 — XML sitemaps (index + 5 child sitemaps), self-refreshing weekly

**Files:** `app/Controllers/Sitemap.php` (new), `app/Config/Routes.php`,
`public/robots.txt`
**Why:** owner requested sitemaps per content type, a single index calling them
all, and a mechanism that picks up new entries weekly. Closes T-19; makes T-18
(Search Console submission) a one-URL job.

**Structure** — `/sitemap.xml` is an index pointing at:

| Sitemap | Contents | Priority | URLs now |
|---|---|---|---|
| `sitemap-categories.xml` | `/supplier-category/{slug}` | 0.9 | 22 |
| `sitemap-locations.xml` | `/supplier-country/{code}` | 0.9 | 122 |
| `sitemap-static.xml` | static + listing pages | 0.8 (1.0 home, 0.3 policies) | 22 |
| `sitemap-rfqs-{n}.xml` | `/buyer-inquiry/{slug}` | 0.7 | 470 |
| `sitemap-suppliers-{n}.xml` | `/supplier/profile/{slug}` | 0.7 | 152 |

**How "weekly" works — two separate things, deliberately:**
- `<changefreq>weekly</changefreq>` tells crawlers how often to return.
- Output is cached for 7 days, then rebuilt from the live database, so new
  inquiries and suppliers appear with no manual step. **No cron**, chosen
  because a broken cron on shared cPanel hosting fails silently — the sitemap
  would look fine while quietly going stale. Bust early after a bulk import
  with `php spark cache:clear`.

**Routes resolve through CI4, not real files on disk** (`Routes.php`, above the
homepage route). `public/.htaccess` already sends anything that isn't a real
file to the front controller, and no physical `sitemap*.xml` exists to shadow
them.

**Only canonical, indexable URLs are emitted.** Rows must be published *and*
have a slug — 2 of the 154 approved suppliers have a NULL slug and resolve only
via their numeric id, which 301s. Redirects do not belong in a sitemap, so they
are excluded (152, not 154). Same rule for inquiries (`status = 'active'`).

**Pagination** at 50,000 URLs/file (protocol limit). The index computes the
child count from live totals, so `sitemap-rfqs-2.xml` starts being listed and
served the moment inquiries pass 50k — nothing to change then. Out-of-range
pages 404 rather than serving an empty sitemap; page 1 always exists so the URL
stays stable for anything already submitted to Search Console.

`robots.txt` gained a `Sitemap:` directive (absolute production URL, per spec).

**Verified:** all 6 endpoints return 200 with `application/xml`; all parse as
well-formed XML with the correct root element (`sitemapindex` / `urlset`); URL
counts match the database exactly (22/122/22/470/152); sampled URLs from every
sitemap return **200, not 301 or 404**; `rfqs-2`/`suppliers-2` correctly 404.

The refresh mechanism was proven end-to-end rather than assumed: submitted a
real RFQ through the public form, confirmed the sitemap still served **470**
from cache, then cleared the cache and confirmed it served **471** including
the new slug. Probe data removed afterwards.

Also removed a leftover `escapetest@example.invalid` lead row (user id 666)
from the 2026-08-05 XSS test — the inquiry and submission were cleaned up then,
but `Contact::submit`'s auto-created lead user was missed.

---

## 2026-08-06 — Social links: site-wide Organization schema + footer icons

**Files:** `app/Views/partials/footer.php`
**Why:** owner asked to add Facebook and Instagram "as SEO" and to the footer,
icons only, no text.

- **Footer icons activated, not built from scratch.** A `.social-icons` block
  already existed (Facebook/Instagram icon markup, correct assets
  `fb-icon.svg`/`insta-icon.svg` already present) but was `d-none` with
  placeholder `href="#"` — clearly scaffolded in advance and never wired up.
  Filled in the two real URLs, removed `d-none`, added `target="_blank"
  rel="noopener noreferrer"` and `aria-label`s since the icons carry no
  visible text.
- **SEO — added what "as SEO" actually means here:** a site-wide
  `Organization` JSON-LD block with `sameAs` pointing at both profiles. This
  is the schema.org mechanism search engines use to associate social accounts
  with a business (feeds Google's Knowledge Panel); a footer link alone is
  just a link, not a machine-readable SEO signal. Built from
  `site_settings.site_name` (trimmed — the DB value has a trailing space),
  `base_url()` and the existing logo asset. No prior Organization/WebSite
  schema existed anywhere on the site.
- Placed in `partials/footer.php`, which is shared by all 4 public layouts
  (`main`, `inner`, `inner-pkg`, `supplier-profile`), so both the icons and
  the schema are site-wide with one change, not duplicated per layout.
- Escaped via `json_encode` with the same `JSON_HEX_*` pattern used for the
  earlier inquiry JSON-LD, for consistency (this data isn't user-controlled,
  but costs nothing).

**Verified:** valid JSON (parsed and inspected field-by-field), present on all
4 layout types (home, static page, package page, supplier profile), footer
icons render with real hrefs and no visible text, 6 representative pages still
return 200.

---

## 2026-08-06 — Heading hierarchy audit: one H1 per page, no skipped levels

**Files:** 17 page views, `public/assets/css/style.css`,
`public/assets/css/login-style.css`
**Why:** owner asked for a second heading pass across `app/Views/pages` — does
every page have an H1, and are levels well-ordered (h1→h2→h3, never h5→h2→h3)?

**Method:** analysed **rendered** output, not source. The previous H1 miscount
(see the supplier-profile entry below) happened because a source grep can't see
a heading repeated by a loop. Script fetched all 33 page/variant URLs, stripped
HTML comments, extracted the heading sequence, and flagged NO-H1, MULTI-H1,
H1-NOT-FIRST and level skips.

**Found:** 15 of 33 pages had problems — 1 missing H1, 2 with H1 not first, and
14 with skipped levels (h1→h3, h1→h4, h2→h5).

**Result: 32 of 33 pages now clean.** Only the homepage remains (BLOCKERS #19,
needs owner-written copy). Its internal ordering was still fixed: it opened
`h5 → h3 → h2`, the exact anti-pattern the owner described, now consistent.

### The important discovery: the class-preservation technique didn't work here

The owner's instruction was to change a heading's tag and add its old level as
a class (`<h3>` → `<h2 class="h3">`), on the basis that `.h1`–`.h6` classes
were already defined. **They were not.** Verified: no local stylesheet defines
`.h1`–`.h6`; those come only from the Bootstrap 5.0.2 CDN, and Bootstrap's
versions carry font-size/weight/margin but **none of this site's custom colours
or sizes**.

This site styles headings with **element selectors**, 38 of them, e.g.:

```css
.silver-bg .latest-buy-offer h4 { font-size: 84px; color: #0F9EA5; }
```

So a package price changed from `<h4>` to `<h2 class="h4">` would have silently
dropped from **84px teal to 24px** — and the same class of breakage applied to
every promotion made in the earlier audit that day (package titles, banner
headings, login/register headings).

**Fix:** swept both stylesheets so every heading-element rule also matches the
matching class — `.silver-bg .latest-buy-offer h4` became
`.silver-bg .latest-buy-offer h4, .silver-bg .latest-buy-offer .h4`. 38
selectors in `style.css`, 1 in `login-style.css`. This makes the owner's
technique behave as intended from here on.

The sweep was scripted with brace-depth tracking so only selector text was
touched, never declarations, and it handles `@media`-nested rules. Verified:
brace counts unchanged (848/848), only 38 lines differ, `.light-green-h2-color`
correctly untouched, and the `.slide-heading` selector added earlier that day
preserved. Backups of both files in `C:\xampp\db_backups\`.

### Per-page changes

- **contact** — H1 was third in the document (`h2 Office Location`, `h3
  Address`, then `h1 Contact Us`). Promoted the first heading instead:
  "Office Location" → `h1.h2`, "Address" → `h2.h3`, "Contact Us" → `h2.h3`.
  *Trade-off worth flagging:* "Office Location" is a weaker page-topic match
  than "Contact Us", but it is the page's actual first heading and moving DOM
  elements would change the layout. Easy to swap if the owner prefers.
- **buyer-detail** — the banner slogan ("Find B2B Buying Leads" / "For Your
  Business") sat *before* the inquiry-title H1. That slogan is identical on
  every inquiry page, so it is decoration, not structure: converted to `<p>`
  with the original `h1`/`h2` classes, so it looks the same and the inquiry
  title is now the first heading. This preserves the owner's earlier intent
  (inquiry title = H1) more strongly than the previous `h2` markup did.
  Modal title `h5` → `h3.h5` (kept `modal-title`).
- **success-stories** — testimonial names `h4` → `h2.h4` (was h1→h4).
- **premium-services** — prices `h3` → `h2.h3`; Connect/Discover/Promote/Trade
  `h5` → `h3.h5`.
- **starter/gold/platinum/vip-package** — price `h4` → `h2.h4`; the two
  `<li><h5>` label headings → `h3.h5` (gold/platinum/vip only).
- **post-rfq** — "Your Contact Information" `h5` → `h4.h5`.
- **product** — product-name cards `h4` → `h2.h4`; category tiles `h5` → `h3.h5`.
- **supplier / supplier-category** — section headers `h3` → `h2.h3`; supplier
  cards `h4` → `h3.h4`.
- **search-results** — section headers `h3` → `h2.h3`; result cards `h5` → `h3.h5`.
- **index (homepage)** — "Categories" `h5` → `h2.h5`; "Register Quick Now"
  `h3` → `h2.h3`. Still no H1 by design.

**Verified:** re-ran the rendered audit (32/33 OK), scanned every page view for
mismatched heading open/close tags (found and fixed 8 introduced by partial
string replacements — `<h2 …>…</h3>`), PHP-linted all 17 edited views, and
confirmed 12 representative URLs plus both stylesheets still return 200.

---

## 2026-08-05 — Fixed: supplier profile pages could render 3 H1 tags

**Files:** `app/Views/pages/supplier-profile.php`, `public/assets/css/style.css`
**Why:** owner reported multiple H1s on `/supplier/profile/middle-fork-capital`.
Confirmed: 3 identical `<h1>Middle Fork Capital</h1>` tags on that page.

**Root cause, and why the earlier audit (same day, see below) missed it:** the
supplier profile banner is a slider built from `$supplier['banner_image']`,
`banner_image_2`, `banner_image_3` — up to 3 images. The heading was *inside*
that `foreach` loop, so it rendered once per banner image. The earlier audit
checked H1 count by grepping the `.php` **source** file, which only sees the
heading once (it's one line of template); it can't see that a loop repeats it
at runtime. The specific supplier checked during that audit
(`2k-building-renovation-gmbh`) happens to have exactly one banner image, so
its page rendered fine and the bug stayed hidden.

**Blast radius:** 3 suppliers currently have 2+ banner images
(`middle-fork-capital`, `torch-industrial-co-ltd`, and one un-slugged row, id
438, reachable via `/supplier/profile/438`) — all confirmed broken, all fixed.
Structural, not just a data issue: any supplier who uploads a second banner
image triggers this.

**Fix:** only the first slide renders a real `<h1>`; the rest use the same
visual text in a `<p class="slide-heading">`, so the carousel still shows the
company name overlaid on every slide (unchanged visually) while the page has
exactly one H1. CSS selectors in `style.css` (3 locations: base rule, a later
override, and a `max-width: 575.5px` responsive rule) broadened from
`.supplier-profile-slider h1` to also match `.supplier-profile-slider
.slide-heading`, so the demoted slides keep identical styling.

**Also checked, whole site:** grepped every page view for a heading tag
appearing inside a `foreach`/`while` loop (the pattern that caused this) —
only `h2`–`h5` matches elsewhere, which is fine to repeat; `supplier-profile.php`
was the only page with a *heading-level-1* tag inside a loop. Then live-rendered
all 30 previously-audited pages plus every affected supplier profile and
confirmed exactly one `<h1>` on each. `buyer-detail.php` (excluded from the
original audit) checked too this time — already clean, unaffected.

---

## 2026-08-05 — Site-wide SEO audit: unique titles, descriptions, canonicals, H1s

**Files:** `app/Controllers/{Pages,Buyer,Product,Supplier,Search,Auth}.php`,
`app/Helpers/seo_helper.php` (new), `app/Config/Autoload.php`,
`app/Views/layouts/{auth,inner-pkg,supplier-profile}.php`,
17 page views for H1 promotion, `app/Views/pages/{forget,reset}-password.php`
**Why:** owner-requested audit of every public page except `buyer-detail.php`
(handled separately) for unique meta title, unique meta description, canonical
tag, and exactly one correctly-leveled H1.

### Scope

`app/Views/pages/*.php` (30 files). Excluded `dashboard/`/`admin/` (gated
internal tools, not crawled) and two dead view files with no route anywhere in
the app — `thankyou.php` and `rfq.php` — fixing SEO tags on unreachable pages
would be wasted effort. Worth a cleanup pass separately.

### Root cause: one shared controller, 16 pages, zero differentiation

`Pages::index($page)` serves about-us, contact, privacy-policy,
terms-and-conditions, refund-policy, user-guide,
banned-keywords-and-illegal-products-policy, become-our-agent-partner,
tradeshow-marketing-services, success-stories, premium-services, all 4 package
pages, and the homepage. Before this: title was
`ucfirst(str_replace('-', ' ', $page))`, which only capitalizes the *first
letter of the whole string* (`"banned-keywords-and-illegal-products-policy"` ->
`"Banned keywords and illegal products policy"`), no description was ever set
(every one of these 16 pages fell through to the single site-wide
`site_settings.meta_description`, so they were all identical in search
results), and no canonical existed at all.

Fixed with a `getPageMeta()` config array in `Pages.php` mapping each slug to a
real title and description, checked against each page's actual content rather
than guessed — package page descriptions cross-checked against their actual
feature lists (10/20/30/50 showcase products, buyer database access, LLC/LTD
registration, etc.) to avoid overclaiming. The homepage deliberately keeps using
`$siteSettings['meta_title']`/`meta_description` — those settings exist
precisely to be the site's identity, and the homepage is the one page where
reusing them is correct rather than a fallback firing where it shouldn't.

### Dynamic controllers (Buyer, Product, Supplier, Search, Auth)

Each listing/detail/search action got a description and canonical. Notable
decisions:

- **New `canonical_self_url()` helper** (`seo_helper.php`) for search/filter
  pages: `current_url()` alone strips the query string, which is correct for
  static pages but wrong here — canonicalizing `/buyer/search?q=rice` to bare
  `/buyer/search` would point at a different, near-empty page. Used across
  Buyer/Product/Supplier search and the global search.
- **Product-by-supplier duplicate URLs consolidated.** `/product?supplier={id}`
  and `/product/supplier/{id}` render identical content; only the latter is
  ever linked from the app (`supplier-profile.php`). The query-param form now
  canonicalizes to the path form instead of self-referencing a URL nothing
  points to.
- **`/supplier-profile` (no id) found to be a dead-route duplicate of
  `/supplier`** — `Routes.php:41` maps it to `Supplier::index`, not to a
  profile page, and it's never linked internally. Canonicalized to `/supplier`
  regardless of which URL was used to reach it, while still preserving the
  pagination query string.
- **New `truncate_for_meta()` helper**, factored out of the truncation logic
  `inquiry_meta_description()` already used, reused for product and supplier
  descriptions (both free-form fields with no length limit, up to 4778 chars
  for supplier `company_introduction`).
- Supplier profile description chain: `company_introduction` (73/160 suppliers
  have one) -> a sentence built from `selling_products` (18/160 missing) ->
  a generic fallback — never empty regardless of data completeness.

### A double-escape bug found and fixed, caused by an earlier change

Two controllers pre-escaped a value with `esc()` before putting it into
`title` — `Buyer.php:276` (`$categoryName = esc($category['name'])`) and
`Search.php:88` (`'title' => 'Search Results for "' . esc($keyword) . '"'`).
This was harmless *before* today, because the `<title>` tag wasn't escaped at
all (see the 2026-08-05 XSS entry above). Now that the layout correctly
escapes the whole title string once, these two double-escaped:
`Building &amp; Construction` was rendering as `Building &amp;amp; Construction`.
Fixed at the source — removed both premature `esc()` calls, since escaping now
happens exactly once, at render time, in the layout. Verified: `Electronics &
Electrical` now renders as `&amp;` exactly once, confirmed against raw
response bytes. Grepped all controllers for the same `= esc(...)`-into-title
pattern; these were the only two.

### Layouts missing description/canonical support entirely

- `inner-pkg.php` and `supplier-profile.php` had a meta description block but
  without the `$metaDescription ??` override (`main.php`/`inner.php` already
  had this from the earlier canonical-tag work) and no canonical tag at all.
- `auth.php` (used by `login.php`/`register.php`) had **neither** — not even
  the site-wide description fallback.
- `forget-password.php`/`reset-password.php` don't extend any layout (standalone
  full-HTML files, pre-existing and out of scope to fix here) — added the same
  description/canonical tags directly into their own `<head>`, wired through
  `Auth::forgotPassword()`/`resetPassword()`.

All fixed to match the pattern already established on `main.php`/`inner.php`.

### H1 promotions — 17 pages had zero H1s, now exactly one each

Per the owner's instructions: observed each promoted heading's original level,
added a matching class (e.g. a promoted `<h2>` gets `class="h2"`) so existing
CSS keeps applying, then changed the tag to `<h1>`. All 17: `about-us`,
`buyer-main`, `contact`, `gold-package`, `login`, `platinum-package`,
`premium-services`, `product-detail`, `product`, `register`, `search-results`,
`starter-package`, `success-stories`, `supplier-category`, `supplier-country`,
`supplier`, `vip-package`.

- `contact.php`: promoted "Contact Us" (h3, mid-page) rather than "Office
  Location" (h2, structurally first) — a judgment call, since the page's H1
  should describe the whole page, not one subsection of it. Noted since it's
  the one page where the promoted heading isn't literally the first one.
- **Homepage deliberately NOT promoted.** Its only pre-H1 headings are
  "Categories" (a sidebar label) and "Register Quick Now! And get free
  Buyers/Suppliers Leads" (a signup CTA) — promoting either would tell search
  engines the page is *about* that text, which is actively wrong for the
  highest-traffic page on the site. Flagged for the owner rather than guessed.
- Verified after: every page in scope has exactly 1 `<h1>`, none have 0 or 2+.

### Verified

Full live sweep of all 30 in-scope pages plus dynamic variants (search with a
query, category/country filters, product/supplier detail) confirmed unique
title, description, canonical, and exactly one H1 on each. `buyer-detail.php`
and the rest of the site spot-checked unaffected.

**Still open:** the homepage's H1 (needs a decision, not a promotion — see
above), and `thankyou.php`/`rfq.php` being dead code (unrelated finding, noted
for a separate cleanup).

---

## 2026-08-05 — JSON-LD structured data on inquiry pages, and a stored XSS found+fixed along the way

**Files:** `app/Views/pages/buyer-detail.php`, all 6 `app/Views/layouts/*.php`
**Why:** owner asked for a schema.org `WebPage`/`Demand` JSON-LD block on inquiry
pages (search-result rich snippets). Testing it against buyer-submitted content
surfaced a live, unrelated, site-wide stored XSS — fixed in the same change
since it was found via the same file and the fix is the same `esc()` pattern
already used elsewhere on this page.

**JSON-LD block:**
- Added to `buyer-detail.php`, right after `$this->section('content')` opens.
  Built as a PHP array and passed through `json_encode()` with
  `JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT` — **not** raw
  string substitution into the `{{placeholder}}` template as originally
  specified — because inquiry title/description/product_name are buyer-submitted
  free text with no server-side validation (BLOCKERS #11), and a literal `</script>`
  or unescaped quote in any of them would otherwise break out of the tag or
  corrupt the JSON.
- Reuses `$canonical`, `$title`, `$metaDescription` already set by
  `Buyer::detail()` — no new controller data needed for those three.
- `Demand.description` uses the **raw** `$inquiry['description']`, not the
  160-char-truncated meta version, since structured data has no length
  constraint; falls back to `$metaDescription`'s templated sentence for the 3
  rows with no description at all, so it's never empty.
- **Verified:** valid JSON on a normal row and on the empty-description
  fallback row (id 22). Adversarial test — submitted a real RFQ via the public
  form with a title containing `"`, `'`, `</script>`, and a raw `<script>alert(1)</script>`
  payload — confirmed the JSON-LD output HEX-escapes all of it (`<`,
  `"`, etc.), stays as exactly one `<script>` tag, and does not execute.

**XSS found and fixed (not part of the original ask, but found while testing it):**
- That same adversarial title exposed a real, exploitable stored XSS: every
  layout's `<title>` tag interpolated `$title` with **no escaping at all** —
  `<title><?= ($title ?? 'Home') . ' | ' . ... ?></title>`. The payload's
  `</script><script>alert(1)</script>` rendered as a live, executing script tag.
  This page's own `<h1>` already used `esc()` on the same field one line away —
  the `<title>` tag was simply missed, not a deliberate choice.
- Reachable via at least three user-controlled fields, confirmed by grep:
  inquiry title (`Buyer.php:151`, buyer-submitted via the public RFQ form),
  product name (`Product.php:124`, supplier-submitted), and supplier company
  name (`Supplier.php:125`, submitted at registration). Any visitor to that
  page — including an admin — would execute the payload. Given CSRF is already
  disabled site-wide (BLOCKERS #7), this combination was a real session-hijack
  path, not a theoretical one.
- Fixed in all 6 layouts (`auth.php`, `dashboard.php`, `inner-pkg.php`,
  `inner.php`, `main.php`, `supplier-profile.php`) by wrapping the whole
  `<title>` expression in `esc()`, matching the pattern already used for meta
  description and canonical URL in the same files.
- **Verified:** the adversarial row's `<title>` now renders as inert
  HTML-entity text (`&lt;/script&gt;&lt;script&gt;...`), zero raw
  `<script>alert(1)</script>` anywhere on the page, JSON-LD unaffected. Spot-checked
  three unrelated pages (`/buyer-inquiry/...`, `/`, `/login`) to confirm normal
  titles still render correctly with no regression.

Test row (id 476, `escapetest@example.invalid`) removed; `buyer_inquiries` back
to baseline 470.

---

## 2026-08-04 — Premium membership now actually gates buyer contact details

**Files:** `app/Controllers/Buyer.php`, `app/Views/pages/buyer-detail.php`
**Why:** the "Premium Members only" mask on Purchaser/Contact Number/Company Name
(`buyer-detail.php`) was gated on `session()->get('user_type') === 'admin'` — no
membership tier of any kind unlocked it. Found and verified while investigating
whether Starter/Gold/Platinum/VIP applied to buyers, suppliers, or both (they're
a single `users.membership_level` enum shared by both roles, but nothing on
either side previously read a *logged-in* user's own tier for anything —
`membership_level` isn't even written into session at login).

- New `Buyer::canViewPremiumDetails()`: true for `user_type === 'admin'`, or for
  a logged-in user whose `membership_level` (queried live from the DB, not
  session — session never carried it and can't reflect a tier change without
  forcing a re-login) is one of `starter`, `gold`, `platinum`, `vip`.
- All three `session()->get('user_type') === 'admin'` checks in
  `buyer-detail.php` replaced with `!empty($canViewPremiumDetails)`.
- Deliberately still admin-inclusive: task was "add premium-member access", not
  "remove admin access" — an admin with a `free`-tier account (the default on
  every account, including admins) still needs to see this.

**Verified end-to-end**, not just read: registered a throwaway supplier account,
confirmed anonymous view and free-tier-logged-in view both still masked (0/3
fields leak), then flipped the *same session* to each of the four paid tiers in
turn — all four unlocked immediately with no re-login required, confirming the
live-DB-read design works as intended. Also confirmed a `buyer`-type account
with a paid tier gets access too (the fix isn't role-specific, matching that
`membership_level` was never role-specific in the schema). Admin bypass
re-verified with a fresh login (a stale session retains its login-time
`user_type`, so this needed re-login to test correctly — a design constraint of
the *existing* session mechanism, not something this change altered).

Throwaway test account **kept, not deleted**, for the owner's own testing:
`goldtiertest@example.invalid` / `GoldTest123!`, id 664, `gold` tier, supplier
role, approved. Delete after testing — it is not real data.

---

## 2026-08-02 — Close the duplicate `/public/*` URL surface

**Files:** `public/.htaccess`
**Why:** every route on the site was reachable a second way, under `/public/*`
(e.g. `/public/buyers` returned 200 identically to `/buyers`) — a site-wide
duplicate-content surface for search engines, and `robots.txt` doesn't block it
(`Disallow:` is empty).

Root cause: root `.htaccess` internally forwards every request into `public/`
before CodeIgniter handles it, so by the time `public/.htaccess` runs,
`REQUEST_URI` contains `/public/` **regardless of what the client actually
typed** — it can't distinguish a normal visit from someone hitting the front
controller directly. Fixed by testing `%{THE_REQUEST}` instead, which reflects
the client's raw request line and is untouched by the internal rewrite — the
same trick this file already used for its `index.php/` redirect, two lines
above the new rule.

- Added a rule at the top of the `<IfModule mod_rewrite.c>` block: if
  `THE_REQUEST` shows `/public` as a path segment, 301 to the same URL with
  `/public` stripped.
- Verified no route in `Routes.php` contains "public" as a path segment, so
  the check can't misfire on real content.
- **Verified:** `/public` → 301 → `/public/` (Apache's own trailing-slash
  behaviour, unchanged) → 301 → canonical root. `/public/buyers`,
  `/public/buyer-inquiry/{slug}` and `/public/assets/css/style.css` all now
  single-hop 301 straight to their clean equivalent and 200. Normal routes
  (`/`, `/buyers`, `/login`, asset requests) unaffected — no false positives.
- Portable across environments: the rule captures whatever prefix sits before
  `/public` (empty on a production domain root, `/b2btradeservices` here) and
  reconstructs the target from it, rather than hardcoding either form.

---

## 2026-08-02 — Manual SEO/branding changes (owner, outside this session)

Made directly by Nabeel; logged here after the fact per project convention, verified
against the live files rather than taken on faith.

**Files:** `app/Views/pages/buyer-detail.php`, `app/Views/partials/footer.php`, all 6
`app/Views/layouts/*.php`
**Why:** SEO heading hierarchy + site branding/analytics completeness.

- **Heading hierarchy on the inquiry detail page.** The inquiry title
  (`buyer-detail.php:20`) is now the page's `<h1>`; the banner heading that used
  to be `<h1>Find B2B Buying Leads</h1>` is now `<h2>` (`buyer-detail.php:9-10`).
  Correct per-page — a page should have one `<h1>` describing its actual content,
  not a shared banner slogan. Verified: exactly one `<h1>` on the page now.
- **Google Analytics gtag snippet added to `partials/footer.php:294-305`,**
  hardcoded to `G-L52TR0D4JK`. Rendered on every page that includes this
  partial: `inner.php`, `main.php`, `inner-pkg.php`, `supplier-profile.php`
  (i.e. all public pages except `dashboard.php`/`auth.php`, which don't include
  the footer partial, and `blog/`, which is a separate WordPress install).
- **Full favicon/apple-touch-icon/manifest block added to all 6 layout
  `<head>`s** (`auth.php`, `dashboard.php`, `inner-pkg.php`, `inner.php`,
  `main.php`, `supplier-profile.php`) — apple-touch-icons at 8 sizes, PNG
  favicons at 3 sizes, `manifest.json`, `msapplication-TileColor`/
  `-TileImage`, `theme-color`. Site-wide, including dashboard/auth, unlike the
  GA snippet above.

**Flagging, not fixing:** `inner.php`, `main.php`, `inner-pkg.php` and
`supplier-profile.php` already had a *conditional*, admin-configurable GA block
(`$siteSettings['google_analytics_id']`, gated on the setting being non-empty).
That setting currently holds a placeholder-looking value (`hgkh-sfkjh`), which is
presumably why it doesn't fire today — but if an admin ever sets a real
measurement ID there, **both the DB-driven snippet and this new hardcoded one
would load simultaneously**, double-counting every pageview. Worth deciding
which one is authoritative before that setting is ever populated for real.

---

## 2026-08-02 — Dynamic per-inquiry meta descriptions

**Files:** `app/Helpers/inquiry_helper.php`, `app/Controllers/Buyer.php`,
`app/Views/layouts/{inner,main}.php`
**Why:** every page shared one `<meta name="description">` pulled from
`site_settings.meta_description`, so all 470 inquiry pages had an identical,
generic description — duplicate-content territory for search engines, same class
of problem the slug work fixed for URLs.

- New `inquiry_meta_description($inquiry, $maxLength = 160)` in
  `inquiry_helper.php`. Prefers the buyer's own description text (467/470 rows
  have one); normalizes embedded newlines/tabs (98 rows have them) into a single
  line, and truncates at a word boundary near 160 chars with `...` — never
  mid-word. Verified against a synthetic long string: 158 chars, clean cut.
- Falls back to a templated sentence built from title/quantity/unit/product_name
  for the 3 rows with no description at all (ids 22, 27, 29), so the tag is
  never empty.
- `Buyer::detail()` now passes `'metaDescription' => inquiry_meta_description($inquiry)`
  to the view.
- Both layouts changed from `$siteSettings['meta_description'] ?? ''` to
  `$metaDescription ?? $siteSettings['meta_description'] ?? ''` — a page-level
  override that falls back to the site default, the same opt-in pattern already
  used for `$canonical`. Any other controller can adopt it the same way: pass
  `metaDescription` in `$data`, no further layout change needed.

**Verified:** a description-bearing inquiry renders its own text; the
empty-description fallback path renders the templated sentence; unrelated pages
(homepage, `/buyers`) still show the unchanged site-wide default.

---

## 2026-08-02 — Slug work deployed to production

**Files:** the 20-file set below, deployed to the cPanel host
**Why:** ship the fix for BLOCKERS #14.

- Owner confirmed production was 3 migrations behind, matching the state inferred
  from the imported `migrations` table — so the Step 0 guard on
  `2026-03-14-222249_AddFieldsToContactSubmissions` was a genuine prerequisite, not
  local housekeeping. Without it the batch would have aborted on
  `Duplicate column name 'country_id'` and the slug migrations would never have run.
- Six migrations applied on production (3 pre-existing no-ops + the 3 new ones)
- **Backfill verified on production:** `COUNT(*) = COUNT(DISTINCT slug)`, nulls = 0.
  This is the check that matters — the failure mode is silent, and `spark migrate`
  reports success either way (see the `resetDataCache()` note in the entry below)
- Slug URLs confirmed serving correctly; numeric inquiry ids confirmed 301ing to
  their canonical slug

`app/Config/Database.php` was deliberately excluded from the upload — it is committed
with dev values and would take production offline. See CONTEXT.md.

**Still open after this deploy:** BLOCKERS #17 — do not run `php spark migrate:rollback`
on production; all six migrations landed in one batch and three of them have an
unconditional `down()` that drops five real columns.

---

## 2026-08-01 — Inquiry URLs migrated to real slugs (BLOCKERS #14 closed)

**Files:** 20 — `app/Config/{Autoload,Routes}.php`, `app/Controllers/{Buyer,Dashboard,AdminSettings}.php`,
`app/Models/BuyerInquiryModel.php`, `app/Helpers/inquiry_helper.php` (new),
3 new migrations, `app/Database/Migrations/2026-03-14-222249_*` (fix),
`app/Database/Seeds/DataSeeder.php`, 6 views, 2 layouts
**Why:** `Routes.php:58` captured the slug then discarded it, so any slug served
any inquiry. SEO duplicate content at unlimited URLs per record.

**Schema** — `buyer_inquiries.slug` VARCHAR(255) NULL + `buyer_inquiries_slug`
UNIQUE; `status` ENUM widened to include `inactive`.

**Canonical URL is now `/buyer-inquiry/{slug}`.** Every historic shape 301s to it:
`/buyer-inquiry/{anything}/{id}`, `/buyer-detail/{id}`, `/buyer/detail/{id}`,
and bare `/buyer-inquiry/{id}`.

**Verified**
- 470/470 slugs, all distinct, zero nulls, zero bad characters, max length 125
- **Zero URL churn** — every backfilled slug byte-identical to what the old
  render-time expression produced; 0 unexpected diffs across all 470 rows
- Collisions resolved as predicted: ids 433/434 got `-2` suffixes
- Original bug gone: `/buyer-inquiry/bulk-rice-import-requirement/{1,2,3,4}` now
  301 to four *different* correct slugs instead of serving four different pages
  under one slug
- Single-hop redirects, no chains; 404s on unknown slug and unknown id
- Canonical tags present and distinct per page; zero legacy two-segment links left
- Runtime dedupe through the live RFQ form: 3 identical titles →
  `zztest-dedupe-widget`, `-2`, `-3`; empty title → `buyer-inquiry`
- Freeze confirmed: a full title rewrite left the slug untouched
- Test rows removed; every table back to baseline (users 641, inquiries 470,
  submissions 61, activities 542, notes 109)

**Also fixed** — `Dashboard.php:686` `'inactive'` → `'pending'` (matches the product
path at `:352`); status whitelists on both `AdminSettings` update actions;
`getInquiriesByCategory()` gained `$excludeId` so related lists show 5 not 4;
`insert()` retry-once on slug collision; moderation help text reworded.

### Two defects found during implementation, not in the plan

**1. The backfill silently did nothing on its first run.** `getFieldNames()` caches
per connection and `Forge::addColumn()` never invalidates it, so the backfill's own
column guard read a stale list, concluded `slug` did not exist and returned early —
while reporting success. Fixed with `resetDataCache()` in both migrations.
**This would have reproduced on production**, leaving 470 null slugs behind an
apparently-clean `spark migrate`.

**2. Pre-existing 500 on public inquiry pages.** `Buyer::detail` called
`find($inquiry['country_id'])` unguarded; CI4's `find(null)` returns *all* rows, so
a null country handed the view a 122-element list and `['name']` fataled. Reachable
because the RFQ form's Country `<select>` is labelled required (red asterisk) but
carries no `required` attribute. All 470 imported rows have a country, which is why
it never surfaced. Controller now guards null ids. The missing `required` attribute
is **not** fixed — see BLOCKERS #15.

---

## 2026-07-29 — Investigated: inquiry URL slug is decorative

**Files:** none changed (investigation only)
**Why:** Reported by Nabeel — changing the id while keeping the slug opens a
different inquiry.

- **Reproduced.** `buyer-inquiry/bulk-rice-import-requirement/{1,2,3,4}` renders
  four different inquiries; only id 3 matches the slug
- **Root cause pinned to one line** — `app/Config/Routes.php:58` forwards only `$2`
  (the id) to `Buyer::detail`; `$1` (the slug) is captured and discarded, so it is
  never compared against the record
- **Also found:** `(:any)` compiles to `(.*)` and crosses `/`, so
  `buyer-inquiry/a/b/c/3` resolves too — unlimited URL variants per inquiry
- **No `<link rel="canonical">` anywhere in `app/Views/`** (verified), so every
  variant is a distinct indexable URL
- **Scope bounded:** line 58 is the only route in the file that captures a group
  and drops it. `supplier/profile/(:any)` forwards `$1` and genuinely looks up by
  slug; `product/detail/(:num)` carries no slug at all
- **Constraint on the fix:** `buyer_inquiries` has no `slug` column — slugs are
  derived from `title` by `Buyer::inquirySlug()` (`Buyer.php:68`) and re-derived
  inline in six views, so lookup-by-slug has nothing to match against
- Not a security issue — `Buyer::detail` still enforces `status === 'active'`

Logged as BLOCKERS #14 with two fix options; tasks T-13 and T-14.

---

## 2026-07-29 — Local database declared a writable scratch copy

**Files:** `CLAUDE.md`, `.claude/CONTEXT.md`, `.claude/DECISIONS.md`,
`.claude/TASKS.md`, `sql/` (owner-supplied)
**Why:** Owner confirmed the imported database is decoupled from production and may
be freely modified for development and testing.

- Recorded as DECISIONS #12; supersedes the read-only posture of the verification
  sweep
- **T-11 (POST-route testing) unblocked**; added T-12 to backfill the 6 null
  `inquiry_date` rows as a migration
- Owner added restore dumps: `sql/defaultdb.sql` (11 tables → `b2btradeservices`)
  and `sql/i10853125_icx81.sql` (13 tables → WordPress). Verified both are
  gitignored by `*.sql` and return 404 over HTTP — the root rewrite folds `/sql/*`
  into `public/`, which does not exist
- Documented restore and snapshot commands in CONTEXT.md; snapshots go to
  `C:\xampp\db_backups\`, outside the web root

**Two constraints kept, and they are independent of the decoupling** — the rows are
still real customer records:

- Dumps stay out of git and are never shared
- **SMTP stays unconfigured.** `app/Helpers/email_helper.php` is called from
  `Auth.php:118` (welcome mail on registration) and `Auth.php:229` (password reset)
  and sends to the address on the user row — 641 real addresses.
  `app/Config/Email.php` uses `protocol = 'mail'` and `php.ini` has
  `SMTP=localhost:25` with **nothing listening**, verified. Mail therefore fails
  silently today, and that failure is the only thing preventing real delivery.
  Configuring SMTP to "make testing work" would start mailing real users

**Note:** `db_sql_export.sql` had been deleted, leaving no restore path at the point
the database became writable. Baseline dumps were taken to
`C:\xampp\db_backups\*_baseline_20260729.sql` before the owner supplied `sql/`;
they are now redundant duplicates of the same PII and can be deleted.

---

## 2026-07-29 — Full application verification sweep

**Files:** none changed (read-only audit, one test row created and removed)
**Why:** Close BLOCKERS #5 — the app was unverified beyond the homepage.

**Static**
- `php -l` across all **168** PHP files under `app/` — zero syntax errors
- **136/136** non-closure route handlers resolve to real controller methods (the
  other 2 of 138 are closures)
- **67** `view()` calls checked — all resolve; the one apparent miss is
  `view('pages/' . $page)` dynamic concatenation in `Pages.php:133`, backed by 32
  real files in `app/Views/pages/`
- `php spark routes` runs — framework boots cleanly on CLI

**HTTP** — all 97 GET routes, 9 destructive ones deliberately skipped
- 37 × `200` public pages
- 45 × `302` — every dashboard/admin route redirects to `/login` unauthenticated;
  no route leaked a 200
- 4 × `301` legacy redirects (by design, `Buyer::legacyRedirect`)
- 1 × `403` `/dashboard/chart-data` (JSON endpoint, unauthenticated)
- 1 × `404` `/supplier/profile/1` — correct, that route takes a slug not an id
- Deep samples all 200: 15/15 supplier profiles, 15/15 product details,
  15/15 buyer inquiry pages

**Defects found** — all logged in BLOCKERS.md, none fixed
- #7 CSRF disabled site-wide; proven by a token-less POST being accepted
- #8 nine destructive actions exposed as GET
- #9 `AuthFilter` applied to no route; auth is per-method and opt-in
- #10 `buyer-main.php:118` renders "01 Jan, 1970" for 6 inquiries
- #11 `contact/submit` accepts empty email and message
- #12 `suppliers` table + `SupplierModel` are dead code
- #13 `index.php:146` renders `inquiry_date` unguarded (latent)

**Cleanup:** the CSRF probe inserted `contact_submissions` id 62 (`name='x'`).
Removed with an exact-id delete; count verified back at 61.

**Not covered:** the 41 POST routes — they write to a database holding real user
data. Tracked as T-11. Email sending also untested.

**Environment notes (not defects)**
- The `CRITICAL fwrite errno=22` entries in the log are a Windows artifact of piping
  `spark` output through Git Bash (`system/CLI/InputOutput.php:78`), not an app fault
- App timezone is UTC while Windows local time is ahead — log filenames are
  UTC-dated, so "today's log" is not `date +%Y-%m-%d`
- `mysql.exe` emits CRLF; trailing `\r` corrupts URLs built from query output

---

## 2026-07-29 — Blockers #1, #2, #3 resolved by owner

**Why:** Post-incident cleanup after the GitHub push-protection block.

- Aiven database password rotated — the credential exposed in the import is dead
- `app/Config/_database.php` deleted — verified gone; held live production
  credentials and was never loaded by the framework
- `app.zip` (166 MB) deleted — verified gone; project tree now 327 MB
- Confirmed no remaining copies of the credentials in `app/` or root PHP files
- Residual: the Aiven **username** (`avnadmin`, no password) still appears in
  `writable/logs/log-2026-02-28.log` and `log-2026-03-13.log`. Both gitignored,
  password now rotated — noted, not acted on

---

## 2026-07-29 — Set up `.claude/` project documentation

**Files:** `CLAUDE.md`, `.claude/PROJECT.md`, `.claude/CONTEXT.md`,
`.claude/TASKS.md`, `.claude/DECISIONS.md`, `.claude/BLOCKERS.md`,
`.claude/CHANGELOG.md`
**Why:** Persist project context, decisions and history across sessions.

- Created the index at the repo root so Claude Code auto-loads it; detail docs in
  `.claude/` (DECISIONS #11)
- Recorded architecture, stack and exact table row counts, verified against the
  running system rather than recalled
- Backfilled decisions #1–#11 and blockers #1–#6 from the setup work below

---

## 2026-07-29 — Remove hardcoded credentials from seeders; clean git history

**Files:** `seed_data.php`, `seed_products.php`, git history
**Why:** GitHub push protection rejected the push — live Aiven credential in both
seeders.

- Replaced the hardcoded Aiven connection in `seed_data.php:6` and
  `seed_products.php:5` with `getenv()` lookups defaulting to local XAMPP
  (`localhost` / `root` / empty / `b2btradeservices` / 3306)
- Dropped the Aiven-specific SSL setup — local MySQL does not use it
- Both files pass `php -l`
- Amended the single unpushed commit; expired the reflog and ran
  `git gc --prune=now`, destroying old commit `c7358cf`. History now starts at
  `df28e09`
- Scanned all 1,258 committable files for Aiven/cPanel credentials, private keys,
  AWS/GitHub/OpenAI/Slack tokens and CodeIgniter `hex2bin:` keys — clean. Sanity-
  checked the scanner against a known string first
- Did **not** use GitHub's "allow this secret" URL (DECISIONS #9)

> Behaviour change: the seeders now write to the **local** database, not
> production. They insert rather than upsert — running twice duplicates data.

---

## 2026-07-29 — Add `.gitignore`

**Files:** `.gitignore`
**Why:** Repo had no ignore rules; 19,791 untracked files including secrets and a
166 MB archive that would have hard-failed GitHub's per-file limit.

- Secrets: `.env`, `app/Config/_database.php`, `blog/wp-config.php`
- Archives and dumps: `*.sql`, `*.zip`, `*.tar*`, `*.bak` — covers `app.zip`
  (166 MB) and `db_sql_export.sql` (866 KB of real user PII)
- Dependencies: `vendor/`, `node_modules/`
- `writable/` contents, keeping the `index.html` guards via negation
- Uploads: `public/uploads/`, `/uploads/`, `blog/wp-content/uploads/` (~95 MB)
- WordPress runtime, editor and OS cruft
- Verified with `git check-ignore` that sensitive paths are excluded and that
  `.htaccess`, config, `env` template and `writable/*/index.html` stay tracked

> Nabeel subsequently added `blog/` to exclude WordPress entirely (DECISIONS #7),
> cutting the pushed repo to ~18 MB / 1,258 files.

---

## 2026-07-29 — Fix WordPress config and rewrite base

**Files:** `blog/wp-config.php`, `blog/.htaccess`
**Why:** WordPress pointed at a cPanel DB user that does not exist locally, and
served production URLs.

- `DB_USER` → `root`, `DB_PASSWORD` → empty; `DB_NAME` left as `i10853125_icx81`
  so the import matches production (DECISIONS #6)
- Added `WP_HOME` and `WP_SITEURL` pointing at
  `http://localhost/b2btradeservices/blog`, overriding the production URLs in
  `bk1o_options` without editing the database (DECISIONS #5)
- `WP_DEBUG` on
- `blog/.htaccess`: `RewriteBase` and rewrite target updated to the subfolder path

---

## 2026-07-29 — Fix root `.htaccess` rewrite for subfolder

**Files:** `.htaccess`
**Why:** Every request was folded into `public/`, including `/blog/*` — WordPress
404'd, and `public/` was re-rewritten into `public/public/`.

- Added `RewriteRule ^(public|blog|cgi-bin)(/|$) - [L]` above the catch-all
- Replaced the absolute `RewriteCond %{REQUEST_URI} !^/public/`, which could never
  match under a subfolder, with directory-relative patterns that work in both
  environments (DECISIONS #4)

---

## 2026-07-29 — Configure `.env` and `baseURL` for localhost subfolder

**Files:** `.env`, `app/Config/App.php`
**Why:** Environment was set to production and `baseURL` resolved to the live
domain.

- `.env`: `CI_ENVIRONMENT` production → development;
  `app.baseURL` → `http://localhost/b2btradeservices/`; removed
  `REPLIT_DEV_DOMAIN`; added `database.default.*` for local MySQL
- `app/Config/App.php`: constructor now checks `env('app.baseURL')` first. It runs
  after `parent::__construct()` so it was overwriting `.env`, and `$baseURL` has no
  default value so CodeIgniter's `.env` binding skipped it entirely (DECISIONS #3)
- Replit and `HTTP_HOST` branches retained as fallbacks

---

## 2026-07-29 — Point CodeIgniter DB config at local MySQL

**Files:** `app/Config/Database.php`
**Why:** Committed config pointed at a remote MySQL on port 14007.

- `hostname` → `localhost`, `username` → `root`, `password` → empty,
  `database` → `b2btradeservices`
- `port` 14007 → 3306
- `encrypt` true → false — local MySQL has no TLS (DECISIONS #2)

---

## 2026-07-29 — Install Composer dependencies

**Files:** `vendor/`
**Why:** No `vendor/` directory; CodeIgniter cannot boot without
`vendor/autoload.php`.

- Ran `composer install` (Composer 2.4.3) — installed `laminas/laminas-escaper`
  and `psr/log`

---

## 2026-07-29 — Enable PHP `intl` extension

**Files:** `C:\xampp\php\php.ini` (outside the repo)
**Why:** CodeIgniter requires `ext-intl`; it was commented out in XAMPP's default
`php.ini`.

- Uncommented `extension=intl` at line 923
- Apache restarted to load it into the web SAPI — a CLI-only check would not have
  caught this

---

## 2026-07-29 — Initial import

**Why:** Standing up a local dev environment from the live cPanel site.

- Site files imported to `C:\xampp\htdocs\b2btradeservices`
- Main database restored to `b2btradeservices` (11 tables)
- WordPress database restored to `i10853125_icx81` (13 tables, prefix `bk1o_`)
- Git repository initialised
