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

        $this->assertStringContainsString('xilero', $response->json('data.icon_url'));
        $this->assertStringContainsString('12345', $response->json('data.icon_url'));
        $this->assertStringContainsString('xilero', $response->json('data.collection_url'));
        $this->assertStringContainsString('12345', $response->json('data.collection_url'));
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

        $this->assertStringContainsString('retro', $response->json('data.icon_url'));
        $this->assertStringContainsString('54321', $response->json('data.icon_url'));
    }

    #[Test]
    public function can_filter_items_by_multiple_item_ids(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('read-token', ['read']);
        $item1 = Item::factory()->create(['item_id' => 1001]);
        $item2 = Item::factory()->create(['item_id' => 1002]);
        Item::factory()->create(['item_id' => 1003]);

        $response = $this->withToken($token->plainTextToken)
            ->getJson('/api/v1/items?ids=1001,1002')
            ->assertOk();

        $this->assertCount(2, $response->json('data'));
        $itemIds = collect($response->json('data'))->pluck('item_id')->toArray();
        $this->assertContains(1001, $itemIds);
        $this->assertContains(1002, $itemIds);
    }

    #[Test]
    public function can_filter_items_by_multiple_types(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('read-token', ['read']);
        $weapon = Item::factory()->weapon()->create();
        $armor = Item::factory()->armor()->create();
        Item::factory()->healing()->create();

        $response = $this->withToken($token->plainTextToken)
            ->getJson('/api/v1/items?type=Weapon,Armor')
            ->assertOk();

        $this->assertCount(2, $response->json('data'));
    }

    #[Test]
    public function can_filter_items_by_subtype(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('read-token', ['read']);
        $dagger = Item::factory()->weapon()->create(['subtype' => 'Dagger']);
        Item::factory()->weapon()->create(['subtype' => '1hSword']);

        $response = $this->withToken($token->plainTextToken)
            ->getJson('/api/v1/items?subtype=Dagger')
            ->assertOk();

        $this->assertCount(1, $response->json('data'));
        $this->assertEquals($dagger->id, $response->json('data.0.id'));
    }

    #[Test]
    public function can_filter_items_by_multiple_subtypes(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('read-token', ['read']);
        Item::factory()->weapon()->create(['subtype' => 'Dagger']);
        Item::factory()->weapon()->create(['subtype' => '1hSword']);
        Item::factory()->weapon()->create(['subtype' => 'Bow']);

        $response = $this->withToken($token->plainTextToken)
            ->getJson('/api/v1/items?subtype=Dagger,1hSword')
            ->assertOk();

        $this->assertCount(2, $response->json('data'));
    }

    #[Test]
    public function can_filter_items_by_min_slots(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('read-token', ['read']);
        $slotted = Item::factory()->create(['slots' => 3]);
        Item::factory()->create(['slots' => 1]);

        $response = $this->withToken($token->plainTextToken)
            ->getJson('/api/v1/items?min_slots=2')
            ->assertOk();

        $this->assertCount(1, $response->json('data'));
        $this->assertEquals($slotted->id, $response->json('data.0.id'));
    }

    #[Test]
    public function can_filter_items_by_refineable(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('read-token', ['read']);
        $refineable = Item::factory()->create(['refineable' => true]);
        Item::factory()->create(['refineable' => false]);

        $response = $this->withToken($token->plainTextToken)
            ->getJson('/api/v1/items?refineable=true')
            ->assertOk();

        $this->assertCount(1, $response->json('data'));
        $this->assertEquals($refineable->id, $response->json('data.0.id'));
    }

    #[Test]
    public function can_filter_items_by_job(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('read-token', ['read']);
        $knightItem = Item::factory()->create(['jobs' => ['Knight', 'Crusader']]);
        Item::factory()->create(['jobs' => ['Mage', 'Wizard']]);

        $response = $this->withToken($token->plainTextToken)
            ->getJson('/api/v1/items?job=Knight')
            ->assertOk();

        $this->assertCount(1, $response->json('data'));
        $this->assertEquals($knightItem->id, $response->json('data.0.id'));
    }

    #[Test]
    public function bulk_endpoint_returns_multiple_items_by_item_id(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('read-token', ['read']);
        $item1 = Item::factory()->create(['item_id' => 2001]);
        $item2 = Item::factory()->create(['item_id' => 2002]);
        $item3 = Item::factory()->create(['item_id' => 2003]);
        Item::factory()->create(['item_id' => 2004]);

        $response = $this->withToken($token->plainTextToken)
            ->postJson('/api/v1/items/bulk', ['ids' => '2001,2002,2003'])
            ->assertOk();

        $this->assertCount(3, $response->json('data'));
        $itemIds = collect($response->json('data'))->pluck('item_id')->toArray();
        $this->assertContains(2001, $itemIds);
        $this->assertContains(2002, $itemIds);
        $this->assertContains(2003, $itemIds);
    }

    #[Test]
    public function bulk_endpoint_requires_ids_parameter(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('read-token', ['read']);

        $this->withToken($token->plainTextToken)
            ->postJson('/api/v1/items/bulk', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['ids']);
    }

    #[Test]
    public function bulk_endpoint_returns_empty_for_nonexistent_ids(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('read-token', ['read']);

        $response = $this->withToken($token->plainTextToken)
            ->postJson('/api/v1/items/bulk', ['ids' => '99998,99999'])
            ->assertOk();

        $this->assertCount(0, $response->json('data'));
    }

    #[Test]
    public function bulk_endpoint_is_limited_to_100_items(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('read-token', ['read']);

        $items = Item::factory()->count(110)->create();
        $ids = $items->pluck('item_id')->implode(',');

        $response = $this->withToken($token->plainTextToken)
            ->postJson('/api/v1/items/bulk', ['ids' => $ids])
            ->assertOk();

        $this->assertLessThanOrEqual(100, count($response->json('data')));
    }
}
