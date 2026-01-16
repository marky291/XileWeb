<x-filament-panels::page>
    <form wire:submit="apply">
        {{ $this->form }}

        <div class="mt-6">
            <x-filament::button type="submit" color="success" size="lg" icon="heroicon-o-check-circle">
                Apply Donation
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
