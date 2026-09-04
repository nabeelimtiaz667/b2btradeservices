# DECISIONS.md — decision log

Why things are the way they are. **Append-only.** Never edit or delete an entry —
if a decision is reversed, add a new one and mark the old `Superseded by #N`.

Format: what was decided, why, what else was considered, and the consequence.

---

## #1 — Keep the app in a subfolder rather than creating a vhost
**Date:** 2026-07-29 · **Status:** Active

Serve the app at `http://localhost/b2btradeservices/` instead of setting up an
Apache vhost like `b2b.local`.

**Why:** The owner explicitly wanted this URL, and it avoids touching XAMPP's
Apache config and the Windows hosts file.

**Alternative:** A vhost with the project root as document root would have matched
production more closely and removed all the subfolder special-casing below.

**Consequence:** Rewrite rules, `baseURL` and `RewriteBase` all have to be
subfolder-aware, and differ from production. Revisit if path bugs pile up.

---

## #2 — Point local config at local MySQL, not the remote database
**Date:** 2026-07-29 · **Status:** Active

`app/Config/Database.php` shipped pointing at a remote MySQL on port 14007 with
`encrypt => true`. Changed to `localhost` / `root` / empty password /
`b2btradeservices`, port 3306, `encrypt => false`.

**Why:** A local dev environment must not be able to read or write production data.
The imported dump is already restored locally.

**Consequence:** The committed config is now dev-safe by default. Deployment must
supply real values via `.env`, which is the CodeIgniter-intended path anyway.

---

## #3 — Make `.env` win over the hardcoded `baseURL` logic
**Date:** 2026-07-29 · **Status:** Active

`app/Config/App.php` had a constructor deriving `baseURL` from `REPLIT_DEV_DOMAIN`,
then `HTTP_HOST`, then a hardcoded fallback. Added an `env('app.baseURL')` check
ahead of all of them.

**Why:** Two problems. The constructor runs *after* `parent::__construct()`, so it
overwrote anything `.env` supplied. And `public string $baseURL;` has no default
value — uninitialised typed properties are skipped by CodeIgniter's `.env` binding,
so `app.baseURL` was being ignored entirely. Neither remaining branch produces a
subfolder URL: `HTTP_HOST` yields `http://localhost/`, dropping
`/b2btradeservices/`.

**Alternative:** Hardcode the subfolder into the constructor. Rejected — it would
have to be undone before deploying.

**Consequence:** `.env` is now authoritative, matching standard CodeIgniter
behaviour. Production is unaffected, since its `.env` already sets `app.baseURL`.
The Replit and `HTTP_HOST` branches are retained as fallbacks.

---

## #4 — Exclude `blog/` from the root rewrite instead of rewriting per-environment
**Date:** 2026-07-29 · **Status:** Active

Root `.htaccess` folded *every* request into `public/`, including `/blog/*`, which
broke WordPress. Added `RewriteRule ^(public|blog|cgi-bin)(/|$) - [L]` above the
catch-all.

**Why:** The pattern is directory-relative, so the same rule works whether the app
is served from a document root or a subfolder. The original
`RewriteCond %{REQUEST_URI} !^/public/` was absolute and could never match under a
subfolder — it also re-rewrote `public/` into `public/public/`.

**Consequence:** One rule works in both environments; safe to commit and deploy.

---

## #5 — Override WordPress URLs in `wp-config.php`, not in the database
**Date:** 2026-07-29 · **Status:** Active

`bk1o_options` holds production URLs (`https://b2btradeservices.com/blog`). Rather
than rewriting them, `WP_HOME` and `WP_SITEURL` are defined in `wp-config.php`.

**Why:** Keeps the imported database byte-identical to production, so it can be
re-imported or diffed without local edits leaking in. `wp-config.php` is gitignored
and machine-local.

**Alternative:** A search-replace across the WP database. Rejected — destructive,
and has to be undone to compare against production.

**Consequence:** The database stays clean. Anything reading the URL from the
database directly rather than through WordPress will still see production URLs.

---

## #6 — Point WordPress at MySQL `root` rather than recreating the cPanel user
**Date:** 2026-07-29 · **Status:** Active

`wp-config.php` used cPanel DB user `i10853125_icx81` with a password. Switched to
`root` with an empty password; database name left unchanged.

**Why:** That MySQL user does not exist locally. Creating it would mean putting a
production password into the local MySQL instance for no benefit.

**Consequence:** The old production password no longer appears in any local file.
Database name stays as-is so the import matches production.

---

## #7 — Exclude `blog/` from the repository entirely
**Date:** 2026-07-29 · **Status:** Active · **Decided by:** Nabeel

`blog/` is gitignored in full.

**Why:** Owner's call — all work happens on the main CodeIgniter site, and the
WordPress install will not be edited.

