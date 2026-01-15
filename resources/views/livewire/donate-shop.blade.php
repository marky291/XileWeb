<div class="bg-clash-bg min-h-screen pt-28 pb-16 px-4" x-data="{ mobileFiltersOpen: false }">
    {{-- Global Loading Bar --}}
    <div wire:loading.delay class="fixed top-0 left-0 right-0 z-50">
        <div class="h-1 bg-amber-500 animate-pulse"></div>
    </div>

    <div class="max-w-6xl w-full mx-auto">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-white mb-1">Uber Shop</h1>
                <p class="text-gray-400">Exchange your Ubers for items. Delivered on next login.</p>
            </div>
            @auth
                <div class="mt-4 sm:mt-0">
                    <div class="px-5 py-3 bg-gray-900 border border-gray-700 rounded-lg flex items-center gap-3">
                        <i class="fas fa-coins text-amber-400 text-xl"></i>
                        <div>
                            <p class="text-xs text-gray-500">Your Balance</p>
                            <p class="text-lg font-bold text-amber-400">{{ number_format($userBalance) }} <span class="text-sm text-gray-400">Ubers</span></p>
                        </div>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}" class="mt-4 sm:mt-0 px-4 py-2 bg-amber-500 hover:bg-amber-400 text-gray-900 font-bold rounded-lg transition-colors flex items-center gap-2">
                    <i class="fas fa-sign-in-alt"></i>
                    Login to Purchase
                </a>
            @endauth
        </div>

        {{-- Flash Messages --}}
        @if (session()->has('success'))
            <div class="mb-6 p-4 bg-green-900/50 border border-green-500 rounded-lg text-green-300">
                {{ session('success') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-6 p-4 bg-red-900/50 border border-red-500 rounded-lg text-red-300">
                {{ session('error') }}
            </div>
        @endif

        {{-- Pending Redemptions --}}
        @auth
            @if ($pendingPurchases->isNotEmpty())
                <div class="mb-6 block-home bg-gray-900 rounded-lg overflow-hidden">
                    <div class="p-4 border-b border-gray-800">
                        <h2 class="text-lg font-semibold text-white">
                            <i class="fas fa-clock text-amber-400 mr-2"></i>
                            Pending Redemption ({{ $pendingPurchases->count() }})
                        </h2>
                        <p class="text-sm text-gray-400 mt-1">Items will be delivered on your next login</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-800/50 text-gray-400 text-left">
                                <tr>
                                    <th class="px-4 py-3 font-medium">Item</th>
                                    <th class="px-4 py-3 font-medium">Server</th>
                                    <th class="px-4 py-3 font-medium">Account</th>
                                    <th class="px-4 py-3 font-medium">Cost</th>
                                    <th class="px-4 py-3 font-medium">Purchased</th>
                                    <th class="px-4 py-3 font-medium"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-800">
                                @foreach ($pendingPurchases as $purchase)
                                    @php
                                        $purchaseGameAccount = $gameAccounts->where('ragnarok_account_id', $purchase->account_id)->first();
                                    @endphp
                                    <tr class="hover:bg-gray-800/30">
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-3">
                                                <span class="w-8 h-8 bg-gray-800 rounded flex items-center justify-center overflow-hidden shrink-0">
                                                    @if ($purchase->shopItem?->icon_path)
                                                        <img src="{{ asset('storage/xilero/items/icons/' . basename($purchase->shopItem->icon_path)) }}" alt="" class="max-h-full max-w-full object-contain">
                                                    @else
                                                        <i class="fas fa-box text-gray-500 text-xs"></i>
                                                    @endif
                                                </span>
                                                <span class="text-white">
                                                    @if ($purchase->refine_level > 0)+{{ $purchase->refine_level }} @endif{{ $purchase->item_name }}@if ($purchase->quantity > 1) <span class="text-gray-400">x{{ $purchase->quantity }}</span>@endif
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-gray-300">{{ $purchaseGameAccount?->serverName() ?? 'Unknown' }}</td>
                                        <td class="px-4 py-3 text-gray-300">{{ $purchase->account_name }}</td>
                                        <td class="px-4 py-3 text-amber-400">{{ $purchase->uber_cost }} Ubers</td>
                                        <td class="px-4 py-3 text-gray-400">{{ $purchase->purchased_at->diffForHumans() }}</td>
                                        <td class="px-4 py-3">
                                            <button
                                                wire:click="cancelPendingPurchase({{ $purchase->id }})"
                                                wire:confirm="Cancel this purchase and refund {{ $purchase->uber_cost }} Ubers?"
                                                class="text-gray-400 hover:text-red-400 transition-colors"
                                                title="Cancel and refund"
                                            >
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            {{-- Purchase History (Claimed items eligible for refund) --}}
            @if ($claimedPurchases->isNotEmpty())
                <div class="mb-6 block-home bg-gray-900 rounded-lg overflow-hidden">
                    <div class="p-4 border-b border-gray-800">
                        <h2 class="text-lg font-semibold text-white">
                            <i class="fas fa-history text-blue-400 mr-2"></i>
                            Recent Purchases
                        </h2>
                        <p class="text-sm text-gray-400 mt-1">Items can be refunded within {{ $refundHours }} hours if still in inventory</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-800/50 text-gray-400 text-left">
                                <tr>
                                    <th class="px-4 py-3 font-medium">Item</th>
                                    <th class="px-4 py-3 font-medium">Server</th>
                                    <th class="px-4 py-3 font-medium">Character</th>
                                    <th class="px-4 py-3 font-medium">Cost</th>
                                    <th class="px-4 py-3 font-medium">Claimed</th>
                                    <th class="px-4 py-3 font-medium"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-800">
                                @foreach ($claimedPurchases as $purchase)
                                    @php
                                        $purchaseGameAccount = $gameAccounts->where('ragnarok_account_id', $purchase->account_id)->first();
                                        $canRefund = $purchase->claimed_at && $purchase->claimed_at->addHours($refundHours)->isFuture();
                                        $refundExpiresAt = $purchase->claimed_at?->addHours($refundHours);
                                    @endphp
                                    <tr class="hover:bg-gray-800/30">
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-3">
                                                <span class="w-8 h-8 bg-gray-800 rounded flex items-center justify-center overflow-hidden shrink-0">
                                                    @if ($purchase->shopItem?->icon_path)
                                                        <img src="{{ asset('storage/xilero/items/icons/' . basename($purchase->shopItem->icon_path)) }}" alt="" class="max-h-full max-w-full object-contain">
                                                    @else
                                                        <i class="fas fa-box text-gray-500 text-xs"></i>
                                                    @endif
                                                </span>
                                                <span class="text-white">
                                                    @if ($purchase->refine_level > 0)+{{ $purchase->refine_level }} @endif{{ $purchase->item_name }}@if ($purchase->quantity > 1) <span class="text-gray-400">x{{ $purchase->quantity }}</span>@endif
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-gray-300">{{ $purchaseGameAccount?->serverName() ?? 'Unknown' }}</td>
                                        <td class="px-4 py-3 text-gray-300">{{ $purchase->claimed_by_char_name ?? '-' }}</td>
                                        <td class="px-4 py-3 text-amber-400">{{ $purchase->uber_cost }} Ubers</td>
                                        <td class="px-4 py-3 text-gray-400">{{ $purchase->claimed_at?->diffForHumans() ?? '-' }}</td>
                                        <td class="px-4 py-3">
                                            @if ($canRefund)
                                                <button
                                                    wire:click="refundPurchase({{ $purchase->id }})"
                                                    wire:confirm="Refund this item? The item will be removed from your character's inventory and {{ $purchase->uber_cost }} Ubers will be returned."
                                                    class="text-blue-400 hover:text-blue-300 transition-colors text-xs font-medium"
                                                    title="Refund expires {{ $refundExpiresAt->diffForHumans() }}"
                                                >
                                                    <i class="fas fa-undo mr-1"></i>Refund
                                                </button>
                                            @else
                                                <span class="text-gray-500 text-xs">Expired</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        @endauth

        {{-- Search & Filters --}}
        <div class="mb-6 block-home bg-gray-900 rounded-lg p-5">
            <div class="flex flex-col lg:flex-row gap-4">
                {{-- Search Input --}}
                <div class="relative flex-1 max-w-md">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-500"></i>
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Search items..."
                        class="w-full pl-10 pr-10 py-2.5 bg-gray-800/50 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-xilero-gold/50 focus:border-xilero-gold transition-colors"
                    >
                    @if ($search)
                        <button wire:click="clearSearch" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-white transition-colors">
                            <i class="fas fa-times"></i>
                        </button>
                    @endif
                </div>

                {{-- Category Pills (Desktop) --}}
                <div class="hidden lg:flex items-center gap-2 flex-wrap">
                    <button
                        wire:click="selectCategory(null)"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ !$category ? 'bg-amber-500 text-gray-900' : 'bg-gray-800 text-gray-300 hover:bg-gray-700' }}"
                    >
                        All Items
                    </button>
                    @foreach ($categories as $cat)
                        <button
                            wire:click="selectCategory('{{ $cat->name }}')"
                            class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ $category === $cat->name ? 'bg-amber-500 text-gray-900' : 'bg-gray-800 text-gray-300 hover:bg-gray-700' }}"
                        >
                            {{ $cat->clean_display_name }}
                        </button>
                    @endforeach
                </div>

                {{-- Mobile Filter Toggle --}}
                <button
                    @click="mobileFiltersOpen = !mobileFiltersOpen"
                    class="lg:hidden px-4 py-2.5 bg-gray-800 border border-gray-700 rounded-lg text-gray-300 hover:bg-gray-700 transition-colors flex items-center gap-2"
                >
                    <i class="fas fa-filter"></i>
                    Categories
                    @if ($category)
                        <span class="bg-amber-500 text-gray-900 text-xs font-bold px-2 py-0.5 rounded">1</span>
                    @endif
                </button>
            </div>

            {{-- Mobile Categories --}}
            <div x-show="mobileFiltersOpen" x-collapse class="lg:hidden mt-4 pt-4 border-t border-gray-800">
                <div class="flex flex-wrap gap-2">
                    <button
                        wire:click="selectCategory(null)"
                        @click="mobileFiltersOpen = false"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ !$category ? 'bg-amber-500 text-gray-900' : 'bg-gray-800 text-gray-300' }}"
                    >
                        All Items
                    </button>
                    @foreach ($categories as $cat)
                        <button
                            wire:click="selectCategory('{{ $cat->name }}')"
                            @click="mobileFiltersOpen = false"
                            class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ $category === $cat->name ? 'bg-amber-500 text-gray-900' : 'bg-gray-800 text-gray-300' }}"
                        >
                            {{ $cat->clean_display_name }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Results Info --}}
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3 flex-wrap">
                @if ($selectedCategory)
                    <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-amber-500/20 text-amber-400 rounded-lg text-sm">
                        {{ $selectedCategory->clean_display_name }}
                        <button wire:click="selectCategory(null)" class="hover:text-amber-200">
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
                <span class="text-white font-medium">{{ $itemCount }}</span>
                @if ($itemCount !== $totalItemCount) of {{ $totalItemCount }} @endif
                {{ Str::plural('item', $itemCount) }}
            </p>
        </div>

        {{-- Items Grid --}}
        <div class="grid gap-4 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3" wire:loading.class="opacity-50">
            @forelse ($items as $item)
                <button
                    wire:key="item-{{ $item->id }}"
                    wire:click="selectItem({{ $item->id }})"
                    class="block-home bg-gray-900 rounded-lg p-4 text-left hover:bg-gray-800/80 transition-colors {{ !$item->is_available ? 'opacity-60' : '' }}"
                >
                    <div class="flex gap-4">
                        {{-- Item Image --}}
                        <div class="shrink-0 w-16 h-20 bg-gray-800 rounded-lg overflow-hidden flex items-center justify-center">
                            @if ($item->icon_path)
                                <img
                                    src="{{ asset('storage/xilero/items/collection/' . basename($item->icon_path)) }}"
                                    alt="{{ $item->raw_name }}"
                                    class="max-h-full max-w-full object-contain"
                                    onerror="this.onerror=null; this.src='{{ asset('storage/xilero/items/icons/' . basename($item->icon_path)) }}';"
                                    loading="lazy"
                                >
                            @else
                                <i class="fas fa-box text-gray-600 text-2xl"></i>
                            @endif
                        </div>

                        {{-- Item Info --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <h3 class="font-semibold text-white truncate">{{ $item->display_name }}</h3>
                                @if ($item->exclusive_server)
                                    <span class="shrink-0 text-[10px] font-medium px-1.5 py-0.5 rounded {{ $item->is_xilero ? 'bg-blue-500/20 text-blue-400' : 'bg-purple-500/20 text-purple-400' }}">
                                        {{ $item->exclusive_server }}
                                    </span>
                                @endif
                            </div>
                            @if ($item->quantity > 1)
                                <span class="text-xs text-gray-500">x{{ $item->quantity }}</span>
                            @endif

                            @if ($item->description)
                                <p class="text-xs text-gray-400 mt-1 line-clamp-2">{{ $item->description }}</p>
                            @elseif ($item->item_type)
                                <p class="text-xs text-gray-500 mt-0.5">
                                    {{ $item->item_type }}{{ $item->item_subtype ? ' / ' . $item->item_subtype : '' }}
                                </p>
                            @endif

                            <div class="mt-2 flex items-center justify-between">
                                <span class="text-amber-400 font-bold">
                                    {{ $item->uber_cost }} {{ Str::plural('Uber', $item->uber_cost) }}
                                </span>
                                @if ($item->stock !== null)
                                    @if ($item->stock > 0)
                                        <span class="text-xs text-green-400">{{ $item->stock }} left</span>
                                    @else
                                        <span class="text-xs text-red-400">Sold Out</span>
                                    @endif
                                @endif
                            </div>
                        </div>
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
                    @if ($search || $category)
                        <button
                            wire:click="$set('search', ''); $wire.selectCategory(null)"
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
    </div>

    {{-- Item Detail Modal --}}
    @if ($selectedItem)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-on:keydown.escape.window="$wire.selectItem(null)">
            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-black/70" wire:click="selectItem(null)"></div>

            {{-- Modal Panel --}}
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative w-full max-w-md block-home bg-gray-900 rounded-lg" @click.stop>
                    {{-- Header --}}
                    <div class="flex items-center justify-between p-5 border-b border-gray-800">
                        <h2 class="text-lg font-semibold text-white">Item Details</h2>
                        <button wire:click="selectItem(null)" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    {{-- Content --}}
                    <div class="p-5">
                        {{-- Item Info --}}
                        <div class="flex gap-4 mb-5">
                            <div class="shrink-0 w-16 h-20 bg-gray-800 rounded-lg overflow-hidden flex items-center justify-center">
                                @if ($selectedItem->icon_path)
                                    <img
                                        src="{{ asset('storage/xilero/items/collection/' . basename($selectedItem->icon_path)) }}"
                                        alt="{{ $selectedItem->item_name }}"
                                        class="max-h-full max-w-full object-contain"
                                        onerror="this.onerror=null; this.src='{{ asset('storage/xilero/items/icons/' . basename($selectedItem->icon_path)) }}';"
                                    >
                                @else
                                    <i class="fas fa-box text-gray-600 text-2xl"></i>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <h3 class="font-semibold text-white">{{ $selectedItem->display_name }}</h3>
                                    @if ($selectedItem->exclusive_server)
                                        <span class="shrink-0 text-[10px] font-medium px-1.5 py-0.5 rounded {{ $selectedItem->is_xilero ? 'bg-blue-500/20 text-blue-400' : 'bg-purple-500/20 text-purple-400' }}">
                                            {{ $selectedItem->exclusive_server }} Only
                                        </span>
                                    @endif
                                </div>
                                @if ($selectedItem->category)
                                    <p class="text-sm text-gray-400">{{ $selectedItem->category->clean_display_name }}</p>
                                @endif
                                <p class="mt-2 text-lg font-bold text-amber-400">
                                    {{ $selectedItem->uber_cost }} {{ Str::plural('Uber', $selectedItem->uber_cost) }}
                                </p>
                            </div>
                        </div>

                        @if ($selectedItem->description)
                            <p class="text-sm text-gray-400 mb-4">{!! nl2br(e($selectedItem->description)) !!}</p>
                        @endif

                        @if ($selectedItem->stock !== null)
                            <div class="mb-4 p-3 rounded-lg {{ $selectedItem->stock > 0 ? 'bg-green-900/50 border border-green-500' : 'bg-red-900/50 border border-red-500' }}">
                                @if ($selectedItem->stock > 0)
                                    <p class="text-green-300 text-sm"><i class="fas fa-check-circle mr-2"></i>{{ $selectedItem->stock }} remaining in stock</p>
                                @else
                                    <p class="text-red-300 text-sm"><i class="fas fa-times-circle mr-2"></i>Currently out of stock</p>
                                @endif
                            </div>
                        @endif

                        {{-- Purchase Actions --}}
                        @auth
                            {{-- Game Account Selector --}}
                            @if ($gameAccounts->count() > 1)
                                <div class="mb-4">
                                    <label for="gameAccountSelect" class="block text-sm text-gray-400 mb-2">Deliver to:</label>
                                    <select
                                        id="gameAccountSelect"
                                        wire:model.live="selectedGameAccountId"
                                        class="w-full px-3 py-2 bg-gray-800 border border-gray-700 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500"
                                    >
                                        @foreach ($gameAccounts as $account)
                                            <option value="{{ $account->id }}">
                                                {{ $account->userid }} — {{ $account->serverName() }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @elseif ($gameAccounts->count() === 1)
                                <p class="text-sm text-gray-400 mb-4">
                                    Deliver to: <span class="text-white">{{ $gameAccounts->first()->userid }}</span> <span class="text-gray-500">— {{ $gameAccounts->first()->serverName() }}</span>
                                </p>
                            @endif

                            @php
                                $canPurchaseForServer = $selectedGameAccount && $selectedItem->isAvailableForServer($selectedGameAccount->server);
                            @endphp

                            @if (! $selectedItem->is_available)
                                <button class="w-full px-4 py-2.5 bg-gray-700 text-gray-500 font-bold rounded-lg cursor-not-allowed" disabled>
                                    Currently Unavailable
                                </button>
                            @elseif (! $canPurchaseForServer)
                                <div class="mb-4 p-3 bg-orange-900/50 border border-orange-500 rounded-lg">
                                    <p class="text-orange-300 text-sm">
                                        <i class="fas fa-exclamation-triangle mr-2"></i>This item is only available for <span class="font-semibold">{{ $selectedItem->exclusive_server }}</span>.
                                        @if ($selectedGameAccount)
                                            Your selected account <span class="font-semibold">{{ $selectedGameAccount->userid }}</span> is on {{ $selectedGameAccount->serverName() }}.
                                        @endif
                                    </p>
                                </div>
                                <button class="w-full px-4 py-2.5 bg-gray-700 text-gray-500 font-bold rounded-lg cursor-not-allowed" disabled>
                                    Not Available for This Server
                                </button>
                            @elseif ($userBalance < $selectedItem->uber_cost)
                                <div class="mb-4 p-3 bg-red-900/50 border border-red-500 rounded-lg">
                                    <p class="text-red-300 text-sm">
                                        <i class="fas fa-exclamation-circle mr-2"></i>You need {{ $selectedItem->uber_cost - $userBalance }} more Ubers.
                                    </p>
                                </div>
                                <button class="w-full px-4 py-2.5 bg-gray-700 text-gray-500 font-bold rounded-lg cursor-not-allowed" disabled>
                                    Insufficient Ubers
                                </button>
                            @elseif ($showPurchaseConfirm)
                                <div class="mb-4 p-4 bg-amber-900/50 border border-amber-500 rounded-lg">
                                    <p class="text-amber-200 font-semibold mb-1">Confirm your purchase</p>
                                    <p class="text-gray-400 text-sm">
                                        Purchase <span class="text-white">{{ $selectedItem->display_name }}</span>
                                        for <span class="text-amber-400 font-semibold">{{ $selectedItem->uber_cost }} {{ Str::plural('Uber', $selectedItem->uber_cost) }}</span>?
                                    </p>
                                </div>
                                <div class="flex gap-3">
                                    <button
                                        wire:click="cancelPurchase"
                                        class="flex-1 px-4 py-2.5 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors"
                                    >
                                        Cancel
                                    </button>
                                    <button
                                        wire:click="purchase"
                                        wire:loading.attr="disabled"
                                        class="flex-1 px-4 py-2.5 bg-green-600 hover:bg-green-500 disabled:bg-gray-600 text-white font-bold rounded-lg transition-colors"
                                    >
                                        <span wire:loading.remove wire:target="purchase">Confirm</span>
                                        <span wire:loading wire:target="purchase"><i class="fas fa-spinner fa-spin mr-1"></i>Processing...</span>
                                    </button>
                                </div>
                            @else
                                <button
                                    wire:click="confirmPurchase"
                                    class="w-full px-4 py-2.5 bg-amber-500 hover:bg-amber-400 text-gray-900 font-bold rounded-lg transition-colors"
                                >
                                    <i class="fas fa-shopping-cart mr-2"></i>Purchase for {{ $selectedItem->uber_cost }} {{ Str::plural('Uber', $selectedItem->uber_cost) }}
                                </button>
                                <p class="text-center text-sm text-gray-500 mt-3">
                                    Balance after: <span class="text-amber-400">{{ number_format($userBalance - $selectedItem->uber_cost) }} Ubers</span>
                                </p>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="block w-full px-4 py-2.5 bg-amber-500 hover:bg-amber-400 text-gray-900 font-bold rounded-lg text-center transition-colors">
                                <i class="fas fa-sign-in-alt mr-2"></i>Login to Purchase
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
