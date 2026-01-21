<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\ApiTokenResource;
use App\Filament\Resources\ApiTokenResource\Pages\CreateApiToken;
use App\Filament\Resources\ApiTokenResource\Pages\ListApiTokens;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ApiTokenResourceTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function api_token_resource_has_correct_navigation_group(): void
    {
        $this->assertEquals('Website', ApiTokenResource::getNavigationGroup());
    }

    #[Test]
    public function guest_cannot_access_api_tokens_list(): void
    {
        $this->get('/admin/api-tokens')
            ->assertRedirect('/login');
    }

    #[Test]
    public function non_admin_cannot_access_api_tokens_list(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user)
            ->get('/admin/api-tokens')
            ->assertForbidden();
    }

    #[Test]
    public function admin_can_view_api_tokens_list(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $admin = User::factory()->admin()->create();
        $tokenOwner = User::factory()->create();
        $token = $tokenOwner->createToken('Test Token', ['read']);

        Livewire::actingAs($admin)
            ->test(ListApiTokens::class)
            ->assertSuccessful()
            ->assertCanSeeTableRecords([$token->accessToken]);
    }

    #[Test]
    public function admin_can_create_api_token(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $admin = User::factory()->admin()->create();
        $tokenOwner = User::factory()->create();

        Livewire::actingAs($admin)
            ->test(CreateApiToken::class)
            ->fillForm([
                'tokenable_id' => $tokenOwner->id,
                'name' => 'My API Token',
                'abilities' => ['read', 'write'],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $tokenOwner->id,
            'tokenable_type' => User::class,
            'name' => 'My API Token',
        ]);
    }

    #[Test]
    public function admin_can_create_api_token_with_expiration(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $admin = User::factory()->admin()->create();
        $tokenOwner = User::factory()->create();
        $expiresAt = now()->addDays(30)->startOfMinute();

        Livewire::actingAs($admin)
            ->test(CreateApiToken::class)
            ->fillForm([
                'tokenable_id' => $tokenOwner->id,
                'name' => 'Expiring Token',
                'abilities' => ['read'],
                'expires_at' => $expiresAt,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $tokenOwner->id,
            'name' => 'Expiring Token',
        ]);

        $token = $tokenOwner->tokens()->where('name', 'Expiring Token')->first();
        $this->assertNotNull($token->expires_at);
    }

    #[Test]
    public function admin_can_delete_api_token(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $admin = User::factory()->admin()->create();
        $tokenOwner = User::factory()->create();
        $token = $tokenOwner->createToken('Token to Delete', ['read']);

        Livewire::actingAs($admin)
            ->test(ListApiTokens::class)
            ->callTableAction('delete', $token->accessToken);

        $this->assertDatabaseMissing('personal_access_tokens', [
            'id' => $token->accessToken->id,
        ]);
    }

    #[Test]
    public function token_name_is_required(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $admin = User::factory()->admin()->create();
        $tokenOwner = User::factory()->create();

        Livewire::actingAs($admin)
            ->test(CreateApiToken::class)
            ->fillForm([
                'tokenable_id' => $tokenOwner->id,
                'name' => '',
                'abilities' => ['read'],
            ])
            ->call('create')
            ->assertHasFormErrors(['name' => 'required']);
    }

    #[Test]
    public function user_selection_is_required(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(CreateApiToken::class)
            ->fillForm([
                'tokenable_id' => null,
                'name' => 'Test Token',
                'abilities' => ['read'],
            ])
            ->call('create')
            ->assertHasFormErrors(['tokenable_id' => 'required']);
    }

    #[Test]
    public function admin_can_search_tokens_by_name(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $admin = User::factory()->admin()->create();
        $tokenOwner = User::factory()->create();
        $token1 = $tokenOwner->createToken('Production API', ['read']);
        $token2 = $tokenOwner->createToken('Development API', ['read']);

        Livewire::actingAs($admin)
            ->test(ListApiTokens::class)
            ->searchTable('Production')
            ->assertCanSeeTableRecords([$token1->accessToken])
            ->assertCanNotSeeTableRecords([$token2->accessToken]);
    }

    #[Test]
    public function admin_can_search_tokens_by_user_email(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $admin = User::factory()->admin()->create();
        $user1 = User::factory()->create(['email' => 'alice@example.com']);
        $user2 = User::factory()->create(['email' => 'bob@example.com']);
        $token1 = $user1->createToken('Alice Token', ['read']);
        $token2 = $user2->createToken('Bob Token', ['read']);

        Livewire::actingAs($admin)
            ->test(ListApiTokens::class)
            ->searchTable('alice@example.com')
            ->assertCanSeeTableRecords([$token1->accessToken])
            ->assertCanNotSeeTableRecords([$token2->accessToken]);
    }
}
