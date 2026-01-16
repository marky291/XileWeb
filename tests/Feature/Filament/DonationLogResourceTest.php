<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\DonationLogResource\Pages\ListDonationLogs;
use App\Models\DonationLog;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DonationLogResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    #[Test]
    public function can_revert_donation_fully(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create(['uber_balance' => 100]);

        $donation = DonationLog::factory()
            ->forUser($user)
            ->byAdmin($admin)
            ->create([
                'total_ubers' => 50,
                'base_ubers' => 45,
                'bonus_ubers' => 5,
            ]);

        Livewire::actingAs($admin)
            ->test(ListDonationLogs::class)
            ->callTableAction('revert', $donation)
            ->assertNotified('Donation Reverted');

        // User balance should be reduced
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'uber_balance' => 50,
        ]);

        // Donation record should still exist but be marked as reverted
        $this->assertDatabaseHas('donation_logs', [
            'id' => $donation->id,
            'ubers_recovered' => 50,
            'reverted_by' => $admin->id,
        ]);

        $donation->refresh();
        $this->assertNotNull($donation->reverted_at);
    }

    #[Test]
    public function can_revert_donation_resulting_in_negative_balance(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create(['uber_balance' => 20]);

        $donation = DonationLog::factory()
            ->forUser($user)
            ->byAdmin($admin)
            ->create([
                'total_ubers' => 50,
            ]);

        Livewire::actingAs($admin)
            ->test(ListDonationLogs::class)
            ->callTableAction('revert', $donation)
            ->assertNotified('Donation Reverted - Negative Balance');

        // User balance should be negative
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'uber_balance' => -30,
        ]);

        // Donation should be marked as reverted with full amount
        $this->assertDatabaseHas('donation_logs', [
            'id' => $donation->id,
            'ubers_recovered' => 50,
        ]);
    }

    #[Test]
    public function can_revert_donation_when_user_has_zero_balance(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create(['uber_balance' => 0]);

        $donation = DonationLog::factory()
            ->forUser($user)
            ->byAdmin($admin)
            ->create([
                'total_ubers' => 50,
            ]);

        Livewire::actingAs($admin)
            ->test(ListDonationLogs::class)
            ->callTableAction('revert', $donation)
            ->assertNotified('Donation Reverted - Negative Balance');

        // User balance should be negative
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'uber_balance' => -50,
        ]);

        // Donation should be marked as reverted with full amount
        $this->assertDatabaseHas('donation_logs', [
            'id' => $donation->id,
            'ubers_recovered' => 50,
        ]);
    }

    #[Test]
    public function cannot_revert_already_reverted_donation(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create(['uber_balance' => 100]);

        $donation = DonationLog::factory()
            ->forUser($user)
            ->byAdmin($admin)
            ->create([
                'total_ubers' => 50,
                'reverted_at' => now(),
                'reverted_by' => $admin->id,
                'ubers_recovered' => 50,
            ]);

        Livewire::actingAs($admin)
            ->test(ListDonationLogs::class)
            ->assertTableActionHidden('revert', $donation);
    }

    #[Test]
    public function can_view_donation_history_list(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();

        $donations = DonationLog::factory()
            ->count(3)
            ->forUser($user)
            ->byAdmin($admin)
            ->create();

        Livewire::actingAs($admin)
            ->test(ListDonationLogs::class)
            ->assertCanSeeTableRecords($donations);
    }
}
