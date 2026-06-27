# XileWeb Wiki Engine — Design

**Date:** 2026-06-27
**Status:** Approved design (pending spec review)
**Author:** Marky + Claude

## 1. Purpose

Replace the paid GitBook hosting for XileRO's player wikis with a self-hosted
wiki rendered **inside the existing XileWeb Laravel site**. Content is the
existing GitBook markdown, consumed **in place** from each game-server repo's
`gitbook/` folder. The result is on-brand (XileRO theme), file-synced (no
database, no in-browser editor), serves two servers, and carries none of the
GitBook fees or the legacy-plugin risk of a GitBook fork (Honkit).

### Context

XileRO operates two servers, each with its own wiki and its own git repo:

| Server | Rate | Current GitBook | Content location (its repo) |
|--------|------|-----------------|------------------------------|
| **XileRO** | mid-rate | `info.xilero.net` | `rathena/gitbook/` |
| **XileRetro** | high-rate | `wiki.xilero.net` | its repo's `gitbook/` |

Both wikis are in legacy GitBook format: `SUMMARY.md` (nav) + `README.md`
(landing) + topic markdown + `.gitbook/assets/` (images). The XileWeb site
(Laravel 12, PHP 8.4, Tailwind 4 + Typography, Livewire, Filament,
`spatie/laravel-markdown`) already has a half-built, disabled file-based wiki
(`WikiController`, `resources/views/wiki/{index,show}.blade.php`) that this
design replaces and extends.

## 2. Decisions (settled during brainstorming)

1. **Routing:** path-prefixed, one domain — `xilero.net/wiki/{server}/{path}`,
   `{server}` ∈ `xilero | xileretro`.
2. **Content source:** the markdown stays in each game-server repo's `gitbook/`
   folder; the website reads it **in place** via a configured path. Content is
   **not** copied into the website repo.
3. **Sync model:** file-based only. Edit markdown in the server repo →
   deploy/pull on the host → wiki updates. **No database, no editor.**
4. **Navigation:** each wiki's `SUMMARY.md` defines the sidebar tree (GitBook
   parity; full manual control over order/grouping).
5. **Both servers are Phase 1** — both have real content; the engine is generic
   over the slug (no per-server special-casing).

## 3. Routing

| Route | Behavior |
|-------|----------|
| `GET /wiki` | Server chooser (or redirect to `/wiki/xilero`). |
| `GET /wiki/{server}` | Render that wiki's `README.md` as the landing page. |
| `GET /wiki/{server}/{path}` | Render `{path}.md` (or `{path}/README.md`). |
| `GET /wiki/{server}/assets/{file}` | Stream an image from that repo's `.gitbook/assets/`. |

`{server}` is validated against the configured slugs. An unknown slug, or a
configured-but-missing path, returns a friendly "coming soon / not found" page —
never a 500. (Dev: XileRetro is not cloned locally, so it shows "coming soon"
until its path is configured.)

## 4. Configuration

`config/wiki.php` maps each server slug to an absolute `gitbook/` path, driven
by env so dev and prod differ without code changes:

```php
return [
    'servers' => [
        'xilero' => [
            'label' => 'XileRO',
            'rate'  => 'Mid-Rate',
            'path'  => env('WIKI_XILERO_PATH'),
        ],
        'xileretro' => [
            'label' => 'XileRetro',
            'rate'  => 'High-Rate',
            'path'  => env('WIKI_XILERETRO_PATH'),
        ],
    ],
    'default' => 'xilero',
];
```

```ini
# .env (dev)
WIKI_XILERO_PATH=D:/XileRO/rathena/gitbook
# WIKI_XILERETRO_PATH=   # not cloned locally -> "coming soon"

# .env (live) — game-server repos are cloned onto the XileWeb host
WIKI_XILERO_PATH=/home/forge/repos/xilero/rathena/gitbook
WIKI_XILERETRO_PATH=/home/forge/repos/xileretro/.../gitbook
```

