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

## 2026-07-31 — Close the duplicate `/public/*` URL surface

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

## 2026-07-31 — Manual SEO/branding changes (owner, outside this session)

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

## 2026-07-31 — Dynamic per-inquiry meta descriptions

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

## 2026-07-31 — Slug work deployed to production

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

## 2026-07-30 — Inquiry URLs migrated to real slugs (BLOCKERS #14 closed)

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
