<?php

namespace Tests\Feature\Livewire;

use App\Livewire\DonationUberSender;
use App\Models\GameAccount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DonationUberSenderTest extends TestCase
{
    use RefreshDatabase;

    // ============================================
    // Authorization Tests
    // ============================================

    #[Test]
    public function non_admin_cannot_access_uber_sender(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        // The component aborts with 403 on mount for non-admins
        // Livewire catches this and returns the abort status
        Livewire::actingAs($user)
            ->test(DonationUberSender::class)
            ->assertStatus(403);
    }

    #[Test]
    public function admin_can_access_uber_sender(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(DonationUberSender::class)
            ->assertOk();
    }

    // ============================================
    // Validation Tests
    // ============================================

    #[Test]
    public function username_is_required(): void
    {
        $admin = User::factory()->admin()->create();
        GameAccount::factory()->create(['userid' => 'existinguser']);

        Livewire::actingAs($admin)
            ->test(DonationUberSender::class)
            ->set('username', '')
            ->set('uber_amount', 100)
            ->call('send')
            ->assertHasErrors(['username' => 'required']);
    }

    #[Test]
    public function username_must_be_alphanumeric(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(DonationUberSender::class)
            ->set('username', 'invalid user!')
            ->set('uber_amount', 100)
            ->call('send')
            ->assertHasErrors(['username' => 'alpha_num']);
    }

    #[Test]
    public function username_must_exist_in_database(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(DonationUberSender::class)
            ->set('username', 'nonexistent')
            ->set('uber_amount', 100)
            ->call('send')
            ->assertHasErrors(['username']);
    }

    #[Test]
    public function uber_amount_is_required(): void
    {
        $admin = User::factory()->admin()->create();
        GameAccount::factory()->create(['userid' => 'testuser']);

        Livewire::actingAs($admin)
            ->test(DonationUberSender::class)
            ->set('username', 'testuser')
            ->set('uber_amount', 0)
            ->call('send')
            ->assertHasErrors(['uber_amount']);
    }

    #[Test]
    public function uber_amount_must_be_positive(): void
    {
        $admin = User::factory()->admin()->create();
        GameAccount::factory()->create(['userid' => 'testuser']);

        Livewire::actingAs($admin)
            ->test(DonationUberSender::class)
            ->set('username', 'testuser')
            ->set('uber_amount', -10)
            ->call('send')
            ->assertHasErrors(['uber_amount' => 'min']);
    }

    // ============================================
    // Uber Sending Tests
    // ============================================

    #[Test]
    public function admin_can_send_ubers_to_game_account(): void
    {
        $admin = User::factory()->admin()->create();
        $gameAccount = GameAccount::factory()->create([
            'userid' => 'recipient',
            'uber_balance' => 50,
        ]);

        Livewire::actingAs($admin)
            ->test(DonationUberSender::class)
            ->set('username', 'recipient')
            ->set('uber_amount', 100)
            ->call('send')
            ->assertHasNoErrors();

        $this->assertEquals(150, $gameAccount->fresh()->uber_balance);
    }

    #[Test]
    public function sending_ubers_sets_is_sent_flag(): void
    {
        $admin = User::factory()->admin()->create();
        GameAccount::factory()->create(['userid' => 'recipient']);

        $component = Livewire::actingAs($admin)
            ->test(DonationUberSender::class)
            ->set('username', 'recipient')
            ->set('uber_amount', 100)
            ->call('send');

        $this->assertTrue($component->get('isSent'));
    }

    #[Test]
    public function sending_ubers_shows_success(): void
    {
        $admin = User::factory()->admin()->create();
        $gameAccount = GameAccount::factory()->create([
            'userid' => 'recipient',
            'uber_balance' => 0,
        ]);

        Livewire::actingAs($admin)
            ->test(DonationUberSender::class)
            ->set('username', 'recipient')
            ->set('uber_amount', 100)
            ->call('send')
            ->assertHasNoErrors();

        // Verify the balance was updated
        $this->assertEquals(100, $gameAccount->fresh()->uber_balance);
    }

    #[Test]
    public function send_handles_deleted_game_account(): void
    {
        $admin = User::factory()->admin()->create();

        // Create account for validation to pass
        $gameAccount = GameAccount::factory()->create(['userid' => 'recipient']);

        $component = Livewire::actingAs($admin)
            ->test(DonationUberSender::class)
            ->set('username', 'recipient')
            ->set('uber_amount', 100);

        // Delete account before calling send
        $gameAccount->delete();

        // The call should not throw an exception
        $component->call('send');

        // isSent should be false since account was deleted
        $this->assertFalse($component->get('isSent'));
    }

    // ============================================
    // Input Sanitization Tests
    // ============================================

    #[Test]
    public function uber_sender_has_sanitization_methods(): void
    {
        $component = new DonationUberSender();

        $this->assertTrue(method_exists($component, 'updatingUsername'));
        $this->assertTrue(method_exists($component, 'updatingUberAmount'));
    }

    #[Test]
    public function username_sanitization_converts_array_to_empty_string(): void
    {
        $component = new DonationUberSender();

        $value = ['malicious' => 'payload'];
        $component->updatingUsername($value);
        $this->assertEquals('', $value);
    }

    #[Test]
    public function uber_amount_sanitization_converts_array_to_zero(): void
    {
        $component = new DonationUberSender();

        $value = ['malicious' => 'payload'];
        $component->updatingUberAmount($value);
        $this->assertEquals(0, $value);
    }

    #[Test]
    public function uber_amount_sanitization_accepts_numeric_strings(): void
    {
        $component = new DonationUberSender();

        $value = '500';
        $component->updatingUberAmount($value);
        $this->assertEquals(500, $value);
    }

    // ============================================
    // Authorization Re-check Tests
    // ============================================

    #[Test]
    public function send_method_rechecks_admin_authorization(): void
    {
        $admin = User::factory()->admin()->create();
        GameAccount::factory()->create(['userid' => 'recipient']);

        $component = Livewire::actingAs($admin)
            ->test(DonationUberSender::class)
            ->set('username', 'recipient')
            ->set('uber_amount', 100);

        // Demote user before send
        $admin->is_admin = false;
        $admin->save();

        $component->call('send')
            ->assertForbidden();
    }
}
