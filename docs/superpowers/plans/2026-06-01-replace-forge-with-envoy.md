# Replace Forge with Envoy + Ops Runbook Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Replace Laravel Forge's deploy webhook with a repo-managed Envoy task, and document a runbook so the Forge subscription can be cancelled without breaking SSL renewal or daemons.

**Architecture:** The app stays on the same server (`forge@xilero.net`, Ubuntu 22.04, PHP 8.4-FPM, MySQL, nginx, Horizon-via-supervisor). We add `laravel/envoy` (dev dep) and an `Envoy.blade.php` reproducing the current Forge deploy script, triggered manually from a laptop. A committed `docs/deployment.md` runbook covers the one true lock-in — SSL renewal, which currently runs through Forge's microservice — by migrating it to certbot, plus Horizon/supervisor and corrected cron.

**Tech Stack:** Laravel 12, PHP 8.4, Laravel Envoy, certbot, supervisor, nginx.

---

### Task 1: Add Laravel Envoy as a dev dependency

**Files:**
- Modify: `composer.json` (require-dev block, lines 27-35)

- [ ] **Step 1: Install Envoy**

Run:
```bash
composer require laravel/envoy --dev --no-interaction
```
Expected: `laravel/envoy` added under `require-dev` in `composer.json` and `composer.lock`; `vendor/bin/envoy` exists.

- [ ] **Step 2: Verify the binary**

Run: `vendor/bin/envoy --version`
Expected: prints an Envoy version string (e.g. `Laravel Envoy 2.x`).

- [ ] **Step 3: Commit**

```bash
git add composer.json composer.lock
git commit -m "Add laravel/envoy dev dependency"
```

---

### Task 2: Create the Envoy deploy task

**Files:**
- Create: `Envoy.blade.php` (repo root)

- [ ] **Step 1: Write `Envoy.blade.php`**

Create `Envoy.blade.php` with this exact content:

```blade
@servers(['web' => 'forge@xilero.net'])

@setup
    $repository = '/home/forge/xilero.net';
    $branch = 'master';
@endsetup

@story('deploy')
    update-code
    install-dependencies
    reload-fpm
    migrate
    restart-horizon
    optimize
@endstory

@task('update-code', ['on' => 'web'])
    cd {{ $repository }}
    git pull origin {{ $branch }}
@endtask

@task('install-dependencies', ['on' => 'web'])
    cd {{ $repository }}
    composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader
@endtask

@task('reload-fpm', ['on' => 'web'])
    (
        flock -w 10 9 || exit 1
        echo 'Restarting FPM...'
        sudo -S service php8.4-fpm reload
    ) 9>/tmp/fpmlock
@endtask

@task('migrate', ['on' => 'web'])
    cd {{ $repository }}
    php8.4 artisan migrate --force
@endtask

@task('restart-horizon', ['on' => 'web'])
    cd {{ $repository }}
    php8.4 artisan horizon:terminate
@endtask

@task('optimize', ['on' => 'web'])
    cd {{ $repository }}
    php8.4 artisan optimize
@endtask
```

- [ ] **Step 2: Verify Envoy parses the file (dry-run)**

Run: `vendor/bin/envoy run deploy --pretend`
Expected: prints the commands for each task (`git pull origin master`, `composer install ...`, the flock/fpm block, `php8.4 artisan migrate --force`, `php8.4 artisan horizon:terminate`, `php8.4 artisan optimize`) without connecting to the server. No parse/syntax error.

- [ ] **Step 3: Commit**

```bash
git add Envoy.blade.php
git commit -m "Add Envoy deploy task to replace Forge deploy script"
```

---

### Task 3: Fix the stale Horizon-terminate cron on prod

**Files:**
- Server-side only: `crontab -u forge` on `root@100.118.64.49` (no repo file).

- [ ] **Step 1: Inspect the current forge crontab**

Run:
```bash
ssh root@100.118.64.49 'crontab -u forge -l | grep horizon'
```
Expected: `7 * * * * cd /home/forge/xilero.net && /usr/bin/php8.2 artisan horizon:terminate >/dev/null 2>&1`

- [ ] **Step 2: Replace `php8.2` with `php8.4` in that line**

Run:
```bash
ssh root@100.118.64.49 "crontab -u forge -l | sed 's#/usr/bin/php8.2 artisan horizon:terminate#/usr/bin/php8.4 artisan horizon:terminate#' | crontab -u forge -"
```
Expected: no output (success).

- [ ] **Step 3: Verify the change**

Run:
```bash
ssh root@100.118.64.49 'crontab -u forge -l | grep horizon'
```
Expected: line now uses `/usr/bin/php8.4 artisan horizon:terminate`.

(No commit — server state only.)

---

### Task 4: Write the deployment + decommission runbook

**Files:**
- Create: `docs/deployment.md`

- [ ] **Step 1: Write `docs/deployment.md`**

Create `docs/deployment.md` with this exact content:

````markdown
# Deployment & Server Operations

The app runs on a single self-managed server (`forge@xilero.net`,
`root@100.118.64.49`): Ubuntu 22.04, nginx, PHP 8.4-FPM, MySQL, and Horizon
under supervisor. Laravel Forge is no longer used to deploy or manage SSL.

## Deploying

