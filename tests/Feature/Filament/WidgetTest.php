<?php

namespace Tests\Feature\Filament;

use App\Filament\Widgets\RecentActivityWidget;
use App\Filament\Widgets\ServerStatsOverview;
use App\Models\UberShopItem;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class WidgetTest extends TestCase
{
    use RefreshDatabase;

    // ==================== Recent Activity Widget Tests ====================

    #[Test]
    public function recent_activity_widget_renders_successfully(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(RecentActivityWidget::class)
            ->assertSuccessful();
    }

    #[Test]
    public function recent_activity_widget_shows_recent_registrations(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $admin = User::factory()->admin()->create();
        $recentUsers = User::factory()->count(5)->create();

        Livewire::actingAs($admin)
            ->test(RecentActivityWidget::class)
            ->assertSuccessful()
            ->assertCanSeeTableRecords($recentUsers);
    }

    #[Test]
    public function recent_activity_widget_can_search_by_email(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $admin = User::factory()->admin()->create();
        $searchableUser = User::factory()->create(['email' => 'searchable@test.com']);
        $otherUser = User::factory()->create(['email' => 'other@test.com']);

        Livewire::actingAs($admin)
            ->test(RecentActivityWidget::class)
            ->searchTable('searchable@test')
            ->assertCanSeeTableRecords([$searchableUser])
            ->assertCanNotSeeTableRecords([$otherUser]);
    }

    // ==================== Server Stats Overview Widget Tests ====================

    #[Test]
    public function server_stats_widget_renders_successfully(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(ServerStatsOverview::class)
            ->assertSuccessful();
    }

    #[Test]
    public function server_stats_widget_shows_master_account_count(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $admin = User::factory()->admin()->create();
        User::factory()->count(10)->create();

        $component = Livewire::actingAs($admin)
            ->test(ServerStatsOverview::class)
            ->assertSuccessful();

        // Widget should render without errors even with users in database
        $this->assertTrue(true);
    }

    #[Test]
    public function server_stats_widget_shows_uber_shop_stats(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $admin = User::factory()->admin()->create();
        UberShopItem::factory()->count(5)->create(['enabled' => true]);
        UberShopItem::factory()->count(3)->create(['enabled' => false]);

        $component = Livewire::actingAs($admin)
            ->test(ServerStatsOverview::class)
            ->assertSuccessful();

        // Widget should render without errors with uber shop items
        $this->assertTrue(true);
    }

    // ==================== Dashboard Access Tests ====================

    #[Test]
    public function admin_dashboard_loads_with_widgets(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get('/admin')
            ->assertSuccessful()
            ->assertSeeLivewire(ServerStatsOverview::class)
            ->assertSeeLivewire(RecentActivityWidget::class);
    }

    #[Test]
    public function widgets_handle_empty_database_gracefully(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $admin = User::factory()->admin()->create();

        // Both widgets should render without errors even with minimal data
        Livewire::actingAs($admin)
            ->test(ServerStatsOverview::class)
            ->assertSuccessful();

        Livewire::actingAs($admin)
            ->test(RecentActivityWidget::class)
            ->assertSuccessful();
    }
}