**Consequence:** Repo drops from ~198 MB of WordPress files to ~18 MB pushed.
WordPress core, themes, plugins and uploads are **not** version-controlled or
backed up by this repo — they exist only on this machine and on production. Do not
reverse this without asking.

---

## #8 — Drive the seeders from environment variables, defaulting to local
**Date:** 2026-07-29 · **Status:** Active

`seed_data.php` and `seed_products.php` opened hardcoded SSL connections to the
live Aiven database. Replaced with `getenv()` lookups defaulting to local XAMPP.

**Why:** GitHub push protection blocked the push over the embedded credential. Two
separate problems: a secret in the repo, and dev seeders that wrote straight to
production.

**Alternative:** Gitignore both seeders. Rejected — they are useful project code
and belong in version control; only the credential had to go.

**Consequence:** Running a seeder now writes to the **local** database. Override
with `DB_HOST` / `DB_USER` / `DB_PASS` / `DB_NAME` / `DB_PORT`. The Aiven-specific
SSL setup was dropped, as local MySQL does not use it.

---

## #9 — Amend the initial commit rather than allow the secret through
**Date:** 2026-07-29 · **Status:** Active

The single unpushed commit was amended to remove the credential, then the reflog
was expired and `git gc --prune=now` run to destroy the old commit object.

**Why:** GitHub offered an "allow this secret" URL. Taking it would have published
a live credential and recorded it as knowingly accepted. History rewrite was safe
here: one commit, never pushed, nothing shared.

**Consequence:** Commit `c7358cf` no longer exists; history starts at `df28e09`.
This approach only works because nothing had been pushed — it would not be safe on
a shared branch.

---

## #10 — Gitignore uploads, keep the CodeIgniter framework source
**Date:** 2026-07-29 · **Status:** Active

`public/uploads/` (~54 MB), `public/assets/images/` (~20 MB), and
`blog/wp-content/uploads/` (~41 MB) are excluded. `system/` (CodeIgniter
framework, ~3.4 MB) is committed. `public/assets/css/` and `public/assets/js/`
are **not** excluded — only `assets/images/` is, not all of `assets/`.

**Why:** Uploads are user-generated content, replaceable from production, and the
bulk of the repo size. `assets/images/` was folded into the same ignore block for
the same reason — static image assets, also large, also not something a diff-based
workflow needs to track byte-for-byte. The framework source was already vendored
in the import and committing it keeps the checkout self-contained.

**Consequence:** A fresh clone has no product, supplier, or flag images and will
show broken media until they are copied across. Reverse by deleting the uploads
block in `.gitignore`.