Deploys are run manually from a laptop with [Laravel Envoy](https://laravel.com/docs/envoy):

```bash
vendor/bin/envoy run deploy
```

This runs `Envoy.blade.php`'s `deploy` story: `git pull` → `composer install`
→ reload PHP-FPM → `migrate --force` → `horizon:terminate` → `optimize`.

**Front-end assets are committed to git** (`public/build/`). When you change
assets, build and commit them locally *before* deploying:

```bash
npm run build
git add public/build
git commit -m "Rebuild assets"
git push
vendor/bin/envoy run deploy
```

Dry-run without connecting: `vendor/bin/envoy run deploy --pretend`.

## SSL — migrate from Forge to certbot (DO THIS BEFORE CANCELLING FORGE)

SSL certs currently renew via Forge's microservice
(`/home/forge/.letsencrypt-renew/*` cron calling
`https://forge-certificates.laravel.com/le/...`). Cancelling Forge stops
renewal and HTTPS breaks within ~90 days. Replace it with certbot:

1. Install certbot:
   ```bash
   ssh root@100.118.64.49 'apt-get update && apt-get install -y certbot python3-certbot-nginx'
   ```
2. Issue certs (webroot avoids editing nginx mid-flight; webroot is
   `/home/forge/xilero.net/public`):
   ```bash
   ssh root@100.118.64.49 'certbot certonly --webroot -w /home/forge/xilero.net/public -d xilero.net -d www.xilero.net'
   ```
   Certs land in `/etc/letsencrypt/live/xilero.net/`.
3. Repoint nginx. In `/etc/nginx/sites-available/xilero.net` (and the www
   redirect server block), change:
   ```
   ssl_certificate     /etc/nginx/ssl/xilero.net/3038984/server.crt;
   ssl_certificate_key /etc/nginx/ssl/xilero.net/3038984/server.key;
   ```
   to:
   ```
   ssl_certificate     /etc/letsencrypt/live/xilero.net/fullchain.pem;
   ssl_certificate_key /etc/letsencrypt/live/xilero.net/privkey.pem;
   ```
   Then: `nginx -t && systemctl reload nginx`.
4. Confirm the renewal timer is active and dry-run renewal:
   ```bash
   ssh root@100.118.64.49 'systemctl status certbot.timer --no-pager; certbot renew --dry-run'
   ```
   Expected: timer `active`; dry-run reports success for `xilero.net`.
5. Remove Forge's renewal cron once certbot renewal is verified:
   ```bash
   ssh root@100.118.64.49 'crontab -u forge -l | grep -v letsencrypt-renew | crontab -u forge -'
   ```

## Horizon (queue worker)

Horizon runs under supervisor: `/etc/supervisor/conf.d/daemon-765106.conf`
(`php8.4 artisan horizon`, user `forge`, autostart/autorestart). Deploys call
`php8.4 artisan horizon:terminate`; supervisor restarts it with fresh code.

```bash
ssh root@100.118.64.49 'supervisorctl status'                 # check
ssh root@100.118.64.49 'supervisorctl restart daemon-765106:*' # manual restart
```

## Cron

The forge user crontab runs `horizon:terminate` hourly (now using `php8.4`):

```
7 * * * * cd /home/forge/xilero.net && /usr/bin/php8.4 artisan horizon:terminate >/dev/null 2>&1
```

The app defines no scheduled tasks, so **no `schedule:run` entry is needed**.
If scheduled tasks are added later, install:

```
* * * * * cd /home/forge/xilero.net && /usr/bin/php8.4 artisan schedule:run >> /dev/null 2>&1
```

## nginx

The live config is `/etc/nginx/sites-available/xilero.net` (symlinked from
`sites-enabled`). It includes `forge-conf/xilero.net/*` files, which remain as
static files on disk and keep working after Forge is removed. A reference copy
lives in `forge/nginx.conf` in this repo. Always run `nginx -t` before reload.

## Decommission Forge — ordered checklist

1. [ ] `vendor/bin/envoy run deploy` works end-to-end.
2. [ ] certbot issues certs and `certbot renew --dry-run` succeeds (SSL section).
3. [ ] nginx points at `/etc/letsencrypt/live/...` and serves HTTPS.
4. [ ] Forge `.letsencrypt-renew` cron removed.
5. [ ] forge `horizon:terminate` cron uses `php8.4`.
6. [ ] Horizon supervisor daemon confirmed running (`supervisorctl status`).
7. [ ] Only now: cancel the Forge subscription / remove the server from Forge.
````

- [ ] **Step 2: Verify the runbook renders (no broken fences)**

Run: `sed -n '1,40p' docs/deployment.md`
Expected: clean Markdown, headers and code fences intact.

- [ ] **Step 3: Commit**

```bash
git add docs/deployment.md
git commit -m "Add deployment + Forge decommission runbook"
```

---

### Task 5: Final formatting pass

**Files:**
- Whatever Pint flags as dirty.

- [ ] **Step 1: Run Pint on dirty files**

Run: `vendor/bin/pint --dirty`
Expected: no PHP style violations (Envoy.blade.php is a Blade template, not PHP — Pint should leave it alone; this guards against any stray changes).

- [ ] **Step 2: Commit if Pint changed anything**

```bash
git add -A
git commit -m "Apply Pint formatting" || echo "nothing to format"
```

---

## Notes for the implementer

- **No PHPUnit tests apply** — this is ops/config. The CLAUDE.md "test every change"
  rule is satisfied by the `--pretend` dry-run (Task 2) and `certbot renew --dry-run`
  (runbook), which are the meaningful verifications for this work.
- Tasks 1, 2, 4, 5 are repo changes. Task 3 and the SSL section are server-side
  actions that change prod — run them deliberately, not as part of a CI loop.
- Do **not** cancel Forge until every box in the decommission checklist is ticked.
