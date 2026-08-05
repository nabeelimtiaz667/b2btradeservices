# BLOCKERS.md — open problems and risks

Things that are stuck, waiting on someone, or knowingly unresolved. Remove an entry
only when it is genuinely fixed — record the fix in [CHANGELOG.md](CHANGELOG.md).

Severity: `CRITICAL` · `HIGH` · `MEDIUM` · `LOW`

Resolved and removed: #1 (Aiven credential — rotated), #2 (`_database.php` —
deleted), #3 (`app.zip` — deleted), #5 (app unverified — swept 2026-07-29),
#14 (inquiry slugs — shipped to dev 2026-08-01, pending production deploy T-15).

**Still open despite the slug work:** #10 and #13, the "01 Jan, 1970" rendering
bugs. They share the same `buyer_inquiries` table but are a separate defect —
nothing in the slug change touched `inquiry_date`. Tasks T-5, T-6, T-12.

---

## #19 — Homepage has no H1, and no existing heading is fit to promote
**Severity:** LOW · **Raised:** 2026-08-05 · **Open — needs an owner decision**

Every other page audited for T-24 (see CHANGELOG 2026-08-05) got a proper H1,
either one that already existed or a promoted heading. The homepage is the one
exception, deliberately left alone.

`app/Views/pages/index.php`'s only headings before the fold are "Categories"
(`<h5>`, a sidebar filter label) and "Register Quick Now! And get free
Buyers/Suppliers Leads" (`<h3>`, a signup CTA). Promoting either would tell
search engines the homepage's primary topic is a sidebar label or a signup
nag — actively worse than having no H1 at all, on the single highest-traffic
page on the site.

**Needs:** a real, written H1 for the homepage — something like "B2B Trade
Services — Connect with Verified Global Suppliers and Buyers" (illustrative,
not a suggestion to ship as-is). This is new copy, not a promotion of existing
text, which is why it wasn't done unilaterally here.

**Update 2026-08-06 (T-26):** the homepage's *internal* ordering is now fixed —
it used to open `h5 (Categories) → h3 (Register Quick Now) → h2`, the exact
"starts deep then jumps" pattern; both are now `h2` with their original sizing
preserved via classes. So the outline is clean and consistent apart from the
missing top level. Adding the H1 is now a one-line change with nothing else to
untangle: put it in `app/Views/pages/index.php` above the banner section, and
the page goes from NO-H1 to fully compliant.

---

## #20 — `thankyou.php` and `rfq.php` are dead view files, no route anywhere
**Severity:** LOW · **Raised:** 2026-08-05 · **Open**

Found while auditing all `app/Views/pages/*.php` files for T-24. Neither has a
route in `Routes.php`, and neither is referenced by `view()` in any controller
— confirmed by grepping the whole `app/` tree for both slugs. `post-rfq.php` is
the file actually used for the RFQ form (`Buyer::postRfq()`); `rfq.php` appears
to be an earlier, superseded version left behind.

