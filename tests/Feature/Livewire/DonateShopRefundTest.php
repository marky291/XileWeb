<?php

namespace Tests\Feature\Livewire;

use App\Livewire\DonateShop;
use App\Models\GameAccount;
use App\Models\UberShopCategory;
use App\Models\UberShopItem;
use App\Models\UberShopPurchase;
use App\Models\User;
use App\XileRO\XileRO_Char;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DonateShopRefundTest extends TestCase
{
    use RefreshDatabase;

    // ============================================
    // Cancel Pending Purchase Tests
    // ============================================

    #[Test]
    public function user_can_cancel_pending_purchase(): void
    {
        $user = User::factory()->create(['uber_balance' => 100]);
        $gameAccount = GameAccount::factory()->create([
            'user_id' => $user->id,
            'ragnarok_account_id' => 12345,
        ]);

        $item = UberShopItem::factory()->create([
            'uber_cost' => 50,
            'stock' => 10,
        ]);

        $purchase = UberShopPurchase::factory()->create([
            'account_id' => $gameAccount->ragnarok_account_id,
            'shop_item_id' => $item->id,
            'uber_cost' => 50,
            'status' => UberShopPurchase::STATUS_PENDING,
        ]);

        Livewire::actingAs($user)
            ->test(DonateShop::class)
            ->call('cancelPendingPurchase', $purchase->id);

        // User should get ubers refunded
        $this->assertEquals(150, $user->fresh()->uber_balance);

        // Stock should be restored
        $this->assertEquals(11, $item->fresh()->stock);

        // Purchase should be cancelled
        $this->assertEquals(UberShopPurchase::STATUS_CANCELLED, $purchase->fresh()->status);
    }

    #[Test]
    public function user_cannot_cancel_other_users_purchase(): void
    {
        $user = User::factory()->create(['uber_balance' => 100]);
        $otherUser = User::factory()->create();

        $otherGameAccount = GameAccount::factory()->create([
            'user_id' => $otherUser->id,
            'ragnarok_account_id' => 99999,
        ]);

        $purchase = UberShopPurchase::factory()->create([
            'account_id' => $otherGameAccount->ragnarok_account_id,
            'uber_cost' => 50,
            'status' => UberShopPurchase::STATUS_PENDING,
        ]);

        Livewire::actingAs($user)
            ->test(DonateShop::class)
            ->call('cancelPendingPurchase', $purchase->id);

        // User balance should not change
        $this->assertEquals(100, $user->fresh()->uber_balance);

        // Purchase should still be pending
        $this->assertEquals(UberShopPurchase::STATUS_PENDING, $purchase->fresh()->status);
    }

    #[Test]
    public function user_cannot_cancel_claimed_purchase(): void
    {
        $user = User::factory()->create(['uber_balance' => 100]);
        $gameAccount = GameAccount::factory()->create([
            'user_id' => $user->id,
            'ragnarok_account_id' => 12345,
        ]);

        $purchase = UberShopPurchase::factory()->create([
            'account_id' => $gameAccount->ragnarok_account_id,
            'uber_cost' => 50,
            'status' => UberShopPurchase::STATUS_CLAIMED,
            'claimed_at' => now(),
        ]);

        Livewire::actingAs($user)
            ->test(DonateShop::class)
            ->call('cancelPendingPurchase', $purchase->id);

        // User balance should not change
        $this->assertEquals(100, $user->fresh()->uber_balance);

        // Purchase should still be claimed
        $this->assertEquals(UberShopPurchase::STATUS_CLAIMED, $purchase->fresh()->status);
    }

    #[Test]
    public function guest_cannot_cancel_purchase(): void
    {
        $purchase = UberShopPurchase::factory()->create([
            'status' => UberShopPurchase::STATUS_PENDING,
        ]);

        Livewire::test(DonateShop::class)
            ->call('cancelPendingPurchase', $purchase->id);

        // Purchase should still be pending
        $this->assertEquals(UberShopPurchase::STATUS_PENDING, $purchase->fresh()->status);
    }

    // ============================================
    // Refund Claimed Purchase Tests
    // ============================================

    #[Test]
    public function can_refund_is_disabled(): void
    {
        $component = new DonateShop;

        $pendingPurchase = UberShopPurchase::factory()->make([
            'status' => UberShopPurchase::STATUS_PENDING,
        ]);

        $this->assertFalse($component->canRefund($pendingPurchase));

        $claimedPurchase = UberShopPurchase::factory()->make([
            'status' => UberShopPurchase::STATUS_CLAIMED,
            'claimed_at' => now(),
        ]);

        // Refunds are currently disabled
        $this->assertFalse($component->canRefund($claimedPurchase));
    }

    #[Test]
    public function can_refund_returns_false_regardless_of_time(): void
    {
        $component = new DonateShop;

        // Refunds are disabled, so all return false regardless of time
        $recentPurchase = UberShopPurchase::factory()->make([
            'status' => UberShopPurchase::STATUS_CLAIMED,
            'claimed_at' => now()->subHours(23),
        ]);

        $this->assertFalse($component->canRefund($recentPurchase));

        $oldPurchase = UberShopPurchase::factory()->make([
            'status' => UberShopPurchase::STATUS_CLAIMED,
            'claimed_at' => now()->subHours(25),
        ]);

        $this->assertFalse($component->canRefund($oldPurchase));
    }

    #[Test]
    public function can_refund_requires_claimed_at(): void
    {
        $component = new DonateShop;

        $purchaseWithoutClaimedAt = UberShopPurchase::factory()->make([
            'status' => UberShopPurchase::STATUS_CLAIMED,
            'claimed_at' => null,
        ]);

        $this->assertFalse($component->canRefund($purchaseWithoutClaimedAt));
    }

    #[Test]
    public function refund_hours_is_24(): void
    {
        $component = new DonateShop;
        $this->assertEquals(24, $component->refundHours());
    }

    #[Test]
    public function guest_cannot_refund_purchase(): void
    {
        $purchase = UberShopPurchase::factory()->create([
            'status' => UberShopPurchase::STATUS_CLAIMED,
            'claimed_at' => now(),
        ]);

        Livewire::test(DonateShop::class)
            ->call('refundPurchase', $purchase->id);

        // Purchase should still be claimed
        $this->assertEquals(UberShopPurchase::STATUS_CLAIMED, $purchase->fresh()->status);
    }

    #[Test]
    public function user_cannot_refund_expired_purchase(): void
    {
        $user = User::factory()->create(['uber_balance' => 100]);
        $gameAccount = GameAccount::factory()->create([
            'user_id' => $user->id,
            'ragnarok_account_id' => 12345,
            'server' => GameAccount::SERVER_XILERO,
        ]);

        $purchase = UberShopPurchase::factory()->create([
            'account_id' => $gameAccount->ragnarok_account_id,
            'uber_cost' => 50,
            'status' => UberShopPurchase::STATUS_CLAIMED,
            'claimed_at' => now()->subHours(25), // Expired
        ]);

        Livewire::actingAs($user)
            ->test(DonateShop::class)
            ->call('refundPurchase', $purchase->id);

        // User balance should not change
        $this->assertEquals(100, $user->fresh()->uber_balance);

        // Purchase should still be claimed
        $this->assertEquals(UberShopPurchase::STATUS_CLAIMED, $purchase->fresh()->status);
    }

    #[Test]
    public function user_cannot_refund_other_users_purchase(): void
    {
        $user = User::factory()->create(['uber_balance' => 100]);
        $otherUser = User::factory()->create();

        $otherGameAccount = GameAccount::factory()->create([
            'user_id' => $otherUser->id,
            'ragnarok_account_id' => 99999,
        ]);

        $purchase = UberShopPurchase::factory()->create([
            'account_id' => $otherGameAccount->ragnarok_account_id,
            'uber_cost' => 50,
            'status' => UberShopPurchase::STATUS_CLAIMED,
            'claimed_at' => now(),
        ]);

        Livewire::actingAs($user)
            ->test(DonateShop::class)
            ->call('refundPurchase', $purchase->id);

        // User balance should not change
        $this->assertEquals(100, $user->fresh()->uber_balance);

        // Purchase should still be claimed
        $this->assertEquals(UberShopPurchase::STATUS_CLAIMED, $purchase->fresh()->status);
    }

    // ============================================
    // Pending Purchases Retrieval Tests
    // ============================================

    #[Test]
    public function pending_purchases_returns_only_pending(): void
    {
        $user = User::factory()->create();
        $gameAccount = GameAccount::factory()->create([
            'user_id' => $user->id,
            'ragnarok_account_id' => 12345,
        ]);

        UberShopPurchase::factory()->create([
            'account_id' => $gameAccount->ragnarok_account_id,
            'status' => UberShopPurchase::STATUS_PENDING,
        ]);

        UberShopPurchase::factory()->create([
            'account_id' => $gameAccount->ragnarok_account_id,
            'status' => UberShopPurchase::STATUS_CLAIMED,
        ]);

        UberShopPurchase::factory()->create([
            'account_id' => $gameAccount->ragnarok_account_id,
            'status' => UberShopPurchase::STATUS_CANCELLED,
        ]);

        $component = Livewire::actingAs($user)->test(DonateShop::class);

        $pendingPurchases = $component->instance()->pendingPurchases();

        $this->assertCount(1, $pendingPurchases);
        $this->assertEquals(UberShopPurchase::STATUS_PENDING, $pendingPurchases->first()->status);
    }

    #[Test]
    public function pending_purchases_returns_empty_for_guest(): void
    {
        $component = Livewire::test(DonateShop::class);

        $pendingPurchases = $component->instance()->pendingPurchases();

        $this->assertCount(0, $pendingPurchases);
    }

    // ============================================
    // Claimed Purchases Retrieval Tests
    // ============================================

    #[Test]
    public function claimed_purchases_returns_only_recent_claimed(): void
    {
        $user = User::factory()->create();
        $gameAccount = GameAccount::factory()->create([
            'user_id' => $user->id,
            'ragnarok_account_id' => 12345,
        ]);

        // Recent claimed (within 7 days)
        UberShopPurchase::factory()->create([
            'account_id' => $gameAccount->ragnarok_account_id,
            'status' => UberShopPurchase::STATUS_CLAIMED,
            'claimed_at' => now()->subDays(3),
        ]);

        // Old claimed (more than 7 days)
        UberShopPurchase::factory()->create([
            'account_id' => $gameAccount->ragnarok_account_id,
            'status' => UberShopPurchase::STATUS_CLAIMED,
            'claimed_at' => now()->subDays(10),
        ]);

        // Pending (should not appear)
        UberShopPurchase::factory()->create([
            'account_id' => $gameAccount->ragnarok_account_id,
            'status' => UberShopPurchase::STATUS_PENDING,
        ]);

        // Set filter to 'all' to test the 7-day window behavior
        $component = Livewire::actingAs($user)
            ->test(DonateShop::class)
            ->set('recentFilter', 'all');

        $claimedPurchases = $component->instance()->claimedPurchases();

        $this->assertCount(1, $claimedPurchases);
    }

    // ============================================
    // Input Sanitization Tests
    // ============================================

    #[Test]
    public function donate_shop_has_input_sanitization_methods(): void
    {
        $component = new DonateShop;

        $this->assertTrue(method_exists($component, 'updatingCategory'));
        $this->assertTrue(method_exists($component, 'updatingSearch'));
        $this->assertTrue(method_exists($component, 'updatingSelectedItemId'));
        $this->assertTrue(method_exists($component, 'updatingSelectedGameAccountId'));
        $this->assertTrue(method_exists($component, 'updatingShowPurchaseConfirm'));
        $this->assertTrue(method_exists($component, 'updatingShowPending'));
        $this->assertTrue(method_exists($component, 'updatingShowRecent'));
        $this->assertTrue(method_exists($component, 'updatingRecentFilter'));
    }

    #[Test]
    public function category_sanitization_converts_array_to_null(): void
    {
        $component = new DonateShop;

        $value = ['malicious' => 'payload'];
        $component->updatingCategory($value);
        $this->assertNull($value);
    }

    #[Test]
    public function search_sanitization_converts_array_to_empty_string(): void
    {
        $component = new DonateShop;

        $value = ['malicious' => 'payload'];
        $component->updatingSearch($value);
        $this->assertEquals('', $value);
    }

    #[Test]
    public function selected_item_id_sanitization_converts_array_to_null(): void
    {
        $component = new DonateShop;

        $value = ['malicious' => 'payload'];
        $component->updatingSelectedItemId($value);
        $this->assertNull($value);

        $value = '123';
        $component->updatingSelectedItemId($value);
        $this->assertEquals(123, $value);
    }

    #[Test]
    public function show_purchase_confirm_sanitization_converts_array_to_false(): void
    {
        $component = new DonateShop;

        $value = ['malicious' => 'payload'];
        $component->updatingShowPurchaseConfirm($value);
        $this->assertFalse($value);
    }

    // ============================================
    // Purchasing Restriction Tests
    // ============================================

    #[Test]
    public function can_purchase_returns_false_for_guest(): void
    {
        $component = Livewire::test(DonateShop::class);
        $this->assertFalse($component->instance()->canPurchase());
    }

    #[Test]
    public function can_purchase_returns_true_for_authenticated_user_when_enabled(): void
    {
        config(['xilero.uber_shop.purchasing_enabled' => true]);

        $user = User::factory()->create();
        $component = Livewire::actingAs($user)->test(DonateShop::class);

        $this->assertTrue($component->instance()->canPurchase());
    }

    #[Test]
    public function is_purchasing_restricted_reflects_config(): void
    {
        config(['xilero.uber_shop.purchasing_enabled' => true]);
        $component = new DonateShop;
        $this->assertFalse($component->isPurchasingRestricted());

        config(['xilero.uber_shop.purchasing_enabled' => false]);
        $this->assertTrue($component->isPurchasingRestricted());
    }

    // ============================================
    // Collapse State Tests
    // ============================================

    #[Test]
    public function pending_section_is_expanded_by_default(): void
    {
        $component = new DonateShop;
        $this->assertTrue($component->showPending);
    }

    #[Test]
    public function recent_section_is_collapsed_by_default(): void
    {
        $component = new DonateShop;
        $this->assertFalse($component->showRecent);
    }

    #[Test]
    public function can_toggle_pending_section(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(DonateShop::class)
            ->assertSet('showPending', true)
            ->toggle('showPending')
            ->assertSet('showPending', false)
            ->toggle('showPending')
            ->assertSet('showPending', true);
    }

    #[Test]
    public function can_toggle_recent_section(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(DonateShop::class)
            ->assertSet('showRecent', false)
            ->toggle('showRecent')
            ->assertSet('showRecent', true)
            ->toggle('showRecent')
            ->assertSet('showRecent', false);
    }

    // ============================================
    // Recent Filter Tests
    // ============================================

    #[Test]
    public function recent_filter_defaults_to_refundable(): void
    {
        $component = new DonateShop;
        $this->assertEquals('refundable', $component->recentFilter);
    }

    #[Test]
    public function recent_filter_shows_only_refundable_items(): void
    {
        $user = User::factory()->create();
        $gameAccount = GameAccount::factory()->create([
            'user_id' => $user->id,
            'ragnarok_account_id' => 12345,
        ]);

        // Refundable purchase (within 24 hours)
        UberShopPurchase::factory()->create([
            'account_id' => $gameAccount->ragnarok_account_id,
            'status' => UberShopPurchase::STATUS_CLAIMED,
            'claimed_at' => now()->subHours(12),
        ]);

        // Expired purchase (more than 24 hours but within 7 days)
        UberShopPurchase::factory()->create([
            'account_id' => $gameAccount->ragnarok_account_id,
            'status' => UberShopPurchase::STATUS_CLAIMED,
            'claimed_at' => now()->subHours(48),
        ]);

        $component = Livewire::actingAs($user)
            ->test(DonateShop::class)
            ->assertSet('recentFilter', 'refundable');

        $claimedPurchases = $component->instance()->claimedPurchases();

        $this->assertCount(1, $claimedPurchases);
        $this->assertTrue($claimedPurchases->first()->claimed_at->addHours(24)->isFuture());
    }

    #[Test]
    public function recent_filter_shows_all_items(): void
    {
        $user = User::factory()->create();
        $gameAccount = GameAccount::factory()->create([
            'user_id' => $user->id,
            'ragnarok_account_id' => 12345,
        ]);

        // Refundable purchase
        UberShopPurchase::factory()->create([
            'account_id' => $gameAccount->ragnarok_account_id,
            'status' => UberShopPurchase::STATUS_CLAIMED,
            'claimed_at' => now()->subHours(12),
        ]);

        // Expired purchase (within 7 days)
        UberShopPurchase::factory()->create([
            'account_id' => $gameAccount->ragnarok_account_id,
            'status' => UberShopPurchase::STATUS_CLAIMED,
            'claimed_at' => now()->subDays(3),
        ]);

        // Old purchase (more than 7 days - should not appear)
        UberShopPurchase::factory()->create([
            'account_id' => $gameAccount->ragnarok_account_id,
            'status' => UberShopPurchase::STATUS_CLAIMED,
            'claimed_at' => now()->subDays(10),
        ]);

        $component = Livewire::actingAs($user)
            ->test(DonateShop::class)
            ->set('recentFilter', 'all');

        $claimedPurchases = $component->instance()->claimedPurchases();

        $this->assertCount(2, $claimedPurchases);
    }

    #[Test]
    public function recent_filter_shows_only_expired_items(): void
    {
        $user = User::factory()->create();
        $gameAccount = GameAccount::factory()->create([
            'user_id' => $user->id,
            'ragnarok_account_id' => 12345,
        ]);

        // Refundable purchase (should not appear)
        UberShopPurchase::factory()->create([
            'account_id' => $gameAccount->ragnarok_account_id,
            'status' => UberShopPurchase::STATUS_CLAIMED,
            'claimed_at' => now()->subHours(12),
        ]);

        // Expired purchase (within 7 days)
        UberShopPurchase::factory()->create([
            'account_id' => $gameAccount->ragnarok_account_id,
            'status' => UberShopPurchase::STATUS_CLAIMED,
            'claimed_at' => now()->subDays(3),
        ]);

        // Old purchase (more than 7 days - should not appear)
        UberShopPurchase::factory()->create([
            'account_id' => $gameAccount->ragnarok_account_id,
            'status' => UberShopPurchase::STATUS_CLAIMED,
            'claimed_at' => now()->subDays(10),
        ]);

        $component = Livewire::actingAs($user)
            ->test(DonateShop::class)
            ->set('recentFilter', 'expired');

        $claimedPurchases = $component->instance()->claimedPurchases();

        $this->assertCount(1, $claimedPurchases);
        $this->assertTrue($claimedPurchases->first()->claimed_at->addHours(24)->isPast());
    }

    // ============================================
    // Collapse/Filter Sanitization Tests
    // ============================================

    #[Test]
    public function show_pending_sanitizes_to_boolean(): void
    {
        $component = new DonateShop;

        $value = ['malicious' => 'payload'];
        $component->updatingShowPending($value);
        $this->assertTrue($value);

        $value = 'true';
        $component->updatingShowPending($value);
        $this->assertTrue($value);

        $value = 'false';
        $component->updatingShowPending($value);
        $this->assertFalse($value);

        $value = 'invalid';
        $component->updatingShowPending($value);
        $this->assertTrue($value); // Defaults to true
    }

    #[Test]
    public function show_recent_sanitizes_to_boolean(): void
    {
        $component = new DonateShop;

        $value = ['malicious' => 'payload'];
        $component->updatingShowRecent($value);
        $this->assertFalse($value);

        $value = 'true';
        $component->updatingShowRecent($value);
        $this->assertTrue($value);

        $value = 'false';
        $component->updatingShowRecent($value);
        $this->assertFalse($value);

        $value = 'invalid';
        $component->updatingShowRecent($value);
        $this->assertFalse($value); // Defaults to false
    }

    #[Test]
    public function recent_filter_sanitizes_invalid_values(): void
    {
        $component = new DonateShop;

        // Valid values should pass through
        $value = 'all';
        $component->updatingRecentFilter($value);
        $this->assertEquals('all', $value);

        $value = 'refundable';
        $component->updatingRecentFilter($value);
        $this->assertEquals('refundable', $value);

        $value = 'expired';
        $component->updatingRecentFilter($value);
        $this->assertEquals('expired', $value);

        // Invalid values should default to 'refundable'
        $value = 'invalid';
        $component->updatingRecentFilter($value);
        $this->assertEquals('refundable', $value);

        $value = ['malicious' => 'payload'];
        $component->updatingRecentFilter($value);
        $this->assertEquals('refundable', $value);

        $value = '';
        $component->updatingRecentFilter($value);
        $this->assertEquals('refundable', $value);
    }

    #[Test]
    public function selected_game_account_id_sanitizes_array_to_null(): void
    {
        $component = new DonateShop;

        $value = ['malicious' => 'payload'];
        $component->updatingSelectedGameAccountId($value);
        $this->assertNull($value);

        $value = '123';
        $component->updatingSelectedGameAccountId($value);
        $this->assertEquals(123, $value);

        $value = 'not-a-number';
        $component->updatingSelectedGameAccountId($value);
        $this->assertNull($value);
    }

    // ============================================
    // Untested Method Tests
    // ============================================

    #[Test]
    public function clear_search_resets_search_to_empty(): void
    {
        $user = User::factory()->create();
        GameAccount::factory()->create(['user_id' => $user->id]);

        Livewire::actingAs($user)
            ->test(DonateShop::class)
            ->set('search', 'some search term')
            ->assertSet('search', 'some search term')
            ->call('clearSearch')
            ->assertSet('search', '');
    }

    #[Test]
    public function redirect_to_login_sets_intended_url(): void
    {
        Livewire::test(DonateShop::class)
            ->call('redirectToLogin')
            ->assertRedirect(route('login'));

        $this->assertNotNull(session('url.intended'));
    }

    #[Test]
    public function select_item_increments_views(): void
    {
        $user = User::factory()->create();
        GameAccount::factory()->create(['user_id' => $user->id]);

        $item = UberShopItem::factory()->create(['views' => 5]);

        Livewire::actingAs($user)
            ->test(DonateShop::class)
            ->call('selectItem', $item->id);

        $this->assertEquals(6, $item->fresh()->views);
    }

    #[Test]
    public function select_item_with_null_does_not_increment_views(): void
    {
        $user = User::factory()->create();
        GameAccount::factory()->create(['user_id' => $user->id]);

        $item = UberShopItem::factory()->create(['views' => 5]);

        Livewire::actingAs($user)
            ->test(DonateShop::class)
            ->call('selectItem', null);

        $this->assertEquals(5, $item->fresh()->views);
    }

    // ============================================
    // Game Account Selection Edge Cases
    // ============================================

    #[Test]
    public function updated_selected_game_account_id_resets_to_first_for_invalid_id(): void
    {
        $user = User::factory()->create();
        $account1 = GameAccount::factory()->create(['user_id' => $user->id]);
        $account2 = GameAccount::factory()->create(['user_id' => $user->id]);

        Livewire::actingAs($user)
            ->test(DonateShop::class)
            ->assertSet('selectedGameAccountId', $account1->id)
            ->set('selectedGameAccountId', 99999) // Non-existent ID
            ->assertSet('selectedGameAccountId', $account1->id);
    }

    #[Test]
    public function updated_selected_game_account_id_sets_null_when_no_value(): void
    {
        $user = User::factory()->create();
        GameAccount::factory()->create(['user_id' => $user->id]);

        Livewire::actingAs($user)
            ->test(DonateShop::class)
            ->set('selectedGameAccountId', null)
            ->assertSet('selectedGameAccountId', null);
    }

    // ============================================
    // Refund Edge Case Tests
    // ============================================

    #[Test]
    public function refund_fails_for_xileretro_accounts(): void
    {
        $user = User::factory()->create(['uber_balance' => 100]);
        $gameAccount = GameAccount::factory()->xileretro()->create([
            'user_id' => $user->id,
            'ragnarok_account_id' => 12345,
        ]);

        $purchase = UberShopPurchase::factory()->create([
            'account_id' => $gameAccount->ragnarok_account_id,
            'uber_cost' => 50,
            'status' => UberShopPurchase::STATUS_CLAIMED,
            'claimed_at' => now()->subHours(1),
        ]);

        Livewire::actingAs($user)
            ->test(DonateShop::class)
            ->call('refundPurchase', $purchase->id);

        // Balance should not change
        $this->assertEquals(100, $user->fresh()->uber_balance);
        $this->assertEquals(UberShopPurchase::STATUS_CLAIMED, $purchase->fresh()->status);
    }

    #[Test]
    public function refund_fails_when_no_characters_on_account(): void
    {
        $user = User::factory()->create(['uber_balance' => 100]);
        $gameAccount = GameAccount::factory()->create([
            'user_id' => $user->id,
            'ragnarok_account_id' => 12345,
            'server' => GameAccount::SERVER_XILERO,
        ]);

        $purchase = UberShopPurchase::factory()->create([
            'account_id' => $gameAccount->ragnarok_account_id,
            'uber_cost' => 50,
            'status' => UberShopPurchase::STATUS_CLAIMED,
            'claimed_at' => now()->subHours(1),
        ]);

        // No characters exist for this account

        Livewire::actingAs($user)
            ->test(DonateShop::class)
            ->call('refundPurchase', $purchase->id);

        // Balance should not change (refund should fail)
        $this->assertEquals(100, $user->fresh()->uber_balance);
        $this->assertEquals(UberShopPurchase::STATUS_CLAIMED, $purchase->fresh()->status);
    }

    #[Test]
    public function refund_fails_when_item_not_in_inventory(): void
    {
        $user = User::factory()->create(['uber_balance' => 100]);
        $gameAccount = GameAccount::factory()->create([
            'user_id' => $user->id,
            'ragnarok_account_id' => 12345,
            'server' => GameAccount::SERVER_XILERO,
        ]);

        $shopItem = UberShopItem::factory()->create();

        $purchase = UberShopPurchase::factory()->create([
            'account_id' => $gameAccount->ragnarok_account_id,
            'shop_item_id' => $shopItem->id,
            'item_id' => 1234,
            'refine_level' => 0,
            'quantity' => 1,
            'uber_cost' => 50,
            'status' => UberShopPurchase::STATUS_CLAIMED,
            'claimed_at' => now()->subHours(1),
        ]);

        // Create a character but no matching inventory item
        XileRO_Char::factory()->create([
            'account_id' => $gameAccount->ragnarok_account_id,
        ]);

        Livewire::actingAs($user)
            ->test(DonateShop::class)
            ->call('refundPurchase', $purchase->id);

        // Balance should not change (item not found in inventory)
        $this->assertEquals(100, $user->fresh()->uber_balance);
        $this->assertEquals(UberShopPurchase::STATUS_CLAIMED, $purchase->fresh()->status);
    }

    #[Test]
    public function refund_is_disabled_even_when_item_in_inventory(): void
    {
        $user = User::factory()->create(['uber_balance' => 100]);
        $gameAccount = GameAccount::factory()->create([
            'user_id' => $user->id,
            'ragnarok_account_id' => 12345,
            'server' => GameAccount::SERVER_XILERO,
        ]);

        $shopItem = UberShopItem::factory()->create([
            'stock' => 5,
        ]);

        $purchase = UberShopPurchase::factory()->create([
            'account_id' => $gameAccount->ragnarok_account_id,
            'shop_item_id' => $shopItem->id,
            'item_id' => 1234,
            'refine_level' => 0,
            'quantity' => 1,
            'uber_cost' => 50,
            'status' => UberShopPurchase::STATUS_CLAIMED,
            'claimed_at' => now()->subHours(1),
        ]);

        Livewire::actingAs($user)
            ->test(DonateShop::class)
            ->call('refundPurchase', $purchase->id);

        // Refunds are disabled - balance should not change
        $this->assertEquals(100, $user->fresh()->uber_balance);

        // Purchase should still be claimed
        $this->assertEquals(UberShopPurchase::STATUS_CLAIMED, $purchase->fresh()->status);

        // Stock should not change
        $this->assertEquals(5, $shopItem->fresh()->stock);
    }

    #[Test]
    public function refund_is_disabled_even_with_inventory_amount_greater_than_quantity(): void
    {
        $user = User::factory()->create(['uber_balance' => 100]);
        $gameAccount = GameAccount::factory()->create([
            'user_id' => $user->id,
            'ragnarok_account_id' => 12345,
            'server' => GameAccount::SERVER_XILERO,
        ]);

        $purchase = UberShopPurchase::factory()->create([
            'account_id' => $gameAccount->ragnarok_account_id,
            'item_id' => 1234,
            'uber_cost' => 50,
            'status' => UberShopPurchase::STATUS_CLAIMED,
            'claimed_at' => now()->subHours(1),
        ]);

        Livewire::actingAs($user)
            ->test(DonateShop::class)
            ->call('refundPurchase', $purchase->id);

        // Refunds are disabled - balance should not change
        $this->assertEquals(100, $user->fresh()->uber_balance);

        // Purchase should still be claimed
        $this->assertEquals(UberShopPurchase::STATUS_CLAIMED, $purchase->fresh()->status);
    }

    // ============================================
    // Purchase Edge Case Tests
    // ============================================

    #[Test]
    public function admin_can_purchase_when_purchasing_disabled(): void
    {
        config(['xilero.uber_shop.purchasing_enabled' => false]);

        $admin = User::factory()->admin()->create(['uber_balance' => 100]);
        $gameAccount = GameAccount::factory()->create(['user_id' => $admin->id]);

        $category = UberShopCategory::factory()->create(['enabled' => true]);
        $dbItem = \App\Models\Item::factory()->create();

        $item = UberShopItem::factory()->forXileRO()->create([
            'category_id' => $category->id,
            'item_id' => $dbItem->id,
            'uber_cost' => 10,
            'enabled' => true,
        ]);

        Livewire::actingAs($admin)
            ->test(DonateShop::class)
            ->call('selectItem', $item->id)
            ->call('purchase');

        // Admin should be able to purchase even when disabled
        $this->assertEquals(90, $admin->fresh()->uber_balance);

        // Purchase should be created
        $this->assertDatabaseHas('uber_shop_purchases', [
            'account_id' => $gameAccount->ragnarok_account_id,
            'shop_item_id' => $item->id,
        ]);
    }

    #[Test]
    public function non_admin_cannot_purchase_when_purchasing_disabled(): void
    {
        config(['xilero.uber_shop.purchasing_enabled' => false]);

        $user = User::factory()->create(['uber_balance' => 100]);
        $gameAccount = GameAccount::factory()->create(['user_id' => $user->id]);

        $category = UberShopCategory::factory()->create(['enabled' => true]);
        $dbItem = \App\Models\Item::factory()->create();

        $item = UberShopItem::factory()->forXileRO()->create([
            'category_id' => $category->id,
            'item_id' => $dbItem->id,
            'uber_cost' => 10,
            'enabled' => true,
        ]);

        Livewire::actingAs($user)
            ->test(DonateShop::class)
            ->call('selectItem', $item->id)
            ->call('purchase');

        // Non-admin should NOT be able to purchase when disabled
        $this->assertEquals(100, $user->fresh()->uber_balance);

        // No purchase should be created
        $this->assertDatabaseMissing('uber_shop_purchases', [
            'account_id' => $gameAccount->ragnarok_account_id,
        ]);
    }

    #[Test]
    public function purchase_fails_without_game_account_selected(): void
    {
        config(['xilero.uber_shop.purchasing_enabled' => true]);

        $user = User::factory()->create(['uber_balance' => 100]);
        // No game account created

        $category = UberShopCategory::factory()->create(['enabled' => true]);
        $dbItem = \App\Models\Item::factory()->create();

        $item = UberShopItem::factory()->forXileRO()->create([
            'category_id' => $category->id,
            'item_id' => $dbItem->id,
            'uber_cost' => 10,
            'enabled' => true,
        ]);

        Livewire::actingAs($user)
            ->test(DonateShop::class)
            ->set('selectedItemId', $item->id)
            ->call('purchase');

        // Balance should not change
        $this->assertEquals(100, $user->fresh()->uber_balance);

        // No purchase should be created
        $this->assertDatabaseCount('uber_shop_purchases', 0);
    }

    #[Test]
    public function purchase_fails_when_item_out_of_stock(): void
    {
        config(['xilero.uber_shop.purchasing_enabled' => true]);

        $user = User::factory()->create(['uber_balance' => 100]);
        $gameAccount = GameAccount::factory()->create(['user_id' => $user->id]);

        $category = UberShopCategory::factory()->create(['enabled' => true]);
        $dbItem = \App\Models\Item::factory()->create();

        $item = UberShopItem::factory()->forXileRO()->create([
            'category_id' => $category->id,
            'item_id' => $dbItem->id,
            'uber_cost' => 10,
            'stock' => 0,
            'enabled' => true,
        ]);

        Livewire::actingAs($user)
            ->test(DonateShop::class)
            ->call('selectItem', $item->id)
            ->call('purchase');

        // Balance should not change
        $this->assertEquals(100, $user->fresh()->uber_balance);

        // No purchase should be created
        $this->assertDatabaseMissing('uber_shop_purchases', [
            'account_id' => $gameAccount->ragnarok_account_id,
        ]);
    }

    // ============================================
    // Search Tests
    // ============================================

    #[Test]
    public function search_by_item_id(): void
    {
        $user = User::factory()->create();
        GameAccount::factory()->create(['user_id' => $user->id, 'server' => GameAccount::SERVER_XILERO]);

        $category = UberShopCategory::factory()->create(['enabled' => true]);

        $item1 = \App\Models\Item::factory()->create(['item_id' => 12345, 'name' => 'Test Item']);
        $item2 = \App\Models\Item::factory()->create(['item_id' => 99999, 'name' => 'Other Item']);

        UberShopItem::factory()->forXileRO()->create([
            'category_id' => $category->id,
            'item_id' => $item1->id,
            'enabled' => true,
        ]);

        UberShopItem::factory()->forXileRO()->create([
            'category_id' => $category->id,
            'item_id' => $item2->id,
            'enabled' => true,
        ]);

        Livewire::actingAs($user)
            ->test(DonateShop::class)
            ->set('search', '12345')
            ->assertSee('Test Item')
            ->assertDontSee('Other Item');
    }

    #[Test]
    public function search_by_aegis_name(): void
    {
        $user = User::factory()->create();
        GameAccount::factory()->create(['user_id' => $user->id, 'server' => GameAccount::SERVER_XILERO]);

        $category = UberShopCategory::factory()->create(['enabled' => true]);

        $item1 = \App\Models\Item::factory()->create(['aegis_name' => 'UNIQUE_AEGIS', 'name' => 'Aegis Item']);
        $item2 = \App\Models\Item::factory()->create(['aegis_name' => 'OTHER_AEGIS', 'name' => 'Other Item']);

        UberShopItem::factory()->forXileRO()->create([
            'category_id' => $category->id,
            'item_id' => $item1->id,
            'enabled' => true,
        ]);

        UberShopItem::factory()->forXileRO()->create([
            'category_id' => $category->id,
            'item_id' => $item2->id,
            'enabled' => true,
        ]);

        Livewire::actingAs($user)
            ->test(DonateShop::class)
            ->set('search', 'UNIQUE_AEGIS')
            ->assertSee('Aegis Item')
            ->assertDontSee('Other Item');
    }

    // ============================================
    // Pending/Claimed Purchases Edge Cases
    // ============================================

    #[Test]
    public function pending_purchases_returns_empty_when_user_has_no_game_accounts(): void
    {
        $user = User::factory()->create();
        // No game accounts created

        $component = Livewire::actingAs($user)->test(DonateShop::class);

        $pendingPurchases = $component->instance()->pendingPurchases();

        $this->assertCount(0, $pendingPurchases);
    }

    #[Test]
    public function claimed_purchases_returns_empty_when_user_has_no_game_accounts(): void
    {
        $user = User::factory()->create();
        // No game accounts created

        $component = Livewire::actingAs($user)->test(DonateShop::class);

        $claimedPurchases = $component->instance()->claimedPurchases();

        $this->assertCount(0, $claimedPurchases);
    }

    #[Test]
    public function user_balance_returns_zero_for_guest(): void
    {
        $component = Livewire::test(DonateShop::class);
        $this->assertEquals(0, $component->instance()->userBalance());
    }

    #[Test]
    public function user_balance_returns_correct_balance_for_authenticated_user(): void
    {
        $user = User::factory()->create(['uber_balance' => 500]);

        $component = Livewire::actingAs($user)->test(DonateShop::class);
        $this->assertEquals(500, $component->instance()->userBalance());
    }
}
