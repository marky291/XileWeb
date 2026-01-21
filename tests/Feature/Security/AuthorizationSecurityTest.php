<?php

namespace Tests\Feature\Security;

use App\Livewire\Auth\Dashboard;
use App\Models\GameAccount;
use App\Models\SyncedCharacter;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AuthorizationSecurityTest extends TestCase
{
    use RefreshDatabase;

    // ============================================
    // Game Account Ownership Tests
    // ============================================

    #[Test]
    public function user_cannot_access_other_users_game_account(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $otherGameAccount = GameAccount::factory()->create([
            'user_id' => $otherUser->id,
            'userid' => 'othergameacc',
        ]);

        // When user tries to select another user's game account,
        // the ID is set but the actual access is prevented via selectedGameAccount() method
        // which returns null for accounts not belonging to the user
        $component = Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->call('selectGameAccount', $otherGameAccount->id);

        // The selectedGameAccount method should return null because user doesn't own it
        $this->assertNull($component->instance()->selectedGameAccount());
    }

    #[Test]
    public function user_can_only_see_their_own_game_accounts(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $userAccount = GameAccount::factory()->create([
            'user_id' => $user->id,
            'userid' => 'mygameacc',
        ]);

        GameAccount::factory()->create([
            'user_id' => $otherUser->id,
            'userid' => 'othergameacc',
        ]);

        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->assertSee('mygameacc')
            ->assertDontSee('othergameacc');
    }

    #[Test]
    public function user_cannot_reset_password_for_other_users_game_account(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $originalHash = hash('sha256', 'originalpass'.config('database.secret'));
        $otherGameAccount = GameAccount::factory()->create([
            'user_id' => $otherUser->id,
            'userid' => 'othergameacc',
            'user_pass' => $originalHash,
        ]);

        // First select an account (should not be able to select other user's account)
        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->call('showPasswordResetForm', $otherGameAccount->id);

        // Password should remain unchanged since user doesn't own the account
        $this->assertEquals($originalHash, $otherGameAccount->fresh()->user_pass);
    }

    #[Test]
    public function user_cannot_reset_security_code_for_other_users_account(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $otherGameAccount = GameAccount::factory()->withSecurityCode()->create([
            'user_id' => $otherUser->id,
        ]);

        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->call('resetSecurity', $otherGameAccount->id);

        // Security code should still be set
        $this->assertTrue($otherGameAccount->fresh()->has_security_code);
    }

    // ============================================
    // Character Ownership Tests
    // ============================================

    #[Test]
    public function user_cannot_reset_position_for_other_users_character(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $otherGameAccount = GameAccount::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        $otherCharacter = SyncedCharacter::factory()->create([
            'game_account_id' => $otherGameAccount->id,
            'last_map' => 'prt_fild08',
            'online' => 0,
        ]);

        // Try to reset position - should fail silently due to ownership check
        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->call('resetPosition', $otherCharacter->char_id);

        // Character position should remain unchanged
        $this->assertEquals('prt_fild08', $otherCharacter->fresh()->last_map);
    }

    // ============================================
    // Admin Access Control Tests
    // ============================================

    #[Test]
    public function non_admin_cannot_access_admin_panel(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user)
            ->get('/admin')
            ->assertForbidden();
    }

    #[Test]
    public function admin_can_access_admin_panel(): void
    {
        $user = User::factory()->admin()->create();

        $this->actingAs($user)
            ->get('/admin')
            ->assertOk();
    }

    #[Test]
    public function guest_cannot_access_admin_panel(): void
    {
        $this->get('/admin')
            ->assertRedirect('/login');
    }

    // ============================================
    // Protected Routes Tests
    // ============================================

    #[Test]
    public function guest_cannot_access_dashboard(): void
    {
        $this->get('/dashboard')
            ->assertRedirect('/login');
    }

    #[Test]
    public function guest_cannot_access_protected_dashboard(): void
    {
        // Dashboard requires authentication
        $this->get('/dashboard')
            ->assertRedirect(route('login'));
    }

    #[Test]
    public function unverified_user_is_redirected_to_verification(): void
    {
        $user = User::factory()->unverified()->create();

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertRedirect('/verify-email');
    }

    #[Test]
    public function verified_user_can_access_dashboard(): void
    {
        $user = User::factory()->create(); // Factory creates verified users by default

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertOk();
    }

    // ============================================
    // Game Account Limit Tests
    // ============================================

    #[Test]
    public function user_cannot_create_more_than_max_game_accounts(): void
    {
        $user = User::factory()->create(['max_game_accounts' => 2]);

        // Create 2 accounts (at limit)
        GameAccount::factory()->count(2)->create(['user_id' => $user->id]);

        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->set('showCreateForm', true)
            ->set('gameServer', 'xilero')
            ->set('gameUsername', 'newaccount')
            ->set('gamePassword', 'password123')
            ->set('gamePassword_confirmation', 'password123')
            ->call('createGameAccount');

        // Should still have only 2 accounts
        $this->assertEquals(2, $user->gameAccounts()->count());
    }

    #[Test]
    public function max_game_accounts_respects_user_specific_limit(): void
    {
        $normalUser = User::factory()->create(['max_game_accounts' => 6]);
        $premiumUser = User::factory()->create(['max_game_accounts' => 10]);

        // Normal user at limit
        GameAccount::factory()->count(6)->create(['user_id' => $normalUser->id]);

        // Premium user can still create
        GameAccount::factory()->count(6)->create(['user_id' => $premiumUser->id]);

        $this->assertFalse($normalUser->canCreateGameAccount());
        $this->assertTrue($premiumUser->canCreateGameAccount());
    }

    // ============================================
    // ID Enumeration Protection Tests
    // ============================================

    #[Test]
    public function selecting_nonexistent_game_account_returns_null_on_access(): void
    {
        $user = User::factory()->create();

        $component = Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->call('selectGameAccount', 999999);

        // The ID is set but the actual access via selectedGameAccount() returns null
        $this->assertNull($component->instance()->selectedGameAccount());
    }

    #[Test]
    public function resetting_password_for_nonexistent_account_fails_silently(): void
    {
        $user = User::factory()->create();

        // This should not throw an exception or reveal account existence
        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->call('showPasswordResetForm', 999999);

        // Should not crash and should not reveal account existence
        $this->assertTrue(true);
    }

    // ============================================
    // Mass Assignment Protection Tests
    // ============================================

    #[Test]
    public function user_is_admin_cannot_be_mass_assigned(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        // Attempt to mass assign is_admin
        $user->fill(['is_admin' => true]);
        $user->save();

        // Should still not be admin
        $this->assertFalse($user->fresh()->is_admin);
    }

    #[Test]
    public function user_uber_balance_cannot_be_mass_assigned(): void
    {
        $user = User::factory()->create(['uber_balance' => 0]);

        // Attempt to mass assign uber_balance
        $user->fill(['uber_balance' => 1000000]);
        $user->save();

        // Should still be 0
        $this->assertEquals(0, $user->fresh()->uber_balance);
    }

    #[Test]
    public function user_max_game_accounts_cannot_be_mass_assigned(): void
    {
        $user = User::factory()->create(['max_game_accounts' => 6]);

        // Attempt to mass assign max_game_accounts
        $user->fill(['max_game_accounts' => 1000]);
        $user->save();

        // Should still be 6
        $this->assertEquals(6, $user->fresh()->max_game_accounts);
    }

    #[Test]
    public function admin_can_explicitly_set_is_admin(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        // Explicit assignment (not mass assignment) should work
        $user->is_admin = true;
        $user->save();

        $this->assertTrue($user->fresh()->is_admin);
    }
}
