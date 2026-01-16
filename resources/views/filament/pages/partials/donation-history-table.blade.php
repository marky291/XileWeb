@php
    $donations = $getDonations();
@endphp

<div class="mt-4">
    <h3 class="text-sm font-medium text-gray-950 dark:text-white mb-3">Recent Donation History</h3>

    @if($donations->isEmpty())
        <p class="text-sm text-gray-500 dark:text-gray-400 italic">No previous donations found for this user.</p>
    @else
        <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Amount</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Payment</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Base</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Bonus</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Notes</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Applied By</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($donations as $donation)
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                {{ $donation->created_at->format('M j, Y') }}
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                ${{ number_format($donation->amount, 2) }}
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-sm">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $donation->payment_method === 'crypto' ? 'bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' }}">
                                    {{ $donation->paymentMethodName() }}
                                </span>
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                {{ number_format($donation->base_ubers) }}
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                {{ number_format($donation->bonus_ubers) }}
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-sm">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $donation->isReverted() ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' }}">
                                    {{ number_format($donation->total_ubers) }}
                                </span>
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-sm">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $donation->isReverted() ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' }}">
                                    {{ $donation->isReverted() ? 'Reverted' : 'Active' }}
                                </span>
                            </td>
                            <td class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400 max-w-[150px] truncate" title="{{ $donation->notes }}">
                                {{ $donation->notes ?: '-' }}
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $donation->admin?->name ?: '-' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Showing last {{ $donations->count() }} donations</p>
    @endif
</div>
