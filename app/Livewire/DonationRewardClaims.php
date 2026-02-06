<?php

namespace App\Livewire;

use App\Models\DonationRewardClaim;
use App\Models\GameAccount;
use App\Services\DonationRewardService;
use Exception;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class DonationRewardClaims extends Component
{
    public ?int $selectedGameAccountId = null;

    public ?int $claimingRewardId = null;

    public bool $showClaimConfirm = false;

    /**
     * Sanitize selectedGameAccountId input to prevent array injection attacks.
     */
    public function updatingSelectedGameAccountId(mixed &$value): void
    {
        $value = is_numeric($value) ? (int) $value : null;
    }

    /**
     * Sanitize claimingRewardId input to prevent array injection attacks.
     */
    public function updatingClaimingRewardId(mixed &$value): void
    {
        $value = is_numeric($value) ? (int) $value : null;
    }

    /**
     * Sanitize showClaimConfirm input to prevent array injection attacks.
     */
    public function updatingShowClaimConfirm(mixed &$value): void
    {
        $value = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false;
    }

    public function mount(): void
    {
        if (auth()->check()) {
            $firstAccount = auth()->user()->gameAccounts()->first();
            if ($firstAccount) {
                $this->selectedGameAccountId = $firstAccount->id;
            }
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

        $account = auth()->user()->gameAccounts()->find($value);
        if (! $account) {
            $firstAccount = auth()->user()->gameAccounts()->first();
            $this->selectedGameAccountId = $firstAccount?->id;
        }

        $this->cancelClaim();
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
     * Get pending rewards for the current user, filtered by selected account's server compatibility.
     *
     * @return EloquentCollection<int, DonationRewardClaim>
     */
    public function pendingRewards(): EloquentCollection
    {
        if (! auth()->check()) {
            return new EloquentCollection;
        }

        $user = auth()->user();
        $query = $user->donationRewardClaims()
            ->pending()
            ->notExpired()
            ->with(['tier', 'item']);

        $gameAccount = $this->selectedGameAccount();
        if ($gameAccount) {
            $isRetro = $gameAccount->server === GameAccount::SERVER_XILERETRO;
            $query->forServer($isRetro);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Get claimed rewards history.
     *
     * @return EloquentCollection<int, DonationRewardClaim>
     */
    public function claimedRewards(): EloquentCollection
    {
        if (! auth()->check()) {
            return new EloquentCollection;
        }

        return auth()->user()->donationRewardClaims()
            ->claimed()
            ->with(['tier', 'item'])
            ->orderBy('claimed_at', 'desc')
            ->limit(20)
            ->get();
    }

    /**
     * Get the count of all pending rewards (regardless of server filter).
     */
    public function totalPendingCount(): int
    {
        if (! auth()->check()) {
            return 0;
        }

        return auth()->user()->donationRewardClaims()
            ->pending()
            ->notExpired()
            ->count();
    }

    /**
     * Start the claim process for a reward.
     */
    public function startClaim(int $rewardId): void
    {
        $this->claimingRewardId = $rewardId;
        $this->showClaimConfirm = true;
    }

    /**
     * Cancel the claim process.
     */
    public function cancelClaim(): void
    {
        $this->claimingRewardId = null;
        $this->showClaimConfirm = false;
    }

    /**
     * Get the reward being claimed.
     */
    public function claimingReward(): ?DonationRewardClaim
    {
        if (! $this->claimingRewardId) {
            return null;
        }

        return DonationRewardClaim::with(['tier', 'item'])->find($this->claimingRewardId);
    }

    /**
     * Process the reward claim.
     */
    public function claim(): void
    {
        if (! auth()->check()) {
            session()->flash('error', 'You must be logged in to claim rewards.');

            return;
        }

        $gameAccount = $this->selectedGameAccount();
        if (! $gameAccount) {
            session()->flash('error', 'Please select a game account to receive the reward.');

            return;
        }

        $reward = $this->claimingReward();
        if (! $reward) {
            session()->flash('error', 'Reward not found.');
            $this->cancelClaim();

            return;
        }

        if ($reward->user_id !== auth()->id()) {
            session()->flash('error', 'This reward does not belong to you.');
            $this->cancelClaim();

            return;
        }

        if (! $reward->canBeClaimedBy($gameAccount)) {
            session()->flash('error', 'This reward cannot be claimed on the selected account.');
            $this->cancelClaim();

            return;
        }

        try {
            $rewardService = app(DonationRewardService::class);
            $rewardService->claimReward($reward, $gameAccount);

            $itemName = $reward->item->name;
            $quantity = $reward->quantity;
            $refine = $reward->refine_level > 0 ? "+{$reward->refine_level} " : '';

            session()->flash('success', "{$refine}{$itemName} x{$quantity} will be delivered to {$gameAccount->userid} on next login.");

            $this->cancelClaim();

        } catch (Exception $e) {
            session()->flash('error', 'Failed to claim reward: '.$e->getMessage());
        }
    }

    public function render()
    {
        $gameAccounts = auth()->check() ? auth()->user()->gameAccounts : collect();

        return view('livewire.donation-reward-claims', [
            'pendingRewards' => $this->pendingRewards(),
            'claimedRewards' => $this->claimedRewards(),
            'totalPendingCount' => $this->totalPendingCount(),
            'selectedGameAccount' => $this->selectedGameAccount(),
            'gameAccounts' => $gameAccounts,
            'claimingReward' => $this->claimingReward(),
        ]);
    }
}
