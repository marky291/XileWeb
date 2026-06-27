---
name: deploy-wiki
description: >-
  Runbook for deploying / operating the dual-server wiki engine on the LIVE XileWeb
  server. Activates when deploying the wiki, cloning or pointing at wiki content,
  setting WIKI_* env, configuring GitHub webhooks for the wiki, or troubleshooting
  the live wiki (/wiki/xilero, /wiki/xileretro) — mentions of wiki deploy, gitbook
  content, webhook auto-pull, or wiki-content.
---

# Deploy / Operate the XileWeb Wiki (LIVE — take care)

The wiki renders each game server's GitBook markdown **in place** from repos cloned
**outside** the web root. The XileWeb codebase is path-agnostic (env-driven); content
is never vendored. See `CLAUDE.md` → "Wiki Engine" and
`docs/superpowers/specs/2026-06-27-xileweb-wiki-engine-design.md`.

## ⚠️ This is a live service with active players

- Run a **read-only investigation first**; mutate only after the plan is clear.
- The app at `/home/forge/xilero.net` runs **config/routes cached** — env changes do
  nothing until you re-cache.
- **Always run git/artisan as `forge`** (`sudo -u forge …`), never root, or you create
  root-owned files the web user can't read/write.
- Keep a **rollback** ready: `sudo -u forge git reset --hard <prev-sha> && sudo -u forge php artisan config:cache`.

## Server facts

- SSH: `root@100.118.64.49` (Tailscale). App root: `/home/forge/xilero.net`. Web user: `forge`. PHP 8.4.
- Content lives in `/home/forge/wiki-content/` (outside the web root):
  - `xilero-game/` — sparse clone of `marky291/XileRO` (only `rathena/gitbook`), branch **stable**
  - `xileretro/` — `achenxu/XileRetro-Wiki`, branch **master**
- Both content repos are **private** → the box authenticates via `gh` as `forge`
  (`gh auth login` + `gh auth setup-git`, one-time; `gh` binary in `/usr/local/bin`, arm64).

## Env vars (in `/home/forge/xilero.net/.env`)

```
WIKI_XILERO_PATH=/home/forge/wiki-content/xilero-game/rathena/gitbook
WIKI_XILERO_REPO=/home/forge/wiki-content/xilero-game
WIKI_XILERO_BRANCH=stable
WIKI_XILERETRO_PATH=/home/forge/wiki-content/xileretro
WIKI_XILERETRO_REPO=/home/forge/wiki-content/xileretro
WIKI_XILERETRO_BRANCH=master
WIKI_WEBHOOK_SECRET=<random hex; same value in both GitHub webhooks>
```

### 🔴 .env gotcha (caused a live outage once)

The `.env` may **not end with a newline**. Appending with `echo >> .env` then glues your
var onto the last line → dotenv parse error → `config:cache` wipes the cache mid-build →
**site 500s**. Always ensure a trailing newline first:

```bash
sudo -u forge sh -c '[ -n "$(tail -c1 .env)" ] && printf "\n" >> .env; true'
# then append (tee preserves forge ownership):
printf 'WIKI_FOO=bar\n' | sudo -u forge tee -a .env >/dev/null
```

## Initial deploy

1. **Pull code** (fast-forward; no composer step — spatie/laravel-markdown already installed):
   ```bash
   cd /home/forge/xilero.net && sudo -u forge git pull --ff-only origin master
   ```
2. **Clone content** (as forge; gh provides creds). Clone to a temp dir and swap to avoid a content gap:
   ```bash
   sudo -u forge mkdir -p /home/forge/wiki-content
   # XileRetro (full, gitbook at repo root, master)
   sudo -u forge git clone --depth 1 https://github.com/achenxu/XileRetro-Wiki.git /home/forge/wiki-content/xileretro
   # XileRO (sparse: only rathena/gitbook, STABLE branch)
   sudo -u forge git clone --depth 1 --branch stable --filter=blob:none --sparse https://github.com/marky291/XileRO.git /home/forge/wiki-content/xilero-game
   sudo -u forge git -C /home/forge/wiki-content/xilero-game sparse-checkout set rathena/gitbook
   ```
3. **Set env** (newline-safe, see gotcha above). Generate the secret: `openssl rand -hex 32`.
4. **Re-cache** (env is cached in prod):
   ```bash
   sudo -u forge php artisan config:cache
   sudo -u forge php artisan route:cache
   sudo -u forge php artisan view:clear
   sudo -u forge php artisan cache:clear   # rebuilds the search index
   ```
5. **Verify** (must all be 200):
   ```bash
   for u in / /wiki/xilero /wiki/xileretro /wiki/search-index.json; do
     curl -s -o /dev/null -w "%{http_code}  $u\n" "https://xilero.net$u"; done
   ```

## GitHub webhooks (auto-pull on push)

Endpoint: `POST /webhooks/wiki/{server}` — verifies `X-Hub-Signature-256`, pulls only on
push to the server's branch, rebuilds the search index. CSRF-exempt.

In each repo → Settings → Webhooks → Add webhook:
- Payload URL: `https://xilero.net/webhooks/wiki/xilero` (and `…/xileretro`)
- **Content type: `application/json`** (required — the branch filter parses the JSON `ref`)
- Secret: the `WIKI_WEBHOOK_SECRET` value
- Events: **Just the push event**

Verify endpoint without a real push (ping is ignored, no pull):
```bash
SECRET=...; P='{}'
SIG="sha256=$(printf '%s' "$P" | openssl dgst -sha256 -hmac "$SECRET" | awk '{print $NF}')"
curl -s -X POST https://xilero.net/webhooks/wiki/xilero -H "X-GitHub-Event: ping" \
  -H "Content-Type: application/json" -H "X-Hub-Signature-256: $SIG" -d "$P"   # → {"status":"ignored"}
# no signature → 403
```

## Manual content refresh (if webhooks are off)

```bash
sudo -u forge git -C /home/forge/wiki-content/xilero-game pull --ff-only
sudo -u forge git -C /home/forge/wiki-content/xileretro  pull --ff-only
cd /home/forge/xilero.net && sudo -u forge php artisan cache:clear   # rebuild search index
```

## Troubleshooting

- **Wiki shows "coming soon"** → `WIKI_<SLUG>_PATH` unset/missing or not re-cached → fix env, `config:cache`.
- **500 after env change** → malformed `.env` (newline gotcha) → repair the glued line, `config:cache`.
- **Webhook pulls on wrong branch / not pulling** → content type must be `application/json`; check `WIKI_<SLUG>_BRANCH`.
- **`git pull` permission denied in webhook** → clones must be `forge`-owned (clone as forge).
- **Code (PHP) changes not taking effect** → `php artisan optimize:clear` (not just `view:clear`).
