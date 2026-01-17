<?php

namespace Tests\Unit\Actions;

use App\Actions\TransferLegacyUberBalance;
use App\Models\GameAccount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TransferLegacyUberBalanceTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function transfers_legacy_uber_balance_to_user(): void
    {
        $user = User::factory()->create(['uber_balance' => 100]);
        $gameAccount = GameAccount::factory()->create([
            'user_id' => $user->id,
            'legacy_uber_balance' => 50,
        ]);

        $transferred = TransferLegacyUberBalance::run($gameAccount, $user);

        $this->assertEquals(50, $transferred);
        $this->assertEquals(150, $user->fresh()->uber_balance);
        $this->assertEquals(0, $gameAccount->fresh()->legacy_uber_balance);
    }

    #[Test]
    public function returns_zero_when_no_legacy_balance(): void
    {
        $user = User::factory()->create(['uber_balance' => 100]);
        $gameAccount = GameAccount::factory()->create([
            'user_id' => $user->id,
            'legacy_uber_balance' => 0,
        ]);

        $transferred = TransferLegacyUberBalance::run($gameAccount, $user);

        $this->assertEquals(0, $transferred);
        $this->assertEquals(100, $user->fresh()->uber_balance);
    }

    #[Test]
    public function handles_large_legacy_balance(): void
    {
        $user = User::factory()->create(['uber_balance' => 0]);
        $gameAccount = GameAccount::factory()->create([
            'user_id' => $user->id,
            'legacy_uber_balance' => 10000,
        ]);

        $transferred = TransferLegacyUberBalance::run($gameAccount, $user);

        $this->assertEquals(10000, $transferred);
        $this->assertEquals(10000, $user->fresh()->uber_balance);
        $this->assertEquals(0, $gameAccount->fresh()->legacy_uber_balance);
    }

    #[Test]
    public function only_transfers_once(): void
    {
        $user = User::factory()->create(['uber_balance' => 0]);
        $gameAccount = GameAccount::factory()->create([
            'user_id' => $user->id,
            'legacy_uber_balance' => 100,
        ]);

        // First transfer
        $firstTransfer = TransferLegacyUberBalance::run($gameAccount, $user);
        $this->assertEquals(100, $firstTransfer);
        $this->assertEquals(100, $user->fresh()->uber_balance);

        // Second transfer should return 0 since balance was cleared
        $gameAccount->refresh();
        $secondTransfer = TransferLegacyUberBalance::run($gameAccount, $user);
        $this->assertEquals(0, $secondTransfer);
        $this->assertEquals(100, $user->fresh()->uber_balance);
    }
}
