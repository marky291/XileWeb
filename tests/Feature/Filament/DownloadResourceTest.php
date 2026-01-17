<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\DownloadResource;
use App\Filament\Resources\DownloadResource\Pages\CreateDownload;
use App\Filament\Resources\DownloadResource\Pages\EditDownload;
use App\Filament\Resources\DownloadResource\Pages\ListDownloads;
use App\Models\Download;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DownloadResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    #[Test]
    public function download_resource_has_correct_navigation_group(): void
    {
        $this->assertEquals('Content', DownloadResource::getNavigationGroup());
        $this->assertEquals('Downloads', DownloadResource::getNavigationLabel());
    }

    #[Test]
    public function admin_can_view_downloads_list(): void
    {
        $admin = User::factory()->admin()->create();
        $downloads = Download::factory()->count(3)->create();

        Livewire::actingAs($admin)
            ->test(ListDownloads::class)
            ->assertSuccessful()
            ->assertCanSeeTableRecords($downloads);
    }

    #[Test]
    public function admin_can_create_full_download(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(CreateDownload::class)
            ->fillForm([
                'type' => Download::TYPE_FULL,
                'name' => 'Full Client v8 (3GB)',
                'link' => 'https://drive.google.com/test',
                'version' => '8.0',
                'button_style' => Download::BUTTON_STYLE_PRIMARY,
                'display_order' => 0,
                'enabled' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('downloads', [
            'type' => Download::TYPE_FULL,
            'name' => 'Full Client v8 (3GB)',
            'link' => 'https://drive.google.com/test',
            'version' => '8.0',
            'button_style' => Download::BUTTON_STYLE_PRIMARY,
            'enabled' => true,
        ]);
    }

    #[Test]
    public function admin_can_create_android_download(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(CreateDownload::class)
            ->fillForm([
                'type' => Download::TYPE_ANDROID,
                'name' => 'Android v526 (3MB)',
                'link' => 'https://cdn.discord.com/test.apk',
                'version' => '526',
                'button_style' => Download::BUTTON_STYLE_SECONDARY,
                'display_order' => 0,
                'enabled' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('downloads', [
            'type' => Download::TYPE_ANDROID,
            'name' => 'Android v526 (3MB)',
            'version' => '526',
            'button_style' => Download::BUTTON_STYLE_SECONDARY,
        ]);
    }

    #[Test]
    public function admin_can_edit_download(): void
    {
        $admin = User::factory()->admin()->create();
        $download = Download::factory()->full()->create([
            'name' => 'Original Name',
            'link' => 'https://example.com/original',
        ]);

        Livewire::actingAs($admin)
            ->test(EditDownload::class, ['record' => $download->id])
            ->assertFormSet([
                'name' => 'Original Name',
            ])
            ->fillForm([
                'name' => 'Updated Name',
                'link' => 'https://example.com/updated',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('downloads', [
            'id' => $download->id,
            'name' => 'Updated Name',
            'link' => 'https://example.com/updated',
        ]);
    }

    #[Test]
    public function admin_can_filter_downloads_by_type(): void
    {
        $admin = User::factory()->admin()->create();

        $fullDownload = Download::factory()->full()->create();
        $androidDownload = Download::factory()->android()->create();

        Livewire::actingAs($admin)
            ->test(ListDownloads::class)
            ->filterTable('type', Download::TYPE_FULL)
            ->assertCanSeeTableRecords([$fullDownload])
            ->assertCanNotSeeTableRecords([$androidDownload]);
    }

    #[Test]
    public function admin_can_filter_downloads_by_enabled_status(): void
    {
        $admin = User::factory()->admin()->create();

        $enabledDownload = Download::factory()->create(['enabled' => true]);
        $disabledDownload = Download::factory()->disabled()->create();

        Livewire::actingAs($admin)
            ->test(ListDownloads::class)
            ->filterTable('enabled', '1')
            ->assertCanSeeTableRecords([$enabledDownload])
            ->assertCanNotSeeTableRecords([$disabledDownload]);
    }

    #[Test]
    public function admin_can_search_downloads_by_name(): void
    {
        $admin = User::factory()->admin()->create();

        $searchableDownload = Download::factory()->create(['name' => 'Searchable Download']);
        $otherDownload = Download::factory()->create(['name' => 'Other Download']);

        Livewire::actingAs($admin)
            ->test(ListDownloads::class)
            ->searchTable('Searchable')
            ->assertCanSeeTableRecords([$searchableDownload])
            ->assertCanNotSeeTableRecords([$otherDownload]);
    }

    #[Test]
    public function full_scope_returns_only_enabled_full_downloads(): void
    {
        $enabledFull = Download::factory()->full()->create(['enabled' => true, 'display_order' => 1]);
        $disabledFull = Download::factory()->full()->disabled()->create(['display_order' => 2]);
        $enabledAndroid = Download::factory()->android()->create(['enabled' => true, 'display_order' => 3]);

        $results = Download::full()->get();

        $this->assertCount(1, $results);
        $this->assertTrue($results->contains($enabledFull));
        $this->assertFalse($results->contains($disabledFull));
        $this->assertFalse($results->contains($enabledAndroid));
    }

    #[Test]
    public function android_scope_returns_only_enabled_android_downloads(): void
    {
        $enabledAndroid = Download::factory()->android()->create(['enabled' => true, 'display_order' => 1]);
        $disabledAndroid = Download::factory()->android()->disabled()->create(['display_order' => 2]);
        $enabledFull = Download::factory()->full()->create(['enabled' => true, 'display_order' => 3]);

        $results = Download::android()->get();

        $this->assertCount(1, $results);
        $this->assertTrue($results->contains($enabledAndroid));
        $this->assertFalse($results->contains($disabledAndroid));
        $this->assertFalse($results->contains($enabledFull));
    }

    #[Test]
    public function download_url_returns_link_when_no_file(): void
    {
        $download = Download::factory()->create([
            'link' => 'https://example.com/download',
            'file' => null,
        ]);

        $this->assertEquals('https://example.com/download', $download->download_url);
    }

    #[Test]
    public function download_url_returns_storage_url_when_file_exists(): void
    {
        $download = Download::factory()->withFile()->create();

        $this->assertStringContainsString('/storage/android/apk/', $download->download_url);
    }

    #[Test]
    public function button_class_returns_correct_css_class_for_primary(): void
    {
        $download = Download::factory()->primary()->create();

        $this->assertEquals('btn-primary', $download->button_class);
    }

    #[Test]
    public function button_class_returns_correct_css_class_for_secondary(): void
    {
        $download = Download::factory()->secondary()->create();

        $this->assertEquals('btn-secondary', $download->button_class);
    }

    #[Test]
    public function scopes_order_by_display_order(): void
    {
        Download::factory()->full()->create(['enabled' => true, 'display_order' => 3]);
        Download::factory()->full()->create(['enabled' => true, 'display_order' => 1]);
        Download::factory()->full()->create(['enabled' => true, 'display_order' => 2]);

        $results = Download::full()->get();

        $this->assertEquals(1, $results[0]->display_order);
        $this->assertEquals(2, $results[1]->display_order);
        $this->assertEquals(3, $results[2]->display_order);
    }

    #[Test]
    public function display_name_includes_version_when_present(): void
    {
        $download = Download::factory()->create([
            'name' => 'Full Client (3GB)',
            'version' => '8',
        ]);

        $this->assertEquals('v8 - Full Client (3GB)', $download->display_name);
    }

    #[Test]
    public function display_name_returns_name_only_when_no_version(): void
    {
        $download = Download::factory()->create([
            'name' => 'Full Client (3GB)',
            'version' => null,
        ]);

        $this->assertEquals('Full Client (3GB)', $download->display_name);
    }
}
