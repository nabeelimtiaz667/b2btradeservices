# TASKS.md — work tracker

Active and planned work. Update when starting and finishing items.
Completed items move to the bottom; the full record lives in
[CHANGELOG.md](CHANGELOG.md).

Status key: `TODO` · `IN PROGRESS` · `BLOCKED` · `DONE`

---

## Active

_Nothing in progress._

---

## Up next

| # | Task | Status | Notes |
|---|---|---|---|
| T-5 | Fix epoch date bug in `buyer-main.php:118` | TODO | Guard checks `created_at`, value reads `inquiry_date`. 6 rows render "01 Jan, 1970" on `/buyer/search`. Root cause pinned; fix drafted. See BLOCKERS #10. |
| T-6 | Harden unguarded `inquiry_date` on homepage | TODO | `index.php:146` has no null guard; safe only because nulls sort last. Do together with T-5. See BLOCKERS #13. |
| T-7 | Enable CSRF protection | TODO | Disabled site-wide (`Filters.php:83`). Verified: token-less POST accepted. Needs a pass over all forms first. See BLOCKERS #7. |
| T-8 | Convert 9 destructive GET routes to POST | TODO | Delete/approve/reject reachable by plain link. Compounds T-7. See BLOCKERS #8. |
| T-9 | Apply `AuthFilter` to `dashboard/*` and `admin/*` | TODO | Filter exists but is applied to no route; auth is per-method and opt-in. See BLOCKERS #9. |
| T-10 | Add server-side validation to `contact/submit` | TODO | Accepted a submission with empty email and message. Audit the other 40 POST routes. See BLOCKERS #11. |
| T-11 | Test the 41 POST routes | TODO — **unblocked** | Was blocked on data safety; the local DB is now a writable scratch copy (DECISIONS #12). Snapshot to `C:\xampp\db_backups\` first, restore from `sql/`. Two caveats: CSRF is off so tokens are not needed yet (T-7 changes that), and do not enable SMTP — `Auth.php` registration and password-reset mail real addresses. |
| T-12 | Backfill null `inquiry_date` from `created_at` | TODO | 6 rows (ids 2–7). Fixes the root data issue behind T-5/T-6 across all three render sites. Now doable — the local DB is writable. Write it as a migration so it can be applied to production too. |
| T-18 | Submit new inquiry URLs to Google Search Console | TODO | No sitemap exists, so recrawl is link-driven and slow. The 301s from the old two-segment URLs carry the load for weeks — **do not remove that legacy route** (`Routes.php`, `buyer-inquiry/(:any)/(:num)`). |
| T-19 | Consider a sitemap.xml | TODO | Would make T-18 unnecessary next time and speed recrawl. `public/robots.txt` has no `Sitemap:` directive either. |
| T-16 | Decide on the RFQ Country field | TODO | Labelled required, isn't. See BLOCKERS #15. The 500 it caused is already fixed; the form itself is the open question. |
| T-17 | Make `down()` safe on the three back-filled migrations | TODO | Guarded `up()`, unconditional `down()` — a single `migrate:rollback` drops 5 real columns incl. `users.is_featured` (25 rows set locally). Adding an existence guard does **not** fix it; likely answer is a no-op `down()`. See BLOCKERS #17. |

---

## Backlog

Unscheduled, no commitment yet.

- Confirm outbound email behaviour locally (contact + inquiry forms). XAMPP has no
  mail transport by default, so submissions may fail or silently discard.
- Verify uploads write correctly to `public/uploads/` on Windows.
- Review the 138 routes for any that assume the production domain.
- Consider a `b2b.local` Apache vhost to remove the subfolder special-casing.
- Establish how local schema changes get back to production (migrations vs. manual).

---

## Done

| # | Task | Completed | Detail |
|---|---|---|---|
| T-0.1 | Enable PHP `intl` extension | 2026-07-29 | CHANGELOG 2026-07-29 |
| T-0.2 | Install Composer dependencies | 2026-07-29 | CHANGELOG 2026-07-29 |
| T-0.3 | Point CodeIgniter DB config at local MySQL | 2026-07-29 | CHANGELOG 2026-07-29 |
| T-0.4 | Configure `.env` and `baseURL` for the subfolder | 2026-07-29 | CHANGELOG 2026-07-29 |
| T-0.5 | Fix root `.htaccess` rewrite | 2026-07-29 | CHANGELOG 2026-07-29 |
| T-0.6 | Fix WordPress config and rewrite base | 2026-07-29 | CHANGELOG 2026-07-29 |
| T-0.7 | Verify site and blog respond | 2026-07-29 | Both HTTP 200 |
| T-0.8 | Create `.gitignore` | 2026-07-29 | CHANGELOG 2026-07-29 |
| T-0.9 | Remove hardcoded credentials from seeders, clean git history | 2026-07-29 | CHANGELOG 2026-07-29 |
| T-0.10 | Set up `.claude/` project documentation | 2026-07-29 | This file and its siblings |
| T-1 | Rotate the Aiven database password | 2026-07-29 | Done by owner. BLOCKERS #1 closed |
| T-2 | Delete `app/Config/_database.php` | 2026-07-29 | Done by owner. Verified gone. BLOCKERS #2 closed |
| T-3 | Delete `app.zip` (166 MB) | 2026-07-29 | Done by owner. Verified gone. BLOCKERS #3 closed |
| T-4 | Verify the application under `app/` | 2026-07-29 | Full sweep — 168 files linted, 136 handlers resolved, 97 GET routes exercised. CHANGELOG 2026-07-29 |
| T-13 | Fix decorative slug on `buyer-inquiry` URLs | 2026-07-30 | Shipped to dev. 470/470 slugs, zero URL churn, original bug confirmed fixed. CHANGELOG 2026-07-30 |
| T-14 | Add `<link rel="canonical">` to the layout | 2026-07-30 | Added to both `main.php` and `inner.php`, opt-in via `$canonical` |
| T-15 | Deploy the slug work to production | 2026-07-31 | 20 files + 6 migrations. Backfill verified on production (distinct = total, nulls = 0); slug URLs and id→slug 301s confirmed. CHANGELOG 2026-07-31 |
| T-21 | Wire membership tiers into the "Premium Members only" gate | 2026-07-31 | Was hardcoded to admin-only, no tier ever worked. Now checks live `membership_level` for starter/gold/platinum/vip. Verified all 4 tiers + free-tier negative control + admin regression, dev only. CHANGELOG 2026-07-31 |
| T-20 | Delete the throwaway premium-tier test account | 2026-07-31 | Owner tested and confirmed T-21 works. Deleted user id 664 + 6 dependent `lead_activities` rows. `users` back to baseline 641. |
