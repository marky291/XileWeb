<?php

namespace Tests\Feature\Livewire;

use App\Livewire\DonationRewardClaims;
use App\Models\DonationRewardClaim;
use App\Models\DonationRewardTier;
use App\Models\GameAccount;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DonationRewardClaimsTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function pending_rewards_returns_users_pending_claims(): void
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $gameAccount = GameAccount::factory()->for($user)->create();

        DonationRewardClaim::factory()
            ->forUser($user)
            ->forItem($item)
            ->pending()
            ->count(3)
            ->create();

        $component = new DonationRewardClaims;
        $this->actingAs($user);
        $component->mount();
        $component->selectedGameAccountId = $gameAccount->id;

        $pendingRewards = $component->pendingRewards();
        $this->assertCount(3, $pendingRewards);
    }

    #[Test]
    public function pending_rewards_filters_by_server(): void
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $xileroAccount = GameAccount::factory()->for($user)->create(); // default is xilero
        $xileretroAccount = GameAccount::factory()->xileretro()->for($user)->create();

        DonationRewardClaim::factory()
            ->forUser($user)
            ->forItem($item)
            ->xileroOnly()
            ->pending()
            ->create();

        DonationRewardClaim::factory()
            ->forUser($user)
            ->forItem($item)
            ->xileretroOnly()
            ->pending()
            ->create();

        $component = new DonationRewardClaims;
        $this->actingAs($user);
        $component->mount();

        // With XileRO account selected
        $component->selectedGameAccountId = $xileroAccount->id;
        $pendingRewards = $component->pendingRewards();
        $this->assertCount(1, $pendingRewards);
        $this->assertTrue($pendingRewards->first()->is_xilero);

        // With XileRetro account selected
        $component->selectedGameAccountId = $xileretroAccount->id;
        $pendingRewards = $component->pendingRewards();
        $this->assertCount(1, $pendingRewards);
        $this->assertTrue($pendingRewards->first()->is_xileretro);
    }

    #[Test]
    public function pending_rewards_excludes_expired(): void
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $gameAccount = GameAccount::factory()->for($user)->create();

        DonationRewardClaim::factory()
            ->forUser($user)
            ->forItem($item)
            ->pending()
            ->create(['expires_at' => now()->subDay()]);

        $component = new DonationRewardClaims;
        $this->actingAs($user);
        $component->mount();
        $component->selectedGameAccountId = $gameAccount->id;

        $pendingRewards = $component->pendingRewards();
        $this->assertCount(0, $pendingRewards);
    }

    #[Test]
    public function claimed_rewards_returns_claimed_history(): void
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $tier = DonationRewardTier::factory()->create();

        DonationRewardClaim::factory()
            ->forUser($user)
            ->forItem($item)
            ->claimed()
            ->count(5)
            ->create(['donation_reward_tier_id' => $tier->id]);

        $component = new DonationRewardClaims;
        $this->actingAs($user);
        $component->mount();

        $claimedRewards = $component->claimedRewards();
        $this->assertCount(5, $claimedRewards);
    }

    #[Test]
    public function claimed_rewards_limited_to_20(): void
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $tier = DonationRewardTier::factory()->create();

        DonationRewardClaim::factory()
            ->forUser($user)
            ->forItem($item)
            ->claimed()
            ->count(25)
            ->create(['donation_reward_tier_id' => $tier->id]);

        $component = new DonationRewardClaims;
        $this->actingAs($user);
        $component->mount();

        $claimedRewards = $component->claimedRewards();
        $this->assertCount(20, $claimedRewards);
    }

    #[Test]
    public function total_pending_count_ignores_server_filter(): void
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $gameAccount = GameAccount::factory()->for($user)->create(); // xilero

        DonationRewardClaim::factory()
            ->forUser($user)
            ->forItem($item)
            ->xileroOnly()
            ->pending()
            ->create();

        DonationRewardClaim::factory()
            ->forUser($user)
            ->forItem($item)
            ->xileretroOnly()
            ->pending()
            ->create();

        $component = new DonationRewardClaims;
        $this->actingAs($user);
        $component->mount();
        $component->selectedGameAccountId = $gameAccount->id;

        // Total count should include both, even though only one matches server
        $this->assertEquals(2, $component->totalPendingCount());
        $this->assertCount(1, $component->pendingRewards());
    }

    #[Test]
    public function selected_game_account_returns_correct_account(): void
    {
        $user = User::factory()->create();
        $account1 = GameAccount::factory()->for($user)->create();
        $account2 = GameAccount::factory()->for($user)->create();

        $component = new DonationRewardClaims;
        $this->actingAs($user);
        $component->mount();

        $component->selectedGameAccountId = $account1->id;
        $this->assertEquals($account1->id, $component->selectedGameAccount()->id);

        $component->selectedGameAccountId = $account2->id;
        $this->assertEquals($account2->id, $component->selectedGameAccount()->id);
    }

    #[Test]
    public function selected_game_account_rejects_other_users_account(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $userAccount = GameAccount::factory()->for($user)->create();
        $otherAccount = GameAccount::factory()->for($otherUser)->create();

        $component = new DonationRewardClaims;
        $this->actingAs($user);
        $component->mount();

        // Try to set another user's account
        $component->updatedSelectedGameAccountId($otherAccount->id);

        // Should fall back to user's first account
        $this->assertEquals($userAccount->id, $component->selectedGameAccountId);
    }

    #[Test]
    public function start_claim_sets_claiming_state(): void
    {
        $user = User::factory()->create();
        $claim = DonationRewardClaim::factory()
            ->forUser($user)
            ->pending()
            ->create();

        $component = new DonationRewardClaims;
        $this->actingAs($user);
        $component->mount();

        $component->startClaim($claim->id);

        $this->assertEquals($claim->id, $component->claimingRewardId);
        $this->assertTrue($component->showClaimConfirm);
    }

    #[Test]
    public function cancel_claim_resets_claiming_state(): void
    {
        $user = User::factory()->create();
        $claim = DonationRewardClaim::factory()
            ->forUser($user)
            ->pending()
            ->create();

        $component = new DonationRewardClaims;
        $this->actingAs($user);
        $component->mount();

        $component->startClaim($claim->id);
        $component->cancelClaim();

        $this->assertNull($component->claimingRewardId);
        $this->assertFalse($component->showClaimConfirm);
    }

    #[Test]
    public function claiming_reward_returns_correct_claim(): void
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $tier = DonationRewardTier::factory()->create();

        $claim = DonationRewardClaim::factory()
            ->forUser($user)
            ->forItem($item)
            ->pending()
            ->create(['donation_reward_tier_id' => $tier->id]);

        $component = new DonationRewardClaims;
        $this->actingAs($user);
        $component->mount();
        $component->claimingRewardId = $claim->id;

        $claimingReward = $component->claimingReward();
        $this->assertNotNull($claimingReward);
        $this->assertEquals($claim->id, $claimingReward->id);
        $this->assertNotNull($claimingReward->tier);
        $this->assertNotNull($claimingReward->item);
    }

    #[Test]
    public function claiming_reward_returns_null_when_no_claim_selected(): void
    {
        $user = User::factory()->create();

        $component = new DonationRewardClaims;
        $this->actingAs($user);
        $component->mount();

        $this->assertNull($component->claimingReward());
    }

    #[Test]
    public function mount_auto_selects_first_game_account(): void
    {
        $user = User::factory()->create();
        $account = GameAccount::factory()->for($user)->create();

        $component = new DonationRewardClaims;
        $this->actingAs($user);
        $component->mount();

        $this->assertEquals($account->id, $component->selectedGameAccountId);
    }

    #[Test]
    public function mount_handles_user_with_no_accounts(): void
    {
        $user = User::factory()->create();

        $component = new DonationRewardClaims;
        $this->actingAs($user);
        $component->mount();

        $this->assertNull($component->selectedGameAccountId);
    }

    #[Test]
    public function sanitizes_selected_game_account_id(): void
    {
        $user = User::factory()->create();

        $component = new DonationRewardClaims;
        $this->actingAs($user);
        $component->mount();

        $value = ['malicious' => 'array'];
        $component->updatingSelectedGameAccountId($value);
        $this->assertNull($value);

        $value = '123';
        $component->updatingSelectedGameAccountId($value);
        $this->assertEquals(123, $value);
    }

    #[Test]
    public function updating_game_account_cancels_pending_claim(): void
    {
        $user = User::factory()->create();
        $account1 = GameAccount::factory()->for($user)->create();
        $account2 = GameAccount::factory()->for($user)->create();

        $claim = DonationRewardClaim::factory()
            ->forUser($user)
            ->pending()
            ->create();

        $component = new DonationRewardClaims;
        $this->actingAs($user);
        $component->mount();

        $component->startClaim($claim->id);
        $this->assertTrue($component->showClaimConfirm);

        $component->updatedSelectedGameAccountId($account2->id);
        $this->assertFalse($component->showClaimConfirm);
        $this->assertNull($component->claimingRewardId);
    }
}
