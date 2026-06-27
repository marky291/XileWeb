{{-- Global wiki search — compact nav trigger + centered modal (Ctrl/⌘K).
     Fuzzy, typo-tolerant, content-aware, across all wikis. Self-contained. --}}
<button type="button" class="xsearch-trigger" id="xsearch-trigger" aria-label="Search the wiki"
        data-index-url="{{ url('/wiki/search-index.json') }}">
    <svg class="xsearch-ico" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2">
        <circle cx="9" cy="9" r="6"/><path d="M14 14l4.5 4.5" stroke-linecap="round"/>
    </svg>
    <span class="xsearch-trigger-label">Search</span>
    <kbd class="xsearch-kbd">Ctrl K</kbd>
</button>

<div class="xsearch-modal" id="xsearch-modal" hidden>
    <div class="xsearch-backdrop" data-close></div>
    <div class="xsearch-dialog" role="dialog" aria-modal="true" aria-label="Search the wiki">
        <div class="xsearch-box">
            <svg class="xsearch-ico" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="9" cy="9" r="6"/><path d="M14 14l4.5 4.5" stroke-linecap="round"/>
            </svg>
            <input type="search" class="xsearch-input" id="xsearch-input" placeholder="Search the wiki…"
                   autocomplete="off" spellcheck="false" aria-label="Search the wiki"
                   aria-controls="xsearch-panel" aria-autocomplete="list">
            <kbd class="xsearch-kbd" data-close>Esc</kbd>
        </div>
        <div class="xsearch-filters" id="xsearch-filters">
            <button type="button" class="xsearch-filter is-active" data-server="">All</button>
            @foreach (config('wiki.servers') as $slug => $cfg)
                <button type="button" class="xsearch-filter is-{{ $slug }}" data-server="{{ $slug }}">{{ $cfg['label'] }}</button>
            @endforeach
        </div>
        <div class="xsearch-panel" id="xsearch-panel" role="listbox"></div>
    </div>
</div>