**Consequence discovered in production, 2026-08-23 (see BLOCKERS #25):** any
change under `public/assets/images/` — new files, renames, edits — is invisible
to `git status`/`git diff`, so a deploy process driven off git output silently
skips it. This bit a real deploy: 4 flag SVG files fixed on 2026-08-21
(CHANGELOG) never made it to production because they weren't recognized as
"changed" by any git-based check, and the missing flags went unnoticed until
the owner traced it back by hand.

---

## #11 — Put the index at the repo root, detail docs in `.claude/`
**Date:** 2026-07-29 · **Status:** Active

`CLAUDE.md` sits at the project root; `PROJECT.md`, `CONTEXT.md`, `TASKS.md`,
`DECISIONS.md`, `BLOCKERS.md` and `CHANGELOG.md` sit in `.claude/`.

**Why:** Claude Code auto-loads `CLAUDE.md` from the project root. A pointer file
buried in `.claude/` would not be read automatically, defeating its purpose.

**Consequence:** Root stays uncluttered — one small index file, everything else in
`.claude/`. These docs are committed and pushed, so they must never contain
credentials.

---

## #13 — Inquiry slugs are frozen at creation, never regenerated
**Date:** 2026-08-01 · **Status:** Active · **Decided by:** Nabeel

`BuyerInquiryModel` generates `slug` on `$beforeInsert` only. There is deliberately
**no `$beforeUpdate` entry**, so editing a title never moves the slug.

**Why:** the canonical URL no longer carries an id, so regenerating a slug would
break every indexed URL and inbound link for that row with nothing to fall back on
— an admin fixing a typo would silently 404 a ranking page. Matches `UserModel`,
which does the same for the same reason.

**Alternatives:** regenerate on title change (rejected — that is precisely the SEO
damage this work exists to prevent); regenerate plus a slug-history table with 301s
(rejected as disproportionate at 470 rows on a site with no sitemap, no RSS and no
structured data).

**Consequence:** a slug can drift from its title. Cosmetic — Google reads `<h1>` and
`<title>`, not the slug. `-2` suffixes can also outlive the collision that caused
them; **do not renumber them later**, that would break URLs for no gain.

Note this is strictly better than the old behaviour, not a compromise: slugs used to
be recomputed at render time, so editing a title already changed every internal link
silently, with nothing redirecting the old URL.

**Escape hatch if drift ever matters:** add a "Regenerate slug" button to the admin
edit form calling `uniqueSlug($base, $id)`, and introduce the history table *then* —
deliberate, admin-triggered, rare. Do not quietly add `generateSlug` to
`$beforeUpdate`.

---

## #14 — Widen the inquiry status ENUM rather than remap `'inactive'`
**Date:** 2026-08-01 · **Status:** Active

`buyer_inquiries.status` gained `inactive`, giving
`enum('active','inactive','closed','pending','expired')`. Separately,
`Dashboard.php:686` changed from `'inactive'` to `'pending'`.

**Why:** two code paths wrote `'inactive'` to an ENUM that lacked it, and with
non-STRICT `sql_mode` MariaDB silently coerced them to `''` — which fails every
`status === 'active'` check, so the row vanished from all listings and 404'd. But
the two paths *mean different things*, so they needed different fixes:

- Admin listings dropdown = "an admin deliberately hid this" → genuinely `inactive`,
  so widen the ENUM. `products.status` already had `inactive`, so this aligns them.
- `Dashboard::buyerAddInquiry` = "awaiting approval" → that is `pending`, which was
  already valid. The product path at `Dashboard.php:352` had been writing `'pending'`
  for the identical condition all along, so this was an inconsistency, not a choice.

**Consequence:** `pending` and `inactive` now mean distinct things and both are
storable. Verified safe: no `switch`/`match` on inquiry status exists, all 22 read
sites are equality tests, and the one two-branch display has a generic `else` that
renders `ucfirst($status)`.

---

## #12 — Treat the local database as a writable scratch copy
**Date:** 2026-07-29 · **Status:** Active · **Decided by:** Nabeel

The imported local database is decoupled from production and may be freely
modified for development and testing — inserts, updates, truncation, re-seeding,
migrations, POST-endpoint testing.

**Why:** Owner's call. The data was imported once and has no write path back to
production, so the earlier caution (which had me avoid writing to it during the
verification sweep, and skip all 41 POST routes) no longer applies.

**Supersedes:** the read-only posture taken during the 2026-07-29 verification
sweep, where a single test row was inserted and immediately deleted. That level of
care is no longer required.

**Consequence:** T-11 (POST-route testing) is unblocked. Restore path is `sql/`,
supplied by the owner; snapshots go in `C:\xampp\db_backups\`, outside the web root.

**What this decision does *not* cover** — the rows are still real customer records,
so two constraints are unchanged and independent of where the data lives:

- Dumps stay out of git and are never shared. `*.sql` remains gitignored.
- SMTP stays unconfigured. `Auth.php` mails real addresses from the user rows;
  delivery currently fails only because nothing listens on port 25. Use a local
  mail catcher if a mail path needs testing.

---

## #15 — Country data moved from the `countries` DB table to a file, sourced from a free dataset instead of manual entry
**Date:** 2026-09-03 · **Status:** Active · **Decided by:** Nabeel

`CountryModel` no longer queries the `countries` table. It reads `app/Data/countries.php`,
a plain PHP array file (250 countries/territories) regenerated by
`php spark countries:sync` from the `mledoze/countries` dataset on GitHub
(free, keyless, no rate limit) plus `flagcdn.com` for flag URLs (also free, keyless).

**Why:** the table was manually seeded with only ~122 countries, so most of the
world couldn't register or be searched by country. Investigation before this
change confirmed the codebase was a strong fit for a file-backed source: no DB
foreign-key constraint anywhere references `countries.id` (just a plain indexed
int on `users`/`buyer_inquiries`/`contact_submissions`), only one SQL `JOIN`
anywhere touched the table (`Dashboard::suppliers()`'s `sort=country_name`,
replaced with a PHP-side sort + `Pager::store()` — see that method's comment),
and there was never an admin UI for managing countries.

**Alternative considered and rejected:** the original plan was to sync from
`restcountries.com` (the well-known free country API). That API's free tier
was discontinued between planning and implementation — it now requires
creating an account and an API key, which the "never create accounts on the
user's behalf" rule rules out doing autonomously. Switched to `mledoze/countries`,
a static JSON snapshot on GitHub with the same field shape, genuinely no
account/key required.

**ID stability:** `countries:sync` reads the live DB table's `code → id`
mapping once before writing the file, so every country already in production
keeps its existing id (no `country_id` values on any table needed to change);
new countries get new ids appended after the current max.

**Consequence:** the DB `countries` table itself was deliberately left in
place, unused, rather than dropped — stopping all reads from it carries zero
migration risk; dropping the table would be a separate, unnecessary risk for
no benefit. `php spark countries:sync` needs to be re-run periodically (no
cron wired up yet) to pick up new/changed countries; the existing data file
doesn't go stale in a way that breaks anything, it just won't reflect a country
that's added or renamed upstream until the next sync.
