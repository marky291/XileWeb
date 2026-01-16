<?php

namespace Tests\Feature\Filament;

use App\Filament\Pages\ApplyDonation;
use App\Jobs\SendDonationAppliedEmail;
use App\Models\DonationLog;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ApplyDonationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    #[Test]
    public function guest_cannot_access_apply_donation_page(): void
    {
        $this->get('/admin/apply-donation')
            ->assertRedirect('/login');
    }

    #[Test]
    public function non_admin_user_cannot_access_apply_donation_page(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user)
            ->get('/admin/apply-donation')
            ->assertForbidden();
    }

    #[Test]
    public function admin_user_can_access_apply_donation_page(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get('/admin/apply-donation')
            ->assertOk();
    }

    #[Test]
    public function can_apply_donation_to_user(): void
    {
        Queue::fake();

        $admin = User::factory()->admin()->create();
        $user = User::factory()->create([
            'email' => 'donor@example.com',
            'uber_balance' => 100,
        ]);

        Livewire::actingAs($admin)
            ->test(ApplyDonation::class)
            ->fillForm([
                'user_id' => $user->id,
                'donation_tier' => '20',
                'amount' => '20',
                'payment_method' => 'paypal',
                'base_ubers' => 110,
                'extra_ubers' => 0,
                'total_ubers' => 110,
            ])
            ->call('apply')
            ->assertNotified('Donation Applied Successfully!');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'uber_balance' => 210,
        ]);

        $this->assertDatabaseHas('donation_logs', [
            'user_id' => $user->id,
            'admin_id' => $admin->id,
            'amount' => '20.00',
            'payment_method' => 'paypal',
            'base_ubers' => 110,
            'total_ubers' => 110,
        ]);

        Queue::assertPushed(SendDonationAppliedEmail::class);
    }

    #[Test]
    public function crypto_payment_adds_bonus(): void
    {
        Queue::fake();

        $admin = User::factory()->admin()->create();
        $user = User::factory()->create([
            'email' => 'cryptodonor@example.com',
            'uber_balance' => 0,
        ]);

        Livewire::actingAs($admin)
            ->test(ApplyDonation::class)
            ->fillForm([
                'user_id' => $user->id,
                'donation_tier' => '20',
                'amount' => '20',
                'payment_method' => 'crypto',
                'base_ubers' => 110,
                'extra_ubers' => 0,
                'total_ubers' => 121,
            ])
            ->call('apply')
            ->assertNotified('Donation Applied Successfully!');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'uber_balance' => 121,
        ]);

        $this->assertDatabaseHas('donation_logs', [
            'user_id' => $user->id,
            'base_ubers' => 110,
            'bonus_ubers' => 11,
            'total_ubers' => 121,
        ]);
    }

    #[Test]
    public function can_add_extra_bonus_ubers(): void
    {
        Queue::fake();

        $admin = User::factory()->admin()->create();
        $user = User::factory()->create([
            'uber_balance' => 0,
        ]);

        Livewire::actingAs($admin)
            ->test(ApplyDonation::class)
            ->fillForm([
                'user_id' => $user->id,
                'donation_tier' => '10',
                'amount' => '10',
                'payment_method' => 'paypal',
                'base_ubers' => 50,
                'extra_ubers' => 25,
                'total_ubers' => 75,
            ])
            ->call('apply')
            ->assertNotified('Donation Applied Successfully!');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'uber_balance' => 75,
        ]);

        $this->assertDatabaseHas('donation_logs', [
            'user_id' => $user->id,
            'base_ubers' => 50,
            'bonus_ubers' => 25,
            'total_ubers' => 75,
        ]);
    }

    #[Test]
    public function shows_donation_history_when_user_selected(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();

        DonationLog::factory()
            ->count(3)
            ->forUser($user)
            ->byAdmin($admin)
            ->create();

        Livewire::actingAs($admin)
            ->test(ApplyDonation::class)
            ->fillForm(['user_id' => $user->id])
            ->assertSee('Recent Donation History');
    }

    #[Test]
    public function redirects_to_donation_history_after_successful_donation(): void
    {
        Queue::fake();

        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();

        Livewire::actingAs($admin)
            ->test(ApplyDonation::class)
            ->fillForm([
                'user_id' => $user->id,
                'donation_tier' => '10',
                'amount' => '10',
                'payment_method' => 'paypal',
                'base_ubers' => 50,
                'extra_ubers' => 0,
                'total_ubers' => 50,
            ])
            ->call('apply')
            ->assertNotified('Donation Applied Successfully!')
            ->assertRedirect('/admin/donation-logs');
    }

    #[Test]
    public function email_not_sent_if_donation_reverted_before_delay(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create(['uber_balance' => 100]);

        $donationLog = DonationLog::factory()
            ->forUser($user)
            ->byAdmin($admin)
            ->create([
                'total_ubers' => 50,
                'reverted_at' => now(),
                'reverted_by' => $admin->id,
                'ubers_recovered' => 50,
            ]);

        $job = new SendDonationAppliedEmail(
            donationLog: $donationLog,
            amount: 20.00,
            totalUbers: 50,
            newBalance: 100,
            paymentMethod: 'paypal',
        );

        // Simulate running the job - it should not send notification
        \Illuminate\Support\Facades\Notification::fake();
        $job->handle();

        \Illuminate\Support\Facades\Notification::assertNothingSent();
    }
}
