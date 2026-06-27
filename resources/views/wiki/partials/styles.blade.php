{{-- resources/views/wiki/partials/styles.blade.php --}}
<style>
    /* GitBook-style hint callouts */
    .wiki-hint { display:flex; gap:.75rem; padding:1rem 1.25rem; margin:1.25rem 0; border-radius:.5rem; border-left:4px solid; }
    .wiki-hint-icon { flex-shrink:0; margin-top:.15rem; }
    .wiki-hint-body > :first-child { margin-top:0; }
    .wiki-hint-body > :last-child { margin-bottom:0; }
    .wiki-hint-info    { background:rgba(59,130,246,.10); border-color:#3b82f6; color:#bfdbfe; }
    .wiki-hint-warning { background:rgba(245,158,11,.10); border-color:#f59e0b; color:#fde68a; }
    .wiki-hint-danger  { background:rgba(239,68,68,.10);  border-color:#ef4444; color:#fecaca; }
    .wiki-hint-success { background:rgba(34,197,94,.10);  border-color:#22c55e; color:#bbf7d0; }
    .wiki-hint-info .wiki-hint-icon    { color:#3b82f6; }
    .wiki-hint-warning .wiki-hint-icon { color:#f59e0b; }
    .wiki-hint-danger .wiki-hint-icon  { color:#ef4444; }
    .wiki-hint-success .wiki-hint-icon { color:#22c55e; }

    /* Figures */
    .wiki-content figure { text-align:center; margin:1.5rem 0; }
    .wiki-content figure img { display:inline-block; border-radius:.5rem; }
    .wiki-content figcaption { margin-top:.5rem; font-size:.875rem; color:#9ca3af; }

    /* Details/summary */
    .wiki-content details { border:1px solid #374151; border-radius:.5rem; padding:.5rem 1rem; margin:1rem 0; background:rgba(255,255,255,.02); }
    .wiki-content summary { cursor:pointer; font-weight:600; color:#fbbf24; }
</style>
