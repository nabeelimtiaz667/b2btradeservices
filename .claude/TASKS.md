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
| T-18 | Submit sitemap to Google Search Console | TODO — **now easy** | A sitemap index exists at `/sitemap.xml` (T-27), so this is one URL to submit rather than per-URL work. Original note: No sitemap exists, so recrawl is link-driven and slow. The 301s from the old two-segment URLs carry the load for weeks — **do not remove that legacy route** (`Routes.php`, `buyer-inquiry/(:any)/(:num)`). |
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
| T-13 | Fix decorative slug on `buyer-inquiry` URLs | 2026-08-01 | Shipped to dev. 470/470 slugs, zero URL churn, original bug confirmed fixed. CHANGELOG 2026-08-01 |
| T-14 | Add `<link rel="canonical">` to the layout | 2026-08-01 | Added to both `main.php` and `inner.php`, opt-in via `$canonical` |
| T-15 | Deploy the slug work to production | 2026-08-02 | 20 files + 6 migrations. Backfill verified on production (distinct = total, nulls = 0); slug URLs and id→slug 301s confirmed. CHANGELOG 2026-08-02 |
| T-21 | Wire membership tiers into the "Premium Members only" gate | 2026-08-04 | Was hardcoded to admin-only, no tier ever worked. Now checks live `membership_level` for starter/gold/platinum/vip. Verified all 4 tiers + free-tier negative control + admin regression, dev only. CHANGELOG 2026-08-04 |
| T-22 | Add JSON-LD structured data to inquiry pages | 2026-08-05 | `WebPage`/`Demand` graph, escaped via `json_encode` HEX flags rather than string substitution. Verified on a normal row, the empty-description fallback row, and an adversarial payload. CHANGELOG 2026-08-05 |
| T-23 | Fix stored XSS in the `<title>` tag, all 6 layouts | 2026-08-05 | Unescaped `$title` interpolation, live via inquiry title/product name/company name — all buyer/supplier-submitted. Found while testing T-22, fixed same session. Wrapped in `esc()` in all 6 layouts, matching this page's own `<h1>` which already did it correctly. CHANGELOG 2026-08-05. **Only fixed on dev — needs the same production deploy treatment as the slug work (T-15).** |
| T-24 | Site-wide SEO audit: title/description/canonical/H1 | 2026-08-05 | 30 pages (all of `Views/pages/` except `buyer-detail.php`). Found and fixed a title-casing bug + zero descriptions/canonicals across 16 pages sharing one controller, a double-escape bug the T-23 fix exposed, two layouts missing description/canonical support entirely, and promoted H1 on 17 pages. CHANGELOG 2026-08-05. Homepage H1 (BLOCKERS #19) and `thankyou.php`/`rfq.php` dead code (BLOCKERS #20) deliberately left open. **Dev only — needs production deploy.** |
| T-26 | Heading hierarchy audit: one H1 per page, no skipped levels | 2026-08-06 | 33 rendered page/variant URLs analysed. 15 had problems; 32/33 now clean (homepage H1 = BLOCKERS #19). Discovered `.h1`–`.h6` classes were never defined locally — only Bootstrap — so the class-preservation technique would have silently broken styling (e.g. package price 84px→24px). Swept 39 heading-element CSS selectors to also match `.hN`. CHANGELOG 2026-08-06. **Dev only — needs production deploy.** |
| T-27 | Build XML sitemaps (index + 5 child sitemaps) | 2026-08-07 | Per-content-type sitemaps with the requested priorities, 50k pagination, and a 7-day cache that rebuilds from the live DB (no cron). Verified: valid XML, counts match DB, sampled URLs all 200, refresh proven by inserting a row and observing 470 -> 471 after cache clear. CHANGELOG 2026-08-07. Closes T-19. **Dev only — needs production deploy.** |
| T-28 | Clean, query-string-free URLs for buyer/product/supplier/global search | 2026-08-07 | Keyword + filters became path segments (`/buyer/search/steel/country/us`); old `?q=` URLs 301 to the clean form. Found and fixed a real bug along the way — CI4 re-splits `(:any)` captures on `/` before binding a controller method, silently dropping filters; fixed with variadic params. Verified with real data (not just status codes) and in an actual browser via real form submission. CHANGELOG 2026-08-07. See BLOCKERS #21 for a pre-existing dead filter noticed but not fixed. **Dev only — needs production deploy.** |
| T-25 | Fix triple `<h1>` on supplier profiles with 2+ banner images | 2026-08-05 | Owner-reported on `/supplier/profile/middle-fork-capital`. Heading was inside the banner `foreach` loop; the T-24 audit's source-grep couldn't see that. Fixed by rendering `<h1>` on the first slide only, `<p class="slide-heading">` (same CSS) on the rest. Verified across all 3 affected suppliers + a heuristic sweep of every other page for the same loop pattern — none found. CHANGELOG 2026-08-05. **Dev only — needs production deploy.** |
| T-20 | Delete the throwaway premium-tier test account | 2026-08-04 | Owner tested and confirmed T-21 works. Deleted user id 664 + 6 dependent `lead_activities` rows. `users` back to baseline 641. |
| T-29 | Two-step lead capture: popup CTA → email-verified account creation | 2026-08-14 | Full design in [plans/T-29-lead-capture.md](plans/T-29-lead-capture.md). `leads` table + `LeadModel`, `LeadCapture` controller/routes/verification email/step-2 signup, popup UI + trigger engine, and phase 2 reactivation resend (5-min cooldown) all built and verified end-to-end. One real bug found and fixed along the way (subfolder-relative path matching for the buyer/supplier default radio, see CHANGELOG 2026-08-14). **Dev only — needs production deploy.** |
