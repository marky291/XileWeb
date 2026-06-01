# Replace Laravel Forge with Envoy + Ops Runbook

**Date:** 2026-06-01
**Status:** Approved

## Motivation

Remove the dependency on Laravel Forge for: cost (subscription), control/lock-in
(deployment config should live in the repo, not Forge's UI), and simplicity. The
application stays on the **same, already-provisioned server**
(`root@100.118.64.49`, Ubuntu 22.04, PHP 8.4-FPM, MySQL, nginx, Horizon via
supervisor). This is not a host migration — it is taking ownership of the pieces
Forge currently manages so the Forge subscription can be cancelled safely.

## Current State (verified on prod)

- **Deployment:** Forge "quick deploy" webhook runs a script on push:
  `git pull` → `composer install --no-dev --prefer-dist --optimize-autoloader`
  → flock-guarded `sudo service php8.4-fpm reload` → `artisan migrate --force`
  → `artisan horizon:terminate` → `artisan optimize` → `npm install`.
- **SSL renewal — the critical lock-in:** certs renew by calling Forge's
  microservice (`https://forge-certificates.laravel.com/le/...`) from a cron in
  `/home/forge/.letsencrypt-renew/`. **No `certbot` or `acme.sh` is installed.**
  Cancelling Forge stops renewal; HTTPS dies within ~90 days.
- **Daemons:** Horizon runs via supervisor (`/etc/supervisor/conf.d/daemon-765106.conf`,
  `php8.4 artisan horizon`, user `forge`). Static on disk — survives Forge removal.
- **Cron:** forge user has only `7 * * * * ... php8.2 artisan horizon:terminate`
  — note the **stale `php8.2`** (app runs 8.4). No `schedule:run` entry exists.
- **Scheduler:** app defines **no** scheduled tasks, so the missing `schedule:run`
  cron is correct, not a bug.
- **nginx:** config references `forge-conf/xilero.net/*` includes — these are
  static files on disk and keep working after Forge leaves.
- **Assets:** `public/build/*` (Vite output) is **committed to git**. The server
  serves what `git pull` delivers; the deploy script's `npm install` is dead weight.
- **sudoers:** the deploy `sudo service php8.4-fpm reload` relies on passwordless
  sudo entries Forge created. These persist on disk after Forge is removed.

## Scope

**In scope:** Envoy deploy file (manual trigger) + a committed ops runbook covering
the certbot SSL migration, Horizon/supervisor, corrected cron, and a Forge
decommission checklist.

**Out of scope:** GitHub Actions / CI-triggered deploy (manual chosen); moving
nginx/supervisor configs into the repo (server-specific, reference-only value).

## Design

### 1. Envoy deploy — `Envoy.blade.php` (repo root)

- Add `laravel/envoy` as a dev dependency (`composer require laravel/envoy --dev`).
- `@servers(['web' => 'forge@xilero.net'])`.
- A `deploy` task run manually: `vendor/bin/envoy run deploy`.
- Reproduces the current Forge script with two corrections:
  - Uses **`php8.4`** consistently.
  - **Drops `npm install`** — assets are pre-built and committed.
- Task steps (in order):
  1. `cd /home/forge/xilero.net`
  2. `git pull origin master`
  3. `composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader`
  4. flock-guarded `sudo -S service php8.4-fpm reload`
  5. `php8.4 artisan migrate --force`
  6. `php8.4 artisan horizon:terminate`
  7. `php8.4 artisan optimize`

### 2. Ops runbook — `docs/deployment.md`

- **Deploying:** how to run `vendor/bin/envoy run deploy`; reminder to run
  `npm run build` and commit `public/build/` locally before deploying asset changes.
- **SSL migration to certbot (do BEFORE cancelling Forge):**
  install certbot, issue certs for `xilero.net` + `www.xilero.net`, repoint the
  `ssl_certificate` / `ssl_certificate_key` directives in nginx from
  `/etc/nginx/ssl/...` to `/etc/letsencrypt/live/...`, enable the certbot
  systemd renewal timer, verify `certbot renew --dry-run`, then remove Forge's
  `.letsencrypt-renew` cron.
- **Horizon / supervisor:** document `daemon-765106.conf` and `supervisorctl`
  restart/status commands.
- **Cron:** fix the hourly `horizon:terminate` to `php8.4`; note no `schedule:run`
  is required.
- **Forge decommission checklist:** ordered so certbot renewal is verified before
  the Forge subscription is cancelled.

## Testing

Config/ops change — no application code, so no PHPUnit changes.
Verification:
- `vendor/bin/envoy run deploy --pretend` (dry-run prints the commands).
- A real `vendor/bin/envoy run deploy` against prod.
- `certbot renew --dry-run` confirms SSL renewal works without Forge.
