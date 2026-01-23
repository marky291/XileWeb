@section('title', 'Item Database - Complete Item Guide | XileRO')
@section('description', 'Search the complete XileRO item database. Find weapons, armor, cards, and equipment with stats, drop locations, and more.')
@section('keywords', 'XileRO items, Ragnarok Online database, RO item list, equipment guide, card database, drop rates')

<div class="bg-clash-bg min-h-screen pt-28 pb-16 px-4" x-data="{ mobileFiltersOpen: false }">
    {{-- Global Loading Bar --}}
    <div wire:loading.delay class="fixed top-0 left-0 right-0 z-50">
        <div class="h-1 bg-amber-500 animate-pulse"></div>
    </div>

    <div class="max-w-6xl w-full mx-auto">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-white mb-1">Item Database</h1>
                <p class="text-gray-400">Browse and search all items available in XileRO.</p>
            </div>
        </div>

        @auth
        {{-- Search & Filters --}}
        <div class="mb-6 block-home bg-gray-900 rounded-lg p-5 space-y-4">
            {{-- Top Row: Search, Server, Sort --}}
            <div class="flex flex-col sm:flex-row gap-3">
                {{-- Search Input --}}
                <div class="relative flex-1">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-500"></i>
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Search by name, ID, or description..."
                        class="w-full pl-10 pr-10 py-2.5 bg-gray-800/50 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-xilero-gold/50 focus:border-xilero-gold transition-colors"
                    >
                    @if ($search)
                        <button wire:click="clearSearch" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-white transition-colors">
                            <i class="fas fa-times"></i>
                        </button>
                    @endif
                </div>

                {{-- Server Selector --}}
                <div class="flex bg-gray-800 rounded-lg p-1 shrink-0">
                    @foreach ($serverOptions as $value => $label)
                        <button
                            wire:click="$set('server', '{{ $value }}')"
                            class="px-4 py-1.5 rounded-md text-sm font-medium transition-colors {{ $server === $value ? 'bg-amber-500 text-gray-900' : 'text-gray-300 hover:text-white' }}"
                        >
                            {{ $label }}
                        </button>
                    @endforeach
                </div>

                {{-- Sort Dropdown --}}
                <div class="relative shrink-0">
                    <select
                        wire:model.live="sort"
                        class="appearance-none bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 pr-10 text-white text-sm focus:outline-none focus:ring-2 focus:ring-xilero-gold/50 focus:border-xilero-gold cursor-pointer"
                    >
                        @foreach ($sortOptions as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    <i class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 pointer-events-none text-xs"></i>
                </div>
            </div>

            {{-- Type Pills (Desktop) --}}
            <div class="hidden lg:flex items-center gap-2 flex-wrap pt-2 border-t border-gray-800">
                <span class="text-xs text-gray-500 uppercase tracking-wider mr-2">Type:</span>
                <button
                    wire:click="selectType(null)"
                    class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors {{ !$type ? 'bg-amber-500 text-gray-900' : 'bg-gray-800 text-gray-300 hover:bg-gray-700' }}"
                >
                    All
                </button>
                @foreach ($types as $itemType)
                    <button
                        wire:click="selectType('{{ $itemType }}')"
                        class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors {{ $type === $itemType ? 'bg-amber-500 text-gray-900' : 'bg-gray-800 text-gray-300 hover:bg-gray-700' }}"
                    >
                        {{ ucfirst($itemType) }}
                    </button>
                @endforeach
            </div>

            {{-- Mobile Filter Toggle --}}
            <button
                @click="mobileFiltersOpen = !mobileFiltersOpen"
                class="lg:hidden w-full px-4 py-2.5 bg-gray-800 border border-gray-700 rounded-lg text-gray-300 hover:bg-gray-700 transition-colors flex items-center justify-center gap-2"
            >
                <i class="fas fa-filter"></i>
                Filter by Type
                @if ($type)
                    <span class="bg-amber-500 text-gray-900 text-xs font-bold px-2 py-0.5 rounded">{{ ucfirst($type) }}</span>
                @endif
            </button>

            {{-- Mobile Types --}}
            <div x-show="mobileFiltersOpen" x-collapse class="lg:hidden pt-2 border-t border-gray-800">
                <div class="flex flex-wrap gap-2">
                    <button
                        wire:click="selectType(null)"
                        @click="mobileFiltersOpen = false"
                        class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors {{ !$type ? 'bg-amber-500 text-gray-900' : 'bg-gray-800 text-gray-300' }}"
                    >
                        All
                    </button>
                    @foreach ($types as $itemType)
                        <button
                            wire:click="selectType('{{ $itemType }}')"
                            @click="mobileFiltersOpen = false"
                            class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors {{ $type === $itemType ? 'bg-amber-500 text-gray-900' : 'bg-gray-800 text-gray-300' }}"
                        >
                            {{ ucfirst($itemType) }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Results Info --}}
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3 flex-wrap">
                @if ($type)
                    <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-amber-500/20 text-amber-400 rounded-lg text-sm">
                        {{ ucfirst($type) }}
                        <button wire:click="selectType(null)" class="hover:text-amber-200">
                            <i class="fas fa-times"></i>
                        </button>
                    </span>
                @endif
                @if ($search)
                    <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-500/20 text-blue-400 rounded-lg text-sm">
                        "{{ $search }}"
                        <button wire:click="clearSearch" class="hover:text-blue-200">
                            <i class="fas fa-times"></i>
                        </button>
                    </span>
                @endif
            </div>
            <p class="text-sm text-gray-500">
                <span class="text-white font-medium">{{ number_format($itemCount) }}</span>
                {{ Str::plural('item', $itemCount) }}
            </p>
        </div>

        {{-- Items Grid --}}
        <div class="grid gap-4 grid-cols-2 sm:grid-cols-3 lg:grid-cols-4" wire:loading.class="opacity-50">
            @forelse ($items as $item)
                <button
                    wire:key="item-{{ $item->id }}"
                    wire:click="selectItem({{ $item->id }})"
                    class="block-home bg-gray-900 rounded-lg p-4 text-left hover:bg-gray-800/80 transition-colors"
                >
                    <div class="flex flex-col items-center">
                        {{-- Item Icon --}}
                        <div class="w-10 h-10 bg-gray-800 rounded overflow-hidden flex items-center justify-center mb-2">
                            <img
                                src="{{ $item->icon() }}"
                                alt="{{ $item->name }}"
                                class="max-h-full max-w-full object-contain"
                                loading="lazy"
                            >
                        </div>

                        {{-- Item Info --}}
                        <h3 class="font-semibold text-white text-sm text-center truncate w-full">{{ $item->name }}</h3>
                        <p class="text-xs text-gray-500 mt-0.5">ID: {{ $item->item_id }}</p>
                        <p class="text-xs text-gray-400 mt-1">{{ ucfirst($item->type) }}</p>
                    </div>
                </button>
            @empty
                <div class="col-span-full block-home bg-gray-900 rounded-lg p-12 text-center">
                    <i class="fas fa-search text-4xl text-gray-600 mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-300 mb-2">No items found</h3>
                    <p class="text-gray-500">
                        @if ($search)
                            No items match "{{ $search }}". Try a different search term.
                        @else
                            There are no items available in this category.
                        @endif
                    </p>
                    @if ($search || $type)
                        <button
                            wire:click="$set('search', ''); $wire.selectType(null)"
                            class="mt-4 text-amber-400 hover:text-amber-300 font-medium"
                        >
                            <i class="fas fa-redo mr-2"></i>Reset filters
                        </button>
                    @endif
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if ($items->hasPages())
            <div class="mt-8">
                {{ $items->links() }}
            </div>
        @endif
        @else
            <div class="block-home bg-gray-900 rounded-lg p-12 text-center">
                <i class="fas fa-sign-in-alt text-4xl text-gray-600 mb-4" aria-hidden="true"></i>
                <h3 class="text-xl font-semibold text-gray-300 mb-2">Login Required</h3>
                <p class="text-gray-500 mb-4">Please login to browse and search the Item Database.</p>
                <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-amber-500 hover:bg-amber-400 text-gray-900 font-bold rounded-lg transition-colors">
                    <i class="fas fa-sign-in-alt" aria-hidden="true"></i>
                    Login
                </a>
            </div>
        @endauth
    </div>

    {{-- Item Detail Modal --}}
    @if ($selectedItem)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-on:keydown.escape.window="$wire.selectItem(null)">
            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-black/70" wire:click="selectItem(null)"></div>

            {{-- Modal Panel --}}
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative w-full max-w-lg block-home bg-gray-900 rounded-lg" @click.stop>
                    {{-- Header --}}
                    <div class="flex items-start justify-between px-5 pt-4 pb-2 border-b border-gray-800">
                        <h2 class="text-lg font-semibold text-white">Item Details</h2>
                        <button wire:click="selectItem(null)" class="text-gray-400 hover:text-white transition-colors -mt-0.5">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    {{-- Content --}}
                    <div class="p-5">
                        {{-- Item Info --}}
                        <div class="flex gap-4 mb-5">
                            <div class="shrink-0 w-20 h-24 bg-gray-800 rounded-lg overflow-hidden flex items-center justify-center">
                                <img
                                    src="{{ $selectedItem->collection() }}"
                                    alt="{{ $selectedItem->name }}"
                                    class="max-h-full max-w-full object-contain"
                                    onerror="this.onerror=null; this.src='{{ $selectedItem->icon() }}';"
                                >
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="font-semibold text-white text-lg">{{ $selectedItem->name }}</h3>
                                <p class="text-sm text-gray-400">{{ $selectedItem->aegis_name }}</p>
                                <p class="text-sm text-gray-500 mt-1">ID: {{ $selectedItem->item_id }}</p>
                            </div>
                        </div>

                        {{-- Description --}}
                        @if ($selectedItem->description)
                            <div class="text-sm text-gray-800 mb-5 leading-relaxed bg-white rounded-lg p-3">{!! $selectedItem->formattedDescription() !!}</div>
                        @endif

                        {{-- Stats Grid --}}
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div class="bg-gray-800/50 rounded-lg p-3">
                                <p class="text-gray-500 text-xs mb-1">Type</p>
                                <p class="text-white font-medium">{{ ucfirst($selectedItem->type) }}{{ $selectedItem->subtype ? ' / ' . ucfirst($selectedItem->subtype) : '' }}</p>
                            </div>
                            <div class="bg-gray-800/50 rounded-lg p-3">
                                <p class="text-gray-500 text-xs mb-1">Weight</p>
                                <p class="text-white font-medium">{{ $selectedItem->weight / 10 }}</p>
                            </div>
                            @if ($selectedItem->attack > 0)
                                <div class="bg-gray-800/50 rounded-lg p-3">
                                    <p class="text-gray-500 text-xs mb-1">Attack</p>
                                    <p class="text-white font-medium">{{ $selectedItem->attack }}</p>
                                </div>
                            @endif
                            @if ($selectedItem->defense > 0)
                                <div class="bg-gray-800/50 rounded-lg p-3">
                                    <p class="text-gray-500 text-xs mb-1">Defense</p>
                                    <p class="text-white font-medium">{{ $selectedItem->defense }}</p>
                                </div>
                            @endif
                            @if ($selectedItem->slots > 0)
                                <div class="bg-gray-800/50 rounded-lg p-3">
                                    <p class="text-gray-500 text-xs mb-1">Slots</p>
                                    <p class="text-white font-medium">{{ $selectedItem->slots }}</p>
                                </div>
                            @endif
                            <div class="bg-gray-800/50 rounded-lg p-3">
                                <p class="text-gray-500 text-xs mb-1">Buy Price</p>
                                <p class="text-white font-medium">{{ number_format($selectedItem->buy) }}z</p>
                            </div>
                            <div class="bg-gray-800/50 rounded-lg p-3">
                                <p class="text-gray-500 text-xs mb-1">Sell Price</p>
                                <p class="text-white font-medium">{{ number_format($selectedItem->sell) }}z</p>
                            </div>
                            @if ($selectedItem->refineable)
                                <div class="bg-gray-800/50 rounded-lg p-3">
                                    <p class="text-gray-500 text-xs mb-1">Refineable</p>
                                    <p class="text-green-400 font-medium">Yes</p>
                                </div>
                            @endif
                        </div>

                        {{-- Jobs --}}
                        @if ($selectedItem->jobs && count($selectedItem->jobs) > 0)
                            <div class="mt-4">
                                <p class="text-gray-500 text-xs mb-2">Usable By</p>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach ($selectedItem->jobs as $job => $canUse)
                                        @if ($canUse)
                                            <span class="px-2 py-1 bg-blue-500/20 text-blue-400 rounded text-xs">{{ $job }}</span>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Equip Locations --}}
                        @if ($selectedItem->locations && count($selectedItem->locations) > 0)
                            <div class="mt-4">
                                <p class="text-gray-500 text-xs mb-2">Equip Location</p>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach ($selectedItem->locations as $location => $equipped)
                                        @if ($equipped)
                                            <span class="px-2 py-1 bg-purple-500/20 text-purple-400 rounded text-xs">{{ $location }}</span>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
