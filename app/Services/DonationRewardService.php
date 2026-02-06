<?php

namespace App\Services;

use App\Models\DonationLog;
use App\Models\DonationRewardClaim;
use App\Models\DonationRewardTier;
use App\Models\GameAccount;
use App\Models\UberShopPurchase;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DonationRewardService
{
    /**
     * Apply rewards for a donation by creating pending claims.
     *
     * @return Collection<int, DonationRewardClaim>
     */
    public function applyRewards(DonationLog $log): Collection
    {
        $user = $log->user;
        $amount = (float) $log->amount;
        $claims = collect();

        // Get per-donation tiers that apply
        $perDonationTiers = $this->getApplicableTiersForDonation($amount);

        foreach ($perDonationTiers as $tier) {
            // Skip if already claimed in current period (pass donation log ID for per-donation check)
            if ($this->hasClaimedTierInCurrentPeriod($user, $tier, $log->id)) {
                continue;
            }

            $tierClaims = $this->createClaimsForTier($tier, $user, $log);
            $claims = $claims->merge($tierClaims);
        }

        // Get lifetime tiers that qualify
        $lifetimeTiers = $this->getQualifyingLifetimeTiers($user);

        foreach ($lifetimeTiers as $tier) {
            // Skip if already claimed in current period (pass donation log ID for per-donation check)
            if ($this->hasClaimedTierInCurrentPeriod($user, $tier, $log->id)) {
                continue;
            }

            $tierClaims = $this->createClaimsForTier($tier, $user, $log);
            $claims = $claims->merge($tierClaims);
        }

        return $claims;
    }

    /**
     * Get tiers applicable for a single donation amount.
     *
     * @return Collection<int, DonationRewardTier>
     */
    public function getApplicableTiersForDonation(float $amount): Collection
    {
        $tiers = DonationRewardTier::query()
            ->enabled()
            ->perDonation()
            ->where('minimum_amount', '<=', $amount)
            ->ordered()
            ->with('items')
            ->get();

        // If cumulative is false for a tier, only include that specific tier
        // If cumulative is true, include all tiers at or below the amount
        $result = collect();

        foreach ($tiers as $tier) {
            if ($tier->is_cumulative) {
                $result->push($tier);
            } else {
                // For non-cumulative, only the highest qualifying tier applies
                // We'll add it and filter duplicates later
                $result->push($tier);
            }
        }

        // For non-cumulative tiers, keep only the highest matching tier
        $highestNonCumulative = $tiers->where('is_cumulative', false)->sortByDesc('minimum_amount')->first();

        return $result->filter(function ($tier) use ($highestNonCumulative) {
            if ($tier->is_cumulative) {
                return true;
            }

            return $highestNonCumulative && $tier->id === $highestNonCumulative->id;
        })->values();
    }

    /**
     * Get lifetime tiers that the user qualifies for based on their total donations.
     *
     * @return Collection<int, DonationRewardTier>
     */
    public function getQualifyingLifetimeTiers(User $user): Collection
    {
        $total = $this->getLifetimeDonationTotal($user);

        return DonationRewardTier::query()
            ->enabled()
            ->lifetime()
            ->where('minimum_amount', '<=', $total)
            ->ordered()
            ->with('items')
            ->get();
    }

    /**
     * Check if user has claimed a tier in the current period.
     * For one-time tiers, checks if ever claimed.
     * For per-donation tiers, checks if claimed for this specific donation.
     * For resetting tiers, checks if claimed since period start.
     */
    public function hasClaimedTierInCurrentPeriod(User $user, DonationRewardTier $tier, ?int $donationLogId = null): bool
    {
        $query = $user->donationRewardClaims()
            ->where('donation_reward_tier_id', $tier->id);

        // For per-donation reset, check if already claimed for this specific donation
        // This prevents duplicates if applyRewards() is called twice for same donation
        if ($tier->isPerDonationReset()) {
            if ($donationLogId === null) {
                return false;
            }

            return $query->where('donation_log_id', $donationLogId)->exists();
        }

        // For one-time tiers, any claim means it's been used
        if ($tier->isOneTime()) {
            return $query->exists();
        }

        // For resetting tiers, check if claimed since period start
        $periodStart = $tier->getCurrentPeriodStart();

        if ($periodStart === null) {
            return $query->exists();
        }

        return $query->where('created_at', '>=', $periodStart)->exists();
    }

    /**
     * Get applicable tiers for preview (without creating claims).
     * Filters out tiers already claimed in current period.
     *
     * @return Collection<int, DonationRewardTier>
     */
    public function getApplicableTiersPreview(float $amount, User $user): Collection
    {
        $perDonation = $this->getApplicableTiersForDonation($amount)
            ->filter(function ($tier) use ($user) {
                // Per-donation tiers are always available for preview
                if ($tier->isPerDonationReset()) {
                    return true;
                }

                return ! $this->hasClaimedTierInCurrentPeriod($user, $tier);
            });

        $currentTotal = $this->getLifetimeDonationTotal($user);
        $newTotal = $currentTotal + $amount;

        $lifetime = DonationRewardTier::query()
            ->enabled()
            ->lifetime()
            ->where('minimum_amount', '<=', $newTotal)
            ->ordered()
            ->with('items')
            ->get()
            ->filter(function ($tier) use ($user) {
                // Per-donation tiers are always available for preview
                if ($tier->isPerDonationReset()) {
                    return true;
                }

                return ! $this->hasClaimedTierInCurrentPeriod($user, $tier);
            });

        return $perDonation->merge($lifetime)->values();
    }

    /**
     * Create pending claims for all items in a tier.
     *
     * @return Collection<int, DonationRewardClaim>
     */
    protected function createClaimsForTier(DonationRewardTier $tier, User $user, DonationLog $log): Collection
    {
        $claims = collect();

        foreach ($tier->items as $item) {
            $claim = DonationRewardClaim::create([
                'user_id' => $user->id,
                'donation_log_id' => $log->id,
                'donation_reward_tier_id' => $tier->id,
                'item_id' => $item->id,
                'quantity' => $item->pivot->quantity,
                'refine_level' => $item->pivot->refine_level,
                'status' => DonationRewardClaim::STATUS_PENDING,
                'is_xilero' => $tier->is_xilero,
                'is_xileretro' => $tier->is_xileretro,
            ]);

            $claims->push($claim);
        }

        return $claims;
    }

    /**
     * Claim a reward by creating an UberShopPurchase entry.
     */
    public function claimReward(DonationRewardClaim $claim, GameAccount $account): UberShopPurchase
    {
        return DB::transaction(function () use ($claim, $account) {
            // Lock the claim to prevent race conditions
            $lockedClaim = DonationRewardClaim::where('id', $claim->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (! $lockedClaim->canBeClaimedBy($account)) {
                throw new \RuntimeException('This reward cannot be claimed by the selected account.');
            }

            // Load the item relationship
            $lockedClaim->load('item');

            // Create the purchase record (with 0 cost as it's a reward)
            $purchase = UberShopPurchase::create([
                'account_id' => $account->ragnarok_account_id,
                'account_name' => $account->userid,
                'shop_item_id' => null,
                'item_id' => $lockedClaim->item->item_id,
                'item_name' => $lockedClaim->item->name,
                'refine_level' => $lockedClaim->refine_level,
                'quantity' => $lockedClaim->quantity,
                'uber_cost' => 0,
                'uber_balance_after' => $account->user->uber_balance,
                'status' => UberShopPurchase::STATUS_PENDING,
                'purchased_at' => now(),
                'is_xileretro' => $account->server === GameAccount::SERVER_XILERETRO,
                'is_bonus_reward' => true,
            ]);

            // Mark the claim as claimed
            $lockedClaim->markAsClaimed($account->ragnarok_account_id);

            return $purchase;
        });
    }

    /**
     * Get all pending rewards for a user.
     *
     * @return Collection<int, DonationRewardClaim>
     */
    public function getUserPendingRewards(User $user): Collection
    {
        return $user->donationRewardClaims()
            ->pending()
            ->notExpired()
            ->with(['tier', 'item'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get user's lifetime donation total.
     */
    public function getLifetimeDonationTotal(User $user): float
    {
        return (float) DonationLog::where('user_id', $user->id)
            ->whereNull('reverted_at')
            ->sum('amount');
    }

    /**
     * Check if a user has already claimed rewards for a specific lifetime tier.
     */
    public function hasClaimedLifetimeTier(User $user, DonationRewardTier $tier): bool
    {
        return $user->donationRewardClaims()
            ->where('donation_reward_tier_id', $tier->id)
            ->exists();
    }

    /**
     * Get already claimed lifetime tiers for a user.
     *
     * @return Collection<int, int>
     */
    public function getClaimedLifetimeTierIds(User $user): Collection
    {
        return $user->donationRewardClaims()
            ->whereHas('tier', fn ($q) => $q->lifetime())
            ->pluck('donation_reward_tier_id')
            ->unique();
    }
}
