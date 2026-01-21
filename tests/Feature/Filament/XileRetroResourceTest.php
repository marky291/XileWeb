<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\XileRetroCharResource;
use App\Filament\Resources\XileRetroCharResource\Pages\EditXileRetroChar;
use App\Filament\Resources\XileRetroCharResource\Pages\ListXileRetroChars;
use App\Filament\Resources\XileRetroLoginResource;
use App\Filament\Resources\XileRetroLoginResource\Pages\EditXileRetroLogin;
use App\Filament\Resources\XileRetroLoginResource\Pages\ListXileRetroLogins;
use App\Models\User;
use App\XileRetro\XileRetro_Char;
use App\XileRetro\XileRetro_Login;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class XileRetroResourceTest extends TestCase
{
    use RefreshDatabase;

    // ==================== XileRetro Login Resource Tests ====================

    #[Test]
    public function xileretro_login_resource_has_correct_navigation_group(): void
    {
        $this->assertEquals('XileRetro', XileRetroLoginResource::getNavigationGroup());
        $this->assertEquals('Logins', XileRetroLoginResource::getNavigationLabel());
    }

    #[Test]
    public function guest_cannot_access_xileretro_logins_list(): void
    {
        $this->get('/admin/xile-retro-logins')
            ->assertRedirect('/login');
    }

    #[Test]
    public function admin_can_view_xileretro_logins_list(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $admin = User::factory()->admin()->create();
        $logins = XileRetro_Login::factory()->count(3)->create();

        Livewire::actingAs($admin)
            ->test(ListXileRetroLogins::class)
            ->assertSuccessful()
            ->assertCanSeeTableRecords($logins);
    }

    #[Test]
    public function admin_can_search_xileretro_logins_by_userid(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $admin = User::factory()->admin()->create();
        $searchableLogin = XileRetro_Login::factory()->create([
            'userid' => 'retro_searchable',
        ]);
        $otherLogin = XileRetro_Login::factory()->create([
            'userid' => 'retro_other',
        ]);

        Livewire::actingAs($admin)
            ->test(ListXileRetroLogins::class)
            ->searchTable('retro_searchable')
            ->assertCanSeeTableRecords([$searchableLogin])
            ->assertCanNotSeeTableRecords([$otherLogin]);
    }

    #[Test]
    public function admin_can_view_xileretro_login_edit_page(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $admin = User::factory()->admin()->create();
        $login = XileRetro_Login::factory()->create([
            'userid' => 'original_retro_user',
            'email' => 'retro@example.com',
        ]);

        Livewire::actingAs($admin)
            ->test(EditXileRetroLogin::class, ['record' => $login->account_id])
            ->assertFormSet([
                'userid' => 'original_retro_user',
                'email' => 'retro@example.com',
            ])
            ->assertSuccessful();
    }

    // ==================== XileRetro Char Resource Tests ====================

    #[Test]
    public function xileretro_char_resource_has_correct_navigation_group(): void
    {
        $this->assertEquals('XileRetro', XileRetroCharResource::getNavigationGroup());
        $this->assertEquals('Characters', XileRetroCharResource::getNavigationLabel());
    }

    #[Test]
    public function guest_cannot_access_xileretro_chars_list(): void
    {
        $this->get('/admin/xile-retro-chars')
            ->assertRedirect('/login');
    }

    #[Test]
    public function admin_can_view_xileretro_chars_list(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $admin = User::factory()->admin()->create();
        $login = XileRetro_Login::factory()->create();
        $chars = XileRetro_Char::factory()->count(3)->create([
            'account_id' => $login->account_id,
        ]);

        Livewire::actingAs($admin)
            ->test(ListXileRetroChars::class)
            ->assertSuccessful()
            ->assertCanSeeTableRecords($chars);
    }

    #[Test]
    public function admin_can_search_xileretro_chars_by_name(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $admin = User::factory()->admin()->create();
        $login = XileRetro_Login::factory()->create();
        $searchableChar = XileRetro_Char::factory()->create([
            'account_id' => $login->account_id,
            'name' => 'RetroSearchable',
        ]);
        $otherChar = XileRetro_Char::factory()->create([
            'account_id' => $login->account_id,
            'name' => 'RetroOther',
        ]);

        Livewire::actingAs($admin)
            ->test(ListXileRetroChars::class)
            ->searchTable('RetroSearchable')
            ->assertCanSeeTableRecords([$searchableChar])
            ->assertCanNotSeeTableRecords([$otherChar]);
    }

    #[Test]
    public function admin_can_view_xileretro_char_edit_page(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $admin = User::factory()->admin()->create();
        $login = XileRetro_Login::factory()->create();
        $char = XileRetro_Char::factory()->create([
            'account_id' => $login->account_id,
            'name' => 'RetroOriginal',
        ]);

        Livewire::actingAs($admin)
            ->test(EditXileRetroChar::class, ['record' => $char->char_id])
            ->assertFormSet([
                'name' => 'RetroOriginal',
            ])
            ->assertSuccessful();
    }
}
