<?php

namespace Tests\Feature;

use App\Models\Patch;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RpatchurPatchListTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function the_rpatchur_launcher_page_renders(): void
    {
        $post = Post::factory()->create([
            'client' => Post::CLIENT_XILERO,
            'title' => 'June Update',
        ]);

        $this->get('/xilero/rpatchur')
            ->assertOk()
            ->assertSee($post->title);
    }

    #[Test]
    public function it_lists_only_xilero_rpatchur_patches_in_rpatchur_format(): void
    {
        Patch::factory()->create([
            'client' => Patch::CLIENT_XILERO,
            'patcher' => Patch::PATCHER_RPATCHUR,
            'number' => 2,
            'type' => 'FLD',
            'patch_name' => 'second.thor',
            'comments' => 'second patch',
        ]);
        Patch::factory()->create([
            'client' => Patch::CLIENT_XILERO,
            'patcher' => Patch::PATCHER_RPATCHUR,
            'number' => 1,
            'type' => 'GRF',
            'patch_name' => 'first.thor',
            'comments' => null,
        ]);

        // Must be excluded: legacy xilero patch and an rpatchur patch for another client.
        Patch::factory()->create([
            'client' => Patch::CLIENT_XILERO,
            'patcher' => Patch::PATCHER_LEGACY,
            'number' => 3,
            'patch_name' => 'legacy.gpf',
        ]);
        Patch::factory()->create([
            'client' => Patch::CLIENT_RETRO,
            'patcher' => Patch::PATCHER_RPATCHUR,
            'number' => 4,
            'patch_name' => 'retro.thor',
        ]);

        $response = $this->get('/xilero/rpatchur/list');

        $response->assertOk();
        // rpatchur format: "<zero-padded index> <filename>" ordered by number,
        // no type token, optional " // comment". Legacy/retro excluded.
        $response->assertSee("001 first.thor\r\n002 second.thor // second patch", false);
        $response->assertDontSee('legacy.gpf');
        $response->assertDontSee('retro.thor');
    }

    #[Test]
    public function the_legacy_list_excludes_rpatchur_patches(): void
    {
        Patch::factory()->create([
            'client' => Patch::CLIENT_XILERO,
            'patcher' => Patch::PATCHER_LEGACY,
            'number' => 10,
            'type' => 'GRF',
            'patch_name' => 'old.gpf',
        ]);
        Patch::factory()->create([
            'client' => Patch::CLIENT_XILERO,
            'patcher' => Patch::PATCHER_RPATCHUR,
            'number' => 11,
            'patch_name' => 'new.thor',
        ]);

        $response = $this->get('/xilero/patch/list');

        $response->assertOk();
        $response->assertSee('old.gpf');
        $response->assertDontSee('new.thor');
    }

    #[Test]
    public function the_retro_legacy_list_excludes_rpatchur_patches(): void
    {
        Patch::factory()->create([
            'client' => Patch::CLIENT_RETRO,
            'patcher' => Patch::PATCHER_LEGACY,
            'number' => 1,
            'type' => 'FLD',
            'patch_name' => 'retro.gpf',
        ]);
        Patch::factory()->create([
            'client' => Patch::CLIENT_RETRO,
            'patcher' => Patch::PATCHER_RPATCHUR,
            'number' => 2,
            'patch_name' => 'never.thor',
        ]);

        $response = $this->get('/retro/patch/list');

        $response->assertOk();
        $response->assertSee('retro.gpf');
        $response->assertDontSee('never.thor');
    }
}
