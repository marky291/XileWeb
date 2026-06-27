{{-- resources/views/wiki/partials/styles.blade.php
     Self-contained GitBook-style theme for the wiki (no Tailwind build needed). --}}
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400&display=swap');

    :root {
        --wk-accent: #f59e0b;
        --wk-accent-hi: #fbbf24;
        --wk-text: #c9ced6;
        --wk-strong: #f3f4f6;
        --wk-muted: #8b919c;
        --wk-border: #232a36;
        --wk-border-soft: #1b212b;
        --wk-code-bg: #161b22;
        --wk-surface: rgba(255,255,255,.025);
        --wk-nav: 260px;
        --wk-toc: 220px;
        --wk-read: 760px;
    }

    /* Per-server identity: XileRetro recolors the whole wiki purple; XileRO stays amber. */
    [data-server="xileretro"] { --wk-accent:#a855f7; --wk-accent-hi:#c084fc; }

    /* Reading-progress bar (top) */
    #reading-progress { position:fixed; top:0; left:0; height:2px; width:0; background:var(--wk-accent); z-index:60; transition:width .08s linear; }

    /* Page shell — GitBook uses Inter */
    .wiki-page { max-width:1340px; margin:0 auto; padding:1.25rem 1.5rem 4rem; font-family:Inter,"Inter Fallback",system-ui,arial,sans-serif; }

    /* Shell clears the fixed nav via PADDING (no margin-collapse), so the dark
       page background stays behind the nav instead of exposing the body. */
    .wiki-shell { padding-top:4rem; }
    @media (min-width:1024px) { .wiki-shell { padding-top:5rem; } }

    /* Wiki bar — sticky, glassmorphism, under the fixed site nav */
    .wiki-topbar { position:sticky; top:4rem; z-index:40; background:rgba(13,17,23,.62); -webkit-backdrop-filter:blur(14px) saturate(160%); backdrop-filter:blur(14px) saturate(160%); border-bottom:1px solid rgba(255,255,255,.09); }
    .wiki-topbar-inner { max-width:1340px; margin:0 auto; padding:.7rem 1.5rem; display:flex; align-items:center; justify-content:space-between; gap:1rem; flex-wrap:wrap; }
    .wiki-topbar-id { display:flex; align-items:center; gap:.6rem; text-decoration:none; }
    .wiki-topbar-dot { width:.7rem; height:.7rem; border-radius:50%; background:var(--wk-accent); box-shadow:0 0 0 4px color-mix(in srgb, var(--wk-accent) 18%, transparent); flex:0 0 auto; }
    .wiki-topbar-title { font-size:1.15rem; font-weight:700; color:var(--wk-strong); letter-spacing:-.01em; }
    .wiki-topbar-title span { color:var(--wk-accent); }
    .wiki-topbar-rate { font-size:.62rem; font-weight:700; text-transform:uppercase; letter-spacing:.04em; color:var(--wk-accent); background:color-mix(in srgb, var(--wk-accent) 14%, transparent); border-radius:.3rem; padding:.1rem .4rem; }
    .wiki-topbar-actions { display:flex; align-items:center; gap:.6rem; }
    @media (min-width:1024px) { .wiki-topbar { top:5rem; } }

    /* Breadcrumbs */
    .wiki-breadcrumbs { display:flex; flex-wrap:wrap; align-items:center; gap:.5rem; font-size:.825rem; color:var(--wk-muted); padding:.75rem 0 1.25rem; }
    .wiki-breadcrumbs a { color:var(--wk-muted); text-decoration:none; }
    .wiki-breadcrumbs a:hover { color:var(--wk-accent); }
    .wiki-breadcrumbs .sep { color:#4b5563; }
    .wiki-breadcrumbs .current { color:var(--wk-accent); font-weight:600; }

    /* 3-pane layout */
    .wiki-layout { display:grid; grid-template-columns:var(--wk-nav) minmax(0,1fr) var(--wk-toc); gap:2.75rem; align-items:start; }

    /* Server switcher (XileRO / XileRetro) */
    .wiki-serverbar { display:flex; gap:.4rem; margin-bottom:1.1rem; }
    .wiki-server-pill { flex:1 1 0; display:flex; flex-direction:column; align-items:center; gap:.05rem; padding:.45rem .35rem; border-radius:.55rem; border:1px solid var(--wk-border); background:var(--wk-surface); color:var(--wk-muted); text-decoration:none; font-size:.82rem; font-weight:600; text-align:center; transition:color .12s, border-color .12s, background .12s; }
    .wiki-server-rate { font-size:.58rem; font-weight:500; text-transform:uppercase; letter-spacing:.04em; opacity:.85; }
    .wiki-server-pill:hover { color:var(--wk-text); border-color:#3a4250; }
    .wiki-server-pill.is-current.is-xilero { color:#fbbf24; border-color:#f59e0b; background:rgba(245,158,11,.12); }
    .wiki-server-pill.is-current.is-xileretro { color:#c084fc; border-color:#a855f7; background:rgba(168,85,247,.12); }

    /* Left nav */
    .wiki-nav-inner { position:sticky; top:7.5rem; max-height:calc(100vh - 9rem); overflow-y:auto; padding-right:.5rem; }
    @media (min-width:1024px) { .wiki-nav-inner { top:8.5rem; max-height:calc(100vh - 10rem); } }
    .wiki-nav-section { font-size:.7rem; font-weight:700; letter-spacing:.07em; text-transform:uppercase; color:var(--wk-muted); margin:1.4rem 0 .45rem; }
    .wiki-nav-section:first-child { margin-top:0; }
    .wiki-nav-list { list-style:none; margin:0; padding:0; }
    .wiki-nav-row { display:flex; align-items:center; gap:.15rem; }
    .wiki-nav-link { flex:1 1 auto; display:block; padding:.3rem .6rem; margin:.05rem 0; border-radius:.375rem; font-size:.875rem; line-height:1.4; color:var(--wk-text); text-decoration:none; transition:color .12s, background .12s; }
    .wiki-nav-link:hover { color:var(--wk-accent-hi); background:rgba(255,255,255,.04); }
    .wiki-nav-link.is-active { color:var(--wk-accent); background:rgba(245,158,11,.12); font-weight:600; }
    .wiki-nav-children { margin:.1rem 0 .25rem .55rem; padding-left:.55rem; border-left:1px solid var(--wk-border-soft); }

    /* Collapsible group toggle */
    .wiki-nav-toggle { flex:0 0 auto; display:flex; align-items:center; justify-content:center; width:1.4rem; height:1.4rem; padding:0; border:0; background:none; color:var(--wk-muted); cursor:pointer; border-radius:.25rem; }
    .wiki-nav-toggle:hover { color:var(--wk-accent); background:rgba(255,255,255,.04); }
    .wiki-nav-toggle svg { width:.8rem; height:.8rem; transition:transform .15s; transform:rotate(90deg); } /* expanded: points down */
    .wiki-nav-item.is-collapsed > .wiki-nav-row .wiki-nav-toggle svg { transform:rotate(0deg); }   /* collapsed: points right */
    .wiki-nav-item.is-collapsed > .wiki-nav-children { display:none; }

    /* Center content */
    .wiki-main { min-width:0; }
    .wiki-header { max-width:var(--wk-read); margin-bottom:2rem; }
    .wiki-header h1 { font-size:2rem; line-height:1.2; font-weight:700; letter-spacing:-.01em; color:var(--wk-strong); margin:0 0 .4rem; }
    .wiki-subtitle { font-size:1.0625rem; color:var(--wk-muted); margin:0; line-height:1.6; }

    /* Content typography (GitBook scale: 16px body / 1.75) */
    .wiki-content { max-width:var(--wk-read); color:var(--wk-text); font-size:1rem; line-height:1.75; }
    .wiki-content > :first-child { margin-top:0; }
    .wiki-content p { margin:1rem 0; }
    .wiki-content a { color:var(--wk-accent); text-decoration:none; }
    .wiki-content a:hover { text-decoration:underline; }
    .wiki-content strong { color:var(--wk-strong); font-weight:700; }
    .wiki-content em { color:#e5e7eb; }
    /* GitBook uses <mark style="color:X"> as colored text, not a highlight —
       kill the browser's yellow background; map names to dark-theme shades. */
    .wiki-content mark { background:transparent; color:inherit; padding:0; }
    .wiki-content mark[style*="blue"]   { color:#60a5fa !important; }
    .wiki-content mark[style*="red"]    { color:#f87171 !important; }
    .wiki-content mark[style*="green"]  { color:#4ade80 !important; }
    .wiki-content mark[style*="yellow"] { color:#fbbf24 !important; }
    /* Tailwind Preflight resets list-style to none globally — restore it */
    .wiki-content ul { list-style:disc; margin:1rem 0; padding-left:1.5rem; }
    .wiki-content ol { list-style:decimal; margin:1rem 0; padding-left:1.5rem; }
    .wiki-content ul ul { list-style:circle; }
    .wiki-content ul ul ul { list-style:square; }
    .wiki-content li { margin:.35rem 0; }
    .wiki-content li::marker { color:var(--wk-muted); }
    .wiki-content hr { border:0; border-top:1px solid var(--wk-border); margin:2rem 0; }
    .wiki-content blockquote { margin:1.25rem 0; padding:.25rem 1rem; border-left:3px solid var(--wk-border); color:var(--wk-muted); }
    .wiki-content img { max-width:100%; height:auto; border-radius:.5rem; }
    /* Small inline item/sprite icons (not figure images) sit on the text line */
    .wiki-content :is(p, li, td, th, span) img { display:inline-block; height:1.4em; width:auto; margin:0 .12rem; vertical-align:-.32em; border-radius:.2rem; }

    /* Headings + hover "#" anchor */
    .wiki-content h1, .wiki-content h2, .wiki-content h3, .wiki-content h4 { color:var(--wk-strong); font-weight:700; line-height:1.25; scroll-margin-top:9rem; position:relative; }
    .wiki-content h2 { font-size:1.5rem; margin:2.25rem 0 .9rem; padding-bottom:.4rem; border-bottom:1px solid var(--wk-border-soft); letter-spacing:-.01em; }
    .wiki-content h3 { font-size:1.25rem; margin:1.75rem 0 .7rem; }
    .wiki-content h4 { font-size:1rem; margin:1.4rem 0 .5rem; }
    .wiki-anchor { position:absolute; left:-1.1em; top:0; opacity:0; padding-right:.35em; color:var(--wk-accent); font-weight:400; text-decoration:none; transition:opacity .12s; }
    .wiki-content h2:hover .wiki-anchor, .wiki-content h3:hover .wiki-anchor { opacity:1; }

    /* Code */
    .wiki-content code { font-family:ui-monospace,SFMono-Regular,Menlo,Consolas,monospace; font-size:.875em; background:var(--wk-code-bg); border:1px solid var(--wk-border-soft); border-radius:.3rem; padding:.12em .4em; color:#e5e7eb; }
    .wiki-content pre { background:var(--wk-code-bg); border:1px solid var(--wk-border); border-radius:.5rem; padding:1rem 1.1rem; overflow-x:auto; margin:1.25rem 0; line-height:1.5; }
    .wiki-content pre code { background:none; border:0; padding:0; font-size:.85rem; }

    /* Tables (GFM) */
    .wiki-content table { width:100%; border-collapse:collapse; margin:1.25rem 0; font-size:.875rem; table-layout:auto; }
    .wiki-content th, .wiki-content td { border:1px solid var(--wk-border); padding:.55rem .85rem; text-align:left; vertical-align:top; }
    .wiki-content thead th { background:rgba(255,255,255,.05); color:var(--wk-strong); font-weight:600; white-space:nowrap; }
    .wiki-content tbody tr:nth-child(even) { background:var(--wk-surface); }

    /* Hint callouts */
    .wiki-hint { display:flex; gap:.75rem; padding:1rem 1.15rem; margin:1.4rem 0; border-radius:.5rem; border-left:4px solid; }
    .wiki-hint-icon { flex-shrink:0; margin-top:.1rem; }
    .wiki-hint-icon svg { width:1.25rem; height:1.25rem; display:block; }
    .wiki-hint-body { min-width:0; }
    .wiki-hint-body > :first-child { margin-top:0; }
    .wiki-hint-body > :last-child { margin-bottom:0; }
    .wiki-hint-info    { background:rgba(59,130,246,.10); border-color:#3b82f6; }
    .wiki-hint-warning { background:rgba(245,158,11,.10); border-color:#f59e0b; }
    .wiki-hint-danger  { background:rgba(239,68,68,.10);  border-color:#ef4444; }
    .wiki-hint-success { background:rgba(34,197,94,.10);  border-color:#22c55e; }
    .wiki-hint-info .wiki-hint-icon    { color:#3b82f6; }
    .wiki-hint-warning .wiki-hint-icon { color:#f59e0b; }
    .wiki-hint-danger .wiki-hint-icon  { color:#ef4444; }
    .wiki-hint-success .wiki-hint-icon { color:#22c55e; }

    /* Footnote hover tooltips (GitBook item-info-on-hover) */
    .wiki-fn { position:relative; }
    .wiki-fn-ref { color:var(--wk-accent); border-bottom:1px dashed currentColor; cursor:help; text-decoration:none; font-weight:500; }
    .wiki-fn-ref:hover { color:var(--wk-accent-hi); }
    .wiki-fn-pop { position:absolute; left:0; top:100%; z-index:50; width:max-content; max-width:300px; margin-top:.45rem; padding:.7rem .85rem; background:#0f141b; border:1px solid var(--wk-border); border-radius:.5rem; box-shadow:0 10px 28px rgba(0,0,0,.55); font-size:.8rem; line-height:1.55; font-weight:400; color:var(--wk-text); white-space:normal; opacity:0; visibility:hidden; transform:translateY(-4px); transition:opacity .12s, transform .12s; pointer-events:none; }
    .wiki-fn:hover .wiki-fn-pop, .wiki-fn-ref:focus + .wiki-fn-pop { opacity:1; visibility:visible; transform:translateY(0); }
    .wiki-fn-pop strong { color:var(--wk-strong); }
    /* Flip to the right edge inside the last table column / near viewport edge */
    .wiki-content td:last-child .wiki-fn-pop, .wiki-content th:last-child .wiki-fn-pop { left:auto; right:0; }

    /* Figures */
    .wiki-content figure { text-align:center; margin:1.5rem 0; }
    .wiki-content figure img { display:inline-block; }
    .wiki-content figcaption { margin-top:.5rem; font-size:.85rem; color:var(--wk-muted); }

    /* Details / summary */
    .wiki-content details { border:1px solid var(--wk-border); border-radius:.5rem; padding:.6rem 1rem; margin:1.1rem 0; background:var(--wk-surface); }
    .wiki-content details[open] { padding-bottom:.9rem; }
    .wiki-content summary { cursor:pointer; font-weight:600; color:var(--wk-accent-hi); }

    /* Task lists */
    .wiki-content input[type=checkbox] { margin-right:.5rem; accent-color:var(--wk-accent); }
    .wiki-content ul li.task-list-item { list-style:none; }
    .wiki-content ul:has(> li.task-list-item) { padding-left:.25rem; }

    /* Right TOC */
    .wiki-aside-inner { position:sticky; top:7.5rem; max-height:calc(100vh - 9rem); overflow-y:auto; }
    @media (min-width:1024px) { .wiki-aside-inner { top:8.5rem; max-height:calc(100vh - 10rem); } }
    .wiki-aside-label { font-size:.7rem; font-weight:700; letter-spacing:.07em; text-transform:uppercase; color:var(--wk-muted); margin-bottom:.6rem; }
    .wiki-toc { display:flex; flex-direction:column; }
    .wiki-toc-link { display:block; font-size:.825rem; line-height:1.35; color:var(--wk-muted); padding:.2rem 0 .2rem .8rem; border-left:2px solid transparent; text-decoration:none; transition:color .12s, border-color .12s; }
    .wiki-toc-link.is-sub { padding-left:1.55rem; font-size:.8rem; }
    .wiki-toc-link:hover { color:var(--wk-accent-hi); }
    .wiki-toc-link.is-active { color:var(--wk-accent); border-left-color:var(--wk-accent); }

    /* Subtle scrollbars */
    .wiki-nav-inner, .wiki-aside-inner { scrollbar-width:thin; scrollbar-color:#374151 transparent; }
    .wiki-nav-inner::-webkit-scrollbar, .wiki-aside-inner::-webkit-scrollbar { width:6px; }
    .wiki-nav-inner::-webkit-scrollbar-thumb, .wiki-aside-inner::-webkit-scrollbar-thumb { background:#374151; border-radius:3px; }

    /* Responsive */
    @media (max-width:1100px) {
        .wiki-layout { grid-template-columns:var(--wk-nav) minmax(0,1fr); gap:2rem; }
        .wiki-aside { display:none; }
    }
    @media (max-width:780px) {
        .wiki-layout { grid-template-columns:1fr; }
        .wiki-nav { display:none; }
        .wiki-page { padding-top:5rem; }
        .wiki-header h1 { font-size:1.85rem; }
    }

    @media (prefers-reduced-motion: reduce) {
        #reading-progress { transition:none; }
    }
</style>
