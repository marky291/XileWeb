<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Search Form --}}
        <x-filament::section>
            <x-slot name="heading">Search Players</x-slot>
            <x-slot name="description">Search across XileRO, XileRetro, and Master Accounts</x-slot>

            <form wire:submit="searchPlayers" class="space-y-4">
                {{ $this->form }}

                <div class="flex justify-end">
                    <x-filament::button type="submit" wire:loading.attr="disabled">
                        <x-filament::loading-indicator wire:loading wire:target="searchPlayers" class="h-4 w-4 mr-2" />
                        Search
                    </x-filament::button>
                </div>
            </form>
        </x-filament::section>

        {{-- Results --}}
        @if(count($results) > 0)
            <x-filament::section>
                <x-slot name="heading">Search Results ({{ count($results) }})</x-slot>

                <div class="space-y-2">
                    @foreach($results as $index => $result)
                        @php
                            $isSelected = $selectedPlayer && $selectedPlayer === $result;
                        @endphp
                        <div
                            wire:key="result-{{ $index }}"
                            class="rounded-lg border transition-colors overflow-hidden
                                {{ $isSelected ? 'border-primary-500 bg-primary-50 dark:bg-primary-950' : 'border-gray-200 dark:border-gray-700 hover:border-primary-300 dark:hover:border-primary-700' }}"
                        >
                            {{-- Result Header (clickable) --}}
                            <div
                                wire:click="selectPlayer({{ $index }})"
                                class="p-4 cursor-pointer"
                            >
                                <div class="flex items-start gap-3">
                                    {{-- Icon --}}
                                    @if($result['type'] === 'master')
                                        <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-purple-100 dark:bg-purple-900/50 flex items-center justify-center">
                                            <x-heroicon-o-user-circle class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                                        </div>
                                    @elseif(str_contains($result['type'], 'login'))
                                        <div class="flex-shrink-0 w-10 h-10 rounded-lg {{ $result['server'] === 'XileRO' ? 'bg-green-100 dark:bg-green-900/50' : 'bg-orange-100 dark:bg-orange-900/50' }} flex items-center justify-center">
                                            <x-heroicon-o-server class="w-6 h-6 {{ $result['server'] === 'XileRO' ? 'text-green-600 dark:text-green-400' : 'text-orange-600 dark:text-orange-400' }}" />
                                        </div>
                                    @elseif(str_contains($result['type'], 'char'))
                                        <div class="flex-shrink-0 w-10 h-10 rounded-lg {{ $result['server'] === 'XileRO' ? 'bg-green-100 dark:bg-green-900/50' : 'bg-orange-100 dark:bg-orange-900/50' }} flex items-center justify-center">
                                            <x-heroicon-o-user class="w-6 h-6 {{ $result['server'] === 'XileRO' ? 'text-green-600 dark:text-green-400' : 'text-orange-600 dark:text-orange-400' }}" />
                                        </div>
                                    @endif

                                    {{-- Content --}}
                                    <div class="flex-1 min-w-0">
                                        @if($result['type'] === 'master')
                                            <div class="flex items-center gap-2 mb-1">
                                                <span class="text-xs font-medium text-purple-600 dark:text-purple-400 uppercase tracking-wide">Master Account</span>
                                            </div>
                                            <div class="font-semibold text-gray-900 dark:text-white">{{ $result['name'] }}</div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $result['email'] }}</div>
                                            <div class="flex items-center gap-3 mt-2 text-xs">
                                                <span class="font-bold text-amber-600 dark:text-amber-400">{{ $result['uber_balance'] }} Ubers</span>
                                                <span class="text-gray-400">•</span>
                                                <span class="text-gray-500 dark:text-gray-400">{{ $result['game_accounts_count'] }} game accounts</span>
                                            </div>
                                        @elseif(str_contains($result['type'], 'login'))
                                            <div class="flex items-center gap-2 mb-1">
                                                <span class="text-xs font-medium {{ $result['server'] === 'XileRO' ? 'text-green-600 dark:text-green-400' : 'text-orange-600 dark:text-orange-400' }} uppercase tracking-wide">{{ $result['server'] }} Game Account</span>
                                                @if($result['linked_master_name'] ?? null)
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-300">
                                                        Linked
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-700 dark:bg-yellow-900/50 dark:text-yellow-300">
                                                        Not Linked
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="font-semibold text-gray-900 dark:text-white">{{ $result['userid'] }}</div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $result['email'] }}</div>
                                            <div class="flex items-center gap-3 mt-2 text-xs text-gray-500 dark:text-gray-400">
                                                <span>{{ $result['chars_count'] }} characters</span>
                                                <span class="text-gray-400">•</span>
                                                <span>Last login: {{ $result['lastlogin'] ?? 'Never' }}</span>
                                                @if($result['linked_master_name'] ?? null)
                                                    <span class="text-gray-400">•</span>
                                                    <span class="text-blue-600 dark:text-blue-400">→ {{ $result['linked_master_name'] }}</span>
                                                @endif
                                            </div>
                                        @elseif(str_contains($result['type'], 'char'))
                                            <div class="flex items-center gap-2 mb-1">
                                                <span class="text-xs font-medium {{ $result['server'] === 'XileRO' ? 'text-green-600 dark:text-green-400' : 'text-orange-600 dark:text-orange-400' }} uppercase tracking-wide">{{ $result['server'] }} Character</span>
                                                @if($result['online'])
                                                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-xs font-medium bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-300">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                                                        Online
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="font-semibold text-gray-900 dark:text-white">{{ $result['name'] }}</div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">Account: {{ $result['userid'] }}</div>
                                            <div class="flex items-center gap-3 mt-2 text-xs text-gray-500 dark:text-gray-400">
                                                <span>Level {{ $result['base_level'] }}</span>
                                                <span class="text-gray-400">•</span>
                                                <span>{{ $result['last_map'] }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Chevron --}}
                                    <div class="flex-shrink-0">
                                        <x-heroicon-o-chevron-down class="w-5 h-5 text-gray-400 transition-transform duration-200 {{ $isSelected ? 'rotate-180' : '' }}" />
                                    </div>
                                </div>
                            </div>

                            {{-- Expandable Actions Panel --}}
                            @if($isSelected)
                                <div
                                    x-data
                                    x-show="true"
                                    x-collapse
                                    class="border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 p-4"
                                >
                                    @if($result['type'] === 'master')
                                        {{-- Master Account Actions --}}
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                            <div class="p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                                                <h4 class="font-semibold mb-2 text-sm">Account Info</h4>
                                                <dl class="space-y-0.5 text-sm">
                                                    <div><dt class="inline text-gray-500">ID:</dt> <dd class="inline font-mono">{{ $result['id'] }}</dd></div>
                                                    <div><dt class="inline text-gray-500">Name:</dt> <dd class="inline">{{ $result['name'] }}</dd></div>
                                                    <div><dt class="inline text-gray-500">Email:</dt> <dd class="inline">{{ $result['email'] }}</dd></div>
                                                    <div><dt class="inline text-gray-500">Uber Balance:</dt> <dd class="inline font-bold text-amber-600">{{ $result['uber_balance'] }}</dd></div>
                                                    <div><dt class="inline text-gray-500">Admin:</dt> <dd class="inline">{{ $result['is_admin'] ? 'Yes' : 'No' }}</dd></div>
                                                    <div><dt class="inline text-gray-500">Registered:</dt> <dd class="inline">{{ $result['created_at'] }}</dd></div>
                                                </dl>
                                            </div>

                                            <div class="p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                                                <h4 class="font-semibold mb-2 text-sm">Reset Password</h4>
                                                <div class="space-y-1.5">
                                                    <x-filament::input.wrapper>
                                                        <x-filament::input
                                                            type="text"
                                                            wire:model="newPassword"
                                                            placeholder="New password (or leave blank for random)"
                                                        />
                                                    </x-filament::input.wrapper>
                                                    <p class="text-xs text-gray-500">Leave blank to generate a random 12-character password</p>
                                                    <x-filament::button
                                                        wire:click="resetMasterPassword"
                                                        wire:confirm="Are you sure you want to reset this user's password?"
                                                        color="danger"
                                                        class="w-full"
                                                        size="sm"
                                                    >
                                                        Reset Password
                                                    </x-filament::button>
                                                </div>
                                            </div>

                                            <div class="p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                                                <h4 class="font-semibold mb-2 text-sm">Link Unclaimed Game Account</h4>
                                                <div class="space-y-1.5">
                                                    @if($selectedUnclaimedGameAccountId)
                                                        <div class="flex items-center justify-between p-2 bg-primary-50 dark:bg-primary-900/30 rounded-lg border border-primary-200 dark:border-primary-700">
                                                            <div class="text-sm">
                                                                <span class="font-medium text-primary-700 dark:text-primary-300">{{ $unclaimedGameAccountSearch }}</span>
                                                                <span class="text-primary-500 dark:text-primary-400">({{ $selectedUnclaimedServer }})</span>
                                                            </div>
                                                            <button
                                                                type="button"
                                                                wire:click="clearUnclaimedGameAccountSelection"
                                                                class="text-primary-500 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-200"
                                                            >
                                                                <x-heroicon-m-x-mark class="w-4 h-4" />
                                                            </button>
                                                        </div>
                                                    @else
                                                        <div class="relative">
                                                            <x-filament::input.wrapper>
                                                                <x-filament::input
                                                                    type="text"
                                                                    wire:model.live.debounce.300ms="unclaimedGameAccountSearch"
                                                                    placeholder="Search unclaimed accounts..."
                                                                    autocomplete="off"
                                                                />
                                                            </x-filament::input.wrapper>

                                                            @if(count($unclaimedGameAccountResults) > 0)
                                                                <div class="absolute z-50 w-full mt-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg max-h-48 overflow-y-auto">
                                                                    @foreach($unclaimedGameAccountResults as $unclaimedResult)
                                                                        <button
                                                                            type="button"
                                                                            wire:click="selectUnclaimedGameAccount({{ $unclaimedResult['id'] }}, '{{ $unclaimedResult['server_name'] }}', '{{ addslashes($unclaimedResult['userid']) }}')"
                                                                            class="w-full px-3 py-2 text-left text-sm hover:bg-gray-100 dark:hover:bg-gray-700 border-b border-gray-100 dark:border-gray-700 last:border-b-0"
                                                                        >
                                                                            <div class="flex items-center justify-between">
                                                                                <span class="font-medium">{{ $unclaimedResult['userid'] }}</span>
                                                                                <span class="text-xs px-1.5 py-0.5 rounded {{ $unclaimedResult['server'] === 'xilero' ? 'bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-300' : 'bg-orange-100 text-orange-700 dark:bg-orange-900/50 dark:text-orange-300' }}">
                                                                                    {{ $unclaimedResult['server_name'] }}
                                                                                </span>
                                                                            </div>
                                                                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $unclaimedResult['email'] }}</div>
                                                                        </button>
                                                                    @endforeach
                                                                </div>
                                                            @elseif(strlen($unclaimedGameAccountSearch) >= 2 && count($unclaimedGameAccountResults) === 0)
                                                                <div class="absolute z-50 w-full mt-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg p-3">
                                                                    <p class="text-sm text-gray-500 dark:text-gray-400">No unclaimed accounts found</p>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <p class="text-xs text-gray-500">Search by username or email</p>
                                                    @endif

                                                    <x-filament::button
                                                        wire:click="linkUnclaimedToMaster"
                                                        wire:confirm="Are you sure you want to link this game account to this master account?"
                                                        color="primary"
                                                        class="w-full"
                                                        size="sm"
                                                        :disabled="!$selectedUnclaimedGameAccountId"
                                                    >
                                                        Link Game Account
                                                    </x-filament::button>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Linked Game Accounts Section --}}
                                        <div class="mt-3 p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                                            <h4 class="font-semibold mb-2 text-sm flex items-center gap-2">
                                                <x-heroicon-o-link class="w-4 h-4" />
                                                Linked Game Accounts ({{ count($this->linkedGameAccounts) }})
                                            </h4>

                                            @if(count($this->linkedGameAccounts) > 0)
                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                                    @foreach($this->linkedGameAccounts as $linkedAccount)
                                                        <div class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-100 dark:border-gray-600">
                                                            <div class="flex items-center gap-2">
                                                                <div class="w-7 h-7 rounded {{ $linkedAccount['server'] === 'xilero' ? 'bg-green-100 dark:bg-green-900/50' : 'bg-orange-100 dark:bg-orange-900/50' }} flex items-center justify-center">
                                                                    <x-heroicon-o-server class="w-3.5 h-3.5 {{ $linkedAccount['server'] === 'xilero' ? 'text-green-600 dark:text-green-400' : 'text-orange-600 dark:text-orange-400' }}" />
                                                                </div>
                                                                <div>
                                                                    <div class="font-medium text-sm leading-tight">{{ $linkedAccount['userid'] }}</div>
                                                                    <div class="text-xs text-gray-500 dark:text-gray-400 leading-tight">
                                                                        {{ $linkedAccount['server_name'] }}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <x-filament::button
                                                                wire:click="unlinkGameAccount({{ $linkedAccount['id'] }})"
                                                                wire:confirm="Are you sure you want to unlink this game account? It will become unclaimed."
                                                                color="gray"
                                                                size="xs"
                                                            >
                                                                Unlink
                                                            </x-filament::button>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-3">
                                                    No game accounts linked to this master account.
                                                </p>
                                            @endif
                                        </div>
                                    @elseif(str_contains($result['type'], 'char'))
                                        {{-- Character Actions --}}
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div class="p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                                                <h4 class="font-semibold mb-2 text-sm">Character Info</h4>
                                                <dl class="space-y-1 text-sm">
                                                    <div><dt class="inline text-gray-500">Name:</dt> <dd class="inline">{{ $result['name'] }}</dd></div>
                                                    <div><dt class="inline text-gray-500">Account:</dt> <dd class="inline">{{ $result['userid'] }}</dd></div>
                                                    <div><dt class="inline text-gray-500">Level:</dt> <dd class="inline">{{ $result['base_level'] }}</dd></div>
                                                    <div><dt class="inline text-gray-500">Location:</dt> <dd class="inline">{{ $result['last_map'] }}</dd></div>
                                                    <div><dt class="inline text-gray-500">Status:</dt> <dd class="inline">{{ $result['online'] ? 'Online' : 'Offline' }}</dd></div>
                                                </dl>
                                            </div>

                                            <div class="p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                                                <h4 class="font-semibold mb-2 text-sm">Quick Actions</h4>
                                                <div class="space-y-2">
                                                    <x-filament::button
                                                        wire:click="resetCharacterPosition('{{ $result['server'] }}', {{ $result['char_id'] }})"
                                                        color="warning"
                                                        class="w-full"
                                                    >
                                                        Reset Position to Prontera
                                                    </x-filament::button>
                                                </div>
                                            </div>
                                        </div>
                                    @elseif(str_contains($result['type'], 'login'))
                                        {{-- Login Account Actions --}}
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                            <div class="p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                                                <h4 class="font-semibold mb-2 text-sm">Account Info</h4>
                                                <dl class="space-y-1 text-sm">
                                                    <div><dt class="inline text-gray-500">Server:</dt> <dd class="inline">{{ $result['server'] }}</dd></div>
                                                    <div><dt class="inline text-gray-500">Username:</dt> <dd class="inline">{{ $result['userid'] }}</dd></div>
                                                    <div><dt class="inline text-gray-500">Email:</dt> <dd class="inline">{{ $result['email'] }}</dd></div>
                                                    <div><dt class="inline text-gray-500">Account ID:</dt> <dd class="inline font-mono">{{ $result['account_id'] }}</dd></div>
                                                    <div><dt class="inline text-gray-500">Group ID:</dt> <dd class="inline">{{ $result['group_id'] }}</dd></div>
                                                    <div><dt class="inline text-gray-500">Last IP:</dt> <dd class="inline font-mono">{{ $result['last_ip'] }}</dd></div>
                                                    <div><dt class="inline text-gray-500">Last Login:</dt> <dd class="inline">{{ $result['lastlogin'] ?? 'Never' }}</dd></div>
                                                    <div><dt class="inline text-gray-500">Characters:</dt> <dd class="inline">{{ $result['chars_count'] }}</dd></div>
                                                </dl>
                                            </div>

                                            <div class="p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                                                <h4 class="font-semibold mb-2 text-sm">Reset Game Password</h4>
                                                <div class="space-y-2">
                                                    <x-filament::input.wrapper>
                                                        <x-filament::input
                                                            type="text"
                                                            wire:model="newGamePassword"
                                                            placeholder="New password (or leave blank for random)"
                                                        />
                                                    </x-filament::input.wrapper>
                                                    <p class="text-xs text-gray-500">Leave blank to generate a random 12-character password. Password must be 6-31 characters.</p>
                                                    <x-filament::button
                                                        wire:click="resetGameAccountPassword"
                                                        wire:confirm="Are you sure you want to reset this game account's password?"
                                                        color="danger"
                                                        class="w-full"
                                                    >
                                                        Reset Game Password
                                                    </x-filament::button>
                                                </div>
                                            </div>

                                            <div class="p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                                                <h4 class="font-semibold mb-2 text-sm">
                                                    @if($result['linked_master_name'] ?? null)
                                                        Transfer Game Account
                                                    @else
                                                        Link to Master Account
                                                    @endif
                                                </h4>
                                                @if($result['linked_master_name'] ?? null)
                                                    <div class="space-y-2">
                                                        <div class="p-3 bg-blue-50 dark:bg-blue-900/30 rounded-lg">
                                                            <p class="text-sm text-blue-800 dark:text-blue-200">
                                                                <span class="font-semibold">Currently linked to:</span> {{ $result['linked_master_name'] }}
                                                                <span class="text-blue-600 dark:text-blue-400">(ID: {{ $result['linked_master_id'] }})</span>
                                                            </p>
                                                        </div>

                                                        {{-- Selected Master Account Display --}}
                                                        @if($linkToMasterAccountId)
                                                            <div class="flex items-center justify-between p-2 bg-primary-50 dark:bg-primary-900/30 rounded-lg border border-primary-200 dark:border-primary-700">
                                                                <div class="text-sm">
                                                                    <span class="font-medium text-primary-700 dark:text-primary-300">{{ $masterAccountSearch }}</span>
                                                                    <span class="text-primary-500 dark:text-primary-400">(ID: {{ $linkToMasterAccountId }})</span>
                                                                </div>
                                                                <button
                                                                    type="button"
                                                                    wire:click="clearMasterAccountSelection"
                                                                    class="text-primary-500 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-200"
                                                                >
                                                                    <x-heroicon-m-x-mark class="w-4 h-4" />
                                                                </button>
                                                            </div>
                                                        @else
                                                            {{-- Search Input --}}
                                                            <div class="relative">
                                                                <x-filament::input.wrapper>
                                                                    <x-filament::input
                                                                        type="text"
                                                                        wire:model.live.debounce.300ms="masterAccountSearch"
                                                                        placeholder="Search new master account..."
                                                                        autocomplete="off"
                                                                    />
                                                                </x-filament::input.wrapper>

                                                                {{-- Search Results Dropdown --}}
                                                                @if(count($masterAccountSearchResults) > 0)
                                                                    <div class="absolute z-50 w-full mt-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg max-h-48 overflow-y-auto">
                                                                        @foreach($masterAccountSearchResults as $masterResult)
                                                                            <button
                                                                                type="button"
                                                                                wire:click="selectMasterAccountForLinking({{ $masterResult['id'] }}, '{{ addslashes($masterResult['name']) }}')"
                                                                                class="w-full px-3 py-2 text-left text-sm hover:bg-gray-100 dark:hover:bg-gray-700 border-b border-gray-100 dark:border-gray-700 last:border-b-0"
                                                                            >
                                                                                <div class="font-medium">{{ $masterResult['name'] }}</div>
                                                                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $masterResult['email'] }} (ID: {{ $masterResult['id'] }})</div>
                                                                            </button>
                                                                        @endforeach
                                                                    </div>
                                                                @elseif(strlen($masterAccountSearch) >= 2 && count($masterAccountSearchResults) === 0)
                                                                    <div class="absolute z-50 w-full mt-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg p-3">
                                                                        <p class="text-sm text-gray-500 dark:text-gray-400">No master accounts found</p>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                            <p class="text-xs text-gray-500">
                                                                Search for a different master account to transfer to
                                                            </p>
                                                        @endif

                                                        <x-filament::button
                                                            wire:click="transferGameAccountToMaster"
                                                            wire:confirm="Are you sure you want to transfer this game account to a different master account?"
                                                            color="warning"
                                                            class="w-full"
                                                            :disabled="!$linkToMasterAccountId"
                                                        >
                                                            Transfer to Master Account
                                                        </x-filament::button>
                                                    </div>
                                                @else
                                                    <div class="space-y-2">
                                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                                            This game account is not linked to any master account.
                                                        </p>

                                                        {{-- Selected Master Account Display --}}
                                                        @if($linkToMasterAccountId)
                                                            <div class="flex items-center justify-between p-2 bg-primary-50 dark:bg-primary-900/30 rounded-lg border border-primary-200 dark:border-primary-700">
                                                                <div class="text-sm">
                                                                    <span class="font-medium text-primary-700 dark:text-primary-300">{{ $masterAccountSearch }}</span>
                                                                    <span class="text-primary-500 dark:text-primary-400">(ID: {{ $linkToMasterAccountId }})</span>
                                                                </div>
                                                                <button
                                                                    type="button"
                                                                    wire:click="clearMasterAccountSelection"
                                                                    class="text-primary-500 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-200"
                                                                >
                                                                    <x-heroicon-m-x-mark class="w-4 h-4" />
                                                                </button>
                                                            </div>
                                                        @else
                                                            {{-- Search Input --}}
                                                            <div class="relative" x-data="{ open: @entangle('masterAccountSearchResults').length > 0 }">
                                                                <x-filament::input.wrapper>
                                                                    <x-filament::input
                                                                        type="text"
                                                                        wire:model.live.debounce.300ms="masterAccountSearch"
                                                                        placeholder="Search by name or email..."
                                                                        autocomplete="off"
                                                                    />
                                                                </x-filament::input.wrapper>

                                                                {{-- Search Results Dropdown --}}
                                                                @if(count($masterAccountSearchResults) > 0)
                                                                    <div class="absolute z-50 w-full mt-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg max-h-48 overflow-y-auto">
                                                                        @foreach($masterAccountSearchResults as $masterResult)
                                                                            <button
                                                                                type="button"
                                                                                wire:click="selectMasterAccountForLinking({{ $masterResult['id'] }}, '{{ addslashes($masterResult['name']) }}')"
                                                                                class="w-full px-3 py-2 text-left text-sm hover:bg-gray-100 dark:hover:bg-gray-700 border-b border-gray-100 dark:border-gray-700 last:border-b-0"
                                                                            >
                                                                                <div class="font-medium">{{ $masterResult['name'] }}</div>
                                                                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $masterResult['email'] }} (ID: {{ $masterResult['id'] }})</div>
                                                                            </button>
                                                                        @endforeach
                                                                    </div>
                                                                @elseif(strlen($masterAccountSearch) >= 2 && count($masterAccountSearchResults) === 0)
                                                                    <div class="absolute z-50 w-full mt-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg p-3">
                                                                        <p class="text-sm text-gray-500 dark:text-gray-400">No master accounts found</p>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                            <p class="text-xs text-gray-500">
                                                                Type at least 2 characters to search
                                                            </p>
                                                        @endif

                                                        <x-filament::button
                                                            wire:click="linkGameAccountToMaster"
                                                            wire:confirm="Are you sure you want to link this game account to the specified master account?"
                                                            color="primary"
                                                            class="w-full"
                                                            :disabled="!$linkToMasterAccountId"
                                                        >
                                                            Link to Master Account
                                                        </x-filament::button>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </x-filament::section>
        @endif
    </div>
</x-filament-panels::page>
