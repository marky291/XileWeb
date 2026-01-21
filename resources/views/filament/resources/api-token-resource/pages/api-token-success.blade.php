<x-filament-panels::page>
    <div class="space-y-6">
        <x-filament::section icon="heroicon-o-check-circle" icon-color="success">
            <x-slot name="heading">
                Token Created Successfully
            </x-slot>

            <x-slot name="description">
                Your API token "{{ $tokenName }}" has been created.
            </x-slot>

            <div class="space-y-4">
                <div class="p-4 rounded-lg bg-warning-50 dark:bg-warning-950 border border-warning-200 dark:border-warning-800">
                    <div class="flex items-start gap-3">
                        <x-heroicon-o-exclamation-triangle class="w-5 h-5 text-warning-600 dark:text-warning-400 shrink-0 mt-0.5" />
                        <div>
                            <p class="text-sm font-medium text-warning-800 dark:text-warning-200">
                                Copy your token now!
                            </p>
                            <p class="text-sm text-warning-700 dark:text-warning-300 mt-1">
                                This token will only be displayed once. Store it securely - you won't be able to see it again.
                            </p>
                        </div>
                    </div>
                </div>

                <div
                    x-data="{
                        token: @js($plainTextToken),
                        copied: false,
                        copyToken() {
                            navigator.clipboard.writeText(this.token);
                            this.copied = true;
                            setTimeout(() => this.copied = false, 2000);
                        }
                    }"
                    class="space-y-3"
                >
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Your API Token</label>

                    <div class="flex gap-2">
                        <div class="flex-1 relative">
                            <input
                                type="text"
                                readonly
                                :value="token"
                                class="w-full px-3 py-2 pr-10 rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-sm font-mono text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                            />
                        </div>

                        <x-filament::button
                            color="gray"
                            x-on:click="copyToken()"
                            icon="heroicon-o-clipboard-document"
                        >
                            <span x-show="!copied">Copy</span>
                            <span x-show="copied" x-cloak>Copied!</span>
                        </x-filament::button>
                    </div>
                </div>

                <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Usage Example</h4>
                    <div class="bg-gray-900 rounded-lg p-4 overflow-x-auto">
                        <pre class="text-sm text-gray-100 font-mono">curl -X GET "{{ url('/api/v1/items') }}" \
  -H "Authorization: Bearer {{ $plainTextToken }}" \
  -H "Accept: application/json"</pre>
                    </div>
                </div>
            </div>
        </x-filament::section>

        <div class="flex justify-end gap-3">
            <x-filament::button
                tag="a"
                :href="\App\Filament\Resources\ApiTokenResource::getUrl('create')"
                color="gray"
            >
                Create Another Token
            </x-filament::button>

            <x-filament::button
                tag="a"
                :href="\App\Filament\Resources\ApiTokenResource::getUrl('index')"
            >
                View All Tokens
            </x-filament::button>
        </div>
    </div>
</x-filament-panels::page>