Not fixed here (SEO tags on an unreachable page would be wasted work) or
deleted (out of scope for an SEO audit — deleting code wasn't asked for). Same
category of issue as BLOCKERS #12 (`suppliers` table / `SupplierModel`): safe
to delete once someone confirms neither is referenced from outside this repo
(an email template, a hardcoded link elsewhere, etc.).

---

## #18 — Two Google Analytics snippets would fire simultaneously if the admin GA setting is ever used
**Severity:** LOW · **Raised:** 2026-08-02 (owner change, logged after the fact) · **Open**

`app/Views/partials/footer.php:294-305` now has a hardcoded `gtag.js` snippet for
`G-L52TR0D4JK`, rendered on every page that includes the footer partial (`inner.php`,
`main.php`, `inner-pkg.php`, `supplier-profile.php` — i.e. all public pages).

Those same four layouts **already had** a separate, conditional GA block driven by
`$siteSettings['google_analytics_id']` — it only renders if that setting is non-empty.
Today it's harmless: the DB value is `hgkh-sfkjh`, which doesn't look like a real
measurement ID and presumably has never fired.

**But if an admin ever sets a real value in Admin → Settings → General → Google
Analytics ID, both snippets load on the same page** — two `dataLayer` pushes, two
`gtag('config', ...)` calls, double-counted pageviews. Neither snippet is aware of
the other.

**Fix, when someone gets to it:** pick one mechanism. Either delete the hardcoded
footer snippet and put `G-L52TR0D4JK` into `site_settings.google_analytics_id`
(makes it admin-editable, consistent with the existing feature), or remove the
conditional block from the four layouts and treat the footer snippet as
authoritative (simpler, but re-hardcodes something the admin panel was built to
configure). Not urgent — only bites if that setting is ever populated.

---

## #15 — RFQ form's Country field is labelled required but is not
**Severity:** MEDIUM · **Raised:** 2026-08-01 · **Open**

`app/Views/pages/post-rfq.php:89-90` renders
`Country <span class="text-danger">*</span>` above a `<select name="country">` that
has **no `required` attribute**. The Category select right above it does have one.
`Contact::submit` does not validate it either (`Contact.php:133` →
`getPost('country') ?: null`).

So a buyer can submit an RFQ with no country and get an inquiry with
`country_id = NULL`.

**This used to produce a 500 on the inquiry's public page.** `Buyer::detail` called
`$this->countryModel->find(null)`, and CI4's `find(null)` returns *every* row — so
the view received a 122-element list and `$inquiry['country']['name']` fataled with
`Undefined array key "name"`. All 470 imported rows have a country, which is why it
had never been seen.

**Already fixed:** the controller now guards null ids, so such pages render with the
country omitted rather than crashing. Verified.

**Still open:** the form itself. Either add `required` to the select to match its own
asterisk, or drop the asterisk and accept country-less inquiries. Left alone because
it changes submission behaviour on a live public form — owner's call.

Worth auditing the other nullable FKs on the same form (`category_id`) and the same
`find($id)` pattern elsewhere in the codebase.

---

## #17 — `migrate:rollback` will drop five real production columns
**Severity:** HIGH · **Raised:** 2026-08-01 · **Open**

**Do not run `php spark migrate:rollback` on this codebase without reading this.**

Three migrations have a **guarded `up()` but an unconditional `down()`**:

| Migration | `up()` | `down()` |
|---|---|---|
| `2024-03-14-000001_AddIsFeaturedToUsers` | `if (! in_array('is_featured', ...))` | `dropColumn('users', 'is_featured')` |
| `2026-03-14-222249_AddFieldsToContactSubmissions` | guarded (fixed 2026-08-01) | `dropColumn('contact_submissions', ['country_id','partnership','whatsapp'])` |
| `2026-03-14-222841_AddFormDataToContactSubmissions` | `if (! in_array('form_data', ...))` | `dropColumn('contact_submissions', 'form_data')` |

All three columns sets already existed when the migrations were written — they were
added to the database by hand, and the migrations were back-filled afterwards. So
each `up()` correctly does nothing. But each `down()` happily drops columns its own
`up()` never created, along with their data.

### Why this is now reachable

CI4's `migrate:rollback` defaults to `$batch = $runner->getLastBatch() - 1`
(`system/Commands/Database/MigrateRollback.php`), i.e. it regresses the **whole last
batch**, not a single migration.

On production all six pending migrations will run in **one** `spark migrate`
invocation, so they land in one batch. A single `migrate:rollback` — the obvious
thing to reach for if the slug work misbehaves — therefore undoes all six and drops:

- `users.is_featured`
- `contact_submissions.country_id`
- `contact_submissions.partnership`
- `contact_submissions.whatsapp`
- `contact_submissions.form_data`

Measured on the local copy of production data: **25 users have `is_featured = 1`**.
The `contact_submissions` columns are currently empty locally, but production may
differ — and `is_featured` alone is real, user-visible state.

The original 10 migrations (batches 1–4) are **not** touched by a single rollback.
`migrate:rollback -b 0` would take everything, including the `CREATE TABLE`s.

### What to do instead

Use the database dump for rollback. For a surgical undo of just the slug work:

```sql
ALTER TABLE buyer_inquiries DROP INDEX buyer_inquiries_slug;
ALTER TABLE buyer_inquiries DROP COLUMN slug;
DELETE FROM migrations WHERE version IN
  ('2026-07-30-000001','2026-07-30-000002','2026-07-30-000003');
```

### Fixing it properly is not just "add a guard"

An existence check in `down()` does not help — the columns **do** exist, so the guard
passes and the drop still happens. The information that matters (did *this* migration
create the column?) is not available at rollback time.

Realistic options:

1. Make `down()` a no-op on all three with a comment explaining the columns predate
   the migration. Honest, and makes rollback safe.
2. Have `up()` record whether it actually added anything (a marker row in
   `site_settings`, say) and have `down()` respect it. More correct, more machinery.
3. Leave as-is and treat `migrate:rollback` as forbidden on this project.

Option 1 is probably right. Not done here because it changes rollback semantics for
migrations unrelated to the slug work, and should be a deliberate separate change.

Related: the schema drift that caused this is the same drift behind `buyer_whatsapp`,
`inquiry_date` and `users.slug` existing with no migration at all (PROJECT.md).

---

## #16 — HEAD requests return 404 on every route
**Severity:** LOW · **Raised:** 2026-08-01 · **Open**

Verified site-wide, including untouched routes:

| Route | HEAD | GET |
|---|---|---|
| `/` | 404 | 200 |
| `/about-us` | 404 | 200 |
| `/login` | 404 | 200 |
| `/buyers` | 404 | 200 |

`Routes.php` registers everything with `$routes->get(...)`, which does not answer
HEAD. Pre-existing and unrelated to the slug work — it was found because a
`curl -I` redirect-chain check returned a misleading `hops=0`.

Affects link checkers, uptime monitors, some crawlers and CDN prefetch. Low impact
today; worth knowing before trusting any HEAD-based tooling against this site.

---

## #4 — PHP 8.1.5 is past end of security support
**Severity:** MEDIUM · **Raised:** 2026-07-29 · **Open — deferred by owner**

XAMPP is running PHP 8.1.5 (released April 2022). PHP 8.1 has reached end of
security support, and 8.1.5 is well behind even the last 8.1 patch.

CodeIgniter 4.6.4 requires `^8.1`, so it runs, but the local environment carries
known unpatched CVEs and drifts from whatever production runs.

**Needs:** confirmation of the production PHP version, then a decision on whether to
upgrade both together. Note #10 below is a PHP 8.1 deprecation that becomes a fatal
error in PHP 9 — worth fixing before any major upgrade.

---

## #6 — Production encryption key is on the dev machine
**Severity:** LOW · **Raised:** 2026-07-29 · **Open — deferred by owner**

`.env` carries the encryption key from production. Gitignored, so it will not be
committed.

Convenient — production-encrypted values stay readable locally — but it means a
production secret now lives on a development machine. Worth generating a separate
key for local use if the environment is ever shared or the machine is not solely
the owner's.

---

## #7 — CSRF protection is disabled site-wide
**Severity:** HIGH · **Raised:** 2026-07-29 · **Open**

`app/Config/Filters.php:83` has `// 'csrf',` — the CSRF filter is registered as an
alias but commented out of `$globals`, so **no route is CSRF-protected**.

**Verified empirically:** a `POST` to `/contact/submit` with no CSRF token and no
session was accepted and inserted a row (test row removed afterwards).

This affects all 41 POST routes, including the authenticated dashboard ones —
user edits, supplier/product changes, admin imports and settings updates.

Compounded by #8: with CSRF off *and* destructive actions on GET, an authenticated
admin merely loading a hostile page can have records deleted.

**Fix:** uncomment `'csrf'` in `$globals['before']`, then confirm every form and
AJAX call sends the token. Not done unilaterally — enabling it will break any form
that does not currently include `csrf_field()`, so it needs a pass over the views.

---

## #8 — Nine destructive actions are exposed as GET routes
**Severity:** HIGH · **Raised:** 2026-07-29 · **Open**

State-changing operations reachable by a plain link:

```
dashboard/delete/(:num)                 dashboard/approve/(:num)
dashboard/agents/delete/(:num)          dashboard/reject/(:num)
dashboard/suppliers/delete/(:num)       dashboard/submissions/delete/(:num)
dashboard/inquiries/delete/(:num)       dashboard/buyer/inquiries/delete/(:num)
logout
```

GET is meant to be safe and idempotent. As written these are triggerable by
prefetchers, crawlers, browser accelerators, or an `<img src>` on any page an
authenticated admin visits — and with #7 there is no token to stop it.

**Fix:** convert to POST/DELETE with a CSRF token and a confirmation step.

**Note for testing:** these routes were deliberately excluded from the verification
sweep. Do not curl them against a database holding real data.

---

## #9 — `AuthFilter` exists but is applied to no route
**Severity:** MEDIUM · **Raised:** 2026-07-29 · **Open**

`app/Filters/AuthFilter.php` is written and aliased as `'auth'` in `Filters.php`,
but `Routes.php` never references it — every route's only before-filter is
`sitesettings` (plus `role:admin` on the 6 import routes).

Authentication is instead enforced method-by-method inside the controllers.
Empirically this currently works — all 45 dashboard/admin routes tested redirect to
`/login` when unauthenticated, and none leaked a 200.

The risk is structural, not present-tense: protection is opt-in per method, so any
new controller method that forgets the check is public by default. A filter on the
`dashboard/*` and `admin/*` groups would make it opt-out instead.

---

## #10 — Buyer search renders "01 Jan, 1970" for 6 inquiries
**Severity:** MEDIUM · **Raised:** 2026-07-29 · **Open** · **Tasks:** T-5, T-6

### Root cause — one line

`app/Views/pages/buyer-main.php:118`:

```php
<?= isset($inquiry['created_at']) ? date('d M, Y', strtotime($inquiry['inquiry_date'])) : 'N/A' ?>
```

The guard **checks `created_at`** but the value **reads `inquiry_date`**.
`created_at` is always populated, so the guard passes even when `inquiry_date` is
null; `strtotime(null)` returns `false` and `date()` renders the Unix epoch instead
of the intended `'N/A'`.

### Trigger data

6 of 470 rows in `buyer_inquiries` have `inquiry_date IS NULL` — ids 2, 3, 4, 5, 6,
7. All 6 have a valid `created_at`, so the correct date is available.

### Where it is visible

| Page | Result |
|---|---|
| `/buyer/search` | **6 × "01 Jan, 1970"** — confirmed |
| `/buyer` pages 1–3 | clean, but only by luck |
| `/buyer-inquiry/x/2` (detail) | correct — "25 Jan, 2026" |

`/buyer` hides it because `Buyer::index` (`app/Controllers/Buyer.php:45`) orders by
`inquiry_date DESC`, and MySQL sorts nulls **last** under `DESC` — so the 6 rows are
pushed to the final page rather than handled.

### All three render sites, compared

| Site | Pattern | Verdict |
|---|---|---|
| `pages/buyer-main.php:118` | guards `created_at`, reads `inquiry_date` | **broken** |
| `pages/buyer-detail.php:56` | guards and reads `inquiry_date`, falls back to `created_at` | **correct** — this is the intended pattern |
| `pages/index.php:146` | no guard at all | **latent** — see #13 |

`buyer-detail.php:56` already does it right: `isset()` returns false for null, so it
falls through to `created_at`. Line 118 looks like a copy of it with the guard key
left wrong.

### Fix

```php
<?= !empty($inquiry['inquiry_date'] ?? $inquiry['created_at'])
      ? date('d M, Y', strtotime($inquiry['inquiry_date'] ?? $inquiry['created_at']))
      : 'N/A' ?>
```

Also emits `[DEPRECATED] strtotime(): Passing null...` on PHP 8.1 — a fatal error in
PHP 9, see #4. This is production data, so production shows the same thing.

A migration backfilling `inquiry_date` from `created_at` where null would fix the
root data issue across all three sites. `Contact.php:139` already sets the field on
new inserts; the 6 nulls are legacy rows.

---

## #13 — Homepage renders `inquiry_date` with no null guard
**Severity:** LOW (latent) · **Raised:** 2026-07-29 · **Open** · **Task:** T-6

`app/Views/pages/index.php:146`:

```php
<?= date('M d, Y', strtotime($inq['inquiry_date'])) ?>
```

No guard whatsoever. Not currently broken — and safe only by accident.

The homepage query (`Pages.php:107` chaining `orderBy('inquiry_date','DESC')` into
`BuyerInquiryModel::getActiveInquiries(8)`) sorts nulls last, so the 6 null rows
never reach the top 8. Verified: all 8 rows the homepage receives have dates.

Any of these exposes the epoch on the **homepage**: changing the sort column or
direction, raising the limit, adding a filter, or a new inquiry being created with a
null `inquiry_date`. Fix alongside #10.

---

## #11 — Contact form accepts submissions with no email address
**Severity:** LOW · **Raised:** 2026-07-29 · **Open**

`POST /contact/submit` with only `name=x` was accepted and stored, producing a row
with an empty `email` and empty `message`. Server-side validation either is not
applied or does not require those fields.

Leaves the table open to junk rows and makes submissions unactionable — there is no
way to reply. Worth checking the other 40 POST endpoints for the same gap.

---

## #12 — `suppliers` table and `SupplierModel` are dead code
**Severity:** LOW · **Raised:** 2026-07-29 · **Open**

`SupplierModel` is referenced by no controller, and the `suppliers` table (10 rows)
is not the source of supplier data — `Supplier::profile` and the rest read from
`users` where `user_type='supplier' AND status='approved'` (153 approved rows).

The two have already drifted: `techelectro-china` exists in `suppliers` but has no
`users` row, so that slug 404s. The other 9 slugs happen to exist in both.

Not a live bug — nothing reads the stale table — but it is a trap for anyone who
assumes `suppliers` is authoritative. Either drop the table and model, or document
it as legacy. See PROJECT.md.
