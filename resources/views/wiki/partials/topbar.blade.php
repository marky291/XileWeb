{{-- Sticky glass wiki bar under the site nav: current-wiki heading + search.
     expects: $server (slug). --}}
@php
    $sv = $server ?? config('wiki.default');
    $lbl = config("wiki.servers.{$sv}.label") ?? 'XileRO';
    $rate = config("wiki.servers.{$sv}.rate");
@endphp
<div class="wiki-topbar" data-server="{{ $sv }}">
    <div class="wiki-topbar-inner">
        <a href="/wiki/{{ $sv }}" class="wiki-topbar-id">
            <span class="wiki-topbar-dot"></span>
            <span class="wiki-topbar-title">{{ $lbl }} <span>Wiki</span></span>
            @if ($rate)<span class="wiki-topbar-rate">{{ $rate }}</span>@endif
        </a>
        <div class="wiki-topbar-actions">
            @include('layouts.partials.wiki-search')
        </div>
    </div>
</div>