<style>
    /* Search trigger (lives in the wiki bar) */
    .xsearch-trigger { display:inline-flex; align-items:center; gap:.5rem; min-width:230px; padding:.5rem .8rem; background:rgba(255,255,255,.04); border:1px solid var(--wk-border, rgba(255,255,255,.12)); border-radius:.6rem; color:#9ca3af; font-family:Inter,system-ui,sans-serif; font-size:.85rem; cursor:pointer; transition:border-color .12s,background .12s; }
    .xsearch-trigger .xsearch-trigger-label { flex:1 1 auto; text-align:left; }
    .xsearch-trigger:hover { border-color:#f59e0b; background:rgba(255,255,255,.07); color:#e5e7eb; }
    .xsearch-trigger .xsearch-ico { width:1rem; height:1rem; flex:0 0 auto; }
    .xsearch-trigger-label { color:inherit; }
    .xsearch-kbd { font-size:.62rem; font-family:inherit; color:#9ca3af; background:rgba(255,255,255,.07); border:1px solid rgba(255,255,255,.14); border-radius:.3rem; padding:.05rem .3rem; }

    /* Modal */
    .xsearch-modal { position:fixed; inset:0; z-index:200; font-family:Inter,system-ui,sans-serif; }
    .xsearch-backdrop { position:absolute; inset:0; background:rgba(3,7,12,.7); backdrop-filter:blur(2px); }
    .xsearch-dialog { position:absolute; left:50%; top:12vh; transform:translateX(-50%); width:600px; max-width:92vw; background:#0f141b; border:1px solid #232a36; border-radius:.8rem; box-shadow:0 24px 60px rgba(0,0,0,.6); overflow:hidden; }
    .xsearch-box { display:flex; align-items:center; gap:.6rem; padding:.85rem 1rem; border-bottom:1px solid #1b212b; }
    .xsearch-box .xsearch-ico { width:1.15rem; height:1.15rem; color:#9ca3af; flex:0 0 auto; }
    .xsearch-input { flex:1 1 auto; min-width:0; background:none; border:0; outline:none; color:#e5e7eb; font-size:1rem; }
    .xsearch-input::placeholder { color:#9ca3af; }

    .xsearch-filters { display:flex; gap:.4rem; padding:.6rem .85rem; border-bottom:1px solid #1b212b; }
    .xsearch-filter { font-size:.74rem; font-weight:600; color:#9ca3af; background:rgba(255,255,255,.04); border:1px solid #232a36; border-radius:.45rem; padding:.28rem .65rem; cursor:pointer; font-family:inherit; transition:color .12s, border-color .12s, background .12s; }
    .xsearch-filter:hover { color:#e5e7eb; border-color:#3a4250; }
    .xsearch-filter.is-active { color:#fbbf24; border-color:#f59e0b; background:rgba(245,158,11,.13); }
    .xsearch-filter.is-active.is-xileretro { color:#c084fc; border-color:#a855f7; background:rgba(168,85,247,.13); }

    .xsearch-panel { max-height:60vh; overflow-y:auto; padding:.4rem; }
    .xsearch-panel:empty { display:none; }
    .xsearch-item { display:block; padding:.6rem .7rem; border-radius:.5rem; text-decoration:none; }
    .xsearch-item:hover, .xsearch-item.is-sel { background:rgba(245,158,11,.13); }
    .xsearch-item-top { display:flex; align-items:center; gap:.5rem; }
    .xsearch-item-title { font-size:.9rem; font-weight:600; color:#f3f4f6; }
    .xsearch-item.is-sel .xsearch-item-title, .xsearch-item:hover .xsearch-item-title { color:#fbbf24; }
    .xsearch-badge { flex:0 0 auto; font-size:.6rem; font-weight:700; text-transform:uppercase; letter-spacing:.03em; border-radius:.3rem; padding:.08rem .4rem; }
    .xsearch-badge.is-xilero { color:#fbbf24; background:rgba(245,158,11,.16); }
    .xsearch-badge.is-xileretro { color:#c084fc; background:rgba(168,85,247,.16); }
    .xsearch-snippet { display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; margin-top:.2rem; font-size:.8rem; line-height:1.45; color:#8b919c; }
    .xsearch-snippet mark { background:rgba(245,158,11,.28); color:#fde68a; border-radius:.15rem; padding:0 .1em; }
    .xsearch-empty { padding:1.5rem; text-align:center; color:#8b919c; font-size:.85rem; }
    .xsearch-tip { padding:.55rem .8rem; border-top:1px solid #1b212b; font-size:.72rem; color:#6b7280; display:flex; gap:1rem; }
</style>

<script>
(function () {
    const trigger = document.getElementById('xsearch-trigger');
    const modal = document.getElementById('xsearch-modal');
    if (!trigger || !modal) return;
    const input = document.getElementById('xsearch-input');
    const panel = document.getElementById('xsearch-panel');
    const indexUrl = trigger.dataset.indexUrl;

    const filterEls = Array.from(modal.querySelectorAll('.xsearch-filter'));
    let fuse = null, ready = false, loading = false, sel = -1, filter = '';

    function loadScript(src) {
        return new Promise((res, rej) => {
            if (window.Fuse) return res();
            const s = document.createElement('script');
            s.src = src; s.onload = res; s.onerror = rej; document.head.appendChild(s);
        });
    }

    async function ensureReady() {
        if (ready || loading) return;
        loading = true;
        try {
            await loadScript('https://cdn.jsdelivr.net/npm/fuse.js@7.0.0/dist/fuse.min.js');
            sessionStorage.removeItem('xwiki-index'); // drop any stale cached index
            const data = await fetch(indexUrl, { cache: 'no-store' }).then(r => r.json());
            fuse = new window.Fuse(data, {
                includeScore: true, threshold: 0.4, ignoreLocation: true, minMatchCharLength: 2,
                keys: [
                    { name: 'title', weight: 0.5 }, { name: 'headings', weight: 0.25 },
                    { name: 'description', weight: 0.15 }, { name: 'content', weight: 0.1 },
                ],
            });
            ready = true;
            if (input.value.trim()) run(input.value);
        } catch (e) {
            panel.innerHTML = '<div class="xsearch-empty">Search is unavailable right now.</div>';
        } finally { loading = false; }
    }

    function esc(s) { return s.replace(/[&<>"]/g, c => ({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;' }[c])); }

    function snippet(entry, query) {
        const hay = entry.content || entry.description || entry.headings || '';
        const words = query.toLowerCase().split(/\s+/).filter(w => w.length > 1);
        let at = -1;
        for (const w of words) { const i = hay.toLowerCase().indexOf(w); if (i !== -1 && (at === -1 || i < at)) at = i; }
        let text = at !== -1 ? (at > 50 ? '…' : '') + hay.slice(Math.max(0, at - 50), Math.max(0, at - 50) + 160) + '…' : (entry.description || hay.slice(0, 150));
        let out = esc(text);
        for (const w of words) out = out.replace(new RegExp('(' + w.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + ')', 'ig'), '<mark>$1</mark>');
        return out;
    }

    function render(results, query) {
        if (!query.trim()) { panel.innerHTML = ''; return; }
        if (!results.length) { panel.innerHTML = '<div class="xsearch-empty">No results for “' + esc(query) + '”</div>'; sel = -1; return; }
        panel.innerHTML = results.map((r, i) =>
            '<a class="xsearch-item' + (i === 0 ? ' is-sel' : '') + '" role="option" href="' + r.url + '">' +
                '<span class="xsearch-item-top"><span class="xsearch-item-title">' + esc(r.title) + '</span>' +
                '<span class="xsearch-badge is-' + esc(r.server) + '">' + esc(r.serverLabel) + '</span></span>' +
                '<span class="xsearch-snippet">' + snippet(r, query) + '</span>' +
            '</a>'
        ).join('');
        sel = 0;
    }

    function run(q) {
        if (!ready || !fuse) return;
        let res = fuse.search(q.trim()).map(x => x.item);
        if (filter) res = res.filter(r => r.server === filter);
        render(res.slice(0, 8), q);
    }

    filterEls.forEach(b => b.addEventListener('click', () => {
        filter = b.dataset.server;
        filterEls.forEach(x => x.classList.toggle('is-active', x === b));
        run(input.value);
        input.focus();
    }));

    function open() { modal.hidden = false; document.body.style.overflow = 'hidden'; input.focus(); ensureReady(); }
    function close() { modal.hidden = true; document.body.style.overflow = ''; }

    trigger.addEventListener('click', open);
    // Close on click outside the dialog (or on a data-close element)
    modal.addEventListener('mousedown', e => {
        if (e.target.hasAttribute('data-close') || !e.target.closest('.xsearch-dialog')) close();
    });

    let t;
    input.addEventListener('input', () => { clearTimeout(t); const q = input.value; t = setTimeout(() => ready ? run(q) : ensureReady(), 110); });
    input.addEventListener('keydown', e => {
        const items = Array.from(panel.querySelectorAll('.xsearch-item'));
        if (e.key === 'ArrowDown') { e.preventDefault(); sel = Math.min(sel + 1, items.length - 1); }
        else if (e.key === 'ArrowUp') { e.preventDefault(); sel = Math.max(sel - 1, 0); }
        else if (e.key === 'Enter') { if (items[sel]) location.href = items[sel].getAttribute('href'); return; }
        else if (e.key === 'Escape') { close(); return; }
        else return;
        items.forEach((el, i) => el.classList.toggle('is-sel', i === sel));
        if (items[sel]) items[sel].scrollIntoView({ block: 'nearest' });
    });

    document.addEventListener('keydown', e => {
        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') { e.preventDefault(); modal.hidden ? open() : close(); }
        else if (e.key === 'Escape' && !modal.hidden) { e.preventDefault(); close(); }
    });
})();
</script>
