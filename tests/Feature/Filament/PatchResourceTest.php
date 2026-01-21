<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\PatchResource;
use App\Filament\Resources\PatchResource\Pages\CreatePatch;
use App\Filament\Resources\PatchResource\Pages\ListPatches;
use App\Jobs\CompilePatch;
use App\Models\Patch;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PatchResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('xilero_patch');
        Storage::fake('retro_patch');
    }

    #[Test]
    public function patch_resource_has_correct_navigation_group(): void
    {
        $this->assertEquals('Content', PatchResource::getNavigationGroup());
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
    public function admin_can_access_create_patch_page(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get('/admin/patches/create')
            ->assertSuccessful();
    }

    #[Test]
    public function create_patch_form_validates_required_fields(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(CreatePatch::class)
            ->fillForm([
                'type' => '',
                'client' => '',
            ])
            ->call('create')
            ->assertHasFormErrors(['type', 'client']);
    }

    #[Test]
    public function admin_can_filter_patches_by_type(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $admin = User::factory()->admin()->create();
        $fldPatch = Patch::factory()->create([
            'type' => 'FLD',
            'client' => Patch::CLIENT_XILERO,
            'number' => 1,
        ]);
        $grfPatch = Patch::factory()->create([
            'type' => 'GRF',
            'client' => Patch::CLIENT_XILERO,
            'number' => 2,
        ]);

        Livewire::actingAs($admin)
            ->test(ListPatches::class)
            ->filterTable('type', 'FLD')
            ->assertCanSeeTableRecords([$fldPatch])
            ->assertCanNotSeeTableRecords([$grfPatch]);
    }

    #[Test]
    public function admin_can_filter_patches_by_client(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $admin = User::factory()->admin()->create();
        $xileroPatch = Patch::factory()->create([
            'client' => Patch::CLIENT_XILERO,
            'number' => 1,
        ]);
        $retroPatch = Patch::factory()->create([
            'client' => Patch::CLIENT_RETRO,
            'number' => 1,
        ]);

        Livewire::actingAs($admin)
            ->test(ListPatches::class)
            ->filterTable('client', Patch::CLIENT_XILERO)
            ->assertCanSeeTableRecords([$xileroPatch])
            ->assertCanNotSeeTableRecords([$retroPatch]);
    }

    #[Test]
    public function admin_can_access_edit_patch_page(): void
    {
        $admin = User::factory()->admin()->create();
        $patch = Patch::factory()->create([
            'client' => Patch::CLIENT_XILERO,
            'number' => 1,
        ]);

        $this->actingAs($admin)
            ->get('/admin/patches/'.$patch->id.'/edit')
            ->assertSuccessful();
    }

    #[Test]
    public function admin_can_compile_fld_patch(): void
    {
        Queue::fake();
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $admin = User::factory()->admin()->create();

        Storage::disk('xilero_patch')->put('test_patch.gpf', 'fake content');

        $patch = Patch::factory()->create([
            'type' => 'FLD',
            'client' => Patch::CLIENT_XILERO,
            'number' => 1,
            'file' => 'test_patch.gpf',
        ]);

        Livewire::actingAs($admin)
            ->test(ListPatches::class)
            ->callTableAction('compile', $patch)
            ->assertNotified('Compile job queued');

        Queue::assertPushed(CompilePatch::class, function ($job) use ($patch) {
            return $job->patch->id === $patch->id;
        });
    }

    #[Test]
    public function admin_can_compile_grf_patch(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        Queue::fake();

        $admin = User::factory()->admin()->create();

        Storage::disk('xilero_patch')->put('test_patch.gpf', 'fake content');

        $grfPatch = Patch::factory()->create([
            'type' => 'GRF',
            'client' => Patch::CLIENT_XILERO,
            'number' => 1,
            'file' => 'test_patch.gpf',
        ]);

        Livewire::actingAs($admin)
            ->test(ListPatches::class)
            ->callTableAction('compile', $grfPatch)
            ->assertNotified('Compile job queued');

        Queue::assertPushed(CompilePatch::class, function ($job) use ($grfPatch) {
            return $job->patch->id === $grfPatch->id;
        });
    }

    #[Test]
    public function compile_action_not_visible_for_patches_without_file(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $admin = User::factory()->admin()->create();

        $patch = Patch::factory()->create([
            'type' => 'FLD',
            'client' => Patch::CLIENT_XILERO,
            'number' => 1,
            'file' => '',
        ]);

        Livewire::actingAs($admin)
            ->test(ListPatches::class)
            ->assertTableActionHidden('compile', $patch);
    }
}