Adding a third server later is one more config block + env line; no code change.

## 5. Components

Each unit has one purpose, a narrow interface, and is independently testable.

### 5.1 `WikiRepository`
- **Does:** Given a server slug, resolves its configured base path; provides
  `pageFile(path)`, `summary()`, `asset(file)`, `exists(path)`, `hasServer()`.
  Owns **path-traversal safety** — all resolved paths must stay within the
  server's `gitbook/` root (realpath containment check). This is the one
  security-critical unit.
- **Depends on:** `config/wiki.php`, filesystem.

### 5.2 `SummaryParser`
- **Does:** Parses a wiki's `SUMMARY.md` into a nav tree: ordered sections, each
  with title + items (label, href, depth, children). Also yields a flat ordered
  list for prev/next (Phase 2).
- **Depends on:** the `SUMMARY.md` text. Pure (no IO).

### 5.3 `FrontmatterParser`
- **Does:** Splits leading YAML frontmatter from the markdown body. Returns
  `[attributes, body]`. Consumers use `description` (→ page subtitle); `cover`,
  `coverY` are ignored (GitBook SaaS-only); `icon` optional (Phase 2).
- **Depends on:** a YAML parser (Symfony YAML, already transitive via Laravel).

### 5.4 `WikiMarkdownRenderer`
- **Does:** Renders a GitBook markdown body to HTML faithfully. Wraps
  `spatie/laravel-markdown` / league/commonmark configured with
  `html_input: allow` so `<figure>` and `<details>` pass through. Adds:
  - **Hint rendering** — `{% hint style="info|warning|danger|success" %}…
    {% endhint %}` → a themed callout (colored panel + icon) styled to resemble
    GitBook. The hint's **inner content is itself markdown** and must be parsed
    (CommonMark does not parse markdown inside a raw block-level `<div>`), so the
    implementation renders inner content as markdown and composes the callout —
    via a custom CommonMark block extension (preferred) or a two-pass
    placeholder substitution (acceptable fallback).
  - **Asset URL rewriting** — `(../).gitbook/assets/<file>` in `img`/links →
    `/wiki/{server}/assets/<file>`.
- **Depends on:** `spatie/laravel-markdown`, the current `{server}` slug.

### 5.5 `WikiController`
- **Does:** Orchestrates a request: validate slug → `WikiRepository` →
  `FrontmatterParser` + `WikiMarkdownRenderer` for the body, `SummaryParser` for
  the sidebar → return the wiki view with `{server, nav, title, subtitle,
  html, breadcrumbs}`. A separate thin action streams assets. Replaces the
  current `WikiController`.
- **Depends on:** all of the above.

## 6. Rendering pipeline (per page request)

```
file bytes
  → FrontmatterParser            (strip YAML; capture description)
  → WikiMarkdownRenderer
        → hint blocks → themed callouts (inner markdown parsed)
        → commonmark render (html_input: allow → figure/details kept)
        → asset URLs rewritten → /wiki/{server}/assets/*
  → view: sidebar (SummaryParser) | content (prose) | right TOC
```

## 7. GitBook-ism handling (faithful render of the existing pages)

| GitBook feature | In content | Handling |
|-----------------|-----------:|----------|
| `{% hint style=… %}` | 27 blocks | Themed callout (4 styles + icon), inner markdown parsed |
| `<figure><figcaption>` | 20 files | Pass through + CSS centering/caption |
| `<details><summary>` | 36 blocks | Pass through + styled disclosure |
| `description:` frontmatter | ~41 pages | Gray page subtitle under the H1 (GitBook parity) |
| `cover:` / `coverY:` | 2 pages | Ignored (SaaS-only banner) |
| `icon:` | 1 page | Ignored in P1; optional P2 |
| `../.gitbook/assets/*` | 620 assets | Rewritten to the assets route, streamed from repo |

