{{-- resources/views/wiki/partials/nav.blade.php --}}
{{-- expects: $items (array of ['label','url','children']), $currentUrl --}}
<ul class="space-y-1">
    @foreach ($items as $item)
        @php $active = ($item['url'] === $currentUrl); @endphp
        <li>
            <a href="{{ $item['url'] }}"
               class="block px-3 py-1.5 rounded text-sm transition-colors
                      {{ $active ? 'bg-amber-600/20 text-amber-400 font-semibold' : 'text-gray-300 hover:text-amber-400 hover:bg-white/5' }}">
                {{ $item['label'] }}
            </a>
            @if (! empty($item['children']))
                <div class="ml-3 mt-1 border-l border-gray-800 pl-2">
                    @include('wiki.partials.nav', ['items' => $item['children'], 'currentUrl' => $currentUrl])
                </div>
            @endif
        </li>
    @endforeach
</ul>
