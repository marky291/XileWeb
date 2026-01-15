@section('title', 'My Account - XileRO')

<section class="bg-clash-bg min-h-screen pt-28 pb-16 px-4">
    {{-- Global Loading Bar --}}
    <div wire:loading.delay class="fixed top-0 left-0 right-0 z-50">
        <div class="h-1 bg-amber-500 animate-pulse"></div>
    </div>

    <div class="max-w-4xl w-full mx-auto">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-white mb-1">My Account</h1>
                <div class="flex items-center gap-3 flex-wrap">
                    <p class="text-gray-400">{{ $user->email }}</p>
                    @if($user->uber_balance > 0)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-amber-500/20 text-amber-400 rounded-lg text-sm">
                            <i class="fas fa-coins"></i>
                            {{ number_format($user->uber_balance) }} {{ Str::plural('Uber', $user->uber_balance) }}
                        </span>
                    @endif
                </div>
            </div>
            <div class="mt-4 sm:mt-0 flex flex-wrap gap-3">
                <button
                    wire:click="refreshData"
                    wire:loading.attr="disabled"
                    wire:target="refreshData"
                    class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors duration-200 flex items-center gap-2 disabled:opacity-50"
                    title="Refresh character data from game server"
                >
                    <span wire:loading.remove wire:target="refreshData">
                        <i class="fas fa-sync-alt"></i>
                    </span>
                    <span wire:loading wire:target="refreshData">
                        <i class="fas fa-spinner fa-spin"></i>
                    </span>
                    Refresh
                </button>
                <a href="{{ url('/#steps2play') }}"
                   class="px-4 py-2 bg-amber-500 hover:bg-amber-400 text-gray-900 font-bold rounded-lg transition-colors duration-200 flex items-center gap-2">
                    <i class="fas fa-download"></i>
                    Download Client
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors duration-200 flex items-center gap-2">
                        <i class="fas fa-sign-out-alt"></i>
                        Logout
                    </button>
                </form>
            </div>
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

        {{-- Create Game Account Section --}}
        @if($canCreateMore)
            @if($gameAccounts->isEmpty() || $showCreateForm)
                <div class="block-home bg-gray-900 p-6 rounded-lg mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-200">
                            {{ $gameAccounts->isEmpty() ? 'Create Your First Game Account' : 'Create New Game Account' }}
                        </h2>
                        @if($gameAccounts->isNotEmpty())
                            <button
                                wire:click="$set('showCreateForm', false)"
                                class="text-gray-400 hover:text-white transition-colors"
                            >
                                <i class="fas fa-times"></i>
                            </button>
                        @endif
                    </div>

                    @if($gameAccounts->isEmpty())
                        <p class="text-gray-400 text-sm mb-6">
                            Create your in-game login credentials to start playing. You can have up to {{ $user->max_game_accounts }} game accounts.
                        </p>
                    @endif

                    <form wire:submit="createGameAccount" class="space-y-4">
                        {{-- Server Selection --}}
                        <div class="grid grid-cols-2 gap-3">
                            <label class="relative cursor-pointer">
                                <input type="radio" wire:model="gameServer" value="xilero" class="peer sr-only" />
                                <div class="p-4 rounded-lg border-2 transition-all peer-checked:border-amber-500 peer-checked:bg-amber-500/10 border-gray-700 hover:border-gray-600">
                                    <p class="font-semibold text-white">XileRO</p>
                                    <p class="text-sm text-gray-400">MidRate Server</p>
                                </div>
                            </label>
                            <label class="relative cursor-pointer">
                                <input type="radio" wire:model="gameServer" value="xileretro" class="peer sr-only" />
                                <div class="p-4 rounded-lg border-2 transition-all peer-checked:border-purple-500 peer-checked:bg-purple-500/10 border-gray-700 hover:border-gray-600">
                                    <p class="font-semibold text-white">XileRetro</p>
                                    <p class="text-sm text-gray-400">HighRate Server</p>
                                </div>
                            </label>
                        </div>
                        @error('gameServer')
                            <p class="text-xs text-red-400">{{ $message }}</p>
                        @enderror

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1">Username</label>
                                <input
                                    wire:model.live="gameUsername"
                                    type="text"
                                    placeholder="Choose a username"
                                    class="w-full px-3 py-2.5 bg-gray-800/50 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-xilero-gold/50 focus:border-xilero-gold transition-colors"
                                >
                                <p class="mt-1 text-xs text-gray-500">4-23 characters, alphanumeric</p>
                                @error('gameUsername')
                                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1">Password</label>
                                <input
                                    wire:model="gamePassword"
                                    type="password"
                                    placeholder="Choose a password"
                                    class="w-full px-3 py-2.5 bg-gray-800/50 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-xilero-gold/50 focus:border-xilero-gold transition-colors"
                                >
                                <p class="mt-1 text-xs text-gray-500">6-31 characters</p>
                                @error('gamePassword')
                                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1">Confirm Password</label>
                                <input
                                    wire:model="gamePassword_confirmation"
                                    type="password"
                                    placeholder="Confirm password"
                                    class="w-full px-3 py-2.5 bg-gray-800/50 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-xilero-gold/50 focus:border-xilero-gold transition-colors"
                                >
                            </div>
                        </div>

                        <button
                            type="submit"
                            class="px-6 py-3 bg-gradient-to-r from-xilero-gold to-amber-600 hover:from-amber-500 hover:to-amber-600 text-gray-900 font-bold rounded-lg shadow-md transition-all duration-200"
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-75 cursor-wait"
                        >
                            <span wire:loading.remove wire:target="createGameAccount">Create Game Account</span>
                            <span wire:loading wire:target="createGameAccount">Creating...</span>
                        </button>
                    </form>
                </div>
            @else
                <button
                    wire:click="$set('showCreateForm', true)"
                    class="w-full mb-6 px-4 py-3 bg-gray-900 hover:bg-gray-800 border border-gray-700 hover:border-amber-500/50 text-gray-300 hover:text-amber-400 rounded-lg transition-all flex items-center justify-center gap-2"
                >
                    <i class="fas fa-plus"></i>
                    Create New Game Account
                    <span class="text-gray-500 text-sm">({{ $gameAccounts->count() }}/{{ $user->max_game_accounts }})</span>
                </button>
            @endif
        @endif

        {{-- Game Accounts List --}}
        @if($gameAccounts->isNotEmpty())
            <div class="space-y-4" wire:loading.class="opacity-50 pointer-events-none">
                @foreach($gameAccounts as $account)
                    @php
                        $accountChars = $account->syncedCharacters;
                        $isExpanded = $selectedGameAccountId === $account->id;
                    @endphp
                    <div wire:key="account-{{ $account->id }}" class="block-home bg-gray-900 rounded-lg overflow-hidden">
                        {{-- Account Header --}}
                        <div class="p-5 flex items-center justify-between">
                            <button
                                wire:click="selectGameAccount({{ $isExpanded ? 'null' : $account->id }})"
                                class="flex-1 text-left flex items-center gap-4 hover:opacity-80 transition-opacity"
                            >
                                <div class="w-12 h-12 rounded-lg flex items-center justify-center {{ $account->server === 'xilero' ? 'bg-amber-500/20' : 'bg-purple-500/20' }}">
                                    <i class="fas fa-user text-xl {{ $account->server === 'xilero' ? 'text-amber-400' : 'text-purple-400' }}"></i>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h3 class="text-lg font-semibold text-white">{{ $account->userid }}</h3>
                                        <span class="text-xs px-2 py-0.5 rounded {{ $account->server === 'xilero' ? 'bg-amber-500/20 text-amber-400' : 'bg-purple-500/20 text-purple-400' }}">
                                            {{ $account->server === 'xilero' ? 'MidRate' : 'HighRate' }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-400">
                                        {{ $accountChars->count() }} {{ Str::plural('character', $accountChars->count()) }}
                                    </p>
                                </div>
                            </button>

                            <div class="flex items-center gap-3">
                                {{-- Reset Security Button (Account-level action) --}}
                                @if($account->has_security_code)
                                    @php $hasOnlineChars = $accountChars->contains('online', true); @endphp
                                    <button
                                        wire:click="resetSecurity({{ $account->id }})"
                                        @if(!$hasOnlineChars) wire:confirm="Reset @security code for {{ $account->userid }}? You will need to set a new one in-game." @endif
                                        wire:loading.attr="disabled"
                                        wire:target="resetSecurity({{ $account->id }})"
                                        class="px-3 py-1.5 text-sm font-medium rounded-lg transition-colors disabled:opacity-50 {{ $hasOnlineChars ? 'bg-gray-800 text-gray-500 cursor-not-allowed' : 'bg-gray-700 hover:bg-red-600 text-gray-300 hover:text-white' }}"
                                        title="{{ $hasOnlineChars ? 'Log out of the game first to reset security' : 'Reset @security code for ' . $account->userid }}"
                                    >
                                        <span wire:loading.remove wire:target="resetSecurity({{ $account->id }})">
                                            <i class="fas fa-lock-open"></i>
                                            <span class="hidden sm:inline ml-1.5">Reset Security</span>
                                        </span>
                                        <span wire:loading wire:target="resetSecurity({{ $account->id }})">
                                            <i class="fas fa-spinner fa-spin"></i>
                                        </span>
                                    </button>
                                @endif

                                {{-- Expand/Collapse Button --}}
                                <button
                                    wire:click="selectGameAccount({{ $isExpanded ? 'null' : $account->id }})"
                                    class="p-2 text-gray-400 hover:text-white transition-colors"
                                >
                                    <span wire:loading.remove wire:target="selectGameAccount">
                                        <i class="fas fa-chevron-down transition-transform {{ $isExpanded ? 'rotate-180' : '' }}"></i>
                                    </span>
                                    <span wire:loading wire:target="selectGameAccount">
                                        <i class="fas fa-spinner fa-spin text-amber-400"></i>
                                    </span>
                                </button>
                            </div>
                        </div>

                        {{-- Characters List (Expanded) --}}
                        @if($isExpanded)
                            <div class="border-t border-gray-800">
                                @forelse($accountChars as $character)
                                    @php $isCharSelected = $selectedCharacterId === $character->char_id; @endphp
                                    <div wire:key="char-{{ $character->char_id }}" class="border-b border-gray-800 last:border-b-0">
                                        {{-- Character Row (Clickable) --}}
                                        <button
                                            wire:click="selectCharacter({{ $character->char_id }})"
                                            class="w-full p-4 text-left flex items-center justify-between hover:bg-gray-800/30 transition-colors {{ $isCharSelected ? 'bg-gray-800/50' : '' }}"
                                        >
                                            <div class="flex items-center gap-4">
                                                {{-- Online Status Indicator --}}
                                                <div class="relative">
                                                    <div class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center">
                                                        <i class="fas fa-user-ninja text-gray-400"></i>
                                                    </div>
                                                    <span class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 rounded-full border-2 border-gray-900 {{ $character->online ? 'bg-green-500' : 'bg-gray-600' }}"></span>
                                                </div>

                                                <div>
                                                    <div class="flex items-center gap-2">
                                                        <h4 class="font-semibold text-white">{{ $character->name }}</h4>
                                                        @if($character->guild_name)
                                                            <span class="text-xs text-gray-500">
                                                                <i class="fas fa-shield-alt mr-1"></i>{{ $character->guild_name }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <p class="text-sm text-gray-400">
                                                        {{ $character->class_name }}
                                                        <span class="text-gray-500 mx-1">&middot;</span>
                                                        Lv. {{ $character->base_level }}/{{ $character->job_level }}
                                                    </p>
                                                </div>
                                            </div>

                                            <div class="flex items-center gap-3">
                                                @if($character->online)
                                                    <span class="px-3 py-1.5 text-xs font-medium bg-green-900/30 text-green-400 rounded-lg">
                                                        Online
                                                    </span>
                                                @endif
                                                <span wire:loading.remove wire:target="selectCharacter({{ $character->char_id }})">
                                                    <i class="fas fa-chevron-down text-gray-500 text-sm transition-transform {{ $isCharSelected ? 'rotate-180' : '' }}"></i>
                                                </span>
                                                <span wire:loading wire:target="selectCharacter({{ $character->char_id }})">
                                                    <i class="fas fa-spinner fa-spin text-amber-400 text-sm"></i>
                                                </span>
                                            </div>
                                        </button>

                                        {{-- Character Details (Expanded) --}}
                                        @if($isCharSelected)
                                            <div class="px-4 pb-4 pt-1 bg-gray-800/30">
                                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-4">
                                                    <div>
                                                        <p class="text-xs text-gray-500 mb-1">Base Level</p>
                                                        <p class="text-lg font-bold text-white">{{ $character->base_level }}</p>
                                                    </div>
                                                    <div>
                                                        <p class="text-xs text-gray-500 mb-1">Job Level</p>
                                                        <p class="text-lg font-bold text-white">{{ $character->job_level }}</p>
                                                    </div>
                                                    <div>
                                                        <p class="text-xs text-gray-500 mb-1">Zeny</p>
                                                        <p class="text-lg font-bold text-amber-400">{{ number_format($character->zeny) }}z</p>
                                                    </div>
                                                    <div>
                                                        <p class="text-xs text-gray-500 mb-1">Location</p>
                                                        <p class="text-sm text-gray-300">{{ $character->last_map }}</p>
                                                    </div>
                                                </div>

                                                @if($character->guild_name)
                                                    <div class="mb-4 p-3 bg-gray-900/50 rounded-lg">
                                                        <p class="text-xs text-gray-500 mb-1">Guild</p>
                                                        <p class="text-white font-medium">
                                                            <i class="fas fa-shield-alt text-amber-400 mr-2"></i>{{ $character->guild_name }}
                                                        </p>
                                                    </div>
                                                @endif

                                                {{-- Actions --}}
                                                @if(!$character->online)
                                                    <button
                                                        wire:click.stop="resetPosition({{ $character->char_id }})"
                                                        wire:confirm="Reset {{ $character->name }}'s position to Prontera?"
                                                        wire:loading.attr="disabled"
                                                        wire:target="resetPosition({{ $character->char_id }})"
                                                        class="px-4 py-2 text-sm font-medium bg-gray-700 hover:bg-amber-600 text-gray-300 hover:text-white rounded-lg transition-colors disabled:opacity-50"
                                                    >
                                                        <span wire:loading.remove wire:target="resetPosition({{ $character->char_id }})">
                                                            <i class="fas fa-map-marker-alt mr-2"></i>
                                                            Reset Position to Prontera
                                                        </span>
                                                        <span wire:loading wire:target="resetPosition({{ $character->char_id }})">
                                                            <i class="fas fa-spinner fa-spin mr-2"></i>
                                                            Resetting...
                                                        </span>
                                                    </button>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                @empty
                                    <div class="p-8 text-center">
                                        <i class="fas fa-user-plus text-3xl text-gray-600 mb-3"></i>
                                        <p class="text-gray-400">No characters yet</p>
                                        <p class="text-sm text-gray-500 mt-1">Create a character in-game to see it here</p>
                                    </div>
                                @endforelse
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            {{-- Empty State --}}
            <div class="block-home bg-gray-900 p-12 rounded-lg text-center">
                <i class="fas fa-gamepad text-5xl text-gray-600 mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-300 mb-2">No Game Accounts Yet</h3>
                <p class="text-gray-500">Create a game account above to get started!</p>
            </div>
        @endif

        {{-- Quick Links --}}
        @if($gameAccounts->isNotEmpty())
            <div class="mt-8 p-4 bg-gray-900/50 rounded-lg border border-gray-800">
                <p class="text-gray-500 text-sm text-center">
                    Need help?
                    <a href="https://discord.gg/xilero" target="_blank" class="text-amber-400 hover:text-amber-300">Join our Discord</a>
                    <span class="mx-2">&middot;</span>
                    <a href="https://wiki.xilero.net" target="_blank" class="text-amber-400 hover:text-amber-300">Visit the Wiki</a>
                </p>
            </div>
        @endif
    </div>
</section>