**Known CommonMark consideration:** markdown nested inside raw block HTML
(`<details>`, hint `<div>`) is not re-parsed by CommonMark unless separated by
blank lines. The hint renderer handles this explicitly (5.4); for `<details>`,
content authored with surrounding blank lines renders correctly — flagged for
the implementer to verify against real pages.

## 8. Layout (GitBook 3-pane, XileRO skin)

A wiki layout inside `<x-app-layout>` (keeps site nav/footer/theme):

```
┌───────────────────────────────────────────────────────────┐
│  site nav (existing)                                        │
├──────────────┬───────────────────────────┬────────────────┤
│ SIDEBAR      │ CONTENT                    │ ON THIS PAGE   │
│ (SUMMARY     │ H1 + description subtitle  │ (TOC, exists)  │
│  tree,       │ prose markdown             │                │
│  collapsible,│ breadcrumbs, progress      │                │
│  active hl)  │                            │                │
├──────────────┴───────────────────────────┴────────────────┤
│  site footer (existing)                                     │
└───────────────────────────────────────────────────────────┘
```

The **left sidebar** (SUMMARY tree) is the main new UI; content column reuses
the existing `prose`/`wiki-content` styling, breadcrumbs, reading-progress, and
right-hand TOC already in `show.blade.php`. Sidebar collapses on mobile.

## 9. Security

- **Path traversal:** every page/asset path is resolved with realpath and must
  be contained within the server's `gitbook/` root; reject otherwise (covered by
  `WikiRepository`). The current controller's string-strip approach is replaced
  by realpath containment.
- **Asset serving:** only files under `.gitbook/assets/` are streamable; content
  type from a safe extension allowlist (png/jpg/gif/webp/bmp); no arbitrary
  file reads.
- **HTML passthrough:** `html_input: allow` is scoped to wiki rendering only.
  Content is first-party (our own repos), not user input, so the raw-HTML risk
  is bounded; still, the renderer strips `<script>`/event-handler attributes
  defensively.

## 10. Deployment

- On the live XileWeb host, **clone each game-server repo** (or a sparse/`gitbook`-
  only checkout) to a stable path; point `WIKI_*_PATH` at each clone's `gitbook/`.
- Content updates = pulling those clones (fold into the existing deploy/patch
  flow). No website redeploy needed for content-only changes.
- `config:cache` safe (paths come from env at runtime via `config()`).

## 11. Phasing

- **Phase 1 (this spec):** routing, `config/wiki.php`, the five components,
  sidebar from `SUMMARY.md`, asset serving, hint/figure/details/frontmatter
  rendering, both XileRO and XileRetro live end-to-end (XileRetro shows
  "coming soon" in dev until its path is set), the 3-pane layout, security,
  feature tests.
- **Phase 2:** client-side search (prebuilt per-wiki JSON index + Alpine/Fuse),
  prev/next page nav, "edit on git" link, optional page `icon`.
- **Out of scope:** database storage, in-browser editor, PDF/ePub export,
  multi-language, comments.

## 12. Testing (Phase 1 feature tests)

- `/wiki/xilero` renders the README landing with sidebar.
- `/wiki/xilero/{known page}` renders; H1, subtitle, content present.
- A `{% hint %}` page renders the themed callout with inner markdown parsed.
- A `<figure>` page keeps the image + caption; asset route returns the image
  with correct content type.
- `../.gitbook/assets/x.png` rewritten to `/wiki/xilero/assets/x.png`.
- Path traversal (`/wiki/xilero/../../etc/passwd` style) is rejected.
- Unknown server slug and unconfigured server → "coming soon"/404, not 500.

## 13. Risks / open items

- **Hint inner-markdown** is the trickiest render detail (§5.4) — pick the
  extension vs two-pass approach during planning and verify on real pages.
- **`<details>` nested markdown** depends on authoring blank lines — verify
  against the 36 real blocks; add a normalization step if needed.
- **XileRetro local testing** is blocked until its repo is on the dev box; layout
  can be validated by temporarily aliasing its env path to the XileRO folder.
