# Deployment & Server Operations

The app runs on a single self-managed server (`forge@xilero.net`,
`root@100.118.64.49`): Ubuntu 22.04, nginx, PHP 8.4-FPM, MySQL, and Horizon
under supervisor. Laravel Forge is no longer used to deploy. SSL is handled by
Cloudflare (see the SSL section).

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

## SSL — handled by Cloudflare

**Decision:** SSL is handled by Cloudflare; we do not run certbot or manage
Let's Encrypt renewal on the origin. Forge's renewal cron in
`/home/forge/.letsencrypt-renew/*` (which calls
`https://forge-certificates.laravel.com/le/...`) becomes inert once Forge is
cancelled, and that is acceptable for the **proxied** apex.

DNS layout (verified 2026-06-01):

- `xilero.net` (apex) → **proxied through Cloudflare** (resolves to `2606:4700::`
  Cloudflare IPs). Browsers get Cloudflare's edge cert; TLS terminates at
  Cloudflare, so the origin cert expiring does not break the apex.
- `www.xilero.net` → **direct to origin** (`52.76.254.40`, not proxied).
  Browsers hitting `www` get the **origin's** Let's Encrypt cert directly.

> ⚠️ **Residual risk on `www`.** The origin Let's Encrypt cert
> (`/etc/nginx/ssl/xilero.net/3038984/`, covers `xilero.net` + `www.xilero.net`)
> currently expires **2026-06-21** and is renewed only by Forge. Once Forge is
> cancelled, nothing renews it, so **`https://www.xilero.net` will fail** when
> it expires, even though the proxied apex keeps working. To remove this risk
> without depending on Forge or certbot, do one of:
>
> 1. **Proxy `www` through Cloudflare too** (orange-cloud the `www` DNS record),
>    so Cloudflare terminates TLS for it as well. Simplest.
> 2. **Install a Cloudflare Origin CA certificate** on nginx (valid 15 years, no
>    renewal) and set the zone SSL mode to Full (strict).
>
> Both leave Let's Encrypt/certbot out entirely. Until one is done, treat the
> 2026-06-21 expiry as a hard deadline for `www`.

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

The live config is `/etc/nginx/sites-enabled/xilero.net` — note this is a
**regular file, not a symlink**, and it diverges from
`/etc/nginx/sites-available/xilero.net` (the enabled file has the current
HTTP/patch + ACME-challenge layout). Edit the **enabled** file. It includes
`forge-conf/xilero.net/*` files, which remain as static files on disk and keep
working after Forge is removed. A reference copy of an older revision lives in
`forge/nginx.conf` in this repo. Always run `nginx -t` before reload.

The HTTP server block already serves ACME challenges via
`location /.well-known/acme-challenge { alias /home/forge/.letsencrypt; }` — left
in place in case Cloudflare/origin cert handling changes later.

## Decommission Forge — ordered checklist

1. [ ] `vendor/bin/envoy run deploy` works end-to-end.
2. [x] forge `horizon:terminate` cron uses `php8.4` (fixed 2026-06-01).
3. [ ] SSL decision understood: apex is Cloudflare-proxied (safe); resolve the
       `www` origin-cert risk (proxy `www` or install a Cloudflare Origin CA cert
       on nginx) — see the SSL section. Hard deadline: cert expiry 2026-06-21.
4. [ ] Horizon supervisor daemon confirmed running (`supervisorctl status`).
5. [ ] Only now: cancel the Forge subscription / remove the server from Forge.
