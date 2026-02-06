<?php

namespace Tests\Feature;

use App\Models\DonationLog;
use App\Models\DonationRewardClaim;
use App\Models\DonationRewardTier;
use App\Models\DonationRewardTierItem;
use App\Models\GameAccount;
use App\Models\Item;
use App\Models\UberShopPurchase;
use App\Models\User;
use App\Services\DonationRewardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DonationRewardServiceTest extends TestCase
{
    use RefreshDatabase;

    private DonationRewardService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(DonationRewardService::class);
    }

    #[Test]
    public function it_applies_per_donation_rewards_when_threshold_met(): void
    {
        $user = User::factory()->create();
        $admin = User::factory()->admin()->create();
        $item = Item::factory()->create(['name' => 'Test Reward']);

        $tier = DonationRewardTier::factory()->perDonation()->minimumAmount(25.00)->create();
        DonationRewardTierItem::create([
            'donation_reward_tier_id' => $tier->id,
            'item_id' => $item->id,
            'quantity' => 1,
            'refine_level' => 5,
        ]);

        $donationLog = DonationLog::factory()->forUser($user)->byAdmin($admin)->create([
            'amount' => 30.00,
        ]);

        $claims = $this->service->applyRewards($donationLog);

        $this->assertCount(1, $claims);
        $this->assertDatabaseHas('donation_reward_claims', [
            'user_id' => $user->id,
            'donation_log_id' => $donationLog->id,
            'donation_reward_tier_id' => $tier->id,
            'item_id' => $item->id,
            'quantity' => 1,
            'refine_level' => 5,
            'status' => 'pending',
        ]);
    }

    #[Test]
    public function it_does_not_apply_rewards_when_below_threshold(): void
    {
        $user = User::factory()->create();
        $admin = User::factory()->admin()->create();
        $item = Item::factory()->create();

        $tier = DonationRewardTier::factory()->perDonation()->minimumAmount(50.00)->create();
        DonationRewardTierItem::create([
            'donation_reward_tier_id' => $tier->id,
            'item_id' => $item->id,
            'quantity' => 1,
            'refine_level' => 0,
        ]);

        $donationLog = DonationLog::factory()->forUser($user)->byAdmin($admin)->create([
            'amount' => 25.00,
        ]);

        $claims = $this->service->applyRewards($donationLog);

        $this->assertCount(0, $claims);
        $this->assertDatabaseMissing('donation_reward_claims', [
            'user_id' => $user->id,
        ]);
    }

    #[Test]
    public function it_applies_highest_non_cumulative_tier_only(): void
    {
        $user = User::factory()->create();
        $admin = User::factory()->admin()->create();

        $item1 = Item::factory()->create(['name' => 'Bronze Reward']);
        $item2 = Item::factory()->create(['name' => 'Silver Reward']);

        $bronzeTier = DonationRewardTier::factory()->perDonation()
            ->minimumAmount(10.00)
            ->create(['is_cumulative' => false]);
        DonationRewardTierItem::create([
            'donation_reward_tier_id' => $bronzeTier->id,
            'item_id' => $item1->id,
            'quantity' => 1,
            'refine_level' => 0,
        ]);

        $silverTier = DonationRewardTier::factory()->perDonation()
            ->minimumAmount(25.00)
            ->create(['is_cumulative' => false]);
        DonationRewardTierItem::create([
            'donation_reward_tier_id' => $silverTier->id,
            'item_id' => $item2->id,
            'quantity' => 1,
            'refine_level' => 0,
        ]);

        $donationLog = DonationLog::factory()->forUser($user)->byAdmin($admin)->create([
            'amount' => 30.00,
        ]);

        $claims = $this->service->applyRewards($donationLog);

        $this->assertCount(1, $claims);
        $this->assertDatabaseHas('donation_reward_claims', [
            'donation_reward_tier_id' => $silverTier->id,
            'item_id' => $item2->id,
        ]);
        $this->assertDatabaseMissing('donation_reward_claims', [
            'donation_reward_tier_id' => $bronzeTier->id,
        ]);
    }

    #[Test]
    public function it_applies_all_cumulative_tiers(): void
    {
        $user = User::factory()->create();
        $admin = User::factory()->admin()->create();

        $item1 = Item::factory()->create(['name' => 'Bronze Reward']);
        $item2 = Item::factory()->create(['name' => 'Silver Reward']);

        $bronzeTier = DonationRewardTier::factory()->perDonation()->cumulative()
            ->minimumAmount(10.00)
            ->create();
        DonationRewardTierItem::create([
            'donation_reward_tier_id' => $bronzeTier->id,
            'item_id' => $item1->id,
            'quantity' => 1,
            'refine_level' => 0,
        ]);

        $silverTier = DonationRewardTier::factory()->perDonation()->cumulative()
            ->minimumAmount(25.00)
            ->create();
        DonationRewardTierItem::create([
            'donation_reward_tier_id' => $silverTier->id,
            'item_id' => $item2->id,
            'quantity' => 1,
            'refine_level' => 0,
        ]);

        $donationLog = DonationLog::factory()->forUser($user)->byAdmin($admin)->create([
            'amount' => 30.00,
        ]);

        $claims = $this->service->applyRewards($donationLog);

        $this->assertCount(2, $claims);
        $this->assertDatabaseHas('donation_reward_claims', [
            'donation_reward_tier_id' => $bronzeTier->id,
        ]);
        $this->assertDatabaseHas('donation_reward_claims', [
            'donation_reward_tier_id' => $silverTier->id,
        ]);
    }

    #[Test]
    public function it_applies_lifetime_tier_when_cumulative_total_crosses_threshold(): void
    {
        $user = User::factory()->create();
        $admin = User::factory()->admin()->create();
        $item = Item::factory()->create(['name' => 'VIP Reward']);

        // Create previous donation to establish lifetime total
        DonationLog::factory()->forUser($user)->byAdmin($admin)->create([
            'amount' => 50.00,
        ]);

        $lifetimeTier = DonationRewardTier::factory()->lifetime()
            ->minimumAmount(75.00)
            ->create();
        DonationRewardTierItem::create([
            'donation_reward_tier_id' => $lifetimeTier->id,
            'item_id' => $item->id,
            'quantity' => 1,
            'refine_level' => 10,
        ]);

        $newDonation = DonationLog::factory()->forUser($user)->byAdmin($admin)->create([
            'amount' => 30.00,
        ]);

        $claims = $this->service->applyRewards($newDonation);

        $this->assertCount(1, $claims);
        $this->assertDatabaseHas('donation_reward_claims', [
            'user_id' => $user->id,
            'donation_reward_tier_id' => $lifetimeTier->id,
        ]);
    }

    #[Test]
    public function it_does_not_apply_lifetime_tier_already_claimed(): void
    {
        $user = User::factory()->create();
        $admin = User::factory()->admin()->create();
        $item = Item::factory()->create();

        // Create previous donation that crossed threshold
        $firstDonation = DonationLog::factory()->forUser($user)->byAdmin($admin)->create([
            'amount' => 100.00,
        ]);

        $lifetimeTier = DonationRewardTier::factory()->lifetime()
            ->minimumAmount(75.00)
            ->create();
        DonationRewardTierItem::create([
            'donation_reward_tier_id' => $lifetimeTier->id,
            'item_id' => $item->id,
            'quantity' => 1,
            'refine_level' => 0,
        ]);

        // Simulate that the tier was already claimed
        DonationRewardClaim::factory()
            ->forUser($user)
            ->forItem($item)
            ->claimed()
            ->create([
                'donation_log_id' => $firstDonation->id,
                'donation_reward_tier_id' => $lifetimeTier->id,
            ]);

        // New donation should not trigger this tier again since already claimed
        $newDonation = DonationLog::factory()->forUser($user)->byAdmin($admin)->create([
            'amount' => 25.00,
        ]);

        $claims = $this->service->applyRewards($newDonation);

        $this->assertCount(0, $claims);
    }

    #[Test]
    public function it_does_not_apply_disabled_tiers(): void
    {
        $user = User::factory()->create();
        $admin = User::factory()->admin()->create();
        $item = Item::factory()->create();

        $tier = DonationRewardTier::factory()->perDonation()->disabled()
            ->minimumAmount(10.00)
            ->create();
        DonationRewardTierItem::create([
            'donation_reward_tier_id' => $tier->id,
            'item_id' => $item->id,
            'quantity' => 1,
            'refine_level' => 0,
        ]);

        $donationLog = DonationLog::factory()->forUser($user)->byAdmin($admin)->create([
            'amount' => 50.00,
        ]);

        $claims = $this->service->applyRewards($donationLog);

        $this->assertCount(0, $claims);
    }

    #[Test]
    public function it_inherits_server_flags_from_tier(): void
    {
        $user = User::factory()->create();
        $admin = User::factory()->admin()->create();
        $item = Item::factory()->create();

        $tier = DonationRewardTier::factory()->perDonation()->xileroOnly()
            ->minimumAmount(10.00)
            ->create();
        DonationRewardTierItem::create([
            'donation_reward_tier_id' => $tier->id,
            'item_id' => $item->id,
            'quantity' => 1,
            'refine_level' => 0,
        ]);

        $donationLog = DonationLog::factory()->forUser($user)->byAdmin($admin)->create([
            'amount' => 50.00,
        ]);

        $claims = $this->service->applyRewards($donationLog);

        $this->assertCount(1, $claims);
        $this->assertDatabaseHas('donation_reward_claims', [
            'user_id' => $user->id,
            'is_xilero' => true,
            'is_xileretro' => false,
        ]);
    }

    #[Test]
    public function it_can_claim_reward_to_game_account(): void
    {
        $user = User::factory()->create(['uber_balance' => 100]);
        $gameAccount = GameAccount::factory()->for($user)->create();
        $item = Item::factory()->create(['item_id' => 12345, 'name' => 'Test Item']);

        $claim = DonationRewardClaim::factory()
            ->forUser($user)
            ->forItem($item)
            ->pending()
            ->create([
                'quantity' => 3,
                'refine_level' => 7,
                'is_xilero' => true,
                'is_xileretro' => true,
            ]);

        $purchase = $this->service->claimReward($claim, $gameAccount);

        $this->assertInstanceOf(UberShopPurchase::class, $purchase);
        $this->assertEquals($gameAccount->ragnarok_account_id, $purchase->account_id);
        $this->assertEquals(12345, $purchase->item_id);
        $this->assertEquals('Test Item', $purchase->item_name);
        $this->assertEquals(3, $purchase->quantity);
        $this->assertEquals(7, $purchase->refine_level);
        $this->assertEquals(0, $purchase->uber_cost);
        $this->assertEquals(UberShopPurchase::STATUS_PENDING, $purchase->status);
        $this->assertTrue($purchase->is_bonus_reward);

        $claim->refresh();
        $this->assertEquals(DonationRewardClaim::STATUS_CLAIMED, $claim->status);
        $this->assertNotNull($claim->claimed_at);
    }

    #[Test]
    public function it_prevents_claiming_on_wrong_server(): void
    {
        $user = User::factory()->create();
        $gameAccount = GameAccount::factory()->xileretro()->for($user)->create();
        $item = Item::factory()->create();

        $claim = DonationRewardClaim::factory()
            ->forUser($user)
            ->forItem($item)
            ->xileroOnly()
            ->pending()
            ->create();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('This reward cannot be claimed by the selected account.');

        $this->service->claimReward($claim, $gameAccount);
    }

    #[Test]
    public function it_prevents_claiming_already_claimed_reward(): void
    {
        $user = User::factory()->create();
        $gameAccount = GameAccount::factory()->for($user)->create();
        $item = Item::factory()->create();

        $claim = DonationRewardClaim::factory()
            ->forUser($user)
            ->forItem($item)
            ->claimed()
            ->create();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('This reward cannot be claimed by the selected account.');

        $this->service->claimReward($claim, $gameAccount);
    }

    #[Test]
    public function it_gets_user_pending_rewards(): void
    {
        $user = User::factory()->create();

        $pendingClaims = DonationRewardClaim::factory()
            ->forUser($user)
            ->pending()
            ->count(3)
            ->create();

        DonationRewardClaim::factory()
            ->forUser($user)
            ->claimed()
            ->count(2)
            ->create();

        $pending = $this->service->getUserPendingRewards($user);

        $this->assertCount(3, $pending);
    }

    #[Test]
    public function it_calculates_lifetime_donation_total(): void
    {
        $user = User::factory()->create();
        $admin = User::factory()->admin()->create();

        DonationLog::factory()->forUser($user)->byAdmin($admin)->create(['amount' => 25.00]);
        DonationLog::factory()->forUser($user)->byAdmin($admin)->create(['amount' => 50.00]);
        DonationLog::factory()->forUser($user)->byAdmin($admin)->create([
            'amount' => 100.00,
            'reverted_at' => now(),
        ]);

        $total = $this->service->getLifetimeDonationTotal($user);

        $this->assertEquals(75.00, $total);
    }

    #[Test]
    public function it_creates_multiple_claims_for_multiple_items_in_tier(): void
    {
        $user = User::factory()->create();
        $admin = User::factory()->admin()->create();

        $item1 = Item::factory()->create(['name' => 'Item One']);
        $item2 = Item::factory()->create(['name' => 'Item Two']);

        $tier = DonationRewardTier::factory()->perDonation()->minimumAmount(10.00)->create();
        DonationRewardTierItem::create([
            'donation_reward_tier_id' => $tier->id,
            'item_id' => $item1->id,
            'quantity' => 5,
            'refine_level' => 0,
        ]);
        DonationRewardTierItem::create([
            'donation_reward_tier_id' => $tier->id,
            'item_id' => $item2->id,
            'quantity' => 10,
            'refine_level' => 3,
        ]);

        $donationLog = DonationLog::factory()->forUser($user)->byAdmin($admin)->create([
            'amount' => 20.00,
        ]);

        $claims = $this->service->applyRewards($donationLog);

        $this->assertCount(2, $claims);
        $this->assertDatabaseHas('donation_reward_claims', [
            'item_id' => $item1->id,
            'quantity' => 5,
        ]);
        $this->assertDatabaseHas('donation_reward_claims', [
            'item_id' => $item2->id,
            'quantity' => 10,
            'refine_level' => 3,
        ]);
    }

    #[Test]
    public function it_does_not_apply_one_time_tier_twice(): void
    {
        $user = User::factory()->create();
        $admin = User::factory()->admin()->create();
        $item = Item::factory()->create();

        $tier = DonationRewardTier::factory()->perDonation()
            ->minimumAmount(10.00)
            ->create(['claim_reset_period' => null]);
        DonationRewardTierItem::create([
            'donation_reward_tier_id' => $tier->id,
            'item_id' => $item->id,
            'quantity' => 1,
            'refine_level' => 0,
        ]);

        // First donation creates a claim
        $donationLog1 = DonationLog::factory()->forUser($user)->byAdmin($admin)->create([
            'amount' => 20.00,
        ]);
        $claims1 = $this->service->applyRewards($donationLog1);
        $this->assertCount(1, $claims1);

        // Second donation should not create a claim for the same one-time tier
        $donationLog2 = DonationLog::factory()->forUser($user)->byAdmin($admin)->create([
            'amount' => 20.00,
        ]);
        $claims2 = $this->service->applyRewards($donationLog2);
        $this->assertCount(0, $claims2);
    }

    #[Test]
    public function it_allows_daily_tier_to_be_claimed_in_new_day(): void
    {
        $user = User::factory()->create();
        $admin = User::factory()->admin()->create();
        $item = Item::factory()->create();

        $tier = DonationRewardTier::factory()->perDonation()
            ->resetsDaily()
            ->minimumAmount(10.00)
            ->create();
        DonationRewardTierItem::create([
            'donation_reward_tier_id' => $tier->id,
            'item_id' => $item->id,
            'quantity' => 1,
            'refine_level' => 0,
        ]);

        // Claim from yesterday
        Carbon::setTestNow(now()->subDay());
        $donationLog1 = DonationLog::factory()->forUser($user)->byAdmin($admin)->create([
            'amount' => 20.00,
        ]);
        $claims1 = $this->service->applyRewards($donationLog1);
        $this->assertCount(1, $claims1);

        // Back to today - should allow new claim
        Carbon::setTestNow();
        $donationLog2 = DonationLog::factory()->forUser($user)->byAdmin($admin)->create([
            'amount' => 20.00,
        ]);
        $claims2 = $this->service->applyRewards($donationLog2);
        $this->assertCount(1, $claims2);
    }

    #[Test]
    public function it_prevents_daily_tier_claimed_again_same_day(): void
    {
        $user = User::factory()->create();
        $admin = User::factory()->admin()->create();
        $item = Item::factory()->create();

        $tier = DonationRewardTier::factory()->perDonation()
            ->resetsDaily()
            ->minimumAmount(10.00)
            ->create();
        DonationRewardTierItem::create([
            'donation_reward_tier_id' => $tier->id,
            'item_id' => $item->id,
            'quantity' => 1,
            'refine_level' => 0,
        ]);

        // First donation today
        $donationLog1 = DonationLog::factory()->forUser($user)->byAdmin($admin)->create([
            'amount' => 20.00,
        ]);
        $claims1 = $this->service->applyRewards($donationLog1);
        $this->assertCount(1, $claims1);

        // Second donation same day should not create claim
        $donationLog2 = DonationLog::factory()->forUser($user)->byAdmin($admin)->create([
            'amount' => 20.00,
        ]);
        $claims2 = $this->service->applyRewards($donationLog2);
        $this->assertCount(0, $claims2);
    }

    #[Test]
    public function it_allows_weekly_tier_to_be_claimed_in_new_week(): void
    {
        $user = User::factory()->create();
        $admin = User::factory()->admin()->create();
        $item = Item::factory()->create();

        $tier = DonationRewardTier::factory()->perDonation()
            ->resetsWeekly()
            ->minimumAmount(10.00)
            ->create();
        DonationRewardTierItem::create([
            'donation_reward_tier_id' => $tier->id,
            'item_id' => $item->id,
            'quantity' => 1,
            'refine_level' => 0,
        ]);

        // Claim from last week
        Carbon::setTestNow(now()->subWeek());
        $donationLog1 = DonationLog::factory()->forUser($user)->byAdmin($admin)->create([
            'amount' => 20.00,
        ]);
        $claims1 = $this->service->applyRewards($donationLog1);
        $this->assertCount(1, $claims1);

        // Back to this week - should allow new claim
        Carbon::setTestNow();
        $donationLog2 = DonationLog::factory()->forUser($user)->byAdmin($admin)->create([
            'amount' => 20.00,
        ]);
        $claims2 = $this->service->applyRewards($donationLog2);
        $this->assertCount(1, $claims2);
    }

    #[Test]
    public function it_allows_monthly_tier_to_be_claimed_in_new_month(): void
    {
        $user = User::factory()->create();
        $admin = User::factory()->admin()->create();
        $item = Item::factory()->create();

        $tier = DonationRewardTier::factory()->perDonation()
            ->resetsMonthly()
            ->minimumAmount(10.00)
            ->create();
        DonationRewardTierItem::create([
            'donation_reward_tier_id' => $tier->id,
            'item_id' => $item->id,
            'quantity' => 1,
            'refine_level' => 0,
        ]);

        // Claim from last month
        Carbon::setTestNow(now()->subMonth());
        $donationLog1 = DonationLog::factory()->forUser($user)->byAdmin($admin)->create([
            'amount' => 20.00,
        ]);
        $claims1 = $this->service->applyRewards($donationLog1);
        $this->assertCount(1, $claims1);

        // Back to this month - should allow new claim
        Carbon::setTestNow();
        $donationLog2 = DonationLog::factory()->forUser($user)->byAdmin($admin)->create([
            'amount' => 20.00,
        ]);
        $claims2 = $this->service->applyRewards($donationLog2);
        $this->assertCount(1, $claims2);
    }

    #[Test]
    public function it_prevents_monthly_tier_claimed_again_same_month(): void
    {
        $user = User::factory()->create();
        $admin = User::factory()->admin()->create();
        $item = Item::factory()->create();

        $tier = DonationRewardTier::factory()->perDonation()
            ->resetsMonthly()
            ->minimumAmount(10.00)
            ->create();
        DonationRewardTierItem::create([
            'donation_reward_tier_id' => $tier->id,
            'item_id' => $item->id,
            'quantity' => 1,
            'refine_level' => 0,
        ]);

        // First donation this month
        $donationLog1 = DonationLog::factory()->forUser($user)->byAdmin($admin)->create([
            'amount' => 20.00,
        ]);
        $claims1 = $this->service->applyRewards($donationLog1);
        $this->assertCount(1, $claims1);

        // Second donation same month should not create claim
        $donationLog2 = DonationLog::factory()->forUser($user)->byAdmin($admin)->create([
            'amount' => 20.00,
        ]);
        $claims2 = $this->service->applyRewards($donationLog2);
        $this->assertCount(0, $claims2);
    }

    #[Test]
    public function it_allows_yearly_tier_to_be_claimed_in_new_year(): void
    {
        $user = User::factory()->create();
        $admin = User::factory()->admin()->create();
        $item = Item::factory()->create();

        $tier = DonationRewardTier::factory()->perDonation()
            ->resetsYearly()
            ->minimumAmount(10.00)
            ->create();
        DonationRewardTierItem::create([
            'donation_reward_tier_id' => $tier->id,
            'item_id' => $item->id,
            'quantity' => 1,
            'refine_level' => 0,
        ]);

        // Claim from last year
        Carbon::setTestNow(now()->subYear());
        $donationLog1 = DonationLog::factory()->forUser($user)->byAdmin($admin)->create([
            'amount' => 20.00,
        ]);
        $claims1 = $this->service->applyRewards($donationLog1);
        $this->assertCount(1, $claims1);

        // Back to this year - should allow new claim
        Carbon::setTestNow();
        $donationLog2 = DonationLog::factory()->forUser($user)->byAdmin($admin)->create([
            'amount' => 20.00,
        ]);
        $claims2 = $this->service->applyRewards($donationLog2);
        $this->assertCount(1, $claims2);
    }

    #[Test]
    public function it_correctly_identifies_period_start_for_daily_reset(): void
    {
        $tier = DonationRewardTier::factory()->resetsDaily()->create();

        $periodStart = $tier->getCurrentPeriodStart();

        $this->assertNotNull($periodStart);
        $this->assertEquals(now()->startOfDay()->toDateTimeString(), $periodStart->toDateTimeString());
    }

    #[Test]
    public function it_correctly_identifies_period_start_for_weekly_reset(): void
    {
        $tier = DonationRewardTier::factory()->resetsWeekly()->create();

        $periodStart = $tier->getCurrentPeriodStart();

        $this->assertNotNull($periodStart);
        $this->assertEquals(now()->startOfWeek()->toDateTimeString(), $periodStart->toDateTimeString());
    }

    #[Test]
    public function it_correctly_identifies_period_start_for_monthly_reset(): void
    {
        $tier = DonationRewardTier::factory()->resetsMonthly()->create();

        $periodStart = $tier->getCurrentPeriodStart();

        $this->assertNotNull($periodStart);
        $this->assertEquals(now()->startOfMonth()->toDateTimeString(), $periodStart->toDateTimeString());
    }

    #[Test]
    public function it_correctly_identifies_period_start_for_yearly_reset(): void
    {
        $tier = DonationRewardTier::factory()->resetsYearly()->create();

        $periodStart = $tier->getCurrentPeriodStart();

        $this->assertNotNull($periodStart);
        $this->assertEquals(now()->startOfYear()->toDateTimeString(), $periodStart->toDateTimeString());
    }

    #[Test]
    public function it_returns_null_period_start_for_one_time_tier(): void
    {
        $tier = DonationRewardTier::factory()->create(['claim_reset_period' => null]);

        $periodStart = $tier->getCurrentPeriodStart();

        $this->assertNull($periodStart);
    }

    #[Test]
    public function it_creates_claims_with_server_flags_from_tier(): void
    {
        $user = User::factory()->create();
        $admin = User::factory()->admin()->create();
        $item = Item::factory()->create();

        // Use cumulative tiers so both qualify
        $xileroTier = DonationRewardTier::factory()->perDonation()->cumulative()->xileroOnly()
            ->minimumAmount(10.00)
            ->create();
        DonationRewardTierItem::create([
            'donation_reward_tier_id' => $xileroTier->id,
            'item_id' => $item->id,
            'quantity' => 1,
            'refine_level' => 0,
        ]);

        $xileretroTier = DonationRewardTier::factory()->perDonation()->cumulative()->xileretroOnly()
            ->minimumAmount(10.00)
            ->create();
        DonationRewardTierItem::create([
            'donation_reward_tier_id' => $xileretroTier->id,
            'item_id' => $item->id,
            'quantity' => 1,
            'refine_level' => 0,
        ]);

        $donationLog = DonationLog::factory()->forUser($user)->byAdmin($admin)->create([
            'amount' => 20.00,
        ]);

        $claims = $this->service->applyRewards($donationLog);

        // Both should be created since both are cumulative tiers
        $this->assertCount(2, $claims);
        $this->assertDatabaseHas('donation_reward_claims', [
            'donation_reward_tier_id' => $xileroTier->id,
            'is_xilero' => true,
            'is_xileretro' => false,
        ]);
        $this->assertDatabaseHas('donation_reward_claims', [
            'donation_reward_tier_id' => $xileretroTier->id,
            'is_xilero' => false,
            'is_xileretro' => true,
        ]);
    }

    #[Test]
    public function it_applies_both_per_donation_and_lifetime_tiers(): void
    {
        $user = User::factory()->create();
        $admin = User::factory()->admin()->create();

        $perDonationItem = Item::factory()->create(['name' => 'Per Donation Item']);
        $lifetimeItem = Item::factory()->create(['name' => 'Lifetime Item']);

        // Create a prior donation to establish lifetime total
        DonationLog::factory()->forUser($user)->byAdmin($admin)->create([
            'amount' => 40.00,
        ]);

        $perDonationTier = DonationRewardTier::factory()->perDonation()
            ->minimumAmount(15.00)
            ->create();
        DonationRewardTierItem::create([
            'donation_reward_tier_id' => $perDonationTier->id,
            'item_id' => $perDonationItem->id,
            'quantity' => 1,
            'refine_level' => 0,
        ]);

        $lifetimeTier = DonationRewardTier::factory()->lifetime()
            ->minimumAmount(50.00)
            ->create();
        DonationRewardTierItem::create([
            'donation_reward_tier_id' => $lifetimeTier->id,
            'item_id' => $lifetimeItem->id,
            'quantity' => 1,
            'refine_level' => 0,
        ]);

        // New donation that triggers both types
        $newDonation = DonationLog::factory()->forUser($user)->byAdmin($admin)->create([
            'amount' => 20.00,
        ]);

        $claims = $this->service->applyRewards($newDonation);

        // Should get both per-donation and lifetime tier
        $this->assertCount(2, $claims);
        $this->assertDatabaseHas('donation_reward_claims', [
            'donation_reward_tier_id' => $perDonationTier->id,
        ]);
        $this->assertDatabaseHas('donation_reward_claims', [
            'donation_reward_tier_id' => $lifetimeTier->id,
        ]);
    }

    #[Test]
    public function it_excludes_reverted_donations_from_lifetime_total(): void
    {
        $user = User::factory()->create();
        $admin = User::factory()->admin()->create();
        $item = Item::factory()->create();

        // Create a reverted donation
        DonationLog::factory()->forUser($user)->byAdmin($admin)->create([
            'amount' => 100.00,
            'reverted_at' => now(),
        ]);

        $lifetimeTier = DonationRewardTier::factory()->lifetime()
            ->minimumAmount(50.00)
            ->create();
        DonationRewardTierItem::create([
            'donation_reward_tier_id' => $lifetimeTier->id,
            'item_id' => $item->id,
            'quantity' => 1,
            'refine_level' => 0,
        ]);

        // Small donation that doesn't meet threshold
        $newDonation = DonationLog::factory()->forUser($user)->byAdmin($admin)->create([
            'amount' => 25.00,
        ]);

        $claims = $this->service->applyRewards($newDonation);

        // Should not qualify since reverted donation doesn't count
        $this->assertCount(0, $claims);
    }

    #[Test]
    public function it_previews_applicable_tiers_without_creating_claims(): void
    {
        $user = User::factory()->create();
        $admin = User::factory()->admin()->create();
        $item = Item::factory()->create();

        $tier = DonationRewardTier::factory()->perDonation()
            ->minimumAmount(10.00)
            ->create();
        DonationRewardTierItem::create([
            'donation_reward_tier_id' => $tier->id,
            'item_id' => $item->id,
            'quantity' => 1,
            'refine_level' => 0,
        ]);

        $applicableTiers = $this->service->getApplicableTiersPreview(20.00, $user);

        $this->assertCount(1, $applicableTiers);
        $this->assertEquals($tier->id, $applicableTiers->first()->id);

        // No claims should be created
        $this->assertDatabaseMissing('donation_reward_claims', [
            'user_id' => $user->id,
        ]);
    }

    #[Test]
    public function preview_excludes_already_claimed_tiers(): void
    {
        $user = User::factory()->create();
        $admin = User::factory()->admin()->create();
        $item = Item::factory()->create();

        $tier = DonationRewardTier::factory()->perDonation()
            ->minimumAmount(10.00)
            ->create(['claim_reset_period' => null]);
        DonationRewardTierItem::create([
            'donation_reward_tier_id' => $tier->id,
            'item_id' => $item->id,
            'quantity' => 1,
            'refine_level' => 0,
        ]);

        // Create an existing claim for this tier
        DonationRewardClaim::factory()
            ->forUser($user)
            ->forItem($item)
            ->claimed()
            ->create(['donation_reward_tier_id' => $tier->id]);

        $applicableTiers = $this->service->getApplicableTiersPreview(20.00, $user);

        $this->assertCount(0, $applicableTiers);
    }

    #[Test]
    public function preview_includes_tier_if_reset_period_passed(): void
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $tier = DonationRewardTier::factory()->perDonation()
            ->resetsMonthly()
            ->minimumAmount(10.00)
            ->create();
        DonationRewardTierItem::create([
            'donation_reward_tier_id' => $tier->id,
            'item_id' => $item->id,
            'quantity' => 1,
            'refine_level' => 0,
        ]);

        // Create an old claim from last month
        Carbon::setTestNow(now()->subMonth());
        DonationRewardClaim::factory()
            ->forUser($user)
            ->forItem($item)
            ->claimed()
            ->create(['donation_reward_tier_id' => $tier->id]);

        // Back to current time
        Carbon::setTestNow();

        $applicableTiers = $this->service->getApplicableTiersPreview(20.00, $user);

        // Should include tier since reset period passed
        $this->assertCount(1, $applicableTiers);
    }

    #[Test]
    public function it_handles_tier_with_no_items_gracefully(): void
    {
        $user = User::factory()->create();
        $admin = User::factory()->admin()->create();

        // Create tier without any items
        DonationRewardTier::factory()->perDonation()
            ->minimumAmount(10.00)
            ->create();

        $donationLog = DonationLog::factory()->forUser($user)->byAdmin($admin)->create([
            'amount' => 20.00,
        ]);

        $claims = $this->service->applyRewards($donationLog);

        // No claims created since no items in tier
        $this->assertCount(0, $claims);
    }

    #[Test]
    public function it_correctly_handles_mixed_cumulative_and_non_cumulative(): void
    {
        $user = User::factory()->create();
        $admin = User::factory()->admin()->create();

        $item1 = Item::factory()->create(['name' => 'Non-Cumulative 10']);
        $item2 = Item::factory()->create(['name' => 'Non-Cumulative 25']);
        $item3 = Item::factory()->create(['name' => 'Cumulative 15']);

        // Non-cumulative tier at $10
        $nonCumTier1 = DonationRewardTier::factory()->perDonation()
            ->minimumAmount(10.00)
            ->create(['is_cumulative' => false]);
        DonationRewardTierItem::create([
            'donation_reward_tier_id' => $nonCumTier1->id,
            'item_id' => $item1->id,
            'quantity' => 1,
            'refine_level' => 0,
        ]);

        // Non-cumulative tier at $25
        $nonCumTier2 = DonationRewardTier::factory()->perDonation()
            ->minimumAmount(25.00)
            ->create(['is_cumulative' => false]);
        DonationRewardTierItem::create([
            'donation_reward_tier_id' => $nonCumTier2->id,
            'item_id' => $item2->id,
            'quantity' => 1,
            'refine_level' => 0,
        ]);

        // Cumulative tier at $15
        $cumTier = DonationRewardTier::factory()->perDonation()->cumulative()
            ->minimumAmount(15.00)
            ->create();
        DonationRewardTierItem::create([
            'donation_reward_tier_id' => $cumTier->id,
            'item_id' => $item3->id,
            'quantity' => 1,
            'refine_level' => 0,
        ]);

        $donationLog = DonationLog::factory()->forUser($user)->byAdmin($admin)->create([
            'amount' => 30.00,
        ]);

        $claims = $this->service->applyRewards($donationLog);

        // Should get: highest non-cumulative ($25) + cumulative ($15)
        $this->assertCount(2, $claims);
        $this->assertDatabaseHas('donation_reward_claims', [
            'donation_reward_tier_id' => $nonCumTier2->id,
        ]);
        $this->assertDatabaseHas('donation_reward_claims', [
            'donation_reward_tier_id' => $cumTier->id,
        ]);
        $this->assertDatabaseMissing('donation_reward_claims', [
            'donation_reward_tier_id' => $nonCumTier1->id,
        ]);
    }

    #[Test]
    public function it_claims_reward_and_sets_correct_server_flag(): void
    {
        $user = User::factory()->create(['uber_balance' => 100]);
        $xileretroAccount = GameAccount::factory()->xileretro()->for($user)->create();
        $item = Item::factory()->create(['item_id' => 99999]);

        $claim = DonationRewardClaim::factory()
            ->forUser($user)
            ->forItem($item)
            ->pending()
            ->create([
                'is_xilero' => true,
                'is_xileretro' => true,
            ]);

        $purchase = $this->service->claimReward($claim, $xileretroAccount);

        // Should be marked as XileRetro purchase based on account
        $this->assertTrue($purchase->is_xileretro);
    }

    #[Test]
    public function it_tracks_claimed_lifetime_tier_ids(): void
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $tier1 = DonationRewardTier::factory()->lifetime()->minimumAmount(50)->create();
        $tier2 = DonationRewardTier::factory()->lifetime()->minimumAmount(100)->create();

        // Create claim for tier1
        DonationRewardClaim::factory()
            ->forUser($user)
            ->forItem($item)
            ->claimed()
            ->create(['donation_reward_tier_id' => $tier1->id]);

        $claimedTierIds = $this->service->getClaimedLifetimeTierIds($user);

        $this->assertCount(1, $claimedTierIds);
        $this->assertTrue($claimedTierIds->contains($tier1->id));
        $this->assertFalse($claimedTierIds->contains($tier2->id));
    }

    #[Test]
    public function it_applies_rewards_at_boundary_of_daily_reset(): void
    {
        $user = User::factory()->create();
        $admin = User::factory()->admin()->create();
        $item = Item::factory()->create();

        $tier = DonationRewardTier::factory()->perDonation()
            ->resetsDaily()
            ->minimumAmount(10.00)
            ->create();
        DonationRewardTierItem::create([
            'donation_reward_tier_id' => $tier->id,
            'item_id' => $item->id,
            'quantity' => 1,
            'refine_level' => 0,
        ]);

        // Claim at 11:59 PM
        Carbon::setTestNow(now()->endOfDay()->subMinute());
        $donation1 = DonationLog::factory()->forUser($user)->byAdmin($admin)->create([
            'amount' => 20.00,
        ]);
        $claims1 = $this->service->applyRewards($donation1);
        $this->assertCount(1, $claims1);

        // Try to claim 2 minutes later (past midnight)
        Carbon::setTestNow(now()->addMinutes(2));
        $donation2 = DonationLog::factory()->forUser($user)->byAdmin($admin)->create([
            'amount' => 20.00,
        ]);
        $claims2 = $this->service->applyRewards($donation2);

        // Should get new claim since day changed
        $this->assertCount(1, $claims2);
    }

    #[Test]
    public function it_handles_user_with_no_donations(): void
    {
        $user = User::factory()->create();

        $total = $this->service->getLifetimeDonationTotal($user);

        $this->assertEquals(0.00, $total);
    }

    #[Test]
    public function has_claimed_lifetime_tier_returns_false_for_unclaimed(): void
    {
        $user = User::factory()->create();
        $tier = DonationRewardTier::factory()->lifetime()->create();

        $hasClaimed = $this->service->hasClaimedLifetimeTier($user, $tier);

        $this->assertFalse($hasClaimed);
    }

    #[Test]
    public function has_claimed_lifetime_tier_returns_true_for_claimed(): void
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $tier = DonationRewardTier::factory()->lifetime()->create();

        DonationRewardClaim::factory()
            ->forUser($user)
            ->forItem($item)
            ->claimed()
            ->create(['donation_reward_tier_id' => $tier->id]);

        $hasClaimed = $this->service->hasClaimedLifetimeTier($user, $tier);

        $this->assertTrue($hasClaimed);
    }
}
