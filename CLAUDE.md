# CLAUDE.md — start here

Index for the B2B Trade Services project. Claude Code loads this file automatically
at the start of every session. The detailed docs live in `.claude/`.

## Read these

| File | What it holds | Read it when |
|---|---|---|
| [.claude/PROJECT.md](.claude/PROJECT.md) | What this project is: stack, architecture, URLs, database schema, directory map. Slow-changing. | Starting any task. Always read this first. |
| [.claude/CONTEXT.md](.claude/CONTEXT.md) | Current local environment state, how to run/verify things, environment gotchas. Fast-changing. | Before touching config, the server, or the database. |
| [.claude/TASKS.md](.claude/TASKS.md) | Active, upcoming and completed work items. | Picking up work, or finishing a work item. |
| [.claude/DECISIONS.md](.claude/DECISIONS.md) | Why things are the way they are. Numbered, append-only. | Before changing config or reversing an earlier choice. |
| [.claude/BLOCKERS.md](.claude/BLOCKERS.md) | Open problems, risks and things waiting on Nabeel. | Starting a session; when something is stuck. |
| [.claude/CHANGELOG.md](.claude/CHANGELOG.md) | Dated log of every change made to the project. Append-only. | After making any change. |

## Maintenance rules

Keep these files current — that is the whole point of them.

- **After any change to code, config or the database** → append an entry to
  `CHANGELOG.md`. Never rewrite past entries.
- **When a choice was non-obvious or has a real alternative** → add a numbered
  entry to `DECISIONS.md`. Never edit or delete an old decision; supersede it
  with a new one and mark the old as `Superseded by #N`.
- **When blocked, or when spotting a risk that isn't being fixed now** → add it
  to `BLOCKERS.md`. Remove it only once genuinely resolved.
- **When the environment changes** (PHP ext, ports, DB, vhost) → update
  `CONTEXT.md`, which describes what *is*, not what happened.
- **When starting/finishing work** → update `TASKS.md`.
- `PROJECT.md` changes only when the architecture does.

## Hard rules for this repo

1. **Never commit secrets.** `.env`, `app/Config/_database.php` and
   `blog/wp-config.php` are gitignored and must stay that way. Do not paste
   credentials into any file under `.claude/` — these docs are committed and
   pushed to GitHub.
2. **GitHub push protection is active.** It has already blocked one push. Scan
   for secrets before committing, not after.
3. **`blog/` is gitignored by owner's decision** (see DECISIONS.md #7). WordPress
   changes are not version-controlled. Do not "fix" this without being asked.
4. **The local database is a writable scratch copy — use it.** It was imported from
   production and is now fully decoupled: inserting, updating, truncating and
   re-seeding are all fine, and none of it reaches production. Restore from `sql/`
   (see CONTEXT.md). Never point local config at the production database.
5. **But the rows are still real people's data.** 641 real users with real email
   addresses. Two things survive the decoupling:
   - Never commit or share a dump. `*.sql` is gitignored — keep it that way.
   - **Never configure a working SMTP server.** `Auth.php` sends welcome and
     password-reset mail to whatever address is in the row. Mail currently fails
     because nothing listens on port 25 — that is the only thing preventing
     delivery to real users. If you need mail, point it at a local catcher.
6. **Never run `php spark migrate:rollback` here.** Three migrations have a guarded
   `up()` but an unconditional `down()`, and CI4 rolls back a whole batch — so one
   rollback drops five real columns including `users.is_featured`. Roll back by
   restoring a dump, or with targeted SQL. See BLOCKERS #17.
7. **Verify before claiming.** Check the actual file or HTTP response rather than
   relying on what an earlier session said it did.

## Quick facts

- Stack: CodeIgniter 4.6.4 · PHP 8.1.5 · MySQL/MariaDB · XAMPP on Windows
- App URL: `http://localhost/b2btradeservices/`
- Blog URL: `http://localhost/b2btradeservices/blog/`
- Main DB: `b2btradeservices` · WordPress DB: `i10853125_icx81` (prefix `bk1o_`)
- Remote: `github.com/nabeelimtiaz667/b2btradeservices` (branch `main`)
