@php
    $isExpanded = $this->selectedGameAccountId === $account->id;
    $isXileRO = $account->server === 'xilero';
    $accentColor = $isXileRO ? 'amber' : 'purple';
    $characters = $account->syncedCharacters;
    $onlineCount = $characters->where('online', true)->count();
    $pendingRewards = $pendingRewards ?? collect();
    $hasPendingRewards = $pendingRewards->isNotEmpty();
@endphp

<div class="card-glow-wrapper group transition-all duration-300 {{ $isExpanded ? '' : 'hover:-translate-y-0.5' }}">
    <div class="card-glow-inner">
        {{-- Account Header --}}
        <button
            wire:click="selectGameAccount({{ $isExpanded ? 'null' : $account->id }})"
            class="w-full p-5 flex items-center justify-between text-left"
        >
            <div class="flex items-center gap-4">
                {{-- Avatar --}}
                <div class="relative">
                    <div class="w-12 h-12 rounded-xl bg-{{ $accentColor }}-500/10 flex items-center justify-center border border-{{ $accentColor }}-500/20">
                        <i class="fas fa-user-shield text-lg text-{{ $accentColor }}-400"></i>
                    </div>
                    @if($hasPendingRewards)
                        <div class="absolute -top-1.5 -right-1.5 w-5 h-5 bg-amber-500 rounded-full flex items-center justify-center shadow-lg shadow-amber-500/30 animate-pulse">
                            <i class="fas fa-gift text-[10px] text-gray-900"></i>
                        </div>
                    @endif
                </div>

                {{-- Info --}}
                <div>
                    <div class="flex items-center gap-3">
                        <span class="text-gray-100 font-semibold">{{ $account->userid }}</span>
                        @if($onlineCount > 0)
                            <span class="flex items-center gap-1.5 px-2 py-0.5 text-xs font-medium bg-green-500/20 text-green-400 rounded-full border border-green-500/30">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-400 animate-pulse"></span>
                                Online
                            </span>
                        @endif
                        @if($hasPendingRewards)
                            <span class="text-xs text-amber-400">{{ $pendingRewards->count() }} reward{{ $pendingRewards->count() > 1 ? 's' : '' }}</span>
                        @endif
                    </div>
                    <p class="text-sm text-gray-500 mt-0.5">
                        {{ $characters->count() }} character{{ $characters->count() !== 1 ? 's' : '' }}
                    </p>
                </div>
            </div>

            <i class="fas fa-chevron-down text-gray-500 transition-transform duration-300 {{ $isExpanded ? 'rotate-180' : '' }}"></i>
        </button>

        {{-- Expanded Content --}}
        @if($isExpanded)
            <div class="border-t border-gray-800/50 px-5 pb-5">
                {{-- Characters --}}
                @if($characters->isNotEmpty())
                    <div class="py-4 space-y-2">
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-3">Characters</p>
                        @foreach($characters as $char)
                            @php $isCharSelected = $this->selectedCharacterId === $char->char_id; @endphp
                            <div class="rounded-lg bg-gray-800/30 overflow-hidden">
                                <button
                                    wire:click="selectCharacter({{ $char->char_id }})"
                                    class="w-full px-4 py-3 flex items-center justify-between hover:bg-gray-800/50 transition-colors text-left"
                                >
                                    <div class="flex items-center gap-3">
                                        <span class="w-2 h-2 rounded-full {{ $char->online ? 'bg-green-400 animate-pulse' : 'bg-gray-600' }}"></span>
                                        <div>
                                            <span class="text-gray-100 font-medium">{{ $char->name }}</span>
                                            <span class="text-gray-500 text-sm ml-2">{{ $char->class_name }}</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <div class="text-right">
                                            <span class="text-{{ $accentColor }}-400 font-semibold">Lv.{{ $char->base_level }}</span>
                                            <span class="text-gray-600">/{{ $char->job_level }}</span>
                                        </div>
                                        <i class="fas fa-chevron-down text-gray-600 text-xs transition-transform {{ $isCharSelected ? 'rotate-180' : '' }}"></i>
                                    </div>
                                </button>

                                @if($isCharSelected)
                                    <div class="px-4 pb-3 pt-2 border-t border-gray-700/50 flex items-center justify-between">
                                        <span class="text-xs text-gray-500">
                                            <i class="fas fa-map-marker-alt mr-1"></i>{{ $char->last_map ?? 'Unknown' }}
                                        </span>
                                        @if(!$char->online)
                                            <button
                                                wire:click="resetPosition({{ $char->char_id }})"
                                                wire:loading.attr="disabled"
                                                wire:target="resetPosition({{ $char->char_id }})"
                                                class="px-3 py-1.5 text-xs bg-gray-700 hover:bg-gray-600 text-gray-300 rounded-lg transition-colors"
                                            >
                                                <span wire:loading.remove wire:target="resetPosition({{ $char->char_id }})">Reset Position</span>
                                                <span wire:loading wire:target="resetPosition({{ $char->char_id }})"><i class="fas fa-spinner fa-spin"></i></span>
                                            </button>
                                        @else
                                            <span class="text-xs text-gray-600 italic">Log out to use actions</span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="py-6 text-center">
                        <i class="fas fa-user-plus text-3xl text-gray-700 mb-3"></i>
                        <p class="text-gray-400 mb-1">No characters yet</p>
                        <p class="text-sm text-gray-600">Log in-game to create your first character!</p>
                    </div>
                @endif

                {{-- Pending Rewards --}}
                @if($hasPendingRewards)
                    <div class="py-4 border-t border-gray-800/50">
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Pending Rewards</p>
                            <div class="flex items-center gap-2">
                                {{-- Select/Deselect All Button --}}
                                <button
                                    wire:click="toggleSelectAll({{ $account->id }})"
                                    class="px-3 py-1.5 bg-gray-700 hover:bg-gray-600 text-gray-300 font-medium text-xs rounded-lg transition-colors flex items-center gap-1.5"
                                >
                                    <i class="fas fa-check-double text-xs"></i>
                                    @php
                                        $accountRewardIds = $pendingRewards->pluck('id')->toArray();
                                        $allSelected = !empty($accountRewardIds) && count(array_intersect($this->selectedRewardIds ?? [], $accountRewardIds)) === count($accountRewardIds);
                                    @endphp
                                    {{ $allSelected ? 'Deselect All' : 'Select All' }}
                                </button>

                                {{-- Claim Selected Button --}}
                                @if($this->selectedRewardIds && count($this->selectedRewardIds) > 0)
                                    <button
                                        wire:click="claimSelectedRewards({{ $account->id }})"
                                        wire:loading.attr="disabled"
                                        class="group px-4 py-2 bg-gradient-to-r from-purple-600 via-purple-500 to-blue-600 hover:from-purple-500 hover:via-purple-400 hover:to-blue-500 text-white font-bold text-xs rounded-lg transition-all duration-300 flex items-center gap-2 shadow-lg shadow-purple-500/20 hover:shadow-purple-500/40 hover:-translate-y-0.5"
                                    >
                                        <i class="fas fa-gifts transition-transform group-hover:scale-110"></i>
                                        <span wire:loading.remove wire:target="claimSelectedRewards">Claim {{ count($this->selectedRewardIds) }} Selected</span>
                                        <span wire:loading wire:target="claimSelectedRewards"><i class="fas fa-spinner fa-spin"></i> Claiming...</span>
                                    </button>
                                @endif
                            </div>
                        </div>
                        <div class="space-y-2">
                            @foreach($pendingRewards as $reward)
                                <label class="flex items-center justify-between p-3 bg-amber-500/5 rounded-lg border border-amber-500/20 cursor-pointer hover:bg-amber-500/10 transition-colors group">
                                    <div class="flex items-center gap-3 flex-1">
                                        {{-- Checkbox --}}
                                        <input
                                            type="checkbox"
                                            wire:model.live="selectedRewardIds"
                                            value="{{ $reward->id }}"
                                            class="w-5 h-5 rounded border-2 border-gray-600 bg-gray-800/50 text-amber-500 focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 focus:ring-offset-gray-900 cursor-pointer transition-all checked:border-amber-500 checked:bg-amber-500"
                                        >

                                        {{-- Item Icon --}}
                                        <div class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center overflow-hidden border border-gray-700 group-hover:border-amber-500/30 transition-colors">
                                            @if($reward->item)
                                                <img src="{{ $reward->item->icon() }}" alt="" class="max-h-full max-w-full object-contain">
                                            @else
                                                <i class="fas fa-box text-gray-500"></i>
                                            @endif
                                        </div>

                                        {{-- Item Details --}}
                                        <div>
                                            <p class="text-gray-100 text-sm font-medium">
                                                @if($reward->refine_level > 0)<span class="text-amber-400">+{{ $reward->refine_level }}</span> @endif{{ $reward->item?->name ?? 'Unknown' }}
                                            </p>
                                            <p class="text-xs text-gray-500">x{{ $reward->quantity }} &bull; {{ $reward->tier?->name ?? 'Bonus' }}</p>
                                        </div>
                                    </div>

                                    {{-- Individual Claim Button --}}
                                    <button
                                        type="button"
                                        wire:click.stop="startRewardClaim({{ $reward->id }}, {{ $account->id }})"
                                        class="px-4 py-2 bg-amber-500 hover:bg-amber-400 text-gray-900 font-bold text-xs rounded-lg transition-colors"
                                    >
                                        Claim
                                    </button>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Account Actions --}}
                <div class="pt-4 border-t border-gray-800/50 flex flex-wrap gap-2">
                    <button
                        wire:click="showPasswordResetForm({{ $account->id }})"
                        class="px-4 py-2 bg-gray-800 hover:bg-gray-700 text-gray-300 rounded-lg transition-colors text-sm flex items-center gap-2 border border-gray-700"
                    >
                        <i class="fas fa-key"></i> Reset Password
                    </button>
                    @if($account->hasSecurityCode())
                        <button
                            wire:click="resetSecurity({{ $account->id }})"
                            wire:loading.attr="disabled"
                            wire:target="resetSecurity({{ $account->id }})"
                            wire:confirm="Are you sure you want to reset @security? You'll need to set a new one in-game."
                            class="px-4 py-2 bg-gray-800 hover:bg-red-900/50 text-gray-300 hover:text-red-300 rounded-lg transition-colors text-sm flex items-center gap-2 border border-gray-700 hover:border-red-500/50"
                        >
                            <span wire:loading.remove wire:target="resetSecurity({{ $account->id }})">
                                <i class="fas fa-shield-alt"></i> Reset @security
                            </span>
                            <span wire:loading wire:target="resetSecurity({{ $account->id }})">
                                <i class="fas fa-spinner fa-spin"></i> Resetting...
                            </span>
                        </button>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
