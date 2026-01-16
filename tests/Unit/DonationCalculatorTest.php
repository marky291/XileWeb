<?php

namespace Tests\Unit;

use App\Services\DonationCalculator;
use Tests\TestCase;

class DonationCalculatorTest extends TestCase
{
    public function test_returns_static_ubers_for_tier_amounts(): void
    {
        // These should always return the exact tier values, no calculation
        $this->assertEquals(3, DonationCalculator::calculate(5));
        $this->assertEquals(8, DonationCalculator::calculate(10));
        $this->assertEquals(18, DonationCalculator::calculate(20));
        $this->assertEquals(42, DonationCalculator::calculate(40));
        $this->assertEquals(88, DonationCalculator::calculate(75));
    }

    public function test_returns_zero_for_amounts_below_minimum(): void
    {
        $this->assertEquals(0, DonationCalculator::calculate(0));
        $this->assertEquals(0, DonationCalculator::calculate(1));
        $this->assertEquals(0, DonationCalculator::calculate(4));
    }

    public function test_extrapolates_for_amounts_above_highest_tier(): void
    {
        // $75 = 88 Ubers (rate = 1.173)
        // $80 should be more than 88
        $at80 = DonationCalculator::calculate(80);
        $this->assertGreaterThan(88, $at80);

        // $100 should be more than $80
        $at100 = DonationCalculator::calculate(100);
        $this->assertGreaterThan($at80, $at100);

        // $150 should be more than $100
        $at150 = DonationCalculator::calculate(150);
        $this->assertGreaterThan($at100, $at150);
    }

    public function test_higher_amounts_have_competitive_rates(): void
    {
        $rate75 = DonationCalculator::getRate(75);
        $rate100 = DonationCalculator::getRate(100);
        $rate200 = DonationCalculator::getRate(200);

        // Rates should remain competitive (at least close to highest tier rate)
        // Allow up to 5% variance from highest tier rate
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
        $rate1000 = DonationCalculator::getRate(1000);
        $this->assertLessThanOrEqual($maxRate, $rate1000);
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

    public function test_rounds_down_by_default(): void
    {
        // For any fractional result, should round down
        // Testing with amounts that would produce fractional ubers
        $ubers = DonationCalculator::calculate(85);

        // The result should be an integer
        $this->assertIsInt($ubers);
    }
}
