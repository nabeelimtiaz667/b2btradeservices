# BLOCKERS.md — open problems and risks

Things that are stuck, waiting on someone, or knowingly unresolved. Remove an entry
only when it is genuinely fixed — record the fix in [CHANGELOG.md](CHANGELOG.md).

Severity: `CRITICAL` · `HIGH` · `MEDIUM` · `LOW`

Resolved and removed: #1 (Aiven credential — rotated), #2 (`_database.php` —
deleted), #3 (`app.zip` — deleted), #5 (app unverified — swept 2026-07-29).

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

## #14 — Inquiry URL slug is decorative; any slug serves any inquiry
**Severity:** MEDIUM · **Raised:** 2026-07-29 · **Open** · **Task:** T-13

### Root cause — one line

`app/Config/Routes.php:58`:

```php
$routes->get('buyer-inquiry/(:any)/(:num)', 'Buyer::detail/$2');
```

The route captures two groups but forwards only **`$2`** (the numeric id). `$1` —
the slug — is **discarded**. It is never passed to the controller, so it is never
compared against the record.

`Buyer::detail($id = null)` (`app/Controllers/Buyer.php:86`) accordingly takes a
single `$id` and does `find($id)`. It has no slug parameter and could not validate
one even if the route sent it.

### Reproduction

Same slug, four different inquiries — the slug is ignored entirely:

| URL | Renders |
|---|---|
| `buyer-inquiry/bulk-rice-import-requirement/1` | Looking for Steel Plate Suppliers |
| `buyer-inquiry/bulk-rice-import-requirement/2` | Cotton Fabric for Garment Factory |
| `buyer-inquiry/bulk-rice-import-requirement/3` | Bulk Rice Import Requirement ← the only correct one |
| `buyer-inquiry/bulk-rice-import-requirement/4` | LED Lighting Products Needed |

### It is worse than arbitrary — `(:any)` matches slashes

`(:any)` compiles to `(.*)`, which crosses `/`. So the slug can be any number of
segments of any content:

| URL | Renders |
|---|---|
| `buyer-inquiry/literally-anything/3` | Bulk Rice Import Requirement |
| `buyer-inquiry/a/b/c/3` | Bulk Rice Import Requirement |
| `buyer-inquiry/x/3` | Bulk Rice Import Requirement |

### Impact

**SEO — the main cost.** Every inquiry is reachable at unlimited distinct URLs, and
there is **no `<link rel="canonical">`** anywhere in `app/Views/` (verified). Search
engines see unbounded duplicate content for the same record, splitting ranking
signals. Anyone can mint a URL that renders your content under any slug they choose
— e.g. a competitor's or an offensive phrase — and have it indexed.

Not a data-leak: `Buyer::detail` still enforces `status === 'active'`, so no
unpublished inquiry is exposed. This is a correctness and SEO defect, not an
access-control one.

### Why the obvious fix does not work

There is **no `slug` column** on `buyer_inquiries` (verified — 25 columns, none is
a slug). Slugs are derived from `title` at link-generation time by
`Buyer::inquirySlug()` (`app/Controllers/Buyer.php:68`):

```php
return url_title(strtolower($title), '-', true);
```

…and re-derived inline in six views (`buyer-main.php:106,128`, `index.php:131`,
`buyer-detail.php:111`, `search-results.php:74`,
`dashboard/admin/inquiries.php:93`). So "look the record up by slug" has nothing to
look up against.

### Two fix options

**A — validate and 301 to the canonical slug** (smaller change, keeps ids in URLs).
Pass both groups, then redirect if the slug does not match:

```php
// Routes.php
$routes->get('buyer-inquiry/([^/]+)/(:num)', 'Buyer::detail/$1/$2');

// Buyer::detail($slug = null, $id = null), after loading $inquiry:
$canonical = $this->inquirySlug($inquiry['title']);
if ($slug !== $canonical) {
    return redirect()->to(base_url("buyer-inquiry/{$canonical}/{$id}"), 301);
}
```

Note `([^/]+)` rather than `(:any)` — that alone kills the multi-segment variants.
`legacyRedirect` (`Buyer.php:73`) already builds exactly this canonical URL, so the
pattern is established.

**B — add a real `slug` column**, unique-indexed, backfilled from `title`, and look
up by slug alone. Cleaner URLs and a genuine lookup key, but a migration plus
updates to all six view call sites, and needs a plan for title edits and collisions.

Either way, add a `<link rel="canonical">` to the layout — that is worth doing
independently, since no view emits one today.

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
