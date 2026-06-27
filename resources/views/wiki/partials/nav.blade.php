{{-- resources/views/wiki/partials/nav.blade.php
     Recursive SUMMARY nav with collapsible groups. expects: $items, $currentUrl --}}
<ul class="wiki-nav-list">
    @foreach ($items as $item)
        @php $hasChildren = ! empty($item['children']); @endphp
        <li class="{{ $hasChildren ? 'wiki-nav-item has-children' : 'wiki-nav-item' }}">
            <div class="wiki-nav-row">
                <a href="{{ $item['url'] }}"
                   class="wiki-nav-link{{ $item['url'] === $currentUrl ? ' is-active' : '' }}">
                    {{ $item['label'] }}
                </a>
                @if ($hasChildren)
                    <button type="button" class="wiki-nav-toggle" aria-label="Toggle section" aria-expanded="true">
                        <svg viewBox="0 0 20 20" fill="currentColor"><path d="M7.5 5l5 5-5 5z"/></svg>
                    </button>
                @endif
            </div>
            @if ($hasChildren)
                <div class="wiki-nav-children">
                    @include('wiki.partials.nav', ['items' => $item['children'], 'currentUrl' => $currentUrl])
                </div>
            @endif
        </li>
    @endforeach
</ul>
