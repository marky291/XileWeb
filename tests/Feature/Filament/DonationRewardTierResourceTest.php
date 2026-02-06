<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\DonationRewardTierResource\Pages\CreateDonationRewardTier;
use App\Filament\Resources\DonationRewardTierResource\Pages\EditDonationRewardTier;
use App\Filament\Resources\DonationRewardTierResource\Pages\ListDonationRewardTiers;
use App\Models\DonationRewardTier;
use App\Models\Item;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DonationRewardTierResourceTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function guest_cannot_access_reward_tiers(): void
    {
        $this->get('/admin/donation-reward-tiers')
            ->assertRedirect('/login');
    }

    #[Test]
    public function non_admin_cannot_access_reward_tiers(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user)
            ->get('/admin/donation-reward-tiers')
            ->assertForbidden();
    }

    #[Test]
    public function admin_can_access_reward_tiers_list(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get('/admin/donation-reward-tiers')
            ->assertOk();
    }

    #[Test]
    public function admin_can_view_reward_tiers_list(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $admin = User::factory()->admin()->create();
        $tiers = DonationRewardTier::factory()->count(3)->create();

        Livewire::actingAs($admin)
            ->test(ListDonationRewardTiers::class)
            ->assertCanSeeTableRecords($tiers);
    }

    #[Test]
    public function admin_can_create_reward_tier(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(CreateDonationRewardTier::class)
            ->fillForm([
                'name' => 'Bronze Tier',
                'minimum_amount' => 10.00,
                'description' => 'Starter tier rewards',
                'trigger_type' => DonationRewardTier::TRIGGER_PER_DONATION,
                'is_cumulative' => false,
                'is_xilero' => true,
                'is_xileretro' => true,
                'enabled' => true,
                'display_order' => 1,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('donation_reward_tiers', [
            'name' => 'Bronze Tier',
            'minimum_amount' => 10.00,
            'trigger_type' => 'per_donation',
            'is_cumulative' => false,
            'enabled' => true,
        ]);
    }

    #[Test]
    public function admin_can_create_lifetime_tier(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(CreateDonationRewardTier::class)
            ->fillForm([
                'name' => 'VIP Milestone',
                'minimum_amount' => 100.00,
                'trigger_type' => DonationRewardTier::TRIGGER_LIFETIME,
                'is_cumulative' => false,
                'is_xilero' => true,
                'is_xileretro' => true,
                'enabled' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('donation_reward_tiers', [
            'name' => 'VIP Milestone',
            'trigger_type' => 'lifetime',
        ]);
    }

    #[Test]
    public function admin_can_edit_reward_tier(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $admin = User::factory()->admin()->create();
        $tier = DonationRewardTier::factory()->create([
            'name' => 'Old Name',
            'minimum_amount' => 10.00,
        ]);

        Livewire::actingAs($admin)
            ->test(EditDonationRewardTier::class, ['record' => $tier->id])
            ->fillForm([
                'name' => 'Updated Name',
                'minimum_amount' => 25.00,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('donation_reward_tiers', [
            'id' => $tier->id,
            'name' => 'Updated Name',
            'minimum_amount' => 25.00,
        ]);
    }

    #[Test]
    public function admin_can_toggle_tier_enabled_status(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $admin = User::factory()->admin()->create();
        $tier = DonationRewardTier::factory()->create(['enabled' => true]);

        Livewire::actingAs($admin)
            ->test(ListDonationRewardTiers::class)
            ->callTableAction('toggle', $tier);

        $this->assertDatabaseHas('donation_reward_tiers', [
            'id' => $tier->id,
            'enabled' => false,
        ]);
    }

    #[Test]
    public function admin_can_add_items_to_tier(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $admin = User::factory()->admin()->create();
        $item = Item::factory()->create(['name' => 'Test Sword']);

        Livewire::actingAs($admin)
            ->test(CreateDonationRewardTier::class)
            ->fillForm([
                'name' => 'Item Tier',
                'minimum_amount' => 50.00,
                'trigger_type' => DonationRewardTier::TRIGGER_PER_DONATION,
                'is_xilero' => true,
                'is_xileretro' => true,
                'enabled' => true,
                'tierItems' => [
                    [
                        'item_id' => $item->id,
                        'quantity' => 5,
                        'refine_level' => 7,
                    ],
                ],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $tier = DonationRewardTier::where('name', 'Item Tier')->first();
        $this->assertNotNull($tier);

        $this->assertDatabaseHas('donation_reward_tier_items', [
            'donation_reward_tier_id' => $tier->id,
            'item_id' => $item->id,
            'quantity' => 5,
            'refine_level' => 7,
        ]);
    }

    #[Test]
    public function validation_requires_name_and_minimum_amount(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(CreateDonationRewardTier::class)
            ->fillForm([
                'name' => '',
                'minimum_amount' => null,
            ])
            ->call('create')
            ->assertHasFormErrors(['name', 'minimum_amount']);
    }

    #[Test]
    public function can_filter_tiers_by_trigger_type(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $admin = User::factory()->admin()->create();

        $perDonationTier = DonationRewardTier::factory()->perDonation()->create();
        $lifetimeTier = DonationRewardTier::factory()->lifetime()->create();

        Livewire::actingAs($admin)
            ->test(ListDonationRewardTiers::class)
            ->filterTable('trigger_type', DonationRewardTier::TRIGGER_PER_DONATION)
            ->assertCanSeeTableRecords([$perDonationTier])
            ->assertCanNotSeeTableRecords([$lifetimeTier]);
    }

    #[Test]
    public function can_filter_tiers_by_enabled_status(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $admin = User::factory()->admin()->create();

        $enabledTier = DonationRewardTier::factory()->create(['enabled' => true]);
        $disabledTier = DonationRewardTier::factory()->disabled()->create();

        Livewire::actingAs($admin)
            ->test(ListDonationRewardTiers::class)
            ->filterTable('enabled', true)
            ->assertCanSeeTableRecords([$enabledTier])
            ->assertCanNotSeeTableRecords([$disabledTier]);
    }

    #[Test]
    public function admin_can_create_tier_with_daily_reset(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(CreateDonationRewardTier::class)
            ->fillForm([
                'name' => 'Daily Bonus',
                'minimum_amount' => 5.00,
                'trigger_type' => DonationRewardTier::TRIGGER_PER_DONATION,
                'claim_reset_period' => DonationRewardTier::RESET_DAILY,
                'is_xilero' => true,
                'is_xileretro' => true,
                'enabled' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('donation_reward_tiers', [
            'name' => 'Daily Bonus',
            'claim_reset_period' => 'daily',
        ]);
    }

    #[Test]
    public function admin_can_create_tier_with_weekly_reset(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(CreateDonationRewardTier::class)
            ->fillForm([
                'name' => 'Weekly Bonus',
                'minimum_amount' => 10.00,
                'trigger_type' => DonationRewardTier::TRIGGER_PER_DONATION,
                'claim_reset_period' => DonationRewardTier::RESET_WEEKLY,
                'is_xilero' => true,
                'is_xileretro' => true,
                'enabled' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('donation_reward_tiers', [
            'name' => 'Weekly Bonus',
            'claim_reset_period' => 'weekly',
        ]);
    }

    #[Test]
    public function admin_can_create_tier_with_monthly_reset(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(CreateDonationRewardTier::class)
            ->fillForm([
                'name' => 'Monthly Bonus',
                'minimum_amount' => 25.00,
                'trigger_type' => DonationRewardTier::TRIGGER_PER_DONATION,
                'claim_reset_period' => DonationRewardTier::RESET_MONTHLY,
                'is_xilero' => true,
                'is_xileretro' => true,
                'enabled' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('donation_reward_tiers', [
            'name' => 'Monthly Bonus',
            'claim_reset_period' => 'monthly',
        ]);
    }

    #[Test]
    public function admin_can_create_tier_with_yearly_reset(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(CreateDonationRewardTier::class)
            ->fillForm([
                'name' => 'Yearly Bonus',
                'minimum_amount' => 100.00,
                'trigger_type' => DonationRewardTier::TRIGGER_PER_DONATION,
                'claim_reset_period' => DonationRewardTier::RESET_YEARLY,
                'is_xilero' => true,
                'is_xileretro' => true,
                'enabled' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('donation_reward_tiers', [
            'name' => 'Yearly Bonus',
            'claim_reset_period' => 'yearly',
        ]);
    }

    #[Test]
    public function admin_can_create_one_time_tier(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(CreateDonationRewardTier::class)
            ->fillForm([
                'name' => 'One-time Bonus',
                'minimum_amount' => 50.00,
                'trigger_type' => DonationRewardTier::TRIGGER_PER_DONATION,
                'claim_reset_period' => '',
                'is_xilero' => true,
                'is_xileretro' => true,
                'enabled' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('donation_reward_tiers', [
            'name' => 'One-time Bonus',
            'claim_reset_period' => null,
        ]);
    }

    #[Test]
    public function admin_can_update_reset_period(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $admin = User::factory()->admin()->create();
        $tier = DonationRewardTier::factory()->create([
            'claim_reset_period' => null,
        ]);

        Livewire::actingAs($admin)
            ->test(EditDonationRewardTier::class, ['record' => $tier->id])
            ->fillForm([
                'claim_reset_period' => DonationRewardTier::RESET_MONTHLY,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('donation_reward_tiers', [
            'id' => $tier->id,
            'claim_reset_period' => 'monthly',
        ]);
    }

    #[Test]
    public function admin_can_create_xilero_only_tier(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(CreateDonationRewardTier::class)
            ->fillForm([
                'name' => 'XileRO Exclusive',
                'minimum_amount' => 20.00,
                'trigger_type' => DonationRewardTier::TRIGGER_PER_DONATION,
                'server' => 'xilero',
                'enabled' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('donation_reward_tiers', [
            'name' => 'XileRO Exclusive',
            'is_xilero' => true,
            'is_xileretro' => false,
        ]);
    }

    #[Test]
    public function admin_can_create_xileretro_only_tier(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(CreateDonationRewardTier::class)
            ->fillForm([
                'name' => 'XileRetro Exclusive',
                'minimum_amount' => 20.00,
                'trigger_type' => DonationRewardTier::TRIGGER_PER_DONATION,
                'server' => 'xileretro',
                'enabled' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('donation_reward_tiers', [
            'name' => 'XileRetro Exclusive',
            'is_xilero' => false,
            'is_xileretro' => true,
        ]);
    }

    #[Test]
    public function admin_can_bulk_enable_tiers(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $admin = User::factory()->admin()->create();
        $tiers = DonationRewardTier::factory()->disabled()->count(3)->create();

        Livewire::actingAs($admin)
            ->test(ListDonationRewardTiers::class)
            ->callTableBulkAction('enable', $tiers);

        foreach ($tiers as $tier) {
            $this->assertDatabaseHas('donation_reward_tiers', [
                'id' => $tier->id,
                'enabled' => true,
            ]);
        }
    }

    #[Test]
    public function admin_can_bulk_disable_tiers(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $admin = User::factory()->admin()->create();
        $tiers = DonationRewardTier::factory()->count(3)->create(['enabled' => true]);

        Livewire::actingAs($admin)
            ->test(ListDonationRewardTiers::class)
            ->callTableBulkAction('disable', $tiers);

        foreach ($tiers as $tier) {
            $this->assertDatabaseHas('donation_reward_tiers', [
                'id' => $tier->id,
                'enabled' => false,
            ]);
        }
    }

    #[Test]
    public function table_shows_reset_period_badge(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $admin = User::factory()->admin()->create();
        $monthlyTier = DonationRewardTier::factory()->resetsMonthly()->create();

        Livewire::actingAs($admin)
            ->test(ListDonationRewardTiers::class)
            ->assertCanSeeTableRecords([$monthlyTier]);
    }

    #[Test]
    public function table_can_search_tiers_by_name(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $admin = User::factory()->admin()->create();
        $bronzeTier = DonationRewardTier::factory()->create(['name' => 'Bronze Starter']);
        $goldTier = DonationRewardTier::factory()->create(['name' => 'Gold Elite']);

        Livewire::actingAs($admin)
            ->test(ListDonationRewardTiers::class)
            ->searchTable('Bronze')
            ->assertCanSeeTableRecords([$bronzeTier])
            ->assertCanNotSeeTableRecords([$goldTier]);
    }
}
