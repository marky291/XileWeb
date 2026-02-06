@section('title', 'Donation Rewards - XileRO')
@section('description', 'Claim your donation bonus rewards on XileRO. View pending and claimed reward items from your donations.')

<div class="bg-clash-bg min-h-screen pt-28 pb-16 px-4">
    {{-- Global Loading Bar --}}
    <div wire:loading.delay class="fixed top-0 left-0 right-0 z-50">
        <div class="h-1 bg-amber-500 animate-pulse"></div>
    </div>

    <div class="max-w-4xl w-full mx-auto">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-white mb-1">Donation Rewards</h1>
                <p class="text-gray-400">Claim bonus items from your donations.</p>
            </div>
            @auth
                <div class="mt-4 sm:mt-0">
                    @if ($gameAccounts->isNotEmpty())
                        <div class="flex bg-gray-900 border border-gray-700 rounded-xl overflow-hidden">
                            {{-- Game Account Selector --}}
                            <div class="flex items-center gap-3 px-4 py-3">
                                <div class="w-10 h-10 rounded-lg bg-gray-800 flex items-center justify-center shrink-0">
                                    <i class="fas fa-gamepad text-gray-400 text-lg"></i>
                                </div>
                                <div>
                                    <p class="text-[10px] uppercase tracking-wider text-gray-500 font-medium mb-0.5">Claim to</p>
                                    <select
                                        id="gameAccountSelect"
                                        wire:model.live="selectedGameAccountId"
                                        class="bg-gray-800 text-white text-sm font-semibold border border-gray-600 rounded-lg px-3 py-1.5 pr-8 focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500 cursor-pointer appearance-none"
                                        style="background-image: url('data:image/svg+xml;charset=UTF-8,%3csvg xmlns=%27http://www.w3.org/2000/svg%27 viewBox=%270 0 24 24%27 fill=%27none%27 stroke=%27%239ca3af%27 stroke-width=%272%27 stroke-linecap=%27round%27 stroke-linejoin=%27round%27%3e%3cpolyline points=%276 9 12 15 18 9%27%3e%3c/polyline%3e%3c/svg%3e'); background-repeat: no-repeat; background-position: right 0.5rem center; background-size: 1rem;"
                                    >
                                        @foreach ($gameAccounts as $account)
                                            <option value="{{ $account->id }}" class="bg-gray-800 text-white py-2">
                                                {{ $account->userid }} - {{ $account->server === 'xilero' ? 'XileRO' : 'XileRetro' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('account.game-accounts') }}" class="px-4 py-2 bg-amber-500 hover:bg-amber-400 text-gray-900 font-bold rounded-lg transition-colors flex items-center gap-2">
                            <i class="fas fa-plus"></i>
                            Create Game Account
                        </a>
                    @endif
                </div>
            @else
                <a href="{{ route('login') }}" class="mt-4 sm:mt-0 px-4 py-2 bg-amber-500 hover:bg-amber-400 text-gray-900 font-bold rounded-lg transition-colors flex items-center gap-2">
                    <i class="fas fa-sign-in-alt"></i>
                    Login to View Rewards
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

        @auth
            {{-- Pending Rewards --}}
            <div class="mb-6 block-home bg-gray-900 rounded-lg overflow-hidden">
                <div class="p-4 border-b border-gray-800">
                    <h2 class="text-lg font-semibold text-white">
                        <i class="fas fa-gift text-amber-400 mr-2"></i>
                        Pending Rewards ({{ $totalPendingCount }})
                    </h2>
                    <p class="text-sm text-gray-400 mt-1">
                        @if ($selectedGameAccount)
                            Showing rewards claimable on {{ $selectedGameAccount->serverName() }}
                        @else
                            Select a game account to see available rewards
                        @endif
                    </p>
                </div>

                @if ($pendingRewards->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-800/50 text-gray-400 text-left">
                                <tr>
                                    <th class="px-4 py-3 font-medium">Item</th>
                                    <th class="px-4 py-3 font-medium">Tier</th>
                                    <th class="px-4 py-3 font-medium">Server</th>
                                    <th class="px-4 py-3 font-medium">Received</th>
                                    <th class="px-4 py-3 font-medium"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-800">
                                @foreach ($pendingRewards as $reward)
                                    <tr class="hover:bg-gray-800/30">
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-3">
                                                <span class="w-8 h-8 bg-gray-800 rounded flex items-center justify-center overflow-hidden shrink-0">
                                                    @if ($reward->item)
                                                        <img src="{{ $reward->item->icon() }}" alt="" class="max-h-full max-w-full object-contain">
                                                    @else
                                                        <i class="fas fa-box text-gray-500 text-xs"></i>
                                                    @endif
                                                </span>
                                                <span class="text-white">
                                                    @if ($reward->refine_level > 0)+{{ $reward->refine_level }} @endif{{ $reward->item?->name ?? 'Unknown Item' }}@if ($reward->quantity > 1) <span class="text-gray-400">x{{ $reward->quantity }}</span>@endif
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-900/50 text-amber-300">
                                                {{ $reward->tier?->name ?? 'Unknown' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3">
                                            @if ($reward->is_xilero && $reward->is_xileretro)
                                                <span class="text-gray-300">Both</span>
                                            @elseif ($reward->is_xilero)
                                                <span class="text-purple-300">XileRO</span>
                                            @else
                                                <span class="text-amber-300">XileRetro</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-gray-400">{{ $reward->created_at->diffForHumans() }}</td>
                                        <td class="px-4 py-3">
                                            @if ($selectedGameAccount && $reward->canBeClaimedBy($selectedGameAccount))
                                                <button
                                                    wire:click="startClaim({{ $reward->id }})"
                                                    class="px-3 py-1.5 bg-amber-500 hover:bg-amber-400 text-gray-900 font-semibold text-xs rounded-lg transition-colors"
                                                >
                                                    <i class="fas fa-check mr-1"></i> Claim
                                                </button>
                                            @else
                                                <span class="text-gray-500 text-xs">
                                                    @if (!$selectedGameAccount)
                                                        Select account
                                                    @else
                                                        Wrong server
                                                    @endif
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-8 text-center">
                        <i class="fas fa-gift text-gray-600 text-4xl mb-4"></i>
                        <p class="text-gray-400">
                            @if ($selectedGameAccount)
                                No pending rewards for {{ $selectedGameAccount->serverName() }}.
                            @else
                                No pending rewards. Select a game account to see available rewards.
                            @endif
                        </p>
                    </div>
                @endif
            </div>

            {{-- Claim History --}}
            @if ($claimedRewards->isNotEmpty())
                <div class="block-home bg-gray-900 rounded-lg overflow-hidden">
                    <div class="p-4 border-b border-gray-800">
                        <h2 class="text-lg font-semibold text-white">
                            <i class="fas fa-history text-gray-400 mr-2"></i>
                            Claim History
                        </h2>
                        <p class="text-sm text-gray-400 mt-1">Recently claimed rewards</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-800/50 text-gray-400 text-left">
                                <tr>
                                    <th class="px-4 py-3 font-medium">Item</th>
                                    <th class="px-4 py-3 font-medium">Tier</th>
                                    <th class="px-4 py-3 font-medium">Account</th>
                                    <th class="px-4 py-3 font-medium">Claimed</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-800">
                                @foreach ($claimedRewards as $reward)
                                    <tr class="hover:bg-gray-800/30">
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-3">
                                                <span class="w-8 h-8 bg-gray-800 rounded flex items-center justify-center overflow-hidden shrink-0">
                                                    @if ($reward->item)
                                                        <img src="{{ $reward->item->icon() }}" alt="" class="max-h-full max-w-full object-contain">
                                                    @else
                                                        <i class="fas fa-box text-gray-500 text-xs"></i>
                                                    @endif
                                                </span>
                                                <span class="text-white">
                                                    @if ($reward->refine_level > 0)+{{ $reward->refine_level }} @endif{{ $reward->item?->name ?? 'Unknown Item' }}@if ($reward->quantity > 1) <span class="text-gray-400">x{{ $reward->quantity }}</span>@endif
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-900/50 text-green-300">
                                                {{ $reward->tier?->name ?? 'Unknown' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-gray-300">
                                            {{ $gameAccounts->where('ragnarok_account_id', $reward->claimed_account_id)->first()?->userid ?? 'Unknown' }}
                                        </td>
                                        <td class="px-4 py-3 text-gray-400">{{ $reward->claimed_at?->diffForHumans() ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        @else
            {{-- Guest View --}}
            <div class="block-home bg-gray-900 rounded-lg p-8 text-center">
                <i class="fas fa-gift text-amber-500 text-5xl mb-4"></i>
                <h2 class="text-xl font-bold text-white mb-2">Donation Bonus Rewards</h2>
                <p class="text-gray-400 mb-6">Log in to view and claim your pending donation rewards.</p>
                <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-amber-500 hover:bg-amber-400 text-gray-900 font-bold rounded-lg transition-colors">
                    <i class="fas fa-sign-in-alt"></i>
                    Log In
                </a>
            </div>
        @endauth
    </div>

    {{-- Claim Confirmation Modal --}}
    @if ($showClaimConfirm && $claimingReward)
        <div class="fixed inset-0 bg-black/80 flex items-center justify-center z-50 p-4" x-data x-on:keydown.escape.window="$wire.cancelClaim()">
            <div class="bg-gray-900 rounded-xl max-w-md w-full border border-gray-700 shadow-2xl" x-on:click.away="$wire.cancelClaim()">
                <div class="p-6 border-b border-gray-800">
                    <h3 class="text-xl font-bold text-white">Claim Reward</h3>
                    <p class="text-gray-400 text-sm mt-1">Confirm to claim the item to your account</p>
                </div>
                <div class="p-6">
                    {{-- Item Preview --}}
                    <div class="flex items-center gap-4 p-4 bg-gray-800 rounded-lg mb-6">
                        <span class="w-12 h-12 bg-gray-700 rounded flex items-center justify-center overflow-hidden shrink-0">
                            @if ($claimingReward->item)
                                <img src="{{ $claimingReward->item->icon() }}" alt="" class="max-h-full max-w-full object-contain">
                            @else
                                <i class="fas fa-box text-gray-500"></i>
                            @endif
                        </span>
                        <div>
                            <p class="text-white font-semibold">
                                @if ($claimingReward->refine_level > 0)+{{ $claimingReward->refine_level }} @endif{{ $claimingReward->item?->name ?? 'Unknown Item' }}
                            </p>
                            <p class="text-gray-400 text-sm">
                                Quantity: {{ $claimingReward->quantity }} &bull; {{ $claimingReward->tier?->name ?? 'Unknown Tier' }}
                            </p>
                        </div>
                    </div>

                    {{-- Delivery Account --}}
                    <div class="mb-4 p-3 bg-gray-800/50 rounded-lg">
                        <p class="text-gray-400 text-xs uppercase tracking-wider mb-1">Delivering to</p>
                        <p class="text-white font-semibold">{{ $selectedGameAccount?->userid }} ({{ $selectedGameAccount?->serverName() }})</p>
                    </div>

                    <p class="text-sm text-gray-400 mb-6">Item will be delivered to your account storage on next login.</p>

                    {{-- Actions --}}
                    <div class="flex gap-3">
                        <button
                            wire:click="cancelClaim"
                            class="flex-1 px-4 py-3 bg-gray-700 hover:bg-gray-600 text-white font-semibold rounded-lg transition-colors"
                        >
                            Cancel
                        </button>
                        <button
                            wire:click="claim"
                            wire:loading.attr="disabled"
                            class="flex-1 px-4 py-3 bg-amber-500 hover:bg-amber-400 disabled:bg-amber-500/50 text-gray-900 font-bold rounded-lg transition-colors"
                        >
                            <span wire:loading.remove wire:target="claim">
                                <i class="fas fa-check mr-2"></i> Claim Item
                            </span>
                            <span wire:loading wire:target="claim">
                                <i class="fas fa-spinner fa-spin mr-2"></i> Claiming...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
