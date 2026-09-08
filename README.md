# B2B Trade Services

A B2B trade directory / lead-generation site — suppliers list products, buyers submit
inquiries, and staff work those inquiries as leads through an admin dashboard. Live at
`b2btradeservices.com`. This repository is a CodeIgniter 4 application with a WordPress
blog mounted alongside it.

| | |
|---|---|
| Framework | CodeIgniter 4.6.4 |
| PHP | 8.1+ (see [Known issue](#known-issue-composer-install-and-php-81) below) |
| Database | MySQL/MariaDB |
| Blog | WordPress (`/blog`), separate codebase and database |

This file covers **what to do right after cloning, before the site will run at all.**
For everything else — architecture, environment gotchas, why things are configured the
way they are, open risks — see [`.claude/`](.claude/): start at
[`.claude/PROJECT.md`](.claude/PROJECT.md).

## What you get from `git clone`, and what you don't

A handful of things are deliberately **not** in this repository and won't exist after
cloning:

| Missing after clone | Why | Get it from |
|---|---|---|
| `vendor/` | gitignored — PHP dependencies | `composer install` (see the known issue below first) |
| `.env` | gitignored — local secrets/config | copy `env` → `.env` (step 3) |
| `blog/` (entire WordPress install) | gitignored by design — see [DECISIONS #7](.claude/DECISIONS.md) | the project owner, if you need the blog locally |
| `sql/*.sql` (database dumps) | gitignored — contains real user PII | the project owner |
| `app/Data/countries.php` | gitignored — runtime-generated, not source (see [BLOCKERS #25](.claude/BLOCKERS.md)) | `php spark countries:sync` (step 5) |
| `app/Data/rate_limits.php` | gitignored, same reason | nothing to do — the app treats "missing" the same as "no overrides set" |
| `public/assets/images/`, `public/uploads/` | gitignored — large media (see [DECISIONS #10](.claude/DECISIONS.md)) | the project owner |

If you only have the git repository and nothing else, you can still get the site
*running* (steps 1–6 below), just without real data, the blog, or product/flag images.

## Setup steps

### 1. Requirements

- PHP 8.1+, with the `intl` and `mbstring` extensions enabled (both required by
  CodeIgniter 4) — see the [known composer issue](#known-issue-composer-install-and-php-81)
  before assuming any 8.1.x works cleanly.
- Composer 2.x
- MySQL or MariaDB
- Apache (or another server that can be configured with equivalent rewrite rules —
  this project has only been run under Apache)

### 2. Get the code

```bash
git clone <this repository>
cd b2btradeservices
```

### 3. Install PHP dependencies

```bash
composer install
```

#### Known issue: `composer install` and PHP 8.1

**This currently fails on a truly fresh clone under PHP 8.1.5** — the committed
`composer.lock` has drifted to pin a few packages (including one real runtime
dependency, `laminas/laminas-escaper`) to versions that require PHP 8.2+/8.4. See
[BLOCKERS #27](.claude/BLOCKERS.md) for the full diagnosis. Until that's fixed
properly (a deliberate `composer update` against PHP 8.1, not something to do as a
side effect of following this README):

- **If you're setting up on the same PHP version as an existing working copy of this
  project**, the pragmatic workaround is to copy that machine's already-populated
  `vendor/` directory over rather than running `composer install` fresh.
- **If you're on PHP 8.2 or newer**, `composer install` should work as normal.
- **If neither applies**, you'll need to resolve BLOCKERS #27 first (a full
  `composer update`) before the site will boot at all — `vendor/autoload.php` is
  required by CodeIgniter's front controller.

### 4. Environment file

Copy the example env file and fill it in:

```bash
cp env .env
```

Edit `.env` and set at minimum:

```ini
CI_ENVIRONMENT = development

app.baseURL = 'http://localhost/b2btradeservices/'   # see note below

database.default.hostname = localhost
database.default.database = b2btradeservices
database.default.username = root
database.default.password =
database.default.DBDriver = MySQLi
database.default.port = 3306

encryption.key = <generate one — see below>
```

**`app.baseURL` must match how you're actually serving the site**, trailing slash
included. If the project sits in a subfolder under your document root (as this dev
setup does — `http://localhost/b2btradeservices/`, not a dedicated vhost — see
[DECISIONS #1](.claude/DECISIONS.md)), the subfolder has to be part of the URL. A
vhost pointed straight at `public/` (matching CodeIgniter's own default recommendation
in the [`env`](env) file's comments) would instead use something like
`http://b2btradeservices.local/`. Either works; what matters is that the value here
actually matches your server config, or every generated link on the site will be wrong.

Generate an encryption key rather than leaving it blank:

```bash
php spark key:generate
```

### 5. Database

Create the database:

```bash
mysql -u root -e "CREATE DATABASE b2btradeservices CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;"
```

Then either:

- **Restore a real dump**, if the project owner has provided `sql/defaultdb.sql` (see
  [CONTEXT.md](.claude/CONTEXT.md) for the exact restore command and important notes
  about the dump format — it has no `DROP TABLE`, so it only applies cleanly to an
  empty database), **or**
- **Run migrations against an empty database**, if you don't have a dump:

  ```bash
  php spark migrate
  ```

  This gets you the schema with no data. You'll need to register a user through the
  app itself to get started, and features that expect existing rows (products,
  suppliers, buyer inquiries) will show empty until some exist.

  **Do not run `php spark migrate:rollback`** on this codebase — see
  [BLOCKERS #17](.claude/BLOCKERS.md) for why three of the migrations will drop real
  columns (including data) if you do. Roll back with a database snapshot or targeted
  SQL instead.

Populate the country/phone-code reference data (this file is gitignored — see the
table above — so it won't exist yet):

```bash
php spark countries:sync
```

This fetches from a public dataset on GitHub and writes `app/Data/countries.php`.
Registration, the lead-capture forms, and anywhere else a country dropdown appears
will be empty until this has run once.

### 6. Point your web server at the project

Point Apache's document root at the project root (not `public/`) if you're mirroring
this dev setup's subfolder-under-XAMPP-htdocs style, or at `public/` directly if
you're using a dedicated vhost — see the `app.baseURL` note in step 4, they have to
agree with each other. Either way, `/.htaccess` at the project root routes everything
except `public/`, `blog/` and `cgi-bin/` into `public/index.php`, which is
CodeIgniter's actual front controller.

On Linux (production, or a non-Windows dev box), make sure `writable/` is writable by
the web server user — CodeIgniter writes cache, logs, and session files there and will
error without it.

### 7. Verify it's running

```bash
curl -s -o /dev/null -w "%{http_code}\n" http://localhost/b2btradeservices/
```

Should return `200`. If you restored a real database dump, log in with an existing
admin account; if you ran migrations fresh, register a new account (it'll need
`user_type = 'admin'` set directly in the database to reach the dashboard, since
registration only offers Supplier/Buyer).

## Before you configure anything else

- **Never point this at a working SMTP server** if you restored a real database
  dump — it contains real users' real email addresses (see
  [CONTEXT.md](.claude/CONTEXT.md)'s "Local database" section). Registration and
  password-reset both send mail; leave it unconfigured (or point it at a local
  catcher like Mailpit) unless you specifically intend to send real email.
- **Never commit `.env`, a database dump, or anything under `blog/`.** All three are
  gitignored for a reason — see the table above.
- The WordPress blog, if you set it up locally, needs its own `blog/wp-config.php`
  with local database credentials — not covered here since `blog/` isn't in this
  repository at all (see [DECISIONS #7](.claude/DECISIONS.md)).

## Where to go next

- [`.claude/PROJECT.md`](.claude/PROJECT.md) — architecture, stack, directory map, database schema
- [`.claude/CONTEXT.md`](.claude/CONTEXT.md) — this dev box's exact configuration and known gotchas
- [`.claude/DECISIONS.md`](.claude/DECISIONS.md) — why the config looks the way it does
- [`.claude/BLOCKERS.md`](.claude/BLOCKERS.md) — open risks and known-broken things, including the composer issue above
- [`.claude/CHANGELOG.md`](.claude/CHANGELOG.md) — dated log of every change made to the project
