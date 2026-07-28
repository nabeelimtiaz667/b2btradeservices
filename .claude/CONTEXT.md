# CONTEXT.md — current environment state

Describes what **is** true right now, not what happened (that's CHANGELOG.md).
Update this file whenever the environment changes.

Last verified: 2026-07-29 — both site and blog returning HTTP 200.

## Status

| | |
|---|---|
| Local environment | **Working** |
| App | `http://localhost/b2btradeservices/` → 200 |
| Blog | `http://localhost/b2btradeservices/blog/` → 200 |
| Composer deps | Installed (`vendor/` present) |
| Git | 1 commit (`df28e09`) on `main`, pushed to GitHub |

## Machine setup

- **OS:** Windows 10 Home (19045)
- **XAMPP:** Apache on port 80, MySQL on 3306, both must be running
- **Project path:** `C:\xampp\htdocs\b2btradeservices`
- **Served from a subfolder**, not a vhost — hence the subfolder-aware rewrite
  rules and `baseURL`. Anything assuming the app sits at a document root will
  break here.

### PHP

`C:\xampp\php\php.ini` — `extension=intl` was commented out and is now enabled
(CodeIgniter requires it). Confirmed loaded in CLI.

Enabled and relevant: `intl`, `mbstring`, `mysqli`, `curl`, `gd`, `fileinfo`,
`exif`, `openssl`, `zip`, `dom`, `libxml`, `json`.

> Changing `php.ini` requires an **Apache restart** to affect the web SAPI.
> `php -m` on the CLI can show an extension that Apache hasn't picked up yet.

## Configuration

### Main app

- **`.env`** (gitignored) — `CI_ENVIRONMENT = development`,
  `app.baseURL = 'http://localhost/b2btradeservices/'`, plus `database.default.*`
  pointing at local MySQL (`localhost`, `root`, empty password, port 3306).
- **`app/Config/Database.php`** — local values baked in as a fallback for when
  `.env` is absent: `localhost` / `root` / empty / `b2btradeservices`, port 3306,
  `encrypt => false`.
- **`app/Config/App.php`** — the constructor resolves `baseURL` in priority order:
  `.env` → `REPLIT_DEV_DOMAIN` → `HTTP_HOST` → hardcoded fallback. `$baseURL` has
  no default value, so CodeIgniter's own `.env` binding cannot populate it — the
  constructor has to read it explicitly. See DECISIONS #3.

### WordPress

`blog/wp-config.php` (gitignored) — points at local MySQL as `root` with an empty
password; `WP_HOME` and `WP_SITEURL` are hardcoded to the localhost subfolder URL
so the production URLs stored in `bk1o_options` are overridden without editing the
database. `WP_DEBUG` is on.

`blog/.htaccess` has `RewriteBase /b2btradeservices/blog/`. This is
localhost-specific and would be wrong in production — but `blog/` is gitignored,
so it never leaves this machine.

## Commands

Start Apache and MySQL from the XAMPP Control Panel.

Install/refresh PHP dependencies:

```bash
composer install
```

Check the site responds:

```bash
curl -s -o /dev/null -w "%{http_code}\n" -L http://localhost/b2btradeservices/
```

Open a MySQL shell on the main database:

```bash
/c/xampp/mysql/bin/mysql.exe -u root b2btradeservices
```

Tail the application log (filename is date-stamped):

```bash
ls -t writable/logs/ | head -3
```

Run a seeder (writes to the **local** database — see DECISIONS #8):

```bash
php seed_data.php
```

## Gotchas

1. **Subfolder install.** Rewrite rules and `baseURL` are subfolder-aware. A rule
   anchored at `/` rather than `/b2btradeservices/` will silently misbehave.
2. **`blog/` must stay excluded from the root rewrite.** If `/.htaccess` loses its
   `RewriteRule ^(public|blog|cgi-bin)(/|$) - [L]` line, WordPress 404s.
3. **`app/Config/_database.php` still exists on disk** and contains live production
   credentials. CodeIgniter never loads it (leading underscore). Gitignored.
   It should be deleted — see BLOCKERS #2.
4. **`.env` holds the production encryption key**, carried over from the import.
   Gitignored. Anything encrypted in production stays decryptable locally, which
   is convenient but means the key is now on a dev machine.
5. **The database is a writable scratch copy** — see "Local database" below. Free
   to modify; the rows are still real people's data, so dumps stay out of git and
   SMTP stays unconfigured.
6. **Two seeders exist** (`seed_data.php`, `seed_products.php`). They are standalone
   scripts, not CodeIgniter seeders, and they **insert** rather than upsert —
   running them twice duplicates data.
7. **PHP 8.1.5 is old** and past end of security support. Matching production, so
   not changed unilaterally — see BLOCKERS #4.
8. **The app runs in UTC** (`appTimezone = 'UTC'`) while Windows local time is
   ahead. Log filenames are UTC-dated, so `writable/logs/log-$(date +%Y-%m-%d).log`
   can point at a file that does not exist yet. Check `ls -t writable/logs/` instead.
9. **`mysql.exe` outputs CRLF.** A trailing `\r` silently corrupts any URL or path
   built from query output — curl fails instantly with code `000`. Pipe through
   `tr -d '\r'`.
10. **`curl` inside a `while read` loop consumes stdin** and kills the loop. Add
    `</dev/null` to the curl call.
11. **Piping `php spark` output through Git Bash** logs `CRITICAL fwrite ...
    errno=22` from `system/CLI/InputOutput.php:78`. A Windows pipe artifact, not an
    application fault — ignore it when triaging logs.
12. **Log threshold is 9 in development** (`Logger.php:42`), so everything down to
    DEBUG is written. An empty log genuinely means no errors.

## Local database

**The local database is decoupled from production and safe to modify.** It was
imported from the live site and has no connection back to it. Insert, update,
truncate, re-seed, run migrations, test POST endpoints — none of it reaches
production.

### Restore path

Owner-supplied dumps live in `sql/` (gitignored via `*.sql`, and not reachable over
HTTP — the root rewrite folds `/sql/*` into `public/`, which does not exist):

| File | Target database | Tables |
|---|---|---|
| `sql/defaultdb.sql` | `b2btradeservices` | 11 |
| `sql/i10853125_icx81.sql` | `i10853125_icx81` (WordPress) | 13 |

Two properties of these dumps, both verified:

- **No `USE` or `CREATE DATABASE`** — they restore into whatever database you name
  on the command line. `defaultdb` is just the original Aiven name; it does not
  redirect the restore.
- **No `DROP TABLE`, and plain `CREATE TABLE` / `INSERT INTO`** — so piping one
  into a populated database **fails on the first table** with
  `ERROR 1050 ... Table 'buyer_inquiries' already exists`, and stops. You must drop
  and recreate first.

Restore the main database (drop + recreate is required):

```bash
/c/xampp/mysql/bin/mysql.exe -u root -e "DROP DATABASE IF EXISTS b2btradeservices; CREATE DATABASE b2btradeservices CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;" && /c/xampp/mysql/bin/mysql.exe -u root b2btradeservices < sql/defaultdb.sql
```

Restore WordPress:

```bash
/c/xampp/mysql/bin/mysql.exe -u root -e "DROP DATABASE IF EXISTS i10853125_icx81; CREATE DATABASE i10853125_icx81 CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;" && /c/xampp/mysql/bin/mysql.exe -u root i10853125_icx81 < sql/i10853125_icx81.sql
```

Both databases are `utf8mb4` / `utf8mb4_general_ci`, matching the dumps' own
`DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci` — keep those flags on the
recreate or collation will silently drift.

Take a snapshot before a risky change:

```bash
/c/xampp/mysql/bin/mysqldump.exe -u root --single-transaction b2btradeservices > /c/xampp/db_backups/snap_$(date +%Y%m%d_%H%M).sql
```

Keep snapshots in `C:\xampp\db_backups\` — **outside** the web root, so they are
never HTTP-reachable. This is the mistake `app.zip` made (old BLOCKERS #3).

### What is still off-limits

The data is decoupled, but it is still real customer records — 641 users with real
email addresses.

1. **Never commit or share a dump.** `*.sql` is gitignored; verify with
   `git check-ignore` if you add one anywhere new.
2. **Never configure a working SMTP server.** `app/Helpers/email_helper.php`
   sends via `sendWelcomeEmail()` (`Auth.php:118`, registration) and
   `sendPasswordResetEmail()` (`Auth.php:229`, forgot-password) to the address on
   the row. `app/Config/Email.php` uses `protocol = 'mail'`, and `php.ini` points at
   `SMTP=localhost:25` with nothing listening — so mail silently fails today. That
   failure is the only thing preventing real delivery. Route to a local catcher
   (Mailpit/MailHog on `localhost:1025`) before testing any mail path.

## Verification status

Swept 2026-07-29 (CHANGELOG). All 168 files under `app/` lint clean; 136 route
handlers and 67 view targets resolve; 97 GET routes exercised over HTTP.

**Not verified:** the 41 POST routes (they write to a database holding real user
data — see T-11), and outbound email. Nine destructive GET routes were deliberately
skipped; do not curl them (BLOCKERS #8).
