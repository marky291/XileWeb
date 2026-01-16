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
                @if(strlen($search) >= 2)
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
                                <th class="text-left py-2 px-2">Username</th>
                                <th class="text-left py-2 px-2">Email</th>
                                <th class="text-left py-2 px-2">Last IP</th>
                                <th class="text-left py-2 px-2">Last Login</th>
                                <th class="text-left py-2 px-2">Logins</th>
                                <th class="text-left py-2 px-2">Status</th>
                                <th class="text-left py-2 px-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentLogins as $index => $login)
                                <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-900">
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
                                        <x-filament::button
                                            wire:click="selectAccount({{ $index }})"
                                            size="xs"
                                            color="gray"
                                        >
                                            Manage
                                        </x-filament::button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-500 text-center py-4">No accounts to display</p>
            @endif
        </x-filament::section>

        {{-- Selected Account Actions --}}
        @if($selectedAccount)
            <x-filament::section>
                <x-slot name="heading">Manage Account: {{ $selectedAccount['userid'] }}</x-slot>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Account Info --}}
                    <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <h4 class="font-semibold mb-3">Account Information</h4>
                        <dl class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Account ID:</dt>
                                <dd class="font-mono">{{ $selectedAccount['account_id'] }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Username:</dt>
                                <dd>{{ $selectedAccount['userid'] }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Email:</dt>
                                <dd>{{ $selectedAccount['email'] }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Last IP:</dt>
                                <dd class="font-mono">{{ $selectedAccount['last_ip'] }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Last Login:</dt>
                                <dd>{{ $selectedAccount['lastlogin'] ?? 'Never' }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Login Count:</dt>
                                <dd>{{ $selectedAccount['logincount'] }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Group ID:</dt>
                                <dd>{{ $selectedAccount['group_id'] }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Status:</dt>
                                <dd>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                        @if($selectedAccount['state'] === 0) bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                        @elseif($selectedAccount['state'] === 5) bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                        @else bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                        @endif">
                                        {{ $this->getAccountStateLabel($selectedAccount['state']) }}
                                    </span>
                                </dd>
                            </div>
                            @if($selectedAccount['unban_time'] > 0)
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">Unban Time:</dt>
                                    <dd class="text-red-600">{{ date('Y-m-d H:i:s', $selectedAccount['unban_time']) }}</dd>
                                </div>
                            @endif
                        </dl>
                    </div>

                    {{-- Actions --}}
                    <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <h4 class="font-semibold mb-3">Moderation Actions</h4>

                        @if($selectedAccount['state'] === 5)
                            {{-- Unban UI --}}
                            <div class="space-y-4">
                                <p class="text-sm text-red-600">This account is currently banned.</p>
                                <x-filament::button
                                    wire:click="unbanAccount"
                                    color="success"
                                    class="w-full"
                                >
                                    Unban Account
                                </x-filament::button>
                            </div>
                        @else
                            {{-- Ban UI --}}
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Ban Duration (hours)
                                    </label>
                                    <x-filament::input.wrapper>
                                        <x-filament::input
                                            type="number"
                                            wire:model="banDuration"
                                            min="1"
                                            max="8760"
                                            placeholder="24"
                                        />
                                    </x-filament::input.wrapper>
                                    <p class="text-xs text-gray-500 mt-1">Common: 24h (1 day), 168h (1 week), 720h (1 month)</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Ban Reason (optional)
                                    </label>
                                    <x-filament::input.wrapper>
                                        <x-filament::input
                                            type="text"
                                            wire:model="banReason"
                                            placeholder="Reason for ban..."
                                        />
                                    </x-filament::input.wrapper>
                                </div>

                                <x-filament::button
                                    wire:click="banAccount"
                                    wire:confirm="Are you sure you want to ban this account?"
                                    color="danger"
                                    class="w-full"
                                >
                                    Ban Account
                                </x-filament::button>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="flex justify-end mt-4">
                    <x-filament::button wire:click="clearSelection" color="gray">
                        Close
                    </x-filament::button>
                </div>
            </x-filament::section>
        @endif
    </div>
</x-filament-panels::page>
