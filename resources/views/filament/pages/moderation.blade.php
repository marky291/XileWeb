<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Search Form --}}
        <x-filament::section>
            <x-slot name="heading">Account Moderation</x-slot>
            <x-slot name="description">Search and manage game accounts across servers</x-slot>

            <form wire:submit="searchAccounts" class="space-y-4">
                {{ $this->form }}

                <div class="flex justify-end">
                    <x-filament::button type="submit" wire:loading.attr="disabled">
                        <x-filament::loading-indicator wire:loading wire:target="searchAccounts" class="h-4 w-4 mr-2" />
                        Search
                    </x-filament::button>
                </div>
            </form>
        </x-filament::section>

        {{-- Recent/Search Results --}}
        <x-filament::section>
            <x-slot name="heading">
                @if(strlen($data['search'] ?? '') >= 2)
                    Search Results ({{ count($recentLogins) }})
                @else
                    Recent Logins ({{ count($recentLogins) }})
                @endif
            </x-slot>

            @if(count($recentLogins) > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                <th class="text-left py-2 px-2">Server</th>
                                <th class="text-left py-2 px-2">Username</th>
                                <th class="text-left py-2 px-2">Email</th>
                                <th class="text-left py-2 px-2">Last IP</th>
                                <th class="text-left py-2 px-2">Last Login</th>
                                <th class="text-left py-2 px-2">Logins</th>
                                <th class="text-left py-2 px-2">Status</th>
                                <th class="text-left py-2 px-2"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentLogins as $index => $login)
                                @php
                                    $isSelected = $selectedAccount && ($selectedAccount['account_id'] ?? null) === $login['account_id'];
                                @endphp
                                <tr wire:key="login-{{ $login['account_id'] }}"
                                    class="border-b border-gray-100 dark:border-gray-800 cursor-pointer transition-colors
                                        {{ $isSelected ? 'bg-primary-50 dark:bg-primary-950' : 'hover:bg-gray-50 dark:hover:bg-gray-900' }}"
                                    wire:click="selectAccount({{ $index }})"
                                >
                                    <td class="py-2 px-2">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                            {{ $login['server'] === 'xilero' ? 'bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-300' : 'bg-orange-100 text-orange-700 dark:bg-orange-900/50 dark:text-orange-300' }}">
                                            {{ $login['server'] === 'xilero' ? 'XileRO' : 'XileRetro' }}
                                        </span>
                                    </td>
                                    <td class="py-2 px-2 font-medium">{{ $login['userid'] }}</td>
                                    <td class="py-2 px-2 text-gray-500">{{ $login['email'] }}</td>
                                    <td class="py-2 px-2 font-mono text-xs">{{ $login['last_ip'] }}</td>
                                    <td class="py-2 px-2 text-gray-500">{{ $login['lastlogin'] ?? 'Never' }}</td>
                                    <td class="py-2 px-2 text-center">{{ $login['logincount'] }}</td>
                                    <td class="py-2 px-2">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                            @if($login['state'] === 0) bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                            @elseif($login['state'] === 5) bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                            @else bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                            @endif">
                                            {{ $this->getAccountStateLabel($login['state']) }}
                                        </span>
                                    </td>
                                    <td class="py-2 px-2">
                                        <x-heroicon-o-chevron-down class="w-5 h-5 text-gray-400 transition-transform duration-200 {{ $isSelected ? 'rotate-180' : '' }}" />
                                    </td>
                                </tr>
                                {{-- Inline Expandable Panel --}}
                                @if($isSelected)
                                    <tr wire:key="login-details-{{ $login['account_id'] }}">
                                        <td colspan="8" class="p-0">
                                            <div class="bg-gray-50 dark:bg-gray-800/50 border-t border-gray-200 dark:border-gray-700 p-4">
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                    {{-- Account Info --}}
                                                    <div class="p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                                                        <h4 class="font-semibold mb-2 text-sm">Account Information</h4>
                                                        <dl class="space-y-1 text-sm">
                                                            <div><dt class="inline text-gray-500">Account ID:</dt> <dd class="inline font-mono">{{ $selectedAccount['account_id'] }}</dd></div>
                                                            <div><dt class="inline text-gray-500">Username:</dt> <dd class="inline">{{ $selectedAccount['userid'] }}</dd></div>
                                                            <div><dt class="inline text-gray-500">Email:</dt> <dd class="inline">{{ $selectedAccount['email'] }}</dd></div>
                                                            <div><dt class="inline text-gray-500">Last IP:</dt> <dd class="inline font-mono">{{ $selectedAccount['last_ip'] }}</dd></div>
                                                            <div><dt class="inline text-gray-500">Last Login:</dt> <dd class="inline">{{ $selectedAccount['lastlogin'] ?? 'Never' }}</dd></div>
                                                            <div><dt class="inline text-gray-500">Login Count:</dt> <dd class="inline">{{ $selectedAccount['logincount'] }}</dd></div>
                                                            <div><dt class="inline text-gray-500">Group ID:</dt> <dd class="inline">{{ $selectedAccount['group_id'] }}</dd></div>
                                                            <div>
                                                                <dt class="inline text-gray-500">Status:</dt>
                                                                <dd class="inline">
                                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                                                        @if($selectedAccount['state'] === 0) bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                                                        @elseif($selectedAccount['state'] === 5) bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                                                        @else bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                                                        @endif">
                                                                        {{ $this->getAccountStateLabel($selectedAccount['state']) }}
                                                                    </span>
                                                                </dd>
                                                            </div>
                                                            @if($selectedAccount['unban_time'] > 0)
                                                                <div><dt class="inline text-gray-500">Unban Time:</dt> <dd class="inline text-red-600">{{ date('Y-m-d H:i:s', $selectedAccount['unban_time']) }}</dd></div>
                                                            @endif
                                                        </dl>
                                                    </div>

                                                    {{-- Actions --}}
                                                    <div class="p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                                                        <h4 class="font-semibold mb-2 text-sm">Moderation Actions</h4>

                                                        @if($selectedAccount['state'] === 5)
                                                            {{-- Unban UI --}}
                                                            <div class="space-y-3">
                                                                <p class="text-sm text-red-600">This account is currently banned.</p>
                                                                <x-filament::button
                                                                    wire:click.stop="unbanAccount"
                                                                    color="success"
                                                                    class="w-full"
                                                                    size="sm"
                                                                >
                                                                    Unban Account
                                                                </x-filament::button>
                                                            </div>
                                                        @else
                                                            {{-- Ban UI --}}
                                                            <div class="space-y-3">
                                                                <div>
                                                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                                        Ban Duration (hours)
                                                                    </label>
                                                                    <x-filament::input.wrapper>
                                                                        <x-filament::input
                                                                            type="number"
                                                                            wire:model="banDuration"
                                                                            wire:click.stop
                                                                            min="1"
                                                                            max="8760"
                                                                            placeholder="24"
                                                                        />
                                                                    </x-filament::input.wrapper>
                                                                    <p class="text-xs text-gray-500 mt-1">24h = 1 day, 168h = 1 week, 720h = 1 month</p>
                                                                </div>

                                                                <div>
                                                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                                        Ban Reason (optional)
                                                                    </label>
                                                                    <x-filament::input.wrapper>
                                                                        <x-filament::input
                                                                            type="text"
                                                                            wire:model="banReason"
                                                                            wire:click.stop
                                                                            placeholder="Reason for ban..."
                                                                        />
                                                                    </x-filament::input.wrapper>
                                                                </div>

                                                                <x-filament::button
                                                                    wire:click.stop="banAccount"
                                                                    wire:confirm="Are you sure you want to ban this account?"
                                                                    color="danger"
                                                                    class="w-full"
                                                                    size="sm"
                                                                >
                                                                    Ban Account
                                                                </x-filament::button>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{-- Pagination Controls --}}
                @if(strlen($data['search'] ?? '') < 2 && $this->getTotalPages() > 1)
                    <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
                        <div class="text-sm text-gray-500">
                            Page {{ $page }} of {{ $this->getTotalPages() }}
                        </div>
                        <div class="flex gap-2">
                            <x-filament::button
                                wire:click="previousPage"
                                :disabled="$page <= 1"
                                color="gray"
                                size="sm"
                            >
                                Previous
                            </x-filament::button>
                            <x-filament::button
                                wire:click="nextPage"
                                :disabled="$page >= $this->getTotalPages()"
                                color="gray"
                                size="sm"
                            >
                                Next
                            </x-filament::button>
                        </div>
                    </div>
                @endif
            @else
                <p class="text-gray-500 text-center py-4">No accounts to display</p>
            @endif
        </x-filament::section>
    </div>
</x-filament-panels::page>
