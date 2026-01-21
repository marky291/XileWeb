<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\LoginResource;
use App\Filament\Resources\LoginResource\Pages\EditLogin;
use App\Filament\Resources\LoginResource\Pages\ListLogins;
use App\Models\User;
use App\XileRO\XileRO_Login;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LoginResourceTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function login_resource_has_correct_navigation_group(): void
    {
        $this->assertEquals('XileRO', LoginResource::getNavigationGroup());
        $this->assertEquals('Logins', LoginResource::getNavigationLabel());
    }

    #[Test]
    public function guest_cannot_access_logins_list(): void
    {
        $this->get('/admin/logins')
            ->assertRedirect('/login');
    }

    #[Test]
    public function non_admin_cannot_access_logins_list(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user)
            ->get('/admin/logins')
            ->assertForbidden();
    }

    #[Test]
    public function admin_can_view_logins_list(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $admin = User::factory()->admin()->create();
        $logins = XileRO_Login::factory()->count(3)->create();

        Livewire::actingAs($admin)
            ->test(ListLogins::class)
            ->assertSuccessful()
            ->assertCanSeeTableRecords($logins);
    }

    #[Test]
    public function admin_can_search_logins_by_userid(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $admin = User::factory()->admin()->create();
        $searchableLogin = XileRO_Login::factory()->create([
            'userid' => 'searchable_user',
        ]);
        $otherLogin = XileRO_Login::factory()->create([
            'userid' => 'other_user',
        ]);

        Livewire::actingAs($admin)
            ->test(ListLogins::class)
            ->searchTable('searchable_user')
            ->assertCanSeeTableRecords([$searchableLogin])
            ->assertCanNotSeeTableRecords([$otherLogin]);
    }

    #[Test]
    public function admin_can_filter_staff_logins(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $admin = User::factory()->admin()->create();
        $staffLogin = XileRO_Login::factory()->create([
            'group_id' => 99,
        ]);
        $regularLogin = XileRO_Login::factory()->create([
            'group_id' => 0,
        ]);

        Livewire::actingAs($admin)
            ->test(ListLogins::class)
            ->filterTable('Staff', true)
            ->assertCanSeeTableRecords([$staffLogin])
            ->assertCanNotSeeTableRecords([$regularLogin]);
    }

    #[Test]
    public function admin_can_edit_login(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $admin = User::factory()->admin()->create();
        $login = XileRO_Login::factory()->create([
            'userid' => 'original_user',
            'email' => 'original@example.com',
        ]);

        Livewire::actingAs($admin)
            ->test(EditLogin::class, ['record' => $login->account_id])
            ->assertFormSet([
                'userid' => 'original_user',
                'email' => 'original@example.com',
            ])
            ->fillForm([
                'userid' => 'updated_user',
                'email' => 'updated@example.com',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('login', [
            'account_id' => $login->account_id,
            'userid' => 'updated_user',
            'email' => 'updated@example.com',
        ]);
    }
}
