<?php

namespace App\Services;

class DonationCalculator
{
    /**
     * Calculate Ubers for a given donation amount.
     * Uses CSV-based tiers with generous rates for higher donations.
     */
    public static function calculate(float $amount): int
    {
        $tiers = collect(config('donation.tiers'));
        $calculator = config('donation.calculator');

        // Check if amount matches an existing tier exactly
        $exactTier = $tiers->firstWhere('amount', $amount);
        if ($exactTier) {
            return $exactTier['ubers'];
        }

        // For amounts below minimum, return 0
        if ($amount < $calculator['minimum_amount']) {
            return 0;
        }

        // For amounts between tiers (shouldn't happen with current UI, but handle it)
        // Find the tier just below and above the amount
        $lowerTier = $tiers->where('amount', '<', $amount)->sortByDesc('amount')->first();
        $upperTier = $tiers->where('amount', '>', $amount)->sortBy('amount')->first();

        // If amount is between existing tiers, interpolate
        if ($lowerTier && $upperTier) {
            return self::interpolate($amount, $lowerTier, $upperTier, $calculator);
        }

        // If amount is above the highest tier ($75), extrapolate
        if ($lowerTier && ! $upperTier) {
            return self::extrapolate($amount, $tiers, $calculator);
        }

        // If amount is below the lowest tier but above minimum
        if (! $lowerTier && $upperTier) {
            $baseRate = $calculator['base_rate'];
            $ubers = $amount * $baseRate;

            return $calculator['round_down'] ? (int) floor($ubers) : (int) round($ubers);
        }

        return 0;
    }

    /**
     * Interpolate between two tiers for amounts that fall between them.
     */
    private static function interpolate(float $amount, array $lowerTier, array $upperTier, array $calculator): int
    {
        $amountRange = $upperTier['amount'] - $lowerTier['amount'];
        $uberRange = $upperTier['ubers'] - $lowerTier['ubers'];
        $amountProgress = ($amount - $lowerTier['amount']) / $amountRange;

        $ubers = $lowerTier['ubers'] + ($uberRange * $amountProgress);

        return $calculator['round_down'] ? (int) floor($ubers) : (int) round($ubers);
    }

    /**
     * Extrapolate for amounts above the highest tier.
     * Uses a progressive rate that increases with higher amounts.
     */
    private static function extrapolate(float $amount, $tiers, array $calculator): int
    {
        $highestTier = $tiers->sortByDesc('amount')->first();
        $highestAmount = $highestTier['amount'];
        $highestUbers = $highestTier['ubers'];

        // Calculate the rate at the highest tier
        $highestRate = $highestUbers / $highestAmount;

        // Calculate how much beyond the highest tier
        $extraAmount = $amount - $highestAmount;

        // Progressive rate: starts at highest tier rate and grows slightly
        // The growth factor makes larger donations more appealing
        $growthFactor = $calculator['extrapolation_growth'];
        $maxRate = $calculator['max_rate'];

        // Calculate progressive rate based on extra amount
        // Rate increases logarithmically to prevent runaway values
        $progressiveRate = min(
            $highestRate + ($growthFactor * log10($extraAmount + 1)),
            $maxRate
        );

        // Calculate extra ubers from the amount above highest tier
        $extraUbers = $extraAmount * $progressiveRate;

        // Total ubers = highest tier ubers + extra ubers
        $totalUbers = $highestUbers + $extraUbers;

        return $calculator['round_down'] ? (int) floor($totalUbers) : (int) round($totalUbers);
    }

    /**
     * Get the effective rate (Ubers per dollar) for a given amount.
     */
    public static function getRate(float $amount): float
    {
        if ($amount <= 0) {
            return 0;
        }

        $ubers = self::calculate($amount);

        return round($ubers / $amount, 3);
    }

    /**
     * Get a preview table showing Ubers for various amounts.
     */
    public static function getPreviewTable(array $amounts = []): array
    {
        if (empty($amounts)) {
            $amounts = [5, 10, 20, 40, 75, 80, 100, 125, 150, 200, 250, 300];
        }

        return collect($amounts)->map(fn ($amount) => [
            'amount' => $amount,
            'ubers' => self::calculate($amount),
            'rate' => self::getRate($amount),
        ])->toArray();
    }
}
