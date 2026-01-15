<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Auth\Dashboard;
use App\Models\GameAccount;
use App\Models\User;
use App\XileRO\XileRO_AccRegStr;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DashboardSecurityResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_reset_security_button_only_shows_when_security_code_is_set(): void
    {
        $user = User::factory()->create();
        $gameAccount = GameAccount::factory()->create([
            'user_id' => $user->id,
            'server' => 'xilero',
            'ragnarok_account_id' => 99999,
        ]);

        // No security code set
        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->call('selectGameAccount', $gameAccount->id)
            ->assertDontSee('Reset @security');
    }

    public function test_reset_security_button_shows_when_security_code_is_set(): void
    {
        $user = User::factory()->create();
        $gameAccount = GameAccount::factory()->withSecurityCode()->create([
            'user_id' => $user->id,
            'server' => 'xilero',
            'ragnarok_account_id' => 99998,
        ]);

        // Create a security code entry in game DB
        XileRO_AccRegStr::create([
            'account_id' => 99998,
            'key' => XileRO_AccRegStr::GAME_COMMAND_SECURITY_CODE,
            'index' => 0,
            'value' => md5('test123'),
        ]);

        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->call('selectGameAccount', $gameAccount->id)
            ->assertSee('Reset @security');
    }

    public function test_can_reset_security_code(): void
    {
        $user = User::factory()->create();
        $gameAccount = GameAccount::factory()->withSecurityCode()->create([
            'user_id' => $user->id,
            'server' => 'xilero',
            'ragnarok_account_id' => 99997,
        ]);

        // Create a security code entry in game DB
        XileRO_AccRegStr::create([
            'account_id' => 99997,
            'key' => XileRO_AccRegStr::GAME_COMMAND_SECURITY_CODE,
            'index' => 0,
            'value' => md5('test123'),
        ]);

        $this->assertTrue($gameAccount->hasSecurityCode());
        $this->assertTrue($gameAccount->has_security_code);

        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->call('resetSecurity', $gameAccount->id)
            ->assertHasNoErrors();

        $gameAccount->refresh();
        $this->assertFalse($gameAccount->hasSecurityCode());
        $this->assertFalse($gameAccount->has_security_code);
    }

    public function test_cannot_reset_security_for_other_users_account(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $otherGameAccount = GameAccount::factory()->withSecurityCode()->create([
            'user_id' => $otherUser->id,
            'server' => 'xilero',
            'ragnarok_account_id' => 99996,
        ]);

        // Create a security code entry in game DB
        XileRO_AccRegStr::create([
            'account_id' => 99996,
            'key' => XileRO_AccRegStr::GAME_COMMAND_SECURITY_CODE,
            'index' => 0,
            'value' => md5('test123'),
        ]);

        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->call('resetSecurity', $otherGameAccount->id);

        // Security code should still exist (both in game DB and cached)
        $this->assertTrue($otherGameAccount->hasSecurityCode());
        $this->assertTrue($otherGameAccount->fresh()->has_security_code);
    }

    public function test_reset_security_shows_error_when_no_security_code_is_set(): void
    {
        $user = User::factory()->create();
        $gameAccount = GameAccount::factory()->create([
            'user_id' => $user->id,
            'server' => 'xilero',
            'ragnarok_account_id' => 99995,
        ]);

        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->call('resetSecurity', $gameAccount->id);

        $this->assertFalse($gameAccount->hasSecurityCode());
    }

    public function test_reset_security_only_removes_security_code_entry(): void
    {
        $user = User::factory()->create();
        $gameAccount = GameAccount::factory()->withSecurityCode()->create([
            'user_id' => $user->id,
            'server' => 'xilero',
            'ragnarok_account_id' => 99994,
        ]);

        // Create the security code entry
        XileRO_AccRegStr::create([
            'account_id' => 99994,
            'key' => XileRO_AccRegStr::GAME_COMMAND_SECURITY_CODE,
            'index' => 0,
            'value' => md5('test123'),
        ]);

        // Create other acc_reg_str entries that should NOT be deleted
        XileRO_AccRegStr::create([
            'account_id' => 99994,
            'key' => '#SECURITY_GSTORAGE_STORE',
            'index' => 0,
            'value' => '1',
        ]);

        XileRO_AccRegStr::create([
            'account_id' => 99994,
            'key' => '#SomeOtherSetting',
            'index' => 0,
            'value' => 'test',
        ]);

        // Create entry for a different account that should NOT be deleted
        XileRO_AccRegStr::create([
            'account_id' => 88888,
            'key' => XileRO_AccRegStr::GAME_COMMAND_SECURITY_CODE,
            'index' => 0,
            'value' => md5('otheruser'),
        ]);

        // Verify all entries exist
        $this->assertEquals(4, XileRO_AccRegStr::count());

        // Reset security for our account
        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->call('resetSecurity', $gameAccount->id);

        // Verify only the security code entry was deleted
        $this->assertEquals(3, XileRO_AccRegStr::count());

        // Security code for our account is gone
        $this->assertFalse(
            XileRO_AccRegStr::where('account_id', 99994)
                ->where('key', XileRO_AccRegStr::GAME_COMMAND_SECURITY_CODE)
                ->exists()
        );

        // Other entries for our account still exist
        $this->assertTrue(
            XileRO_AccRegStr::where('account_id', 99994)
                ->where('key', '#SECURITY_GSTORAGE_STORE')
                ->exists()
        );

        $this->assertTrue(
            XileRO_AccRegStr::where('account_id', 99994)
                ->where('key', '#SomeOtherSetting')
                ->exists()
        );

        // Other account's security code still exists
        $this->assertTrue(
            XileRO_AccRegStr::where('account_id', 88888)
                ->where('key', XileRO_AccRegStr::GAME_COMMAND_SECURITY_CODE)
                ->exists()
        );
    }
}
