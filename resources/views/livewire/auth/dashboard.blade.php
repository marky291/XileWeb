@section('title', 'My Account Dashboard | XileRO')
@section('description', 'Manage your XileRO game accounts, view your Uber balance, and access account settings.')
@section('robots', 'noindex, nofollow')

<section class="bg-clash-bg min-h-screen pt-28 pb-16 px-4">
    {{-- Loading --}}
    <div wire:loading.delay class="fixed top-0 left-0 right-0 z-50">
        <div class="h-1 bg-amber-500 animate-pulse"></div>
    </div>

    <div class="max-w-4xl w-full mx-auto">
        {{-- Flash Messages --}}
        @if (session()->has('success'))
            <div class="mb-6 p-4 bg-green-900/50 border border-green-500/50 rounded-lg text-green-300 text-sm flex items-center gap-3">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-6 p-4 bg-red-900/50 border border-red-500/50 rounded-lg text-red-300 text-sm flex items-center gap-3">
                <i class="fas fa-exclamation-circle"></i>
                {{ session('error') }}
            </div>
        @endif

        {{-- Account Header Card --}}
        <div class="card-glow-wrapper mb-8">
            <div class="card-glow-inner p-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6">
                    {{-- User Info --}}
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-amber-500/20 to-amber-600/10 flex items-center justify-center border border-amber-500/20">
                            <i class="fas fa-user text-xl text-amber-400"></i>
                        </div>
                        <div>
                            <h1 class="text-xl font-bold text-gray-100">{{ $user->name }}</h1>
                            <p class="text-sm text-gray-500">{{ $user->email }}</p>
                        </div>
                    </div>

                    {{-- Uber Balance & Actions --}}
                    <div class="flex items-center gap-4">
                        {{-- Balance --}}
                        <div class="text-right">
                            <p class="text-xs text-gray-500 uppercase tracking-wide">Uber Balance</p>
                            <p class="text-2xl font-bold text-amber-400">{{ number_format($user->uber_balance) }}</p>
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center gap-2">
                            @if($user->uber_balance < 500)
                                <a href="{{ url('/donate') }}" class="px-4 py-2.5 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-gray-900 font-bold rounded-lg transition-all text-sm">
                                    Top Up
                                </a>
                            @else
                                <a href="{{ route('donate-shop') }}" class="px-4 py-2.5 bg-amber-500/10 hover:bg-amber-500/20 text-amber-400 font-medium rounded-lg transition-colors text-sm border border-amber-500/20">
                                    Shop
                                </a>
                            @endif
                            <a href="https://discord.gg/eAVAkE5FyT" target="_blank" rel="noopener noreferrer"
                                class="p-2.5 bg-gray-800 hover:bg-indigo-900/50 text-gray-400 hover:text-indigo-400 rounded-lg transition-colors border border-gray-700" title="Discord">
                                <i class="fab fa-discord"></i>
                            </a>
                            <button wire:click="refreshData" wire:loading.attr="disabled" wire:target="refreshData"
                                class="p-2.5 bg-gray-800 hover:bg-gray-700 text-gray-400 rounded-lg transition-colors border border-gray-700" title="Sync characters">
                                <i class="fas fa-sync-alt" wire:loading.class="fa-spin" wire:target="refreshData"></i>
                            </button>
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="p-2.5 bg-gray-800 hover:bg-red-900/50 text-gray-400 hover:text-red-400 rounded-lg transition-colors border border-gray-700" title="Logout">
                                    <i class="fas fa-sign-out-alt"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Game Accounts Section --}}
        <div class="space-y-6">
            {{-- Section Header --}}
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-100">Game Accounts</h2>
                @if($canCreateMore && $gameAccounts->isNotEmpty() && !$showCreateForm)
                    <button wire:click="$set('showCreateForm', true)" class="text-sm text-amber-400 hover:text-amber-300 font-medium flex items-center gap-2">
                        <i class="fas fa-plus"></i> Create Account
                    </button>
                @endif
            </div>

            {{-- Create Form --}}
            @if($canCreateMore && ($gameAccounts->isEmpty() || $showCreateForm))
                <div class="card-glow-wrapper">
                    <div class="card-glow-inner p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-100">
                                {{ $gameAccounts->isEmpty() ? 'Create Your First Game Account' : 'Create New Account' }}
                            </h3>
                            @if($gameAccounts->isNotEmpty())
                                <button wire:click="$set('showCreateForm', false)" class="text-gray-400 hover:text-white"><i class="fas fa-times"></i></button>
                            @endif
                        </div>

                        @if($gameAccounts->isEmpty())
                            <p class="text-gray-400 text-sm mb-6">Create your in-game login credentials to start playing.</p>
                        @endif

                        <form wire:submit="createGameAccount" class="space-y-4">
                            <div class="grid grid-cols-2 gap-3">
                                <label class="cursor-pointer">
                                    <input type="radio" wire:model="gameServer" value="xilero" class="peer sr-only" />
                                    <div class="p-4 rounded-lg border-2 transition-all text-center peer-checked:border-amber-500 peer-checked:bg-amber-500/10 border-gray-700 hover:border-gray-600">
                                        <p class="font-semibold text-white">XileRO</p>
                                        <p class="text-sm text-gray-400">MidRate Server</p>
                                    </div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" wire:model="gameServer" value="xileretro" class="peer sr-only" />
                                    <div class="p-4 rounded-lg border-2 transition-all text-center peer-checked:border-purple-500 peer-checked:bg-purple-500/10 border-gray-700 hover:border-gray-600">
                                        <p class="font-semibold text-white">XileRetro</p>
                                        <p class="text-sm text-gray-400">HighRate Server</p>
                                    </div>
                                </label>
                            </div>
                            @error('gameServer')<p class="text-xs text-red-400">{{ $message }}</p>@enderror

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-300 mb-1">Username</label>
                                    <input wire:model.live="gameUsername" type="text" placeholder="4-23 chars"
                                        class="w-full px-4 py-2.5 bg-gray-800/50 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500">
                                    @error('gameUsername')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-300 mb-1">Password</label>
                                    <input wire:model="gamePassword" type="password" placeholder="6-31 chars"
                                        class="w-full px-4 py-2.5 bg-gray-800/50 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500">
                                    @error('gamePassword')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-300 mb-1">Confirm</label>
                                    <input wire:model="gamePassword_confirmation" type="password" placeholder="Confirm"
                                        class="w-full px-4 py-2.5 bg-gray-800/50 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500">
                                </div>
                            </div>

                            <button type="submit" class="px-6 py-3 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-gray-900 font-bold rounded-lg transition-all"
                                wire:loading.attr="disabled" wire:loading.class="opacity-75">
                                <span wire:loading.remove wire:target="createGameAccount">Create Account</span>
                                <span wire:loading wire:target="createGameAccount"><i class="fas fa-spinner fa-spin mr-2"></i>Creating...</span>
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            {{-- Accounts List --}}
            @if($gameAccounts->isNotEmpty())
                @php
                    $xileroAccounts = $gameAccounts->where('server', 'xilero');
                    $xileretroAccounts = $gameAccounts->where('server', 'xileretro');
                @endphp

                {{-- XileRO --}}
                @if($xileroAccounts->isNotEmpty())
                    <div class="space-y-3">
                        <h3 class="text-sm font-medium text-amber-400 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                            XileRO <span class="text-gray-500 font-normal normal-case">(MidRate)</span>
                        </h3>
                        @foreach($xileroAccounts as $account)
                            @include('livewire.auth.partials.game-account-card', [
                                'account' => $account,
                                'pendingRewards' => $pendingRewardsByServer['xilero'] ?? collect(),
                            ])
                        @endforeach
                    </div>
                @endif

                {{-- XileRetro --}}
                @if($xileretroAccounts->isNotEmpty())
                    <div class="space-y-3">
                        <h3 class="text-sm font-medium text-purple-400 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-purple-400"></span>
                            XileRetro <span class="text-gray-500 font-normal normal-case">(HighRate)</span>
                        </h3>
                        @foreach($xileretroAccounts as $account)
                            @include('livewire.auth.partials.game-account-card', [
                                'account' => $account,
                                'pendingRewards' => $pendingRewardsByServer['xileretro'] ?? collect(),
                            ])
                        @endforeach
                    </div>
                @endif
            @else
                <div class="card-glow-wrapper">
                    <div class="card-glow-inner p-12 text-center">
                        <i class="fas fa-gamepad text-4xl text-gray-600 mb-4"></i>
                        <h3 class="text-lg font-semibold text-gray-300 mb-2">No Game Accounts Yet</h3>
                        <p class="text-gray-500">Create your first account above to start playing!</p>
                    </div>
                </div>
            @endif

            {{-- Slots remaining --}}
            @if($gameAccounts->isNotEmpty())
                <p class="text-center text-sm text-gray-600">
                    {{ $gameAccounts->count() }}/{{ $user->max_game_accounts }} account slots used
                </p>
            @endif
        </div>
    </div>

    {{-- Password Reset Modal --}}
    @if($resettingPasswordFor)
        @php $resettingAccount = $gameAccounts->firstWhere('id', $resettingPasswordFor); @endphp
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4" wire:click.self="cancelPasswordReset">
            <div class="card-glow-wrapper max-w-md w-full">
                <div class="card-glow-inner p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-white">Reset Password</h3>
                        <button wire:click="cancelPasswordReset" class="text-gray-400 hover:text-white"><i class="fas fa-times"></i></button>
                    </div>
                    <p class="text-gray-400 text-sm mb-6">Enter a new password for <span class="text-amber-400 font-medium">{{ $resettingAccount?->userid }}</span></p>
                    <form wire:submit="resetPassword" class="space-y-4">
                        <div>
                            <input wire:model="newPassword" type="password" placeholder="New password (6-31 chars)" autofocus
                                class="w-full px-4 py-3 bg-gray-800/50 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500">
                            @error('newPassword')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <input wire:model="newPassword_confirmation" type="password" placeholder="Confirm password"
                                class="w-full px-4 py-3 bg-gray-800/50 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500">
                        </div>
                        <div class="flex gap-3">
                            <button type="button" wire:click="cancelPasswordReset" class="flex-1 px-4 py-3 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors">Cancel</button>
                            <button type="submit" wire:loading.attr="disabled" class="flex-1 px-4 py-3 bg-amber-500 hover:bg-amber-400 text-gray-900 font-bold rounded-lg transition-colors">
                                <span wire:loading.remove wire:target="resetPassword">Reset Password</span>
                                <span wire:loading wire:target="resetPassword">Resetting...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- Reward Claim Modal --}}
    @if($showClaimConfirm && $claimingReward)
        <div class="fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center z-50 p-4" x-data x-on:keydown.escape.window="$wire.cancelRewardClaim()">
            <div class="card-glow-wrapper max-w-md w-full" x-on:click.away="$wire.cancelRewardClaim()">
                <div class="card-glow-inner p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-white">Claim Reward</h3>
                        <button wire:click="cancelRewardClaim" class="text-gray-400 hover:text-white"><i class="fas fa-times"></i></button>
                    </div>

                    {{-- Item Preview --}}
                    <div class="flex items-center gap-4 p-4 bg-gray-800/50 rounded-lg mb-6">
                        <div class="w-12 h-12 bg-gray-800 rounded-lg flex items-center justify-center overflow-hidden border border-gray-700">
                            @if($claimingReward->item)
                                <img src="{{ $claimingReward->item->icon() }}" alt="" class="max-h-full max-w-full object-contain">
                            @endif
                        </div>
                        <div>
                            <p class="text-gray-100 font-semibold">
                                @if($claimingReward->refine_level > 0)<span class="text-amber-400">+{{ $claimingReward->refine_level }}</span> @endif{{ $claimingReward->item?->name ?? 'Unknown' }}
                            </p>
                            <p class="text-gray-400 text-sm">x{{ $claimingReward->quantity }} &bull; {{ $rewardGameAccount?->userid }}</p>
                        </div>
                    </div>

                    <p class="text-sm text-gray-400 mb-6">Item will be delivered to your account storage on next login.</p>

                    <div class="flex gap-3">
                        <button wire:click="cancelRewardClaim" class="flex-1 px-4 py-3 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors">Cancel</button>
                        <button wire:click="claimReward" wire:loading.attr="disabled" class="flex-1 px-4 py-3 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-gray-900 font-bold rounded-lg transition-all">
                            <span wire:loading.remove wire:target="claimReward"><i class="fas fa-gift mr-2"></i>Claim</span>
                            <span wire:loading wire:target="claimReward"><i class="fas fa-spinner fa-spin mr-2"></i>Claiming...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</section>
