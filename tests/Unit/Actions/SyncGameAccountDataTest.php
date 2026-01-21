<?php

namespace Tests\Unit\Actions;

use App\Actions\SyncGameAccountData;
use App\Models\GameAccount;
use App\Models\SyncedCharacter;
use App\Models\User;
use App\XileRO\XileRO_Char;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SyncGameAccountDataTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_syncs_characters_from_game_database(): void
    {
        $user = User::factory()->create();
        $gameAccount = GameAccount::factory()->create([
            'user_id' => $user->id,
            'server' => 'xilero',
            'ragnarok_account_id' => 12345,
        ]);

        // Create characters in game database
        XileRO_Char::factory()->create([
            'account_id' => 12345,
            'name' => 'TestChar1',
            'base_level' => 99,
            'job_level' => 50,
        ]);

        XileRO_Char::factory()->create([
            'account_id' => 12345,
            'name' => 'TestChar2',
            'base_level' => 75,
            'job_level' => 40,
        ]);

        $syncedCount = SyncGameAccountData::run($user);

        $this->assertEquals(2, $syncedCount);
        $this->assertEquals(2, $gameAccount->syncedCharacters()->count());
    }

    #[Test]
    public function it_updates_existing_synced_characters(): void
    {
        $user = User::factory()->create();
        $gameAccount = GameAccount::factory()->create([
            'user_id' => $user->id,
            'server' => 'xilero',
            'ragnarok_account_id' => 12345,
        ]);

        $char = XileRO_Char::factory()->create([
            'account_id' => 12345,
            'name' => 'UpdateChar',
            'base_level' => 50,
        ]);

        // First sync
        SyncGameAccountData::run($user);

        $syncedChar = SyncedCharacter::where('char_id', $char->char_id)->first();
        $this->assertEquals(50, $syncedChar->base_level);

        // Update character in game DB
        $char->update(['base_level' => 99]);

        // Second sync
        SyncGameAccountData::run($user);

        // Should be updated, not duplicated
        $this->assertEquals(1, SyncedCharacter::where('char_id', $char->char_id)->count());
        $this->assertEquals(99, $syncedChar->fresh()->base_level);
    }

    #[Test]
    public function it_removes_deleted_characters(): void
    {
        $user = User::factory()->create();
        $gameAccount = GameAccount::factory()->create([
            'user_id' => $user->id,
            'server' => 'xilero',
            'ragnarok_account_id' => 12345,
        ]);

        $char1 = XileRO_Char::factory()->create([
            'account_id' => 12345,
            'name' => 'Char1',
        ]);

        $char2 = XileRO_Char::factory()->create([
            'account_id' => 12345,
            'name' => 'Char2',
        ]);

        // First sync - both characters
        SyncGameAccountData::run($user);
        $this->assertEquals(2, $gameAccount->syncedCharacters()->count());

        // Delete one character from game DB
        $char2->delete();

        // Second sync - should remove the deleted character
        SyncGameAccountData::run($user);

        $this->assertEquals(1, $gameAccount->syncedCharacters()->count());
        $this->assertNull(SyncedCharacter::where('char_id', $char2->char_id)->first());
    }

    #[Test]
    public function it_skips_accounts_without_ragnarok_id(): void
    {
        $user = User::factory()->create();
        GameAccount::factory()->create([
            'user_id' => $user->id,
            'ragnarok_account_id' => null,
        ]);

        $syncedCount = SyncGameAccountData::run($user);

        $this->assertEquals(0, $syncedCount);
    }

    #[Test]
    public function it_syncs_multiple_game_accounts(): void
    {
        $user = User::factory()->create();

        $gameAccount1 = GameAccount::factory()->create([
            'user_id' => $user->id,
            'server' => 'xilero',
            'ragnarok_account_id' => 11111,
        ]);

        $gameAccount2 = GameAccount::factory()->create([
            'user_id' => $user->id,
            'server' => 'xilero',
            'ragnarok_account_id' => 22222,
        ]);

        XileRO_Char::factory()->create(['account_id' => 11111]);
        XileRO_Char::factory()->create(['account_id' => 22222]);
        XileRO_Char::factory()->create(['account_id' => 22222]);

        $syncedCount = SyncGameAccountData::run($user);

        $this->assertEquals(3, $syncedCount);
        $this->assertEquals(1, $gameAccount1->syncedCharacters()->count());
        $this->assertEquals(2, $gameAccount2->syncedCharacters()->count());
    }

    #[Test]
    public function it_syncs_character_details(): void
    {
        $user = User::factory()->create();
        $gameAccount = GameAccount::factory()->create([
            'user_id' => $user->id,
            'server' => 'xilero',
            'ragnarok_account_id' => 12345,
        ]);

        XileRO_Char::factory()->create([
            'account_id' => 12345,
            'name' => 'DetailedChar',
            'base_level' => 99,
            'job_level' => 70,
            'zeny' => 1000000,
            'last_map' => 'prontera',
            'online' => 1,
        ]);

        SyncGameAccountData::run($user);

        $syncedChar = $gameAccount->syncedCharacters()->first();

        $this->assertEquals('DetailedChar', $syncedChar->name);
        $this->assertEquals(99, $syncedChar->base_level);
        $this->assertEquals(70, $syncedChar->job_level);
        $this->assertEquals(1000000, $syncedChar->zeny);
        $this->assertEquals('prontera', $syncedChar->last_map);
        $this->assertTrue($syncedChar->online);
    }

    #[Test]
    public function it_returns_total_synced_count(): void
    {
        $user = User::factory()->create();
        GameAccount::factory()->create([
            'user_id' => $user->id,
            'server' => 'xilero',
            'ragnarok_account_id' => 12345,
        ]);

        XileRO_Char::factory()->count(5)->create(['account_id' => 12345]);

        $syncedCount = SyncGameAccountData::run($user);

        $this->assertEquals(5, $syncedCount);
    }

    #[Test]
    public function it_returns_zero_for_user_without_game_accounts(): void
    {
        $user = User::factory()->create();

        $syncedCount = SyncGameAccountData::run($user);

        $this->assertEquals(0, $syncedCount);
    }

    #[Test]
    public function it_sets_synced_at_timestamp(): void
    {
        $user = User::factory()->create();
        $gameAccount = GameAccount::factory()->create([
            'user_id' => $user->id,
            'server' => 'xilero',
            'ragnarok_account_id' => 12345,
        ]);

        XileRO_Char::factory()->create(['account_id' => 12345]);

        SyncGameAccountData::run($user);

        $syncedChar = $gameAccount->syncedCharacters()->first();

        $this->assertNotNull($syncedChar);
        // Just verify synced_at was set (could be a timestamp string or Carbon)
        $this->assertNotNull($syncedChar->synced_at);
    }
}
