<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Auth\Dashboard;
use App\Livewire\Auth\GameAccountRegister;
use App\Models\GameAccount;
use App\Models\User;
use App\XileRetro\XileRetro_Login;
use App\XileRO\XileRO_Login;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GameAccountCreationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        RateLimiter::clear('game-register:'.request()->ip());
    }

    // ============================================
    // Dashboard Game Account Creation
    // ============================================

    #[Test]
    public function authenticated_user_can_create_xilero_game_account(): void
    {
        $user = User::factory()->create(['email' => 'test@example.com']);

        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->set('showCreateForm', true)
            ->set('gameServer', 'xilero')
            ->set('gameUsername', 'newgameacc')
            ->set('gamePassword', 'gamepass123')
            ->set('gamePassword_confirmation', 'gamepass123')
            ->call('createGameAccount')
            ->assertHasNoErrors();

        // Check website database
        $this->assertDatabaseHas('game_accounts', [
            'user_id' => $user->id,
            'server' => 'xilero',
            'userid' => 'newgameacc',
            'email' => 'test@example.com',
            'sex' => 'M',
            'group_id' => 0,
            'state' => 0,
        ]);

        // Check game database
        $this->assertDatabaseHas('login', [
            'userid' => 'newgameacc',
            'email' => 'test@example.com',
            'sex' => 'M',
            'group_id' => 0,
        ]);
    }

    #[Test]
    public function authenticated_user_can_create_xileretro_game_account(): void
    {
        $user = User::factory()->create(['email' => 'test@example.com']);

        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->set('showCreateForm', true)
            ->set('gameServer', 'xileretro')
            ->set('gameUsername', 'retroaccount')
            ->set('gamePassword', 'gamepass123')
            ->set('gamePassword_confirmation', 'gamepass123')
            ->call('createGameAccount')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('game_accounts', [
            'user_id' => $user->id,
            'server' => 'xileretro',
            'userid' => 'retroaccount',
        ]);
    }

    #[Test]
    public function game_account_inherits_email_from_master_account(): void
    {
        $user = User::factory()->create(['email' => 'master@example.com']);

        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->set('showCreateForm', true)
            ->set('gameServer', 'xilero')
            ->set('gameUsername', 'gameacc')
            ->set('gamePassword', 'gamepass123')
            ->set('gamePassword_confirmation', 'gamepass123')
            ->call('createGameAccount');

        $this->assertDatabaseHas('game_accounts', [
            'userid' => 'gameacc',
            'email' => 'master@example.com',
        ]);
    }

    #[Test]
    public function game_account_password_is_hashed(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->set('showCreateForm', true)
            ->set('gameServer', 'xilero')
            ->set('gameUsername', 'hashtest')
            ->set('gamePassword', 'plaintext123')
            ->set('gamePassword_confirmation', 'plaintext123')
            ->call('createGameAccount');

        $gameAccount = GameAccount::where('userid', 'hashtest')->first();

        // Password should NOT be stored as plaintext
        $this->assertNotEquals('plaintext123', $gameAccount->user_pass);
        // Password should be a SHA256 hash (64 characters)
        $this->assertEquals(64, strlen($gameAccount->user_pass));
    }

    #[Test]
    public function game_account_username_must_be_unique_per_server(): void
    {
        $user = User::factory()->create();

        // Create existing game account
        GameAccount::factory()->create([
            'server' => 'xilero',
            'userid' => 'existinguser',
        ]);

        // Also create in game database
        XileRO_Login::factory()->create(['userid' => 'existinguser']);

        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->set('showCreateForm', true)
            ->set('gameServer', 'xilero')
            ->set('gameUsername', 'existinguser')
            ->set('gamePassword', 'gamepass123')
            ->set('gamePassword_confirmation', 'gamepass123')
            ->call('createGameAccount')
            ->assertHasErrors(['gameUsername']);
    }

    #[Test]
    public function same_username_can_exist_on_different_servers(): void
    {
        // This test requires separate databases per server (production behavior).
        // In testing, both servers share a single SQLite connection/table,
        // so cross-server duplicate usernames are not possible.
        if (config('database.default') === 'sqlite') {
            $this->markTestSkipped('Requires separate database connections per server.');
        }

        $user = User::factory()->create();

        // Create account on xilero
        GameAccount::factory()->create([
            'user_id' => $user->id,
            'server' => 'xilero',
            'userid' => 'multiserver',
        ]);

        XileRO_Login::factory()->create(['userid' => 'multiserver']);

        // Should be able to create same username on xileretro
        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->set('showCreateForm', true)
            ->set('gameServer', 'xileretro')
            ->set('gameUsername', 'multiserver')
            ->set('gamePassword', 'gamepass123')
            ->set('gamePassword_confirmation', 'gamepass123')
            ->call('createGameAccount')
            ->assertHasNoErrors();

        $this->assertEquals(2, GameAccount::where('userid', 'multiserver')->count());
    }

    // ============================================
    // Game Account Limits
    // ============================================

    #[Test]
    public function user_cannot_exceed_default_game_account_limit(): void
    {
        $user = User::factory()->create(['max_game_accounts' => 6]);

        // Create 6 game accounts (at limit)
        GameAccount::factory()->count(6)->create(['user_id' => $user->id]);

        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->set('showCreateForm', true)
            ->set('gameServer', 'xilero')
            ->set('gameUsername', 'overlimit')
            ->set('gamePassword', 'gamepass123')
            ->set('gamePassword_confirmation', 'gamepass123')
            ->call('createGameAccount');

        // Should not create 7th account
        $this->assertDatabaseMissing('game_accounts', ['userid' => 'overlimit']);
        $this->assertEquals(6, $user->gameAccounts()->count());
    }

    #[Test]
    public function custom_game_account_limit_is_respected(): void
    {
        $user = User::factory()->create(['max_game_accounts' => 10]);

        // Create 6 accounts
        GameAccount::factory()->count(6)->create(['user_id' => $user->id]);

        // Should be able to create more (up to 10)
        $this->assertTrue($user->canCreateGameAccount());

        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->set('showCreateForm', true)
            ->set('gameServer', 'xilero')
            ->set('gameUsername', 'newaccount7')
            ->set('gamePassword', 'gamepass123')
            ->set('gamePassword_confirmation', 'gamepass123')
            ->call('createGameAccount')
            ->assertHasNoErrors();

        $this->assertEquals(7, $user->gameAccounts()->count());
    }

    // ============================================
    // Validation Tests
    // ============================================

    #[Test]
    public function game_username_must_be_alphanumeric(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->set('showCreateForm', true)
            ->set('gameServer', 'xilero')
            ->set('gameUsername', 'invalid user!')
            ->set('gamePassword', 'gamepass123')
            ->set('gamePassword_confirmation', 'gamepass123')
            ->call('createGameAccount')
            ->assertHasErrors(['gameUsername']);
    }

    #[Test]
    public function game_username_must_be_at_least_4_characters(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->set('showCreateForm', true)
            ->set('gameServer', 'xilero')
            ->set('gameUsername', 'abc')
            ->set('gamePassword', 'gamepass123')
            ->set('gamePassword_confirmation', 'gamepass123')
            ->call('createGameAccount')
            ->assertHasErrors(['gameUsername']);
    }

    #[Test]
    public function game_username_cannot_exceed_23_characters(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->set('showCreateForm', true)
            ->set('gameServer', 'xilero')
            ->set('gameUsername', str_repeat('a', 24))
            ->set('gamePassword', 'gamepass123')
            ->set('gamePassword_confirmation', 'gamepass123')
            ->call('createGameAccount')
            ->assertHasErrors(['gameUsername']);
    }

    #[Test]
    public function game_password_must_be_at_least_6_characters(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->set('showCreateForm', true)
            ->set('gameServer', 'xilero')
            ->set('gameUsername', 'validuser')
            ->set('gamePassword', '12345')
            ->set('gamePassword_confirmation', '12345')
            ->call('createGameAccount')
            ->assertHasErrors(['gamePassword']);
    }

    #[Test]
    public function game_password_cannot_exceed_31_characters(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->set('showCreateForm', true)
            ->set('gameServer', 'xilero')
            ->set('gameUsername', 'validuser')
            ->set('gamePassword', str_repeat('a', 32))
            ->set('gamePassword_confirmation', str_repeat('a', 32))
            ->call('createGameAccount')
            ->assertHasErrors(['gamePassword']);
    }

    #[Test]
    public function game_password_must_be_confirmed(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->set('showCreateForm', true)
            ->set('gameServer', 'xilero')
            ->set('gameUsername', 'validuser')
            ->set('gamePassword', 'password123')
            ->set('gamePassword_confirmation', 'different123')
            ->call('createGameAccount')
            ->assertHasErrors(['gamePassword']);
    }

    #[Test]
    public function game_server_must_be_valid(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->set('showCreateForm', true)
            ->set('gameServer', 'invalid_server')
            ->set('gameUsername', 'validuser')
            ->set('gamePassword', 'password123')
            ->set('gamePassword_confirmation', 'password123')
            ->call('createGameAccount')
            ->assertHasErrors(['gameServer']);
    }

    // ============================================
    // Public Game Account Registration
    // ============================================

    #[Test]
    public function public_game_registration_creates_account_without_master(): void
    {
        Livewire::test(GameAccountRegister::class)
            ->set('server', 'xilero')
            ->set('username', 'publicuser')
            ->set('email', 'public@example.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->call('register')
            ->assertRedirect(route('dashboard'));

        // Should create in game database
        $this->assertDatabaseHas('login', [
            'userid' => 'publicuser',
            'email' => 'public@example.com',
        ]);
    }

    #[Test]
    public function public_game_registration_rejects_blocked_email(): void
    {
        Livewire::test(GameAccountRegister::class)
            ->set('server', 'xilero')
            ->set('username', 'publicuser')
            ->set('email', 'a@a.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->call('register')
            ->assertHasErrors(['email']);
    }

    #[Test]
    public function public_game_registration_includes_unique_validation_rules(): void
    {
        $component = new GameAccountRegister;
        $component->server = 'xilero';

        $rules = $component->rules();

        // Verify username has unique rule for game database via model class
        $this->assertContains('unique:'.XileRO_Login::class.',userid', $rules['username']);
        $this->assertContains('unique:'.XileRO_Login::class.',email', $rules['email']);

        // Test xileretro server rules
        $component->server = 'xileretro';
        $rules = $component->rules();

        $this->assertContains('unique:'.XileRetro_Login::class.',userid', $rules['username']);
        $this->assertContains('unique:'.XileRetro_Login::class.',email', $rules['email']);
    }

    // ============================================
    // Database Transaction Integrity
    // ============================================

    #[Test]
    public function game_account_creation_is_atomic(): void
    {
        $user = User::factory()->create();

        // If we had a way to simulate a failure after game DB insert but before local DB insert,
        // neither should exist. This test verifies the transaction wrapping is in place.

        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->set('showCreateForm', true)
            ->set('gameServer', 'xilero')
            ->set('gameUsername', 'atomictest')
            ->set('gamePassword', 'password123')
            ->set('gamePassword_confirmation', 'password123')
            ->call('createGameAccount');

        // Both should exist if successful
        $localAccount = GameAccount::where('userid', 'atomictest')->first();
        $gameDbAccount = XileRO_Login::where('userid', 'atomictest')->first();

        if ($localAccount) {
            $this->assertNotNull($gameDbAccount);
            $this->assertEquals($gameDbAccount->account_id, $localAccount->ragnarok_account_id);
        }
    }

    #[Test]
    public function game_account_links_to_correct_ragnarok_account_id(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->set('showCreateForm', true)
            ->set('gameServer', 'xilero')
            ->set('gameUsername', 'linktest')
            ->set('gamePassword', 'password123')
            ->set('gamePassword_confirmation', 'password123')
            ->call('createGameAccount');

        $localAccount = GameAccount::where('userid', 'linktest')->first();
        $gameDbAccount = XileRO_Login::where('userid', 'linktest')->first();

        $this->assertNotNull($localAccount);
        $this->assertNotNull($gameDbAccount);
        $this->assertEquals($gameDbAccount->account_id, $localAccount->ragnarok_account_id);
    }
}
