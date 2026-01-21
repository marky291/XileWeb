<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\MasterAccountResource;
use App\Filament\Resources\MasterAccountResource\Pages\CreateMasterAccount;
use App\Filament\Resources\MasterAccountResource\Pages\EditMasterAccount;
use App\Filament\Resources\MasterAccountResource\Pages\ListMasterAccounts;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    #[Test]
    public function guest_cannot_access_admin_dashboard(): void
    {
        $this->get('/admin')
            ->assertRedirect('/login');
    }

    #[Test]
    public function non_admin_user_cannot_access_admin_dashboard(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user)
            ->get('/admin')
            ->assertForbidden();
    }

    #[Test]
    public function admin_user_can_access_admin_dashboard(): void
    {
        $user = User::factory()->admin()->create();

        $this->actingAs($user)
            ->get('/admin')
            ->assertOk();
    }

    #[Test]
    public function admin_can_view_master_accounts_list(): void
    {
        $admin = User::factory()->admin()->create();
        $users = User::factory()->count(3)->create();

        Livewire::actingAs($admin)
            ->test(ListMasterAccounts::class)
            ->assertSuccessful()
            ->assertCanSeeTableRecords($users);
    }

    #[Test]
    public function admin_can_create_master_account(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(CreateMasterAccount::class)
            ->fillForm([
                'name' => 'Test User',
                'email' => 'testuser@example.com',
                'password' => 'password123',
                'max_game_accounts' => 6,
                'is_admin' => false,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'uber_balance' => 0, // uber_balance is set via Apply Donation page, not admin form
            'max_game_accounts' => 6,
            'is_admin' => false,
        ]);
    }

    #[Test]
    public function admin_can_edit_master_account(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create([
            'name' => 'Original Name',
            'uber_balance' => 50,
            'max_game_accounts' => 6,
        ]);

        Livewire::actingAs($admin)
            ->test(EditMasterAccount::class, ['record' => $user->id])
            ->assertFormSet([
                'name' => 'Original Name',
                'max_game_accounts' => 6,
            ])
            ->fillForm([
                'name' => 'Updated Name',
                'max_game_accounts' => 10,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
            'max_game_accounts' => 10,
            'uber_balance' => 50, // uber_balance is unchanged - set via Apply Donation page
        ]);
    }

    #[Test]
    public function admin_can_toggle_user_admin_status(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create(['is_admin' => false]);

        Livewire::actingAs($admin)
            ->test(EditMasterAccount::class, ['record' => $user->id])
            ->assertFormSet(['is_admin' => false])
            ->fillForm(['is_admin' => true])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'is_admin' => true,
        ]);
    }

    #[Test]
    public function admin_can_search_master_accounts(): void
    {
        $admin = User::factory()->admin()->create();
        $searchableUser = User::factory()->create(['email' => 'searchable@example.com']);
        $otherUser = User::factory()->create(['email' => 'other@example.com']);

        Livewire::actingAs($admin)
            ->test(ListMasterAccounts::class)
            ->searchTable('searchable@example')
            ->assertCanSeeTableRecords([$searchableUser])
            ->assertCanNotSeeTableRecords([$otherUser]);
    }

    #[Test]
    public function admin_can_filter_by_admin_status(): void
    {
        $admin = User::factory()->admin()->create();
        $regularUser = User::factory()->create(['is_admin' => false]);
        $adminUser = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(ListMasterAccounts::class)
            ->filterTable('is_admin', true)
            ->assertCanSeeTableRecords([$admin, $adminUser])
            ->assertCanNotSeeTableRecords([$regularUser]);
    }

    #[Test]
    public function master_account_resource_has_correct_navigation_group(): void
    {
        $this->assertEquals('Accounts', MasterAccountResource::getNavigationGroup());
    }

    #[Test]
    public function email_must_be_unique_when_creating_master_account(): void
    {
        $admin = User::factory()->admin()->create();
        $existingUser = User::factory()->create(['email' => 'existing@example.com']);

        Livewire::actingAs($admin)
            ->test(CreateMasterAccount::class)
            ->fillForm([
                'name' => 'New User',
                'email' => 'existing@example.com',
            ])
            ->call('create')
            ->assertHasFormErrors(['email' => 'unique']);
    }
}
