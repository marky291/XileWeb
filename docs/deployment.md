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
