<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\PostResource;
use App\Filament\Resources\PostResource\Pages\CreatePost;
use App\Filament\Resources\PostResource\Pages\EditPost;
use App\Filament\Resources\PostResource\Pages\ListPosts;
use App\Models\Post;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PostResourceTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function post_resource_has_correct_navigation_group(): void
    {
        $this->assertEquals('Content', PostResource::getNavigationGroup());
        $this->assertEquals('Posts', PostResource::getNavigationLabel());
    }

    #[Test]
    public function guest_cannot_access_posts_list(): void
    {
        $this->get('/admin/posts')
            ->assertRedirect('/login');
    }

    #[Test]
    public function non_admin_cannot_access_posts_list(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user)
            ->get('/admin/posts')
            ->assertForbidden();
    }

    #[Test]
    public function admin_can_view_posts_list(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $admin = User::factory()->admin()->create();
        $posts = Post::factory()->count(3)->create();

        Livewire::actingAs($admin)
            ->test(ListPosts::class)
            ->assertSuccessful()
            ->assertCanSeeTableRecords($posts);
    }

    #[Test]
    public function admin_can_create_post(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(CreatePost::class)
            ->fillForm([
                'client' => Post::CLIENT_XILERO,
                'title' => 'Test Post Title',
                'patcher_notice' => 'Test patcher notice content',
                'article_content' => 'Test article content',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('posts', [
            'client' => Post::CLIENT_XILERO,
            'title' => 'Test Post Title',
            'patcher_notice' => 'Test patcher notice content',
        ]);
    }

    #[Test]
    public function admin_can_search_posts_by_title(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $admin = User::factory()->admin()->create();
        $searchablePost = Post::factory()->create(['title' => 'Searchable Post']);
        $otherPost = Post::factory()->create(['title' => 'Other Post']);

        Livewire::actingAs($admin)
            ->test(ListPosts::class)
            ->searchTable('Searchable')
            ->assertCanSeeTableRecords([$searchablePost])
            ->assertCanNotSeeTableRecords([$otherPost]);
    }

    #[Test]
    public function admin_can_filter_posts_by_client(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $admin = User::factory()->admin()->create();
        $xileroPost = Post::factory()->create(['client' => Post::CLIENT_XILERO]);
        $retroPost = Post::factory()->create(['client' => Post::CLIENT_RETRO]);

        Livewire::actingAs($admin)
            ->test(ListPosts::class)
            ->filterTable('client', Post::CLIENT_XILERO)
            ->assertCanSeeTableRecords([$xileroPost])
            ->assertCanNotSeeTableRecords([$retroPost]);
    }

    #[Test]
    public function admin_can_access_edit_post_page(): void
    {
        $admin = User::factory()->admin()->create();
        $post = Post::factory()->create([
            'title' => 'Original Title',
            'patcher_notice' => 'Original notice',
        ]);

        $this->actingAs($admin)
            ->get('/admin/posts/'.$post->slug.'/edit')
            ->assertSuccessful();
    }

    #[Test]
    public function admin_can_edit_post(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $admin = User::factory()->admin()->create();
        $post = Post::factory()->create([
            'title' => 'Original Title',
            'patcher_notice' => 'Original notice',
            'article_content' => 'Original content',
        ]);

        Livewire::actingAs($admin)
            ->test(EditPost::class, ['record' => $post->slug])
            ->assertFormSet([
                'title' => 'Original Title',
            ])
            ->fillForm([
                'title' => 'Updated Title',
                'patcher_notice' => 'Updated notice',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'title' => 'Updated Title',
            'patcher_notice' => 'Updated notice',
        ]);
    }

    #[Test]
    public function post_title_is_required(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(CreatePost::class)
            ->fillForm([
                'title' => '',
                'patcher_notice' => 'Some notice',
                'article_content' => 'Some content',
            ])
            ->call('create')
            ->assertHasFormErrors(['title' => 'required']);
    }

    #[Test]
    public function admin_can_create_post_with_image(): void
    {
        Storage::fake('public');
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(CreatePost::class)
            ->fillForm([
                'client' => Post::CLIENT_XILERO,
                'title' => 'Post With Image',
                'patcher_notice' => 'Test patcher notice',
                'article_content' => 'Test article content',
                'image' => UploadedFile::fake()->image('post-image.jpg'),
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('posts', [
            'title' => 'Post With Image',
        ]);

        $post = Post::where('title', 'Post With Image')->first();
        $this->assertNotNull($post->image);
        Storage::disk('public')->assertExists($post->image);
    }

    #[Test]
    public function post_image_is_optional(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(CreatePost::class)
            ->fillForm([
                'client' => Post::CLIENT_XILERO,
                'title' => 'Post Without Image',
                'patcher_notice' => 'Test patcher notice',
                'article_content' => 'Test article content',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $post = Post::where('title', 'Post Without Image')->first();
        $this->assertNull($post->image);
    }

    #[Test]
    public function create_post_page_has_ai_prompt_action(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(CreatePost::class)
            ->assertActionExists('aiPrompt');
    }

    #[Test]
    public function edit_post_page_has_ai_prompt_action(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $admin = User::factory()->admin()->create();
        $post = Post::factory()->create();

        Livewire::actingAs($admin)
            ->test(EditPost::class, ['record' => $post->slug])
            ->assertActionExists('aiPrompt');
    }
}
