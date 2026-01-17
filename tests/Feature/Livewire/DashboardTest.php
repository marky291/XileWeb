<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Auth\Dashboard;
use App\Models\GameAccount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_requires_authentication(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_authenticated_user_can_access_dashboard(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertStatus(200);
    }

    public function test_dashboard_shows_user_game_accounts(): void
    {
        $user = User::factory()->create();
        $gameAccount = GameAccount::factory()->create([
            'user_id' => $user->id,
            'userid' => 'testgameacc',
        ]);

        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->assertSee('testgameacc');
    }

    public function test_user_can_create_game_account(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->set('showCreateForm', true)
            ->set('gameServer', 'xilero')
            ->set('gameUsername', 'newgameacc')
            ->set('gamePassword', 'gamepass123')
            ->set('gamePassword_confirmation', 'gamepass123')
            ->call('createGameAccount');

        $this->assertDatabaseHas('game_accounts', [
            'user_id' => $user->id,
            'server' => 'xilero',
            'userid' => 'newgameacc',
            'email' => $user->email,
            'sex' => 'M',
        ]);

        // Also check the game database
        $this->assertDatabaseHas('login', [
            'userid' => 'newgameacc',
        ]);
    }

    public function test_user_can_create_xileretro_game_account(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->set('showCreateForm', true)
            ->set('gameServer', 'xileretro')
            ->set('gameUsername', 'retroacc')
            ->set('gamePassword', 'gamepass123')
            ->set('gamePassword_confirmation', 'gamepass123')
            ->call('createGameAccount');

        $this->assertDatabaseHas('game_accounts', [
            'user_id' => $user->id,
            'server' => 'xileretro',
            'userid' => 'retroacc',
            'email' => $user->email,
            'sex' => 'M',
        ]);
    }

    public function test_game_account_server_must_be_valid(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->set('showCreateForm', true)
            ->set('gameServer', 'invalid_server')
            ->set('gameUsername', 'newgameacc')
            ->set('gamePassword', 'gamepass123')
            ->set('gamePassword_confirmation', 'gamepass123')
            ->call('createGameAccount')
            ->assertHasErrors(['gameServer']);
    }

    public function test_user_cannot_exceed_game_account_limit(): void
    {
        $user = User::factory()->create(['max_game_accounts' => 1]);
        GameAccount::factory()->create(['user_id' => $user->id]);

        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->set('showCreateForm', true)
            ->set('gameServer', 'xilero')
            ->set('gameUsername', 'newgameacc')
            ->set('gamePassword', 'gamepass123')
            ->set('gamePassword_confirmation', 'gamepass123')
            ->call('createGameAccount');

        // Verify no new account was created
        $this->assertDatabaseMissing('game_accounts', ['userid' => 'newgameacc']);
        $this->assertEquals(1, $user->gameAccounts()->count());
    }

    public function test_game_account_username_must_be_unique(): void
    {
        $user = User::factory()->create();
        GameAccount::factory()->create(['userid' => 'existingacc']);

        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->set('showCreateForm', true)
            ->set('gameServer', 'xilero')
            ->set('gameUsername', 'existingacc')
            ->set('gamePassword', 'gamepass123')
            ->set('gamePassword_confirmation', 'gamepass123')
            ->call('createGameAccount')
            ->assertHasErrors(['gameUsername']);
    }

    public function test_game_account_username_must_be_alphanumeric(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->set('showCreateForm', true)
            ->set('gameServer', 'xilero')
            ->set('gameUsername', 'invalid user!')
            ->call('createGameAccount')
            ->assertHasErrors(['gameUsername']);
    }

    public function test_user_can_select_game_account(): void
    {
        $user = User::factory()->create();
        $gameAccount = GameAccount::factory()->create(['user_id' => $user->id]);

        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->call('selectGameAccount', $gameAccount->id)
            ->assertSet('selectedGameAccountId', $gameAccount->id);
    }
}
