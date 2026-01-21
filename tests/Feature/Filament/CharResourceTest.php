<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\CharResource;
use App\Filament\Resources\CharResource\Pages\EditChar;
use App\Filament\Resources\CharResource\Pages\ListChars;
use App\Models\User;
use App\XileRO\XileRO_Char;
use App\XileRO\XileRO_Login;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CharResourceTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function char_resource_has_correct_navigation_group(): void
    {
        $this->assertEquals('XileRO', CharResource::getNavigationGroup());
        $this->assertEquals('Characters', CharResource::getNavigationLabel());
    }

    #[Test]
    public function guest_cannot_access_chars_list(): void
    {
        $this->get('/admin/chars')
            ->assertRedirect('/login');
    }

    #[Test]
    public function non_admin_cannot_access_chars_list(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user)
            ->get('/admin/chars')
            ->assertForbidden();
    }

    #[Test]
    public function admin_can_view_chars_list(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $admin = User::factory()->admin()->create();
        $login = XileRO_Login::factory()->create();
        $chars = XileRO_Char::factory()->count(3)->create([
            'account_id' => $login->account_id,
        ]);

        Livewire::actingAs($admin)
            ->test(ListChars::class)
            ->assertSuccessful()
            ->assertCanSeeTableRecords($chars);
    }

    #[Test]
    public function admin_can_search_chars_by_name(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $admin = User::factory()->admin()->create();
        $login = XileRO_Login::factory()->create();
        $searchableChar = XileRO_Char::factory()->create([
            'account_id' => $login->account_id,
            'name' => 'SearchableChar',
        ]);
        $otherChar = XileRO_Char::factory()->create([
            'account_id' => $login->account_id,
            'name' => 'OtherChar',
        ]);

        Livewire::actingAs($admin)
            ->test(ListChars::class)
            ->searchTable('Searchable')
            ->assertCanSeeTableRecords([$searchableChar])
            ->assertCanNotSeeTableRecords([$otherChar]);
    }

    #[Test]
    public function admin_can_edit_char(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $admin = User::factory()->admin()->create();
        $login = XileRO_Login::factory()->create();
        $char = XileRO_Char::factory()->create([
            'account_id' => $login->account_id,
            'name' => 'OriginalName',
            'class' => 0,
        ]);

        Livewire::actingAs($admin)
            ->test(EditChar::class, ['record' => $char->char_id])
            ->assertFormSet([
                'name' => 'OriginalName',
            ])
            ->fillForm([
                'name' => 'UpdatedName',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('char', [
            'char_id' => $char->char_id,
            'name' => 'UpdatedName',
        ]);
    }
}
