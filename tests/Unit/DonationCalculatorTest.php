<?php

namespace Tests\Unit;

use App\Services\DonationCalculator;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class DonationCalculatorTest extends TestCase
{
    public function test_returns_static_ubers_for_anchor_tier_amounts(): void
    {
        // These should always return the exact anchor tier values
        $this->assertEquals(3, DonationCalculator::calculate(5));
        $this->assertEquals(8, DonationCalculator::calculate(10));
        $this->assertEquals(18, DonationCalculator::calculate(20));
        $this->assertEquals(42, DonationCalculator::calculate(40));
        $this->assertEquals(88, DonationCalculator::calculate(75));
        $this->assertEquals(129, DonationCalculator::calculate(100));
        $this->assertEquals(269, DonationCalculator::calculate(200));
        $this->assertEquals(422, DonationCalculator::calculate(300));
        $this->assertEquals(733, DonationCalculator::calculate(500));
        $this->assertEquals(1525, DonationCalculator::calculate(1000));
    }

    public function test_returns_zero_for_amounts_below_minimum(): void
    {
        $this->assertEquals(0, DonationCalculator::calculate(0));
        $this->assertEquals(0, DonationCalculator::calculate(1));
        $this->assertEquals(0, DonationCalculator::calculate(4));
    }

    /**
     * Verify the dynamic algorithm matches all 38 XileRetro target values
     * using only 10 anchor tiers + interpolation/extrapolation.
     */
    #[DataProvider('xileRetroRatesProvider')]
    public function test_matches_xile_retro_rates(int $amount, int $expectedUbers): void
    {
        $this->assertEquals(
            $expectedUbers,
            DonationCalculator::calculate($amount),
            "Expected {$expectedUbers} ubers for \${$amount} donation"
        );
    }

    /** @return array<string, array{int, int}> */
    public static function xileRetroRatesProvider(): array
    {
        return [
            '$5' => [5, 3],
            '$10' => [10, 8],
            '$15' => [15, 13],
            '$20' => [20, 18],
            '$25' => [25, 24],
            '$30' => [30, 30],
            '$35' => [35, 36],
            '$40' => [40, 42],
            '$45' => [45, 49],
            '$50' => [50, 55],
            '$55' => [55, 62],
            '$60' => [60, 68],
            '$65' => [65, 75],
            '$70' => [70, 81],
            '$75' => [75, 88],
            '$80' => [80, 96],
            '$85' => [85, 104],
            '$90' => [90, 113],
            '$95' => [95, 121],
            '$100' => [100, 129],
            '$125' => [125, 164],
            '$150' => [150, 199],
            '$175' => [175, 234],
            '$200' => [200, 269],
            '$250' => [250, 346],
            '$300' => [300, 422],
            '$350' => [350, 500],
            '$400' => [400, 578],
            '$450' => [450, 655],
            '$500' => [500, 733],
            '$600' => [600, 891],
            '$700' => [700, 1050],
            '$800' => [800, 1208],
            '$900' => [900, 1367],
            '$1000' => [1000, 1525],
            '$1250' => [1250, 1921],
            '$1500' => [1500, 2317],
            '$1750' => [1750, 2713],
            '$2000' => [2000, 3109],
        ];
    }

    public function test_extrapolates_for_amounts_above_highest_tier(): void
    {
        // $1000 = 1525 Ubers (highest anchor tier)
        // $1100 should be more than 1525
        $at1100 = DonationCalculator::calculate(1100);
        $this->assertGreaterThan(1525, $at1100);

        // $2500 should be more than $2000
        $at2500 = DonationCalculator::calculate(2500);
        $this->assertGreaterThan(DonationCalculator::calculate(2000), $at2500);
    }

    public function test_higher_amounts_have_competitive_rates(): void
    {
        $rate75 = DonationCalculator::getRate(75);
        $rate100 = DonationCalculator::getRate(100);
        $rate200 = DonationCalculator::getRate(200);

        // Rates should remain competitive (at least close to highest tier rate)
        $minAcceptableRate = $rate75 * 0.95;
        $this->assertGreaterThanOrEqual($minAcceptableRate, $rate100);
        $this->assertGreaterThanOrEqual($minAcceptableRate, $rate200);

        // Higher amounts should eventually surpass the base rate
        $rate500 = DonationCalculator::getRate(500);
        $this->assertGreaterThanOrEqual($rate75, $rate500);
    }

    public function test_rates_are_capped_at_maximum(): void
    {
        $maxRate = config('donation.calculator.max_rate');

        // Even very large amounts shouldn't exceed max rate
        $rate5000 = DonationCalculator::getRate(5000);
        $this->assertLessThanOrEqual($maxRate, $rate5000);
    }

    public function test_interpolates_between_tiers(): void
    {
        // Between $5 (3 ubers) and $10 (8 ubers)
        $at7 = DonationCalculator::calculate(7);
        $this->assertGreaterThan(3, $at7);
        $this->assertLessThan(8, $at7);

        // Between $40 (42 ubers) and $75 (88 ubers)
        $at50 = DonationCalculator::calculate(50);
        $this->assertGreaterThan(42, $at50);
        $this->assertLessThan(88, $at50);

        // Between $500 (733 ubers) and $1000 (1525 ubers)
        $at750 = DonationCalculator::calculate(750);
        $this->assertGreaterThan(733, $at750);
        $this->assertLessThan(1525, $at750);
    }

    public function test_get_preview_table_returns_expected_structure(): void
    {
        $preview = DonationCalculator::getPreviewTable([5, 10, 100]);

        $this->assertCount(3, $preview);
        $this->assertArrayHasKey('amount', $preview[0]);
        $this->assertArrayHasKey('ubers', $preview[0]);
        $this->assertArrayHasKey('rate', $preview[0]);

        // Static tier should return exact value
        $this->assertEquals(3, $preview[0]['ubers']);
        $this->assertEquals(8, $preview[1]['ubers']);
    }

    public function test_calculated_values_are_integers(): void
    {
        // All calculated values should be integers, even for fractional dollar amounts
        $this->assertIsInt(DonationCalculator::calculate(85));
        $this->assertIsInt(DonationCalculator::calculate(137));
        $this->assertIsInt(DonationCalculator::calculate(77.77));
        $this->assertIsInt(DonationCalculator::calculate(1500));
    }

    /**
     * Verify interpolation produces correct results for non-anchor amounts.
     */
    #[DataProvider('interpolatedAmountsProvider')]
    public function test_interpolates_non_anchor_amounts(float $amount, int $expectedUbers): void
    {
        $this->assertEquals(
            $expectedUbers,
            DonationCalculator::calculate($amount),
            "Expected {$expectedUbers} ubers for \${$amount} donation (interpolated)"
        );
    }

    /** @return array<string, array{float, int}> */
    public static function interpolatedAmountsProvider(): array
    {
        return [
            // Between $5 and $10
            '$6' => [6, 4],
            '$7' => [7, 5],
            '$9' => [9, 7],

            // Between $10 and $20
            '$12' => [12, 10],

            // Between $20 and $40
            '$25' => [25, 24],
            '$33' => [33, 34],

            // Between $40 and $75
            '$43' => [43, 46],
            '$50' => [50, 55],
            '$55' => [55, 62],
            '$65' => [65, 75],

            // Between $75 and $100 (fractional dollar)
            '$77.77' => [77.77, 93],

            // Between $100 and $200
            '$110' => [110, 143],
            '$137' => [137, 181],

            // Between $200 and $300
            '$250' => [250, 346],

            // Between $300 and $500
            '$333' => [333, 473],
            '$450' => [450, 655],

            // Between $500 and $1000
            '$750' => [750, 1129],
            '$999' => [999, 1523],
        ];
    }

    /**
     * Verify extrapolation produces correct results for amounts above $1000.
     */
    #[DataProvider('extrapolatedAmountsProvider')]
    public function test_extrapolates_non_anchor_amounts(float $amount, int $expectedUbers): void
    {
        $this->assertEquals(
            $expectedUbers,
            DonationCalculator::calculate($amount),
            "Expected {$expectedUbers} ubers for \${$amount} donation (extrapolated)"
        );
    }

    /** @return array<string, array{float, int}> */
    public static function extrapolatedAmountsProvider(): array
    {
        return [
            '$1100' => [1100, 1683],
            '$1500' => [1500, 2317],
            '$2500' => [2500, 3901],
        ];
    }

    public function test_ubers_increase_monotonically(): void
    {
        $previous = 0;

        for ($amount = 5; $amount <= 2000; $amount += 5) {
            $ubers = DonationCalculator::calculate($amount);
            $this->assertGreaterThan($previous, $ubers, "Ubers should increase at \${$amount}");
            $previous = $ubers;
        }
    }
}
