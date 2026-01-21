<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\PatchResource;
use App\Filament\Resources\PatchResource\Pages\CreatePatch;
use App\Filament\Resources\PatchResource\Pages\EditPatch;
use App\Filament\Resources\PatchResource\Pages\ListPatches;
use App\Models\Patch;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PatchResourceTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function patch_resource_has_correct_navigation_group(): void
    {
        $this->assertEquals('Content', PatchResource::getNavigationGroup());
        $this->assertEquals('Client Patch', PatchResource::getNavigationLabel());
    }

    #[Test]
    public function guest_cannot_access_patches_list(): void
    {
        $this->get('/admin/patches')
            ->assertRedirect('/login');
    }

    #[Test]
    public function non_admin_cannot_access_patches_list(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user)
            ->get('/admin/patches')
            ->assertForbidden();
    }

    #[Test]
    public function admin_can_view_patches_list(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $admin = User::factory()->admin()->create();
        $patches = Patch::factory()->count(3)->create();

        Livewire::actingAs($admin)
            ->test(ListPatches::class)
            ->assertSuccessful()
            ->assertCanSeeTableRecords($patches);
    }

    #[Test]
    public function admin_can_create_patch(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(CreatePatch::class)
            ->fillForm([
                'type' => Patch::TYPE_FLD,
                'client' => Patch::CLIENT_XILERO,
                'comments' => 'Test patch comment',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('patches', [
            'type' => Patch::TYPE_FLD,
            'client' => Patch::CLIENT_XILERO,
            'comments' => 'Test patch comment',
        ]);
    }

    #[Test]
    public function admin_can_filter_patches_by_type(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $admin = User::factory()->admin()->create();
        $fldPatch = Patch::factory()->create(['type' => Patch::TYPE_FLD]);
        $grfPatch = Patch::factory()->create(['type' => Patch::TYPE_GRF]);

        Livewire::actingAs($admin)
            ->test(ListPatches::class)
            ->filterTable('type', Patch::TYPE_FLD)
            ->assertCanSeeTableRecords([$fldPatch])
            ->assertCanNotSeeTableRecords([$grfPatch]);
    }

    #[Test]
    public function admin_can_filter_patches_by_client(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $admin = User::factory()->admin()->create();
        $xileroPatch = Patch::factory()->create(['client' => Patch::CLIENT_XILERO]);
        $retroPatch = Patch::factory()->create(['client' => Patch::CLIENT_XILERETRO]);

        Livewire::actingAs($admin)
            ->test(ListPatches::class)
            ->filterTable('client', Patch::CLIENT_XILERO)
            ->assertCanSeeTableRecords([$xileroPatch])
            ->assertCanNotSeeTableRecords([$retroPatch]);
    }

    #[Test]
    public function admin_can_edit_patch(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $admin = User::factory()->admin()->create();
        $patch = Patch::factory()->create([
            'comments' => 'Original comment',
        ]);

        Livewire::actingAs($admin)
            ->test(EditPatch::class, ['record' => $patch->id])
            ->fillForm([
                'comments' => 'Updated comment',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('patches', [
            'id' => $patch->id,
            'comments' => 'Updated comment',
        ]);
    }
}
