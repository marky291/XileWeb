<?php

namespace Tests\Feature\Filament;

use App\Filament\Pages\PlayerSupport;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PlayerSupportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    #[Test]
    public function guest_cannot_access_player_support_page(): void
    {
        $this->get('/admin/player-support')
            ->assertRedirect('/login');
    }

    #[Test]
    public function non_admin_user_cannot_access_player_support_page(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user)
            ->get('/admin/player-support')
            ->assertForbidden();
    }

    #[Test]
    public function admin_user_can_access_player_support_page(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get('/admin/player-support')
            ->assertOk();
    }

    #[Test]
    public function player_support_page_has_correct_navigation_group(): void
    {
        $this->assertEquals('Support', PlayerSupport::getNavigationGroup());
    }

    #[Test]
    public function search_requires_minimum_characters(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(PlayerSupport::class)
            ->set('search', 'a')
            ->call('searchPlayers')
            ->assertNotified('Search term too short');
    }

    #[Test]
    public function can_search_master_accounts_by_email(): void
    {
        $admin = User::factory()->admin()->create();
        $targetUser = User::factory()->create([
            'name' => 'Target User',
            'email' => 'target@example.com',
            'uber_balance' => 500,
        ]);

        Livewire::actingAs($admin)
            ->test(PlayerSupport::class)
            ->set('searchType', 'master_email')
            ->set('search', 'target@example')
            ->call('searchPlayers')
            ->assertSet('results.0.type', 'master')
            ->assertSet('results.0.email', 'target@example.com')
            ->assertSet('results.0.uber_balance', 500);
    }

    #[Test]
    public function can_search_master_accounts_by_name(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->create([
            'name' => 'Unique Player Name',
            'email' => 'unique@example.com',
        ]);

        Livewire::actingAs($admin)
            ->test(PlayerSupport::class)
            ->set('searchType', 'master_email')
            ->set('search', 'Unique Player')
            ->call('searchPlayers')
            ->assertSet('results.0.type', 'master')
            ->assertSet('results.0.name', 'Unique Player Name');
    }

    #[Test]
    public function can_select_player_from_results(): void
    {
        $admin = User::factory()->admin()->create();
        $targetUser = User::factory()->create([
            'name' => 'Selectable User',
            'email' => 'selectable@example.com',
            'uber_balance' => 100,
        ]);

        Livewire::actingAs($admin)
            ->test(PlayerSupport::class)
            ->set('searchType', 'master_email')
            ->set('search', 'selectable@example')
            ->call('searchPlayers')
            ->call('selectPlayer', 0)
            ->assertSet('selectedPlayer.type', 'master')
            ->assertSet('selectedPlayer.email', 'selectable@example.com');
    }

    #[Test]
    public function can_clear_player_selection(): void
    {
        $admin = User::factory()->admin()->create();
        $targetUser = User::factory()->create([
            'email' => 'clearable@example.com',
        ]);

        Livewire::actingAs($admin)
            ->test(PlayerSupport::class)
            ->set('searchType', 'master_email')
            ->set('search', 'clearable@example')
            ->call('searchPlayers')
            ->call('selectPlayer', 0)
            ->assertSet('selectedPlayer.type', 'master')
            ->call('clearSelection')
            ->assertSet('selectedPlayer', null);
    }

    #[Test]
    public function no_results_notification_when_search_finds_nothing(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(PlayerSupport::class)
            ->set('searchType', 'master_email')
            ->set('search', 'nonexistent@nowhere.com')
            ->call('searchPlayers')
            ->assertNotified('No results found');
    }

    #[Test]
    public function can_reset_master_account_password_with_custom_password(): void
    {
        $admin = User::factory()->admin()->create();
        $targetUser = User::factory()->create([
            'email' => 'resetpw@example.com',
        ]);
        $originalPasswordHash = $targetUser->password;

        Livewire::actingAs($admin)
            ->test(PlayerSupport::class)
            ->set('searchType', 'master_email')
            ->set('search', 'resetpw@example')
            ->call('searchPlayers')
            ->call('selectPlayer', 0)
            ->set('newPassword', 'newSecurePassword123')
            ->call('resetMasterPassword')
            ->assertNotified('Password reset')
            ->assertSet('newPassword', '');

        $targetUser->refresh();
        $this->assertNotEquals($originalPasswordHash, $targetUser->password);
    }

    #[Test]
    public function can_reset_master_account_password_with_random_password(): void
    {
        $admin = User::factory()->admin()->create();
        $targetUser = User::factory()->create([
            'email' => 'randompw@example.com',
        ]);
        $originalPasswordHash = $targetUser->password;

        Livewire::actingAs($admin)
            ->test(PlayerSupport::class)
            ->set('searchType', 'master_email')
            ->set('search', 'randompw@example')
            ->call('searchPlayers')
            ->call('selectPlayer', 0)
            ->set('newPassword', '')
            ->call('resetMasterPassword')
            ->assertNotified('Password reset');

        $targetUser->refresh();
        $this->assertNotEquals($originalPasswordHash, $targetUser->password);
    }

    #[Test]
    public function cannot_reset_password_without_selecting_master_account(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(PlayerSupport::class)
            ->set('newPassword', 'newpassword')
            ->call('resetMasterPassword')
            ->assertNotified('Invalid selection');
    }

    #[Test]
    public function cannot_link_game_account_without_master_account_id(): void
    {
        $admin = User::factory()->admin()->create();

        // Simulate having a login account selected with all required fields
        Livewire::actingAs($admin)
            ->test(PlayerSupport::class)
            ->set('selectedPlayer', [
                'type' => 'xilero_login',
                'server' => 'XileRO',
                'server_key' => 'xilero',
                'account_id' => 12345,
                'userid' => 'testuser',
                'email' => 'testuser@example.com',
                'group_id' => 0,
                'last_ip' => '127.0.0.1',
                'lastlogin' => null,
                'chars_count' => 0,
                'linked_master_id' => null,
                'linked_master_name' => null,
            ])
            ->set('linkToMasterAccountId', null)
            ->call('linkGameAccountToMaster')
            ->assertNotified('No master account selected');
    }

    #[Test]
    public function cannot_link_game_account_to_nonexistent_master_account(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(PlayerSupport::class)
            ->set('selectedPlayer', [
                'type' => 'xilero_login',
                'server' => 'XileRO',
                'server_key' => 'xilero',
                'account_id' => 12345,
                'userid' => 'testuser',
                'email' => 'testuser@example.com',
                'group_id' => 0,
                'last_ip' => '127.0.0.1',
                'lastlogin' => null,
                'chars_count' => 0,
                'linked_master_id' => null,
                'linked_master_name' => null,
            ])
            ->set('linkToMasterAccountId', 99999)
            ->call('linkGameAccountToMaster')
            ->assertNotified('Master account not found');
    }

    #[Test]
    public function cannot_link_game_account_without_selecting_login_type(): void
    {
        $admin = User::factory()->admin()->create();
        $masterAccount = User::factory()->create();

        // Try linking when a master account is selected (not a game account)
        Livewire::actingAs($admin)
            ->test(PlayerSupport::class)
            ->set('selectedPlayer', [
                'type' => 'master',
                'id' => $masterAccount->id,
                'name' => $masterAccount->name,
                'email' => $masterAccount->email,
                'uber_balance' => $masterAccount->uber_balance,
                'is_admin' => $masterAccount->is_admin,
                'game_accounts_count' => 0,
                'created_at' => $masterAccount->created_at?->format('M j, Y'),
            ])
            ->set('linkToMasterAccountId', $masterAccount->id)
            ->call('linkGameAccountToMaster')
            ->assertNotified('Invalid selection');
    }

    #[Test]
    public function clear_selection_resets_link_and_password_fields(): void
    {
        $admin = User::factory()->admin()->create();
        $targetUser = User::factory()->create([
            'email' => 'clearfields@example.com',
        ]);

        Livewire::actingAs($admin)
            ->test(PlayerSupport::class)
            ->set('searchType', 'master_email')
            ->set('search', 'clearfields@example')
            ->call('searchPlayers')
            ->call('selectPlayer', 0)
            ->set('linkToMasterAccountId', 123)
            ->set('newPassword', 'somepassword')
            ->set('newGamePassword', 'gamepassword')
            ->call('clearSelection')
            ->assertSet('selectedPlayer', null)
            ->assertSet('linkToMasterAccountId', null)
            ->assertSet('newPassword', '')
            ->assertSet('newGamePassword', '');
    }

    #[Test]
    public function selecting_player_resets_link_and_password_fields(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->create(['email' => 'player1@example.com']);
        User::factory()->create(['email' => 'player2@example.com']);

        Livewire::actingAs($admin)
            ->test(PlayerSupport::class)
            ->set('searchType', 'master_email')
            ->set('search', 'player')
            ->call('searchPlayers')
            ->call('selectPlayer', 0)
            ->set('linkToMasterAccountId', 123)
            ->set('newPassword', 'somepassword')
            ->set('newGamePassword', 'gamepassword')
            ->call('selectPlayer', 1)
            ->assertSet('linkToMasterAccountId', null)
            ->assertSet('newPassword', '')
            ->assertSet('newGamePassword', '');
    }

    #[Test]
    public function cannot_reset_game_password_without_selecting_login_account(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(PlayerSupport::class)
            ->set('newGamePassword', 'newgamepass')
            ->call('resetGameAccountPassword')
            ->assertNotified('Invalid selection');
    }

    #[Test]
    public function cannot_reset_game_password_when_master_account_selected(): void
    {
        $admin = User::factory()->admin()->create();
        $targetUser = User::factory()->create();

        Livewire::actingAs($admin)
            ->test(PlayerSupport::class)
            ->set('selectedPlayer', [
                'type' => 'master',
                'id' => $targetUser->id,
                'name' => $targetUser->name,
                'email' => $targetUser->email,
                'uber_balance' => $targetUser->uber_balance,
                'is_admin' => $targetUser->is_admin,
                'game_accounts_count' => 0,
                'created_at' => $targetUser->created_at?->format('M j, Y'),
            ])
            ->set('newGamePassword', 'newgamepass')
            ->call('resetGameAccountPassword')
            ->assertNotified('Invalid selection');
    }

    #[Test]
    public function game_password_must_be_at_least_6_characters(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(PlayerSupport::class)
            ->set('selectedPlayer', [
                'type' => 'xilero_login',
                'server' => 'XileRO',
                'server_key' => 'xilero',
                'account_id' => 12345,
                'userid' => 'testuser',
                'email' => 'testuser@example.com',
                'group_id' => 0,
                'last_ip' => '127.0.0.1',
                'lastlogin' => null,
                'chars_count' => 0,
                'linked_master_id' => null,
                'linked_master_name' => null,
            ])
            ->set('newGamePassword', 'short')
            ->call('resetGameAccountPassword')
            ->assertNotified('Invalid password');
    }

    #[Test]
    public function game_password_must_be_at_most_31_characters(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(PlayerSupport::class)
            ->set('selectedPlayer', [
                'type' => 'xilero_login',
                'server' => 'XileRO',
                'server_key' => 'xilero',
                'account_id' => 12345,
                'userid' => 'testuser',
                'email' => 'testuser@example.com',
                'group_id' => 0,
                'last_ip' => '127.0.0.1',
                'lastlogin' => null,
                'chars_count' => 0,
                'linked_master_id' => null,
                'linked_master_name' => null,
            ])
            ->set('newGamePassword', str_repeat('a', 32))
            ->call('resetGameAccountPassword')
            ->assertNotified('Invalid password');
    }
}
