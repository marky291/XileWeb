<div>
    <section id="myaccount" class="bg-clash-bg relative overflow-hidden py-12 px-4 lg:px-24">
        <div class="max-w-screen-xl w-full mx-auto">
            <h2 class="text-2xl font-bold text-gray-100 mb-6">Welcome {{ Str::ucfirst(auth()->user()->name) }}!</h2>

            @if (session()->has('success'))
                <div class="mb-4 p-4 bg-green-900/50 border border-green-500 rounded text-green-300">
                    {{ session('success') }}
                </div>
            @endif

            @if (session()->has('error'))
                <div class="mb-4 p-4 bg-red-900/50 border border-red-500 rounded text-red-300">
                    {{ session('error') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Character Grid --}}
                <div class="lg:col-span-1">
                    <h3 class="text-lg font-semibold text-gray-200 mb-4">Your Characters</h3>
                    <div class="space-y-3">
                        @forelse($characters as $character)
                            <button
                                wire:click="selectCharacter({{ $character->char_id }})"
                                wire:key="char-{{ $character->char_id }}"
                                class="w-full text-left border rounded-lg p-4 transition-all duration-200 {{ $selectedCharacterId === $character->char_id ? 'bg-indigo-900/50 border-indigo-500' : 'bg-gray-900/50 border-gray-700 hover:border-gray-500' }}"
                            >
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-gray-100 font-semibold">{{ $character->name }}</p>
                                        <p class="text-gray-400 text-sm">{{ $character->class_name }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-gray-300 text-sm">Lv. {{ $character->base_level }}/{{ $character->job_level }}</p>
                                        <span class="inline-flex items-center text-xs {{ $character->online ? 'text-green-400' : 'text-gray-500' }}">
                                            <span class="w-2 h-2 rounded-full mr-1 {{ $character->online ? 'bg-green-400' : 'bg-gray-500' }}"></span>
                                            {{ $character->online ? 'Online' : 'Offline' }}
                                        </span>
                                    </div>
                                </div>
                            </button>
                        @empty
                            <div class="border border-gray-700 rounded-lg p-6 text-center">
                                <p class="text-gray-400">No characters found.</p>
                                <p class="text-gray-500 text-sm mt-2">Create a character in-game to see them here.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Character Details Panel --}}
                <div class="lg:col-span-2">
                    @if($selectedChar)
                        <div class="bg-gray-900/50 border border-gray-700 rounded-lg p-6">
                            <div class="flex items-start justify-between mb-6">
                                <div>
                                    <h3 class="text-2xl font-bold text-gray-100">{{ $selectedChar->name }}</h3>
                                    <p class="text-indigo-400">{{ $selectedChar->class_name }}</p>
                                    @if($selectedChar->guild)
                                        <p class="text-gray-400 text-sm mt-1">
                                            <i class="fas fa-shield-alt mr-1"></i>
                                            {{ $selectedChar->guild->name }}
                                        </p>
                                    @endif
                                </div>
                                <div class="text-right">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm {{ $selectedChar->online ? 'bg-green-900/50 text-green-400' : 'bg-gray-800 text-gray-400' }}">
                                        <span class="w-2 h-2 rounded-full mr-2 {{ $selectedChar->online ? 'bg-green-400' : 'bg-gray-500' }}"></span>
                                        {{ $selectedChar->online ? 'Online' : 'Offline' }}
                                    </span>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                {{-- Level & Experience --}}
                                <div class="bg-gray-800/50 rounded-lg p-4">
                                    <h4 class="text-sm font-semibold text-gray-400 uppercase tracking-wide mb-3">Level</h4>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <p class="text-gray-500 text-xs">Base Level</p>
                                            <p class="text-2xl font-bold text-gray-100">{{ $selectedChar->base_level }}</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-500 text-xs">Job Level</p>
                                            <p class="text-2xl font-bold text-gray-100">{{ $selectedChar->job_level }}</p>
                                        </div>
                                    </div>
                                </div>

                                {{-- HP / SP / Zeny --}}
                                <div class="bg-gray-800/50 rounded-lg p-4">
                                    <h4 class="text-sm font-semibold text-gray-400 uppercase tracking-wide mb-3">Resources</h4>
                                    <div class="space-y-2">
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-400">HP</span>
                                            <span class="text-red-400 font-semibold">{{ number_format($selectedChar->hp) }} / {{ number_format($selectedChar->max_hp) }}</span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-400">SP</span>
                                            <span class="text-blue-400 font-semibold">{{ number_format($selectedChar->sp) }} / {{ number_format($selectedChar->max_sp) }}</span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-400">Zeny</span>
                                            <span class="text-yellow-400 font-semibold">{{ number_format($selectedChar->zeny) }}z</span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Stats --}}
                                <div class="bg-gray-800/50 rounded-lg p-4">
                                    <h4 class="text-sm font-semibold text-gray-400 uppercase tracking-wide mb-3">Stats</h4>
                                    <div class="grid grid-cols-3 gap-3">
                                        <div class="text-center">
                                            <p class="text-gray-500 text-xs">STR</p>
                                            <p class="text-xl font-bold text-gray-100">{{ $selectedChar->str }}</p>
                                        </div>
                                        <div class="text-center">
                                            <p class="text-gray-500 text-xs">AGI</p>
                                            <p class="text-xl font-bold text-gray-100">{{ $selectedChar->agi }}</p>
                                        </div>
                                        <div class="text-center">
                                            <p class="text-gray-500 text-xs">VIT</p>
                                            <p class="text-xl font-bold text-gray-100">{{ $selectedChar->vit }}</p>
                                        </div>
                                        <div class="text-center">
                                            <p class="text-gray-500 text-xs">INT</p>
                                            <p class="text-xl font-bold text-gray-100">{{ $selectedChar->int }}</p>
                                        </div>
                                        <div class="text-center">
                                            <p class="text-gray-500 text-xs">DEX</p>
                                            <p class="text-xl font-bold text-gray-100">{{ $selectedChar->dex }}</p>
                                        </div>
                                        <div class="text-center">
                                            <p class="text-gray-500 text-xs">LUK</p>
                                            <p class="text-xl font-bold text-gray-100">{{ $selectedChar->luk }}</p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Traits (4th Job) --}}
                                @if($selectedChar->pow || $selectedChar->sta || $selectedChar->wis || $selectedChar->spl || $selectedChar->con || $selectedChar->crt)
                                    <div class="bg-gray-800/50 rounded-lg p-4">
                                        <h4 class="text-sm font-semibold text-gray-400 uppercase tracking-wide mb-3">Traits</h4>
                                        <div class="grid grid-cols-3 gap-3">
                                            <div class="text-center">
                                                <p class="text-gray-500 text-xs">POW</p>
                                                <p class="text-xl font-bold text-purple-400">{{ $selectedChar->pow }}</p>
                                            </div>
                                            <div class="text-center">
                                                <p class="text-gray-500 text-xs">STA</p>
                                                <p class="text-xl font-bold text-purple-400">{{ $selectedChar->sta }}</p>
                                            </div>
                                            <div class="text-center">
                                                <p class="text-gray-500 text-xs">WIS</p>
                                                <p class="text-xl font-bold text-purple-400">{{ $selectedChar->wis }}</p>
                                            </div>
                                            <div class="text-center">
                                                <p class="text-gray-500 text-xs">SPL</p>
                                                <p class="text-xl font-bold text-purple-400">{{ $selectedChar->spl }}</p>
                                            </div>
                                            <div class="text-center">
                                                <p class="text-gray-500 text-xs">CON</p>
                                                <p class="text-xl font-bold text-purple-400">{{ $selectedChar->con }}</p>
                                            </div>
                                            <div class="text-center">
                                                <p class="text-gray-500 text-xs">CRT</p>
                                                <p class="text-xl font-bold text-purple-400">{{ $selectedChar->crt }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                {{-- Location --}}
                                <div class="bg-gray-800/50 rounded-lg p-4 md:col-span-2">
                                    <h4 class="text-sm font-semibold text-gray-400 uppercase tracking-wide mb-3">Location</h4>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <p class="text-gray-500 text-xs">Current Location</p>
                                            <p class="text-gray-100">{{ $selectedChar->last_map }} ({{ $selectedChar->last_x }}, {{ $selectedChar->last_y }})</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-500 text-xs">Save Point</p>
                                            <p class="text-gray-100">{{ $selectedChar->save_map }} ({{ $selectedChar->save_x }}, {{ $selectedChar->save_y }})</p>
                                        </div>
                                    </div>
                                    @if($selectedChar->last_login)
                                        <div class="mt-3 pt-3 border-t border-gray-700">
                                            <p class="text-gray-500 text-xs">Last Login</p>
                                            <p class="text-gray-300">{{ \Carbon\Carbon::parse($selectedChar->last_login)->diffForHumans() }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="mt-6 pt-6 border-t border-gray-700">
                                <h4 class="text-sm font-semibold text-gray-400 uppercase tracking-wide mb-3">Actions</h4>
                                <div class="flex flex-wrap gap-3">
                                    @if(!$selectedChar->online)
                                        <button
                                            wire:click="resetPosition({{ $selectedChar->char_id }})"
                                            wire:confirm="Are you sure you want to reset {{ $selectedChar->name }}'s position to Prontera?"
                                            class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg transition-colors duration-200 flex items-center gap-2"
                                        >
                                            <i class="fas fa-map-marker-alt"></i>
                                            Reset Position
                                        </button>
                                    @else
                                        <span class="px-4 py-2 bg-gray-700 text-gray-400 rounded-lg cursor-not-allowed flex items-center gap-2" title="Character must be offline">
                                            <i class="fas fa-map-marker-alt"></i>
                                            Reset Position (Offline only)
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="bg-gray-900/50 border border-gray-700 rounded-lg p-12 text-center">
                            <i class="fas fa-user-circle text-6xl text-gray-600 mb-4"></i>
                            <h3 class="text-xl font-semibold text-gray-300 mb-2">Select a Character</h3>
                            <p class="text-gray-500">Click on a character from the list to view their details.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
</div>
