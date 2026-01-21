<x-filament-widgets::widget>
    <div class="space-y-4">
        {{-- AI Prompt Section --}}
        <x-filament::section
            icon="heroicon-o-cpu-chip"
            icon-color="primary"
            collapsible
        >
            <x-slot name="heading">
                Claude AI Prompt
            </x-slot>

            <x-slot name="description">
                Token-efficient API reference for Claude
            </x-slot>

            <div
                x-data="{
                    prompt: @js($this->getAiPrompt()),
                    copied: false,
                    copyPrompt() {
                        navigator.clipboard.writeText(this.prompt);
                        this.copied = true;
                        setTimeout(() => this.copied = false, 2000);
                    }
                }"
                class="space-y-4"
            >
                <div class="flex items-center justify-between">
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Paste into Claude conversation, replace {TOKEN} with your API token.
                    </p>
                    <x-filament::button
                        color="primary"
                        x-on:click="copyPrompt()"
                        icon="heroicon-o-clipboard-document"
                    >
                        <span x-show="!copied">Copy</span>
                        <span x-show="copied" x-cloak>Copied!</span>
                    </x-filament::button>
                </div>

                <div class="bg-gray-900 rounded-lg p-4 overflow-auto max-h-64">
                    <pre class="text-xs text-gray-100 font-mono whitespace-pre-wrap">{{ $this->getAiPrompt() }}</pre>
                </div>
            </div>
        </x-filament::section>

        {{-- Human-Readable Documentation --}}
        <x-filament::section
            icon="heroicon-o-book-open"
            icon-color="info"
            collapsible
            collapsed
        >
            <x-slot name="heading">
                API Documentation
            </x-slot>

            <x-slot name="description">
                Technical reference for direct API integration
            </x-slot>

            <div class="space-y-6">
                {{-- Authentication --}}
                <div>
                    <h3 class="text-base font-semibold text-gray-950 dark:text-white mb-2">Authentication</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                        Include your API token in the <code class="px-1.5 py-0.5 rounded bg-gray-100 dark:bg-gray-800 text-sm font-mono">Authorization</code> header:
                    </p>
                    <div class="bg-gray-900 rounded-lg p-4 overflow-x-auto">
                        <pre class="text-sm text-gray-100 font-mono">Authorization: Bearer YOUR_API_TOKEN</pre>
                    </div>
                </div>

                {{-- List Items --}}
                <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="px-2 py-1 rounded text-xs font-semibold bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">GET</span>
                        <code class="text-sm font-mono text-gray-700 dark:text-gray-300">/api/v1/items</code>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                        Search and filter items with pagination.
                    </p>

                    <div class="text-sm mb-3">
                        <span class="font-medium text-gray-700 dark:text-gray-300">Query Parameters:</span>
                        <div class="mt-2 overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="border-b border-gray-200 dark:border-gray-700">
                                        <th class="text-left py-2 pr-4 font-medium text-gray-700 dark:text-gray-300">Parameter</th>
                                        <th class="text-left py-2 pr-4 font-medium text-gray-700 dark:text-gray-300">Description</th>
                                        <th class="text-left py-2 font-medium text-gray-700 dark:text-gray-300">Example</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-600 dark:text-gray-400">
                                    <tr class="border-b border-gray-100 dark:border-gray-800">
                                        <td class="py-2 pr-4"><code class="px-1 py-0.5 rounded bg-gray-100 dark:bg-gray-800 text-xs font-mono">search</code></td>
                                        <td class="py-2 pr-4">Search by name, aegis_name, or item_id</td>
                                        <td class="py-2"><code class="text-xs">search=Sword</code></td>
                                    </tr>
                                    <tr class="border-b border-gray-100 dark:border-gray-800">
                                        <td class="py-2 pr-4"><code class="px-1 py-0.5 rounded bg-gray-100 dark:bg-gray-800 text-xs font-mono">ids</code></td>
                                        <td class="py-2 pr-4">Comma-separated item_ids</td>
                                        <td class="py-2"><code class="text-xs">ids=1101,1102</code></td>
                                    </tr>
                                    <tr class="border-b border-gray-100 dark:border-gray-800">
                                        <td class="py-2 pr-4"><code class="px-1 py-0.5 rounded bg-gray-100 dark:bg-gray-800 text-xs font-mono">type</code></td>
                                        <td class="py-2 pr-4">Item type(s), comma-separated</td>
                                        <td class="py-2"><code class="text-xs">type=Weapon,Armor</code></td>
                                    </tr>
                                    <tr class="border-b border-gray-100 dark:border-gray-800">
                                        <td class="py-2 pr-4"><code class="px-1 py-0.5 rounded bg-gray-100 dark:bg-gray-800 text-xs font-mono">subtype</code></td>
                                        <td class="py-2 pr-4">Weapon subtype(s)</td>
                                        <td class="py-2"><code class="text-xs">subtype=Dagger</code></td>
                                    </tr>
                                    <tr class="border-b border-gray-100 dark:border-gray-800">
                                        <td class="py-2 pr-4"><code class="px-1 py-0.5 rounded bg-gray-100 dark:bg-gray-800 text-xs font-mono">is_xileretro</code></td>
                                        <td class="py-2 pr-4">Game version filter</td>
                                        <td class="py-2"><code class="text-xs">is_xileretro=true</code></td>
                                    </tr>
                                    <tr class="border-b border-gray-100 dark:border-gray-800">
                                        <td class="py-2 pr-4"><code class="px-1 py-0.5 rounded bg-gray-100 dark:bg-gray-800 text-xs font-mono">refineable</code></td>
                                        <td class="py-2 pr-4">Only refineable items</td>
                                        <td class="py-2"><code class="text-xs">refineable=true</code></td>
                                    </tr>
                                    <tr class="border-b border-gray-100 dark:border-gray-800">
                                        <td class="py-2 pr-4"><code class="px-1 py-0.5 rounded bg-gray-100 dark:bg-gray-800 text-xs font-mono">min_slots</code></td>
                                        <td class="py-2 pr-4">Minimum card slots</td>
                                        <td class="py-2"><code class="text-xs">min_slots=2</code></td>
                                    </tr>
                                    <tr class="border-b border-gray-100 dark:border-gray-800">
                                        <td class="py-2 pr-4"><code class="px-1 py-0.5 rounded bg-gray-100 dark:bg-gray-800 text-xs font-mono">job</code></td>
                                        <td class="py-2 pr-4">Filter by equippable job</td>
                                        <td class="py-2"><code class="text-xs">job=Knight</code></td>
                                    </tr>
                                    <tr>
                                        <td class="py-2 pr-4"><code class="px-1 py-0.5 rounded bg-gray-100 dark:bg-gray-800 text-xs font-mono">per_page</code></td>
                                        <td class="py-2 pr-4">Results per page (max 100)</td>
                                        <td class="py-2"><code class="text-xs">per_page=50</code></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Example Requests</h4>
                    <div class="space-y-2">
                        <div class="bg-gray-100 dark:bg-gray-800 rounded-lg p-3">
                            <code class="text-sm font-mono text-gray-700 dark:text-gray-300 break-all">{{ $this->getBaseUrl() }}/api/v1/items?search=Excalibur</code>
                        </div>
                        <div class="bg-gray-100 dark:bg-gray-800 rounded-lg p-3">
                            <code class="text-sm font-mono text-gray-700 dark:text-gray-300 break-all">{{ $this->getBaseUrl() }}/api/v1/items?type=Weapon&subtype=Dagger&min_slots=1</code>
                        </div>
                        <div class="bg-gray-100 dark:bg-gray-800 rounded-lg p-3">
                            <code class="text-sm font-mono text-gray-700 dark:text-gray-300 break-all">{{ $this->getBaseUrl() }}/api/v1/items?type=Card&per_page=100</code>
                        </div>
                    </div>
                </div>

                {{-- Bulk Lookup --}}
                <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="px-2 py-1 rounded text-xs font-semibold bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">POST</span>
                        <code class="text-sm font-mono text-gray-700 dark:text-gray-300">/api/v1/items/bulk</code>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                        Retrieve multiple items by item_id in a single request. Limited to 100 items.
                    </p>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                        <span class="font-medium">Body:</span> <code class="px-1 py-0.5 rounded bg-gray-100 dark:bg-gray-800 text-xs font-mono">ids=1101,1201,1301,2101</code>
                    </p>

                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Endpoint</h4>
                    <div class="bg-gray-100 dark:bg-gray-800 rounded-lg p-3">
                        <code class="text-sm font-mono text-gray-700 dark:text-gray-300 break-all">{{ $this->getBaseUrl() }}/api/v1/items/bulk</code>
                    </div>
                </div>

                {{-- Get Single Item --}}
                <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="px-2 py-1 rounded text-xs font-semibold bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">GET</span>
                        <code class="text-sm font-mono text-gray-700 dark:text-gray-300">/api/v1/items/{id}</code>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                        Get a single item by its database ID (not item_id).
                    </p>

                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Example Request</h4>
                    <div class="bg-gray-100 dark:bg-gray-800 rounded-lg p-3">
                        <code class="text-sm font-mono text-gray-700 dark:text-gray-300 break-all">{{ $this->getBaseUrl() }}/api/v1/items/1</code>
                    </div>
                </div>

                {{-- Available Types --}}
                <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                    <h3 class="text-base font-semibold text-gray-950 dark:text-white mb-3">Item Types</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($this->getItemTypes() as $type)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200">
                                {{ $type }}
                            </span>
                        @endforeach
                    </div>
                </div>

                {{-- Available Subtypes --}}
                <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                    <h3 class="text-base font-semibold text-gray-950 dark:text-white mb-3">Weapon Subtypes</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($this->getItemSubtypes() as $subtype)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200">
                                {{ $subtype }}
                            </span>
                        @endforeach
                    </div>
                </div>

                {{-- Example Response --}}
                <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                    <h3 class="text-base font-semibold text-gray-950 dark:text-white mb-2">Example Response</h3>
                    <div class="bg-gray-900 rounded-lg p-4 overflow-x-auto">
                        <pre class="text-sm text-gray-100 font-mono">{
  "data": {
    "id": 1,
    "item_id": 1101,
    "aegis_name": "Sword",
    "name": "Sword",
    "description": "A basic sword.",
    "type": "Weapon",
    "subtype": "1hSword",
    "weight": 50,
    "buy": 100,
    "sell": 50,
    "attack": 25,
    "defense": 0,
    "slots": 3,
    "refineable": true,
    "jobs": ["Swordman", "Knight"],
    "locations": null,
    "flags": null,
    "trade": null,
    "script": null,
    "equip_script": null,
    "unequip_script": null,
    "is_xileretro": false,
    "view_id": 1,
    "resource_name": "sword",
    "icon_url": "https://example.com/storage/xilero/item/1101.png",
    "collection_url": "https://example.com/storage/xilero/collection/1101.png"
  }
}</pre>
                    </div>
                </div>

                {{-- Required Abilities --}}
                <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                    <h3 class="text-base font-semibold text-gray-950 dark:text-white mb-2">Required Token Abilities</h3>
                    <div class="flex gap-2">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                            read
                        </span>
                        <span class="text-sm text-gray-600 dark:text-gray-400">Required for all endpoints</span>
                    </div>
                </div>
            </div>
        </x-filament::section>
    </div>
</x-filament-widgets::widget>
