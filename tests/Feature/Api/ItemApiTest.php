<?php

namespace Tests\Feature\Api;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ItemApiTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function unauthenticated_request_returns_unauthorized(): void
    {
        $this->getJson('/api/v1/items')
            ->assertUnauthorized();
    }

    #[Test]
    public function token_without_read_ability_returns_forbidden(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('write-only', ['write']);

        $this->withToken($token->plainTextToken)
            ->getJson('/api/v1/items')
            ->assertForbidden();
    }

    #[Test]
    public function token_with_read_ability_can_list_items(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('read-token', ['read']);
        $items = Item::factory()->count(3)->create();

        $response = $this->withToken($token->plainTextToken)
            ->getJson('/api/v1/items')
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'item_id',
                        'aegis_name',
                        'name',
                        'description',
                        'type',
                        'icon_url',
                        'collection_url',
                    ],
                ],
                'links',
                'meta',
            ]);

        $this->assertCount(3, $response->json('data'));
    }

    #[Test]
    public function token_with_read_ability_can_show_single_item(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('read-token', ['read']);
        $item = Item::factory()->create([
            'name' => 'Test Sword',
            'item_id' => 12345,
        ]);

        $this->withToken($token->plainTextToken)
            ->getJson("/api/v1/items/{$item->id}")
            ->assertOk()
            ->assertJson([
                'data' => [
                    'id' => $item->id,
                    'item_id' => 12345,
                    'name' => 'Test Sword',
                ],
            ]);
    }

    #[Test]
    public function can_search_items_by_name(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('read-token', ['read']);
        $sword = Item::factory()->create(['name' => 'Excalibur Sword']);
        $shield = Item::factory()->create(['name' => 'Iron Shield']);

        $response = $this->withToken($token->plainTextToken)
            ->getJson('/api/v1/items?search=Sword')
            ->assertOk();

        $this->assertCount(1, $response->json('data'));
        $this->assertEquals($sword->id, $response->json('data.0.id'));
    }

    #[Test]
    public function can_search_items_by_aegis_name(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('read-token', ['read']);
        $item = Item::factory()->create(['aegis_name' => 'Bloody_Roar']);
        Item::factory()->create(['aegis_name' => 'Iron_Shield']);

        $response = $this->withToken($token->plainTextToken)
            ->getJson('/api/v1/items?search=Bloody')
            ->assertOk();

        $this->assertCount(1, $response->json('data'));
        $this->assertEquals($item->id, $response->json('data.0.id'));
    }

    #[Test]
    public function can_search_items_by_item_id(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('read-token', ['read']);
        $item = Item::factory()->create(['item_id' => 99999]);
        Item::factory()->create(['item_id' => 11111]);

        $response = $this->withToken($token->plainTextToken)
            ->getJson('/api/v1/items?search=99999')
            ->assertOk();

        $this->assertCount(1, $response->json('data'));
        $this->assertEquals($item->id, $response->json('data.0.id'));
    }

    #[Test]
    public function can_filter_items_by_type(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('read-token', ['read']);
        $weapon = Item::factory()->weapon()->create();
        $armor = Item::factory()->armor()->create();

        $response = $this->withToken($token->plainTextToken)
            ->getJson('/api/v1/items?type=Weapon')
            ->assertOk();

        $this->assertCount(1, $response->json('data'));
        $this->assertEquals($weapon->id, $response->json('data.0.id'));
    }

    #[Test]
    public function can_filter_items_by_is_xileretro(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('read-token', ['read']);
        $xileroItem = Item::factory()->create(['is_xileretro' => false]);
        $retroItem = Item::factory()->xileretro()->create();

        $response = $this->withToken($token->plainTextToken)
            ->getJson('/api/v1/items?is_xileretro=true')
            ->assertOk();

        $this->assertCount(1, $response->json('data'));
        $this->assertEquals($retroItem->id, $response->json('data.0.id'));
    }

    #[Test]
    public function pagination_respects_per_page_parameter(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('read-token', ['read']);
        Item::factory()->count(20)->create();

        $response = $this->withToken($token->plainTextToken)
            ->getJson('/api/v1/items?per_page=5')
            ->assertOk();

        $this->assertCount(5, $response->json('data'));
        $this->assertEquals(5, $response->json('meta.per_page'));
    }

    #[Test]
    public function pagination_max_per_page_is_100(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('read-token', ['read']);
        Item::factory()->count(5)->create();

        $response = $this->withToken($token->plainTextToken)
            ->getJson('/api/v1/items?per_page=999')
            ->assertOk();

        $this->assertEquals(100, $response->json('meta.per_page'));
    }

    #[Test]
    public function expired_token_returns_unauthorized(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('expired-token', ['read'], now()->subDay());

        $this->withToken($token->plainTextToken)
            ->getJson('/api/v1/items')
            ->assertUnauthorized();
    }

    #[Test]
    public function non_expiring_token_works_indefinitely(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('permanent-token', ['read'], null);
        Item::factory()->create();

        $this->withToken($token->plainTextToken)
            ->getJson('/api/v1/items')
            ->assertOk();
    }

    #[Test]
    public function item_resource_includes_icon_and_collection_urls(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('read-token', ['read']);
        $item = Item::factory()->create([
            'item_id' => 12345,
            'is_xileretro' => false,
        ]);

        $response = $this->withToken($token->plainTextToken)
            ->getJson("/api/v1/items/{$item->id}")
            ->assertOk();

        $this->assertEquals('/assets/xilero/item_icons/12345.png', $response->json('data.icon_url'));
        $this->assertEquals('/assets/xilero/item_collection/12345.png', $response->json('data.collection_url'));
    }

    #[Test]
    public function xileretro_item_uses_retro_asset_paths(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('read-token', ['read']);
        $item = Item::factory()->xileretro()->create([
            'item_id' => 54321,
        ]);

        $response = $this->withToken($token->plainTextToken)
            ->getJson("/api/v1/items/{$item->id}")
            ->assertOk();

        $this->assertEquals('/assets/retro/item_icons/54321.png', $response->json('data.icon_url'));
        $this->assertEquals('/assets/retro/item_collection/54321.png', $response->json('data.collection_url'));
    }
}
