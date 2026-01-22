<?php

namespace Tests\Feature\Api;

use App\Models\Npc;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class NpcApiTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function unauthenticated_request_returns_unauthorized(): void
    {
        $this->getJson('/api/v1/npcs')
            ->assertUnauthorized();
    }

    #[Test]
    public function token_without_read_ability_returns_forbidden(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('write-only', ['write']);

        $this->withToken($token->plainTextToken)
            ->getJson('/api/v1/npcs')
            ->assertForbidden();
    }

    #[Test]
    public function token_with_read_ability_can_list_npcs(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('read-token', ['read']);
        $npcs = Npc::factory()->count(3)->create();

        $response = $this->withToken($token->plainTextToken)
            ->getJson('/api/v1/npcs')
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'npc_id',
                        'name',
                        'sprite',
                        'image_url',
                    ],
                ],
                'links',
                'meta',
            ]);

        $this->assertCount(3, $response->json('data'));
    }

    #[Test]
    public function token_with_read_ability_can_show_single_npc(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('read-token', ['read']);
        $npc = Npc::factory()->create([
            'name' => 'JT_KAFRA',
            'npc_id' => 12345,
        ]);

        $this->withToken($token->plainTextToken)
            ->getJson("/api/v1/npcs/{$npc->id}")
            ->assertOk()
            ->assertJson([
                'data' => [
                    'id' => $npc->id,
                    'npc_id' => 12345,
                    'name' => 'JT_KAFRA',
                ],
            ]);
    }

    #[Test]
    public function can_search_npcs_by_name(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('read-token', ['read']);
        $kafra = Npc::factory()->create(['name' => 'JT_KAFRA']);
        $warp = Npc::factory()->create(['name' => 'JT_WARPNPC']);

        $response = $this->withToken($token->plainTextToken)
            ->getJson('/api/v1/npcs?search=KAFRA')
            ->assertOk();

        $this->assertCount(1, $response->json('data'));
        $this->assertEquals($kafra->id, $response->json('data.0.id'));
    }

    #[Test]
    public function can_search_npcs_by_npc_id(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('read-token', ['read']);
        $npc = Npc::factory()->create(['npc_id' => 99999]);
        Npc::factory()->create(['npc_id' => 11111]);

        $response = $this->withToken($token->plainTextToken)
            ->getJson('/api/v1/npcs?search=99999')
            ->assertOk();

        $this->assertCount(1, $response->json('data'));
        $this->assertEquals($npc->id, $response->json('data.0.id'));
    }

    #[Test]
    public function can_filter_npcs_by_sprite(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('read-token', ['read']);
        $hidden = Npc::factory()->create(['sprite' => 'HIDDEN_WARP_NPC']);
        Npc::factory()->create(['sprite' => '1_etc_01']);

        $response = $this->withToken($token->plainTextToken)
            ->getJson('/api/v1/npcs?sprite=HIDDEN')
            ->assertOk();

        $this->assertCount(1, $response->json('data'));
        $this->assertEquals($hidden->id, $response->json('data.0.id'));
    }

    #[Test]
    public function can_filter_npcs_by_multiple_npc_ids(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('read-token', ['read']);
        $npc1 = Npc::factory()->create(['npc_id' => 1001]);
        $npc2 = Npc::factory()->create(['npc_id' => 1002]);
        Npc::factory()->create(['npc_id' => 1003]);

        $response = $this->withToken($token->plainTextToken)
            ->getJson('/api/v1/npcs?ids=1001,1002')
            ->assertOk();

        $this->assertCount(2, $response->json('data'));
        $npcIds = collect($response->json('data'))->pluck('npc_id')->toArray();
        $this->assertContains(1001, $npcIds);
        $this->assertContains(1002, $npcIds);
    }

    #[Test]
    public function pagination_respects_per_page_parameter(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('read-token', ['read']);
        Npc::factory()->count(20)->create();

        $response = $this->withToken($token->plainTextToken)
            ->getJson('/api/v1/npcs?per_page=5')
            ->assertOk();

        $this->assertCount(5, $response->json('data'));
        $this->assertEquals(5, $response->json('meta.per_page'));
    }

    #[Test]
    public function pagination_max_per_page_is_100(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('read-token', ['read']);
        Npc::factory()->count(5)->create();

        $response = $this->withToken($token->plainTextToken)
            ->getJson('/api/v1/npcs?per_page=999')
            ->assertOk();

        $this->assertEquals(100, $response->json('meta.per_page'));
    }

    #[Test]
    public function bulk_endpoint_returns_multiple_npcs_by_npc_id(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('read-token', ['read']);
        Npc::factory()->create(['npc_id' => 2001]);
        Npc::factory()->create(['npc_id' => 2002]);
        Npc::factory()->create(['npc_id' => 2003]);
        Npc::factory()->create(['npc_id' => 2004]);

        $response = $this->withToken($token->plainTextToken)
            ->postJson('/api/v1/npcs/bulk', ['ids' => '2001,2002,2003'])
            ->assertOk();

        $this->assertCount(3, $response->json('data'));
        $npcIds = collect($response->json('data'))->pluck('npc_id')->toArray();
        $this->assertContains(2001, $npcIds);
        $this->assertContains(2002, $npcIds);
        $this->assertContains(2003, $npcIds);
    }

    #[Test]
    public function bulk_endpoint_requires_ids_parameter(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('read-token', ['read']);

        $this->withToken($token->plainTextToken)
            ->postJson('/api/v1/npcs/bulk', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['ids']);
    }

    #[Test]
    public function bulk_endpoint_returns_empty_for_nonexistent_ids(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('read-token', ['read']);

        $response = $this->withToken($token->plainTextToken)
            ->postJson('/api/v1/npcs/bulk', ['ids' => '99998,99999'])
            ->assertOk();

        $this->assertCount(0, $response->json('data'));
    }

    #[Test]
    public function bulk_endpoint_is_limited_to_100_npcs(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('read-token', ['read']);

        $npcs = Npc::factory()->count(110)->create();
        $ids = $npcs->pluck('npc_id')->implode(',');

        $response = $this->withToken($token->plainTextToken)
            ->postJson('/api/v1/npcs/bulk', ['ids' => $ids])
            ->assertOk();

        $this->assertLessThanOrEqual(100, count($response->json('data')));
    }

    #[Test]
    public function expired_token_returns_unauthorized(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('expired-token', ['read'], now()->subDay());

        $this->withToken($token->plainTextToken)
            ->getJson('/api/v1/npcs')
            ->assertUnauthorized();
    }

    #[Test]
    public function non_expiring_token_works_indefinitely(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('permanent-token', ['read'], null);
        Npc::factory()->create();

        $this->withToken($token->plainTextToken)
            ->getJson('/api/v1/npcs')
            ->assertOk();
    }

    #[Test]
    public function npc_resource_includes_image_url(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('read-token', ['read']);
        $npc = Npc::factory()->create([
            'npc_id' => 12345,
        ]);

        $response = $this->withToken($token->plainTextToken)
            ->getJson("/api/v1/npcs/{$npc->id}")
            ->assertOk();

        $this->assertStringContainsString('npc', $response->json('data.image_url'));
        $this->assertStringContainsString('12345', $response->json('data.image_url'));
        $this->assertStringContainsString('.png', $response->json('data.image_url'));
    }
}
