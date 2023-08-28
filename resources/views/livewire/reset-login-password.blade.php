<div>
    <form wire:submit="save">
        {{ $this->form }}

        <button type="submit" class="btn w-auto py-12 text-left mt-4 xilero-button">
            Submit
        </button>
    </form>

    <x-filament-actions::modals />
</div>
