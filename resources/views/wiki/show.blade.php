{{-- resources/views/wiki/show.blade.php
     GitBook-style docs UI. Layout + content are styled by partials/styles.blade.php
     with self-contained CSS (no Tailwind build step), so rendering is reliable. --}}
<x-app-layout>
    @section('title', config("wiki.servers.$server.label") . ' Wiki: ' . $title)
    @section('description', $subtitle ?? 'XileRO Wiki')

    <div id="reading-progress"></div>

    <div class="wiki-shell">
    @include('wiki.partials.topbar', ['server' => $server])

    <div class="wiki-page" data-server="{{ $server }}">
        <nav class="wiki-breadcrumbs" aria-label="Breadcrumb">
            <a href="/wiki">Wiki</a>
            @foreach ($breadcrumbs as $c)
                <span class="sep">/</span>
                @if (! $loop->last)
                    <a href="{{ $c['url'] }}">{{ $c['name'] }}</a>
                @else
                    <span class="current">{{ $c['name'] }}</span>
                @endif
            @endforeach
        </nav>

        <div class="wiki-layout">
            {{-- LEFT: SUMMARY navigation --}}
            <aside class="wiki-nav">
                <div class="wiki-nav-inner">
                    {{-- Server switcher — makes the current wiki (and the other) obvious --}}
                    <div class="wiki-serverbar" role="tablist" aria-label="Choose wiki">
                        @foreach (config('wiki.servers') as $slug => $cfg)
                            <a href="/wiki/{{ $slug }}"
                               class="wiki-server-pill is-{{ $slug }}{{ $slug === $server ? ' is-current' : '' }}"
                               @if ($slug === $server) aria-current="page" @endif>
                                {{ $cfg['label'] }}
                                <span class="wiki-server-rate">{{ $cfg['rate'] ?? '' }}</span>
                            </a>
                        @endforeach
                    </div>
                    @foreach ($nav as $section)
                        @if ($section['title'])
                            <div class="wiki-nav-section">{{ $section['title'] }}</div>
                        @endif
                        @include('wiki.partials.nav', ['items' => $section['items'], 'currentUrl' => $currentUrl])
                    @endforeach
                </div>
            </aside>

            {{-- CENTER: content --}}
            <main class="wiki-main">
                <header class="wiki-header">
                    <h1>{{ $title }}</h1>
                    @if ($subtitle)
                        <p class="wiki-subtitle">{{ $subtitle }}</p>
                    @endif
                </header>
                <article id="wiki-article" class="wiki-content">
                    {!! $html !!}
                </article>
            </main>

            {{-- RIGHT: On this page (built from headings via JS) --}}
            <aside class="wiki-aside">
                <div class="wiki-aside-inner">
                    <div id="wiki-toc-label" class="wiki-aside-label" hidden>On this page</div>
                    <nav id="wiki-toc" class="wiki-toc"></nav>
                </div>
            </aside>
        </div>
    </div>
    </div>

    @include('wiki.partials.styles')

    <script>
    (function () {
        const article = document.getElementById('wiki-article');
        if (!article) return;
        const toc = document.getElementById('wiki-toc');
        const tocLabel = document.getElementById('wiki-toc-label');
        const progress = document.getElementById('reading-progress');

        const heads = Array.from(article.querySelectorAll('h2, h3'));
        const links = [];

        heads.forEach(h => {
            if (!h.id) h.id = (h.textContent || '').trim().toLowerCase().replace(/[^\w]+/g, '-').replace(/^-+|-+$/g, '');

            // GitBook-style "#" anchor that appears on heading hover
            const anchor = document.createElement('a');
            anchor.href = '#' + h.id;
            anchor.className = 'wiki-anchor';
            anchor.setAttribute('aria-label', 'Link to this section');
            anchor.textContent = '#';
            h.appendChild(anchor);

            if (toc) {
                const t = document.createElement('a');
                t.href = '#' + h.id;
                t.textContent = (h.textContent || '').replace(/#$/, '').trim();
                t.className = 'wiki-toc-link' + (h.tagName === 'H3' ? ' is-sub' : '');
                t.dataset.target = h.id;
                toc.appendChild(t);
                links.push(t);
            }
        });
        if (links.length && tocLabel) tocLabel.hidden = false;

        // Smooth in-page scrolling for hash links
        document.querySelectorAll('a[href^="#"]').forEach(a => {
            a.addEventListener('click', e => {
                const id = decodeURIComponent(a.getAttribute('href').slice(1));
                const el = document.getElementById(id);
                if (el) {
                    e.preventDefault();
                    el.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    history.replaceState(null, '', '#' + id);
                }
            });
        });

        // Reading progress + scroll-spy
        function onScroll() {
            if (progress) {
                const docH = document.documentElement.scrollHeight - window.innerHeight;
                progress.style.width = (docH > 0 ? Math.min(100, (window.scrollY / docH) * 100) : 0) + '%';
            }
            let active = null;
            for (const h of heads) {
                if (h.getBoundingClientRect().top < 140) active = h.id; else break;
            }
            links.forEach(l => l.classList.toggle('is-active', l.dataset.target === active));
        }
        window.addEventListener('scroll', onScroll, { passive: true });
        window.addEventListener('resize', onScroll, { passive: true });
        onScroll();

        // Collapsible sidebar groups (GitBook behavior):
        //  - collapsed by default, except the group(s) on the active path
        //  - the chevron toggles in place
        //  - clicking a parent navigates to its page (it expands on arrival);
        //    clicking the page you're already on toggles it instead of reloading
        document.querySelectorAll('.wiki-nav-item.has-children').forEach(li => {
            const row = li.querySelector(':scope > .wiki-nav-row');
            const link = row.querySelector('.wiki-nav-link');
            const toggleBtn = row.querySelector('.wiki-nav-toggle');
            const onActivePath = li.querySelector('.wiki-nav-link.is-active') !== null;

            if (! onActivePath) li.classList.add('is-collapsed');

            const toggle = () => {
                const collapsed = li.classList.toggle('is-collapsed');
                if (toggleBtn) toggleBtn.setAttribute('aria-expanded', String(! collapsed));
            };

            if (toggleBtn) {
                toggleBtn.setAttribute('aria-expanded', String(! li.classList.contains('is-collapsed')));
                toggleBtn.addEventListener('click', e => { e.preventDefault(); e.stopPropagation(); toggle(); });
            }
            if (link && link.classList.contains('is-active')) {
                link.addEventListener('click', e => { e.preventDefault(); toggle(); });
            }
        });
    })();
    </script>
</x-app-layout>
