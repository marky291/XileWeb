@php
    $item = $getRecord()->item;
@endphp

@if($item)
    <img src="{{ $item->icon() }}" alt="{{ $item->name }}" class="w-6 h-6" />
@endif
