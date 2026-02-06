@php
    $tiers = $getApplicableTiers();
@endphp

<div class="mt-4">
    <h3 class="text-sm font-medium text-gray-950 dark:text-white mb-3">Bonus Reward Items</h3>

    @if($tiers->isEmpty())
        <p class="text-sm text-gray-500 dark:text-gray-400 italic">No bonus rewards for this donation amount.</p>
    @else
        <div class="space-y-3">
            @foreach($tiers as $tier)
                <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 p-3">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100">
                            {{ $tier->name }}
                        </h4>
                        <div class="flex gap-2">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $tier->isPerDonation() ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' }}">
                                {{ $tier->isPerDonation() ? 'Per Donation' : 'Lifetime Milestone' }}
                            </span>
                            @if($tier->is_xilero && $tier->is_xileretro)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                    Both Servers
                                </span>
                            @elseif($tier->is_xilero)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                                    XileRO Only
                                </span>
                            @elseif($tier->is_xileretro)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-200">
                                    XileRetro Only
                                </span>
                            @endif
                        </div>
                    </div>
                    @if($tier->description)
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">{{ $tier->description }}</p>
                    @endif
                    @if($tier->items->isNotEmpty())
                        <div class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-600">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                                <thead class="bg-gray-100 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Icon</th>
                                        <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Item</th>
                                        <th scope="col" class="px-3 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Qty</th>
                                        <th scope="col" class="px-3 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Refine</th>
                                        <th scope="col" class="px-3 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Item ID</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                                    @foreach($tier->items as $item)
                                        <tr>
                                            <td class="px-3 py-2 whitespace-nowrap">
                                                <img src="{{ $item->icon() }}" alt="{{ $item->name }}" class="w-6 h-6" />
                                            </td>
                                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                {{ $item->name }}
                                            </td>
                                            <td class="px-3 py-2 whitespace-nowrap text-center text-sm text-gray-700 dark:text-gray-300">
                                                {{ $item->pivot->quantity }}
                                            </td>
                                            <td class="px-3 py-2 whitespace-nowrap text-center text-sm text-gray-700 dark:text-gray-300">
                                                @if($item->pivot->refine_level > 0)
                                                    <span class="text-primary-600 dark:text-primary-400 font-medium">+{{ $item->pivot->refine_level }}</span>
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
                                            </td>
                                            <td class="px-3 py-2 whitespace-nowrap text-right text-xs font-mono text-gray-500 dark:text-gray-400">
                                                {{ $item->item_id }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-xs text-gray-500 dark:text-gray-400 italic">No items configured for this tier.</p>
                    @endif
                </div>
            @endforeach
        </div>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
            These items will be added to the user's pending rewards after applying the donation.
        </p>
    @endif
</div>
