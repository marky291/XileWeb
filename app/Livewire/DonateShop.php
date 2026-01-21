<?php

namespace App\Livewire;

use App\Models\GameAccount;
use App\Models\UberShopCategory;
use App\Models\UberShopItem;
use App\Models\UberShopPurchase;
use App\Notifications\UberShopPurchaseNotification;
use App\XileRO\XileRO_Char;
use App\XileRO\XileRO_Inventory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class DonateShop extends Component
{
    use WithPagination;

    #[Url]
    public ?string $category = null;

    #[Url]
    public string $search = '';

    public ?int $selectedItemId = null;

    public ?int $selectedGameAccountId = null;

    public bool $showPurchaseConfirm = false;

    /**
     * Sanitize category input to prevent array injection attacks.
     */
    public function updatingCategory(mixed &$value): void
    {
        $value = is_string($value) || is_null($value) ? $value : null;
    }

    /**
     * Sanitize search input to prevent array injection attacks.
     */
    public function updatingSearch(mixed &$value): void
    {
        $value = is_string($value) ? $value : '';
    }

    /**
     * Sanitize selectedItemId input to prevent array injection attacks.
     */
    public function updatingSelectedItemId(mixed &$value): void
    {
        $value = is_numeric($value) ? (int) $value : null;
    }

    /**
     * Sanitize selectedGameAccountId input to prevent array injection attacks.
     */
    public function updatingSelectedGameAccountId(mixed &$value): void
    {
        $value = is_numeric($value) ? (int) $value : null;
    }

    /**
     * Sanitize showPurchaseConfirm input to prevent array injection attacks.
     */
    public function updatingShowPurchaseConfirm(mixed &$value): void
    {
        $value = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false;
    }

    public function mount(): void
    {
        // Auto-select first game account if user has one
        if (auth()->check()) {
            $firstAccount = auth()->user()->gameAccounts()->first();
            if ($firstAccount) {
                $this->selectedGameAccountId = $firstAccount->id;
            }
        }
    }

    /**
     * Redirect to login page with intended URL set.
     */
    public function redirectToLogin(): void
    {
        session()->put('url.intended', request()->url());
        $this->redirect(route('login'), navigate: false);
    }

    /**
     * Get all enabled categories.
     *
     * @return Collection<int, UberShopCategory>
     */
    public function categories(): Collection
    {
        return UberShopCategory::where('enabled', true)
            ->orderBy('display_order')
            ->get();
    }

    /**
     * Get the currently selected category.
     */
    public function selectedCategory(): ?UberShopCategory
    {
        if (! $this->category) {
            return null;
        }

        return UberShopCategory::where('name', $this->category)
            ->where('enabled', true)
            ->first();
    }

    /**
     * Get paginated items for the selected category, filtered by the selected game account's server.
     */
    public function items(): LengthAwarePaginator
    {
        $gameAccount = $this->selectedGameAccount();

        // If no game account selected, return empty paginator
        if (! $gameAccount) {
            return new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15);
        }

        $query = UberShopItem::with('item')
            ->where('enabled', true)
            ->orderByDesc('views')
            ->orderBy('display_order');

        // Filter by server based on selected game account
        if ($gameAccount->server === GameAccount::SERVER_XILERO) {
            $query->where('is_xilero', true);
        } else {
            $query->where('is_xileretro', true);
        }

        if ($this->category) {
            $category = $this->selectedCategory();
            if ($category) {
                $query->where('category_id', $category->id);
            }
        }

        if ($this->search) {
            $searchTerm = $this->search;
            $query->whereHas('item', function ($itemQuery) use ($searchTerm) {
                $itemQuery->where('name', 'like', '%'.$searchTerm.'%')
                    ->orWhere('aegis_name', 'like', '%'.$searchTerm.'%')
                    ->orWhere('item_id', $searchTerm);
            });
        }

        return $query->paginate(15);
    }

    /**
     * Reset pagination when search changes.
     */
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Clear the search input.
     */
    public function clearSearch(): void
    {
        $this->search = '';
    }

    /**
     * Select a category to filter items.
     */
    public function selectCategory(?string $categoryName): void
    {
        $this->category = $categoryName;
        $this->selectedItemId = null;
        $this->resetPage();
    }

    /**
     * Select an item to view details.
     */
    public function selectItem(?int $itemId): void
    {
        $this->selectedItemId = $itemId;
        $this->showPurchaseConfirm = false;

        if ($itemId) {
            UberShopItem::where('id', $itemId)->increment('views');
        }
    }

    /**
     * Validate game account selection when updated via wire:model.
     */
    public function updatedSelectedGameAccountId(?int $value): void
    {
        if (! auth()->check() || ! $value) {
            $this->selectedGameAccountId = null;

            return;
        }

        // Verify the account belongs to the user
        $account = auth()->user()->gameAccounts()->find($value);
        if (! $account) {
            // Reset to first valid account if invalid ID provided
            $firstAccount = auth()->user()->gameAccounts()->first();
            $this->selectedGameAccountId = $firstAccount?->id;
        }

        // Reset purchase confirmation and pagination when switching accounts
        $this->showPurchaseConfirm = false;
        $this->selectedItemId = null;
        $this->resetPage();
    }

    /**
     * Get the selected item for the modal.
     */
    public function selectedItem(): ?UberShopItem
    {
        if (! $this->selectedItemId) {
            return null;
        }

        return UberShopItem::with(['category', 'item'])->find($this->selectedItemId);
    }

    /**
     * Get the selected game account.
     */
    public function selectedGameAccount(): ?GameAccount
    {
        if (! auth()->check() || ! $this->selectedGameAccountId) {
            return null;
        }

        return auth()->user()->gameAccounts()->find($this->selectedGameAccountId);
    }

    /**
     * Get the current user's uber balance.
     */
    public function userBalance(): int
    {
        if (! auth()->check()) {
            return 0;
        }

        return auth()->user()->uber_balance ?? 0;
    }

    /**
     * Check if the current user can make purchases.
     *
     * When purchasing_enabled config is true: all users can purchase.
     * When false: only admin users can purchase.
     */
    public function canPurchase(): bool
    {
        if (! auth()->check()) {
            return false;
        }

        // If purchasing is enabled, everyone can purchase
        if (config('xilero.uber_shop.purchasing_enabled', true)) {
            return true;
        }

        // If purchasing is disabled, only admins can purchase
        return auth()->user()->isAdmin();
    }

    /**
     * Check if purchasing is currently restricted to admins only.
     */
    public function isPurchasingRestricted(): bool
    {
        return ! config('xilero.uber_shop.purchasing_enabled', true);
    }

    /**
     * Get the current user's pending purchases across all game accounts.
     *
     * @return EloquentCollection<int, UberShopPurchase>
     */
    public function pendingPurchases(): EloquentCollection
    {
        if (! auth()->check()) {
            return new EloquentCollection;
        }

        $accountIds = auth()->user()->gameAccounts()->pluck('ragnarok_account_id')->filter();

        if ($accountIds->isEmpty()) {
            return new EloquentCollection;
        }

        return UberShopPurchase::whereIn('account_id', $accountIds)
            ->where('status', UberShopPurchase::STATUS_PENDING)
            ->with('shopItem')
            ->orderByDesc('purchased_at')
            ->get();
    }

    /**
     * Cancel a pending purchase and refund the ubers.
     */
    public function cancelPendingPurchase(int $purchaseId): void
    {
        if (! auth()->check()) {
            return;
        }

        $user = auth()->user();
        $accountIds = $user->gameAccounts()->pluck('ragnarok_account_id')->filter();

        $purchase = UberShopPurchase::where('id', $purchaseId)
            ->whereIn('account_id', $accountIds)
            ->where('status', UberShopPurchase::STATUS_PENDING)
            ->first();

        if (! $purchase) {
            session()->flash('error', 'Purchase not found or already claimed.');

            return;
        }

        try {
            DB::transaction(function () use ($user, $purchase) {
                // Refund the ubers to user
                $user->increment('uber_balance', $purchase->uber_cost);

                // Restore stock if item has limited stock
                if ($purchase->shopItem && $purchase->shopItem->stock !== null) {
                    $purchase->shopItem->increment('stock');
                }

                // Mark purchase as cancelled
                $purchase->update(['status' => UberShopPurchase::STATUS_CANCELLED]);
            });

            session()->flash('success', "Purchase cancelled. {$purchase->uber_cost} Ubers have been refunded.");

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to cancel purchase: '.$e->getMessage());
        }
    }

    /**
     * Get the current user's claimed purchases that may be eligible for refund.
     *
     * @return EloquentCollection<int, UberShopPurchase>
     */
    public function claimedPurchases(): EloquentCollection
    {
        if (! auth()->check()) {
            return new EloquentCollection;
        }

        $accountIds = auth()->user()->gameAccounts()->pluck('ragnarok_account_id')->filter();

        if ($accountIds->isEmpty()) {
            return new EloquentCollection;
        }

        // Show claimed purchases from last 7 days
        return UberShopPurchase::whereIn('account_id', $accountIds)
            ->where('status', UberShopPurchase::STATUS_CLAIMED)
            ->where('claimed_at', '>=', now()->subDays(7))
            ->with('shopItem')
            ->orderByDesc('claimed_at')
            ->get();
    }

    /**
     * Get the refund deadline in hours.
     */
    public function refundHours(): int
    {
        return 24;
    }

    /**
     * Check if a purchase is eligible for refund.
     */
    public function canRefund(UberShopPurchase $purchase): bool
    {
        if ($purchase->status !== UberShopPurchase::STATUS_CLAIMED) {
            return false;
        }

        if (! $purchase->claimed_at) {
            return false;
        }

        return $purchase->claimed_at->addHours($this->refundHours())->isFuture();
    }

    /**
     * Refund a claimed purchase by finding and removing the item from inventory.
     */
    public function refundPurchase(int $purchaseId): void
    {
        if (! auth()->check()) {
            return;
        }

        $user = auth()->user();
        $accountIds = $user->gameAccounts()->pluck('ragnarok_account_id')->filter();

        $purchase = UberShopPurchase::where('id', $purchaseId)
            ->whereIn('account_id', $accountIds)
            ->where('status', UberShopPurchase::STATUS_CLAIMED)
            ->first();

        if (! $purchase) {
            session()->flash('error', 'Purchase not found or not eligible for refund.');

            return;
        }

        if (! $this->canRefund($purchase)) {
            session()->flash('error', 'Refund period has expired. Items can only be refunded within '.$this->refundHours().' hours of claiming.');

            return;
        }

        // Find the game account for this purchase to get the server type
        $gameAccount = $user->gameAccounts()->where('ragnarok_account_id', $purchase->account_id)->first();

        if (! $gameAccount) {
            session()->flash('error', 'Game account not found.');

            return;
        }

        // Only XileRO refunds are supported for now
        if ($gameAccount->server !== GameAccount::SERVER_XILERO) {
            session()->flash('error', 'Refunds are only available for XileRO accounts at this time.');

            return;
        }

        // Get all character IDs for this account
        $charIds = XileRO_Char::where('account_id', $purchase->account_id)->pluck('char_id');

        if ($charIds->isEmpty()) {
            session()->flash('error', 'No characters found on this account.');

            return;
        }

        // Find matching item in inventory
        // Must match: item_id, refine level, and all 4 card slots
        $inventoryItem = XileRO_Inventory::whereIn('char_id', $charIds)
            ->where('nameid', $purchase->item_id)
            ->where('refine', $purchase->refine_level)
            ->where('card0', $purchase->shopItem?->card0 ?? 0)
            ->where('card1', $purchase->shopItem?->card1 ?? 0)
            ->where('card2', $purchase->shopItem?->card2 ?? 0)
            ->where('card3', $purchase->shopItem?->card3 ?? 0)
            ->where('amount', '>=', $purchase->quantity)
            ->first();

        if (! $inventoryItem) {
            session()->flash('error', 'Item not found in character inventory. The item may have been modified, traded, or dropped.');

            return;
        }

        try {
            DB::transaction(function () use ($user, $purchase, $inventoryItem) {
                // Remove item from inventory (or reduce amount)
                if ($inventoryItem->amount > $purchase->quantity) {
                    $inventoryItem->decrement('amount', $purchase->quantity);
                } else {
                    $inventoryItem->delete();
                }

                // Refund ubers to user
                $user->increment('uber_balance', $purchase->uber_cost);

                // Restore stock if item has limited stock
                if ($purchase->shopItem && $purchase->shopItem->stock !== null) {
                    $purchase->shopItem->increment('stock');
                }

                // Mark purchase as cancelled/refunded
                $purchase->update(['status' => UberShopPurchase::STATUS_CANCELLED]);
            });

            session()->flash('success', "Item refunded. {$purchase->uber_cost} Ubers have been returned to your account.");

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to process refund: '.$e->getMessage());
        }
    }

    /**
     * Show the purchase confirmation dialog.
     */
    public function confirmPurchase(): void
    {
        $this->showPurchaseConfirm = true;
    }

    /**
     * Cancel the purchase confirmation.
     */
    public function cancelPurchase(): void
    {
        $this->showPurchaseConfirm = false;
    }

    /**
     * Process the item purchase.
     */
    public function purchase(): void
    {
        // Ensure user is authenticated
        if (! auth()->check()) {
            session()->flash('error', 'You must be logged in to make a purchase.');

            return;
        }

        // Check if user can purchase (feature flag + admin check)
        if (! $this->canPurchase()) {
            session()->flash('error', 'Purchasing is currently disabled. Please try again later.');

            return;
        }

        $user = auth()->user();

        $gameAccount = $this->selectedGameAccount();
        if (! $gameAccount) {
            session()->flash('error', 'Please select a game account to receive the item.');

            return;
        }

        $item = $this->selectedItem();

        // Validate item exists
        if (! $item) {
            session()->flash('error', 'Item not found.');
            $this->selectedItemId = null;

            return;
        }

        // Validate item is available
        if (! $item->is_available) {
            session()->flash('error', 'This item is currently unavailable.');

            return;
        }

        // Validate item is available for the selected game account's server
        if (! $item->isAvailableForServer($gameAccount->server)) {
            session()->flash('error', 'This item is not available for '.$gameAccount->serverName().'.');

            return;
        }

        // Refresh user to get the latest balance
        $user->refresh();
        $currentBalance = $user->uber_balance;

        // Validate sufficient balance
        if ($currentBalance < $item->uber_cost) {
            session()->flash('error', 'Insufficient Ubers. You need '.($item->uber_cost - $currentBalance).' more.');

            return;
        }

        try {
            DB::transaction(function () use ($user, $gameAccount, $item, $currentBalance) {
                // Lock the item row to prevent race conditions with stock
                $lockedItem = UberShopItem::with('item')->where('id', $item->id)->lockForUpdate()->first();

                // Re-check availability after lock
                if (! $lockedItem || ! $lockedItem->is_available) {
                    throw new \Exception('Item is no longer available.');
                }

                // Calculate new balance
                $newBalance = $currentBalance - $lockedItem->uber_cost;

                // Deduct ubers from user's balance
                $user->uber_balance = $newBalance;
                $user->save();

                // Decrement stock if item has limited stock
                if ($lockedItem->stock !== null) {
                    $lockedItem->decrement('stock');
                }

                // Create purchase record
                $purchase = UberShopPurchase::create([
                    'account_id' => $gameAccount->ragnarok_account_id,
                    'account_name' => $gameAccount->userid,
                    'shop_item_id' => $lockedItem->id,
                    'item_id' => $lockedItem->item->item_id,
                    'item_name' => $lockedItem->item->name,
                    'refine_level' => $lockedItem->refine_level,
                    'quantity' => $lockedItem->quantity,
                    'uber_cost' => $lockedItem->uber_cost,
                    'uber_balance_after' => $newBalance,
                    'status' => UberShopPurchase::STATUS_PENDING,
                    'purchased_at' => now(),
                ]);

                // Send purchase confirmation email
                $user->notify(new UberShopPurchaseNotification(
                    $purchase,
                    $gameAccount->userid,
                    $gameAccount->serverName()
                ));
            });

            session()->flash('success', "Successfully purchased {$item->display_name} for {$item->uber_cost} Ubers! The item will be delivered to {$gameAccount->userid} on next login.");

            // Reset state
            $this->selectedItemId = null;
            $this->showPurchaseConfirm = false;

        } catch (\Exception $e) {
            session()->flash('error', 'Purchase failed: '.$e->getMessage());
        }
    }

    public function render()
    {
        $items = $this->items();
        $gameAccounts = auth()->check() ? auth()->user()->gameAccounts : collect();

        return view('livewire.donate-shop', [
            'categories' => $this->categories(),
            'items' => $items,
            'itemCount' => $items->total(),
            'totalItemCount' => UberShopItem::where('enabled', true)->count(),
            'selectedCategory' => $this->selectedCategory(),
            'selectedItem' => $this->selectedItem(),
            'selectedGameAccount' => $this->selectedGameAccount(),
            'gameAccounts' => $gameAccounts,
            'userBalance' => $this->userBalance(),
            'pendingPurchases' => $this->pendingPurchases(),
            'claimedPurchases' => $this->claimedPurchases(),
            'refundHours' => $this->refundHours(),
            'canPurchase' => $this->canPurchase(),
            'isPurchasingRestricted' => $this->isPurchasingRestricted(),
        ]);
    }
}
