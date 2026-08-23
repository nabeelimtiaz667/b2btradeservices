# PROJECT.md — what this project is

Stable reference. Update only when the architecture actually changes.
Last verified: 2026-07-29

## Summary

**B2B Trade Services** is a B2B trade directory / lead-generation website — suppliers
list products, buyers submit inquiries, and staff work those inquiries as leads
through an admin dashboard. It is a live commercial site at `b2btradeservices.com`,
hosted on cPanel. This repo is a **local development mirror** of that site, imported
file-by-file with both databases restored into XAMPP.

Two applications live side by side in one document root:

1. A **CodeIgniter 4 application** — the main site (root).
2. A **WordPress installation** — the blog, mounted at `/blog`.

They are independent: separate codebases, separate databases, no shared session or
auth. The only coupling is the shared Apache document root and the rewrite rules
that keep them from colliding.

## Stack

| Component | Version / detail |
|---|---|
| Framework | CodeIgniter 4.6.4 |
| PHP | 8.1.5 (XAMPP, ZTS, VC2019 x64) |
| Database | MySQL/MariaDB via XAMPP, port 3306 |
| Web server | Apache (XAMPP), port 80 |
| Dependency manager | Composer 2.4.3 |
| Blog | WordPress (table prefix `bk1o_`) |
| Production host | cPanel at `b2btradeservices.com` |

Runtime dependencies are deliberately thin — `laminas/laminas-escaper` and
`psr/log` only. Everything else in `composer.json` is dev tooling.

## Directory map

```
b2btradeservices/
├── app/                    CodeIgniter application code
│   ├── Config/             Framework + app config (Database.php, App.php, Routes.php)
│   ├── Controllers/        13 controllers
│   ├── Models/             10 models
│   ├── Views/              admin/ dashboard/ pages/ layouts/ partials/ errors/
│   ├── Database/           Migrations and seeds
│   ├── Filters/ Helpers/ Libraries/ Language/
├── public/                 Web root / front controller
│   ├── index.php           Front controller
│   ├── assets/css/ js/     Tracked in git
│   ├── assets/images/      Product/flag/banner images (~20 MB, gitignored — see DECISIONS #10)
│   └── uploads/            User-uploaded media (~54 MB, gitignored)
├── blog/                   WordPress install (gitignored — see DECISIONS #7)
├── system/                 CodeIgniter framework source (vendored, tracked)
├── writable/               Cache, logs, sessions, debugbar (contents gitignored)
├── vendor/                 Composer packages (gitignored)
├── scripts/                Utility scripts
├── attached_assets/        Reference assets from the original build
├── seed_data.php           Standalone seeder — suppliers, users, leads
├── seed_products.php       Standalone seeder — products
├── db_sql_export.sql       DB dump, contains real PII (gitignored)
├── app.zip                 166 MB import archive (gitignored)
└── .claude/                These docs
```

## Request routing

Apache serves everything from the project root, so two rewrite layers cooperate:

1. **`/.htaccess`** — excludes `public/`, `blog/` and `cgi-bin/` from rewriting,
   then folds every other request into `public/`. The exclusion is what keeps
   WordPress reachable; without it `/blog/*` gets rewritten into
   `public/blog/*` and 404s.
2. **`/public/.htaccess`** — the standard CodeIgniter front-controller rewrite.
3. **`/blog/.htaccess`** — standard WordPress rewrite, with `RewriteBase` pointing
   at the subfolder.

## CodeIgniter application

**Controllers (13):** `AdminImport`, `AdminSettings`, `Auth`, `BaseController`,
`Buyer`, `Contact`, `Dashboard`, `Home`, `LeadManagement`, `Pages`, `Product`,
`Search`, `Supplier`

**Models (10):** `BuyerInquiryModel`, `CategoryModel`, `ContactSubmissionModel`,
`CountryModel`, `LeadActivityModel`, `LeadNoteModel`, `ProductModel`,
`SiteSettingModel`, `SupplierModel`, `UserModel`

**Routes:** ~138 defined in `app/Config/Routes.php`, all explicit — auto-routing is
not relied upon. Broad groups:

- Marketing/static pages via `Pages::index/<slug>` (about, contact, policies,
  premium service tiers, partner pages)
- Product and supplier browsing, plus search
- Buyer inquiry submission
- Authenticated dashboard: contact submissions, lead management, admin settings,
  data import

## Databases

### `b2btradeservices` — main application

11 tables. Exact row counts as of 2026-07-29:

| Table | Rows | Notes |
|---|---:|---|
| `users` | 641 | **Real production user data — PII** |
| `lead_activities` | 542 | Lead activity trail |
| `buyer_inquiries` | 470 | Inbound buyer inquiries |
| `products` | 124 | |
| `countries` | 122 | Reference data |
| `lead_notes` | 109 | |
| `contact_submissions` | 61 | |
| `site_settings` | 27 | Key/value site config, editable via admin |
| `categories` | 22 | Product taxonomy |
| `suppliers` | 10 | **Legacy / unused — do not trust.** See below |
| `migrations` | 10 | CodeIgniter migration ledger |

> **Suppliers do not live in the `suppliers` table.** `SupplierModel` is referenced
> by no controller, and every supplier feature reads from `users` filtered by
> `user_type = 'supplier' AND status = 'approved'` — 153 approved rows, versus 10
> stale rows in `suppliers`. The two have already drifted: `techelectro-china`
> exists in `suppliers` with no matching `users` row, so that slug 404s. Treat
> `users` as authoritative. Tracked as BLOCKERS #12.

## Authentication model

There is an `AuthFilter` (`app/Filters/AuthFilter.php`), aliased as `'auth'` in
`Filters.php` — but **it is applied to no route**. Every route's only before-filter
is `sitesettings`, plus `role:admin` on the 6 `admin/import/*` routes.

Auth is instead checked method-by-method inside the controllers, against
`session()`. This works today — all 45 dashboard/admin routes verified redirecting
to `/login` when unauthenticated — but it is opt-in, so a new controller method
that omits the check is public by default. Tracked as BLOCKERS #9.

CSRF protection is currently **disabled** (BLOCKERS #7), and 9 destructive actions
are exposed as GET routes (BLOCKERS #8). Read both before touching forms or routes.

### `i10853125_icx81` — WordPress

13 tables, prefix `bk1o_`. The database name is the original cPanel-generated name,
kept as-is so the import matches production.

## Related

- Environment specifics and how to run things → [CONTEXT.md](CONTEXT.md)
- Why the config looks the way it does → [DECISIONS.md](DECISIONS.md)
