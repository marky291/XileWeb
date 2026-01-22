<x-filament-panels::page>
    <div class="space-y-6">
        @if($this->newToken)
            {{-- New Token Created - Show AI Prompt with Token --}}
            <x-filament::section icon="heroicon-o-check-circle" icon-color="success">
                <x-slot name="heading">Token Created - Copy AI Prompt</x-slot>
                <x-slot name="description">This prompt will not be shown again</x-slot>

                <div
                    x-data="{
                        prompt: @js($this->getAiPrompt($this->newToken)),
                        copied: false,
                        copy() {
                            navigator.clipboard.writeText(this.prompt);
                            this.copied = true;
                            setTimeout(() => this.copied = false, 2000);
                        }
                    }"
                    class="space-y-4"
                >
                    <div class="bg-gray-900 rounded-lg p-4 overflow-auto max-h-72">
                        <pre class="text-xs text-gray-100 font-mono whitespace-pre-wrap">{{ $this->getAiPrompt($this->newToken) }}</pre>
                    </div>

                    <div class="flex justify-end">
                        <x-filament::button
                            color="primary"
                            x-on:click="copy()"
                            icon="heroicon-o-clipboard-document"
                        >
                            <span x-show="!copied">Copy Prompt</span>
                            <span x-show="copied" x-cloak>Copied!</span>
                        </x-filament::button>
                    </div>
                </div>
            </x-filament::section>
        @else
            {{-- Existing Token - Need to enter token --}}
            <x-filament::section>
                <x-slot name="heading">Claude AI Prompt</x-slot>
                <x-slot name="description">Enter your token to generate the prompt</x-slot>

                <div
                    x-data="{
                        token: '',
                        copied: false,
                        get prompt() {
                            return @js($this->getAiPrompt('{TOKEN}')).replace('{TOKEN}', this.token || '{TOKEN}');
                        },
                        copy() {
                            if (!this.token) {
                                alert('Enter your token first');
                                return;
                            }
                            navigator.clipboard.writeText(this.prompt);
                            this.copied = true;
                            setTimeout(() => this.copied = false, 2000);
                        }
                    }"
                    class="space-y-4"
                >
                    <input
                        type="text"
                        x-model="token"
                        placeholder="Paste your API token"
                        class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm font-mono text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                    />

                    <div class="bg-gray-900 rounded-lg p-4 overflow-auto max-h-72">
                        <pre x-text="prompt" class="text-xs text-gray-100 font-mono whitespace-pre-wrap"></pre>
                    </div>

                    <div class="flex justify-end">
                        <x-filament::button
                            color="primary"
                            x-on:click="copy()"
                            icon="heroicon-o-clipboard-document"
                        >
                            <span x-show="!copied">Copy Prompt</span>
                            <span x-show="copied" x-cloak>Copied!</span>
                        </x-filament::button>
                    </div>
                </div>
            </x-filament::section>
        @endif

        {{-- Token Details --}}
        <x-filament::section collapsible collapsed>
            <x-slot name="heading">Token Details</x-slot>

            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                <div>
                    <dt class="font-medium text-gray-500 dark:text-gray-400">User</dt>
                    <dd class="mt-1 text-gray-900 dark:text-white">{{ $record->tokenable->email }}</dd>
                </div>
                <div>
                    <dt class="font-medium text-gray-500 dark:text-gray-400">Abilities</dt>
                    <dd class="mt-1 flex gap-1">
                        @foreach($record->abilities as $ability)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-primary-100 text-primary-800 dark:bg-primary-900 dark:text-primary-200">
                                {{ $ability }}
                            </span>
                        @endforeach
                    </dd>
                </div>
                <div>
                    <dt class="font-medium text-gray-500 dark:text-gray-400">Created</dt>
                    <dd class="mt-1 text-gray-900 dark:text-white">{{ $record->created_at->format('M j, Y g:i A') }}</dd>
                </div>
                <div>
                    <dt class="font-medium text-gray-500 dark:text-gray-400">Last Used</dt>
                    <dd class="mt-1 text-gray-900 dark:text-white">{{ $record->last_used_at?->format('M j, Y g:i A') ?? 'Never' }}</dd>
                </div>
                <div>
                    <dt class="font-medium text-gray-500 dark:text-gray-400">Expires</dt>
                    <dd class="mt-1 text-gray-900 dark:text-white">{{ $record->expires_at?->format('M j, Y g:i A') ?? 'Never' }}</dd>
                </div>
            </dl>
        </x-filament::section>
    </div>
</x-filament-panels::page>
