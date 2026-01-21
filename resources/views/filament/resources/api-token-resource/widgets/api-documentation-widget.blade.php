<x-filament-widgets::widget>
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
            Use your API token to authenticate requests to the following endpoints
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

            {{-- Items API --}}
            <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                <h3 class="text-base font-semibold text-gray-950 dark:text-white mb-4">Items API</h3>

                {{-- List Items --}}
                <div class="mb-6">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="px-2 py-1 rounded text-xs font-semibold bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">GET</span>
                        <code class="text-sm font-mono text-gray-700 dark:text-gray-300">{{ $this->getBaseUrl() }}/api/v1/items</code>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                        List all items with optional filtering and pagination.
                    </p>

                    <div class="text-sm mb-3">
                        <span class="font-medium text-gray-700 dark:text-gray-300">Query Parameters:</span>
                        <ul class="mt-1 space-y-1 text-gray-600 dark:text-gray-400">
                            <li><code class="px-1 py-0.5 rounded bg-gray-100 dark:bg-gray-800 text-xs font-mono">search</code> - Search by name, aegis_name, or item_id</li>
                            <li><code class="px-1 py-0.5 rounded bg-gray-100 dark:bg-gray-800 text-xs font-mono">type</code> - Filter by item type (Weapon, Armor, Healing, etc.)</li>
                            <li><code class="px-1 py-0.5 rounded bg-gray-100 dark:bg-gray-800 text-xs font-mono">is_xileretro</code> - Filter by game version (true/false)</li>
                            <li><code class="px-1 py-0.5 rounded bg-gray-100 dark:bg-gray-800 text-xs font-mono">per_page</code> - Items per page (default: 15, max: 100)</li>
                        </ul>
                    </div>

                    <div class="bg-gray-900 rounded-lg p-4 overflow-x-auto">
                        <pre class="text-sm text-gray-100 font-mono">curl -X GET "{{ $this->getBaseUrl() }}/api/v1/items?search=sword&type=Weapon&per_page=10" \
  -H "Authorization: Bearer YOUR_API_TOKEN" \
  -H "Accept: application/json"</pre>
                    </div>
                </div>

                {{-- Get Single Item --}}
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="px-2 py-1 rounded text-xs font-semibold bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">GET</span>
                        <code class="text-sm font-mono text-gray-700 dark:text-gray-300">{{ $this->getBaseUrl() }}/api/v1/items/{id}</code>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                        Get a single item by its database ID.
                    </p>

                    <div class="bg-gray-900 rounded-lg p-4 overflow-x-auto">
                        <pre class="text-sm text-gray-100 font-mono">curl -X GET "{{ $this->getBaseUrl() }}/api/v1/items/1" \
  -H "Authorization: Bearer YOUR_API_TOKEN" \
  -H "Accept: application/json"</pre>
                    </div>
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
    "subtype": null,
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
    "icon_url": "/assets/xilero/item_icons/1101.png",
    "collection_url": "/assets/xilero/item_collection/1101.png"
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
                    <span class="text-sm text-gray-600 dark:text-gray-400">Required for all GET endpoints</span>
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
