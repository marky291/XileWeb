<?php

namespace Tests\Feature\Filament;

use App\Filament\Pages\Moderation;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ModerationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    #[Test]
    public function guest_cannot_access_moderation_page(): void
    {
        $this->get('/admin/moderation')
            ->assertRedirect('/login');
    }

    #[Test]
    public function non_admin_user_cannot_access_moderation_page(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user)
            ->get('/admin/moderation')
            ->assertForbidden();
    }

    #[Test]
    public function admin_user_can_access_moderation_page(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get('/admin/moderation')
            ->assertOk();
    }

    #[Test]
    public function moderation_page_has_correct_navigation_group(): void
    {
        $this->assertEquals('Support', Moderation::getNavigationGroup());
    }

    #[Test]
    public function search_requires_minimum_characters(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(Moderation::class)
            ->set('data.search', 'a')
            ->call('searchAccounts')
            ->assertNotified('Search term too short');
    }

    #[Test]
    public function can_switch_between_servers(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(Moderation::class)
            ->assertSet('data.server', 'all')
            ->set('data.server', 'xileretro')
            ->assertSet('data.server', 'xileretro');
    }

    #[Test]
    public function get_account_state_label_returns_correct_labels(): void
    {
        $admin = User::factory()->admin()->create();

        $component = Livewire::actingAs($admin)
            ->test(Moderation::class);

        $this->assertEquals('Active', $component->instance()->getAccountStateLabel(0));
        $this->assertEquals('Banned', $component->instance()->getAccountStateLabel(5));
        $this->assertEquals('Server Locked', $component->instance()->getAccountStateLabel(1));
    }

    #[Test]
    public function get_account_state_color_returns_correct_colors(): void
    {
        $admin = User::factory()->admin()->create();

        $component = Livewire::actingAs($admin)
            ->test(Moderation::class);

        $this->assertEquals('success', $component->instance()->getAccountStateColor(0));
        $this->assertEquals('danger', $component->instance()->getAccountStateColor(5));
        $this->assertEquals('warning', $component->instance()->getAccountStateColor(1));
    }

    #[Test]
    public function can_clear_selection(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(Moderation::class)
            ->set('selectedAccount', [
                'account_id' => 1,
                'userid' => 'test',
                'email' => 'test@example.com',
                'last_ip' => '127.0.0.1',
                'lastlogin' => '2024-01-01 00:00:00',
                'logincount' => 1,
                'state' => 0,
                'unban_time' => 0,
                'group_id' => 0,
            ])
            ->set('banDuration', 48)
            ->set('banReason', 'Test reason')
            ->call('clearSelection')
            ->assertSet('selectedAccount', null)
            ->assertSet('banDuration', 24)
            ->assertSet('banReason', '');
    }
}
