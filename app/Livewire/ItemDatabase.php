<?php

namespace App\Livewire;

use App\Models\Item;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class ItemDatabase extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public ?string $type = null;

    #[Url]
    public string $sort = 'popular';

    #[Url]
    public string $server = 'xilero';

    public ?int $selectedItemId = null;

    /**
     * Get all available item types.
     *
     * @return array<string>
     */
    public function types(): array
    {
        return Item::query()
            ->where('is_xileretro', $this->server === 'xileretro')
            ->distinct()
            ->orderBy('type')
            ->pluck('type')
            ->filter()
            ->values()
            ->toArray();
    }

    /**
     * Get available server options.
     *
     * @return array<string, string>
     */
    public function serverOptions(): array
    {
        return [
            'xilero' => 'XileRO',
            'xileretro' => 'XileRetro',
        ];
    }

    /**
     * Get available sort options.
     *
     * @return array<string, string>
     */
    public function sortOptions(): array
    {
        return [
            'popular' => 'Most Popular',
            'name' => 'Name (A-Z)',
            'name_desc' => 'Name (Z-A)',
            'id' => 'Item ID',
        ];
    }

    /**
     * Get paginated items filtered by search and type.
     */
    public function items(): LengthAwarePaginator
    {
        $query = Item::query()
            ->where('is_xileretro', $this->server === 'xileretro');

        // Apply sorting
        match ($this->sort) {
            'popular' => $query->orderByDesc('views')->orderBy('name'),
            'name' => $query->orderBy('name'),
            'name_desc' => $query->orderByDesc('name'),
            'id' => $query->orderBy('item_id'),
            default => $query->orderByDesc('views')->orderBy('name'),
        };

        if ($this->type) {
            $query->where('type', $this->type);
        }

        if ($this->search) {
            $searchTerm = $this->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', '%'.$searchTerm.'%')
                    ->orWhere('aegis_name', 'like', '%'.$searchTerm.'%')
                    ->orWhere('item_id', $searchTerm)
                    ->orWhere('description', 'like', '%'.$searchTerm.'%');
            });
        }

        return $query->paginate(24);
    }

    /**
     * Reset pagination when search changes.
     */
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Reset pagination when type filter changes.
     */
    public function updatedType(): void
    {
        $this->resetPage();
    }

    /**
     * Reset pagination when sort changes.
     */
    public function updatedSort(): void
    {
        $this->resetPage();
    }

    /**
     * Reset pagination and type when server changes.
     */
    public function updatedServer(): void
    {
        $this->type = null;
        $this->selectedItemId = null;
        $this->resetPage();
    }

    /**
     * Clear the search input.
     */
    public function clearSearch(): void
    {
        $this->search = '';
        $this->resetPage();
    }

    /**
     * Select a type to filter items.
     */
    public function selectType(?string $type): void
    {
        $this->type = $type;
        $this->selectedItemId = null;
        $this->resetPage();
    }

    /**
     * Select an item to view details and increment view count.
     */
    public function selectItem(?int $itemId): void
    {
        $this->selectedItemId = $itemId;

        // Increment view count when an item is selected
        if ($itemId) {
            Item::where('id', $itemId)->increment('views');
        }
    }

    /**
     * Get the selected item for the modal.
     */
    public function selectedItem(): ?Item
    {
        if (! $this->selectedItemId) {
            return null;
        }

        return Item::find($this->selectedItemId);
    }

    public function render()
    {
        $items = $this->items();

        return view('livewire.item-database', [
            'items' => $items,
            'itemCount' => $items->total(),
            'types' => $this->types(),
            'sortOptions' => $this->sortOptions(),
            'serverOptions' => $this->serverOptions(),
            'selectedItem' => $this->selectedItem(),
        ]);
    }
}
