<?php

namespace Tests\Unit\Livewire;

use App\Livewire\DonateShop;
use App\Models\DatabaseItem;
use App\Models\GameAccount;
use App\Models\UberShopCategory;
use App\Models\UberShopItem;
use App\Models\UberShopPurchase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DonateShopTest extends TestCase
{
    use RefreshDatabase;

    private function createCategoryWithItem(array $itemOverrides = [], array $databaseItemOverrides = []): UberShopItem
    {
        $category = UberShopCategory::factory()->create([
            'enabled' => true,
        ]);

        $databaseItem = DatabaseItem::factory()->create($databaseItemOverrides);

        return UberShopItem::factory()->create(array_merge([
            'category_id' => $category->id,
            'database_item_id' => $databaseItem->id,
            'enabled' => true,
            'uber_cost' => 5,
        ], $itemOverrides));
    }

    private function createUserWithGameAccount(int $uberBalance): User
    {
        $user = User::factory()->create([
            'uber_balance' => $uberBalance,
        ]);
        GameAccount::factory()->create([
            'user_id' => $user->id,
        ]);

        return $user;
    }

    #[Test]
    public function it_renders_the_donate_shop_page(): void
    {
        $this->createCategoryWithItem();

        Livewire::test(DonateShop::class)
            ->assertStatus(200)
            ->assertSee('Uber Shop');
    }

    #[Test]
    public function it_displays_categories(): void
    {
        $category = UberShopCategory::factory()->create([
            'name' => 'test-category',
            'display_name' => 'Test Category',
            'enabled' => true,
        ]);

        UberShopItem::factory()->create([
            'category_id' => $category->id,
            'enabled' => true,
        ]);

        Livewire::test(DonateShop::class)
            ->assertSee('Test Category');
    }

    #[Test]
    public function it_displays_items(): void
    {
        $item = $this->createCategoryWithItem([
            'uber_cost' => 10,
        ], [
            'name' => 'Super Sword',
        ]);

        Livewire::test(DonateShop::class)
            ->assertSee('Super Sword')
            ->assertSee('10');
    }

    #[Test]
    public function it_filters_items_by_category(): void
    {
        $category1 = UberShopCategory::factory()->create([
            'name' => 'weapons',
            'enabled' => true,
        ]);
        $category2 = UberShopCategory::factory()->create([
            'name' => 'armor',
            'enabled' => true,
        ]);

        $swordItem = DatabaseItem::factory()->create(['name' => 'Epic Sword']);
        $shieldItem = DatabaseItem::factory()->create(['name' => 'Iron Shield']);

        UberShopItem::factory()->create([
            'category_id' => $category1->id,
            'database_item_id' => $swordItem->id,
            'enabled' => true,
        ]);
        UberShopItem::factory()->create([
            'category_id' => $category2->id,
            'database_item_id' => $shieldItem->id,
            'enabled' => true,
        ]);

        Livewire::test(DonateShop::class)
            ->assertSee('Epic Sword')
            ->assertSee('Iron Shield')
            ->call('selectCategory', 'weapons')
            ->assertSee('Epic Sword')
            ->assertDontSee('Iron Shield');
    }

    #[Test]
    public function guest_users_see_login_prompt(): void
    {
        $item = $this->createCategoryWithItem();

        Livewire::test(DonateShop::class)
            ->call('selectItem', $item->id)
            ->assertSee('Login to Purchase');
    }

    #[Test]
    public function authenticated_user_sees_their_balance(): void
    {
        $user = $this->createUserWithGameAccount(100);
        $this->createCategoryWithItem();

        Livewire::actingAs($user)
            ->test(DonateShop::class)
            ->assertSee('100');
    }

    #[Test]
    public function user_can_open_item_modal(): void
    {
        $user = $this->createUserWithGameAccount(100);
        $item = $this->createCategoryWithItem([], [
            'name' => 'Dragon Slayer',
        ]);

        Livewire::actingAs($user)
            ->test(DonateShop::class)
            ->call('selectItem', $item->id)
            ->assertSet('selectedItemId', $item->id);
    }

    #[Test]
    public function user_can_confirm_purchase(): void
    {
        $user = $this->createUserWithGameAccount(100);
        $item = $this->createCategoryWithItem(['uber_cost' => 10]);

        Livewire::actingAs($user)
            ->test(DonateShop::class)
            ->call('selectItem', $item->id)
            ->call('confirmPurchase')
            ->assertSet('showPurchaseConfirm', true);
    }

    #[Test]
    public function user_can_cancel_purchase_confirmation(): void
    {
        $user = $this->createUserWithGameAccount(100);
        $item = $this->createCategoryWithItem(['uber_cost' => 10]);

        Livewire::actingAs($user)
            ->test(DonateShop::class)
            ->call('selectItem', $item->id)
            ->call('confirmPurchase')
            ->assertSet('showPurchaseConfirm', true)
            ->call('cancelPurchase')
            ->assertSet('showPurchaseConfirm', false);
    }

    #[Test]
    public function user_with_sufficient_balance_can_purchase_item(): void
    {
        $user = $this->createUserWithGameAccount(100);
        $gameAccount = $user->gameAccounts()->first();
        $item = $this->createCategoryWithItem(['uber_cost' => 25]);

        Livewire::actingAs($user)
            ->test(DonateShop::class)
            ->call('selectItem', $item->id)
            ->call('purchase');

        // Verify balance was deducted from user
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'uber_balance' => 75, // 100 - 25
        ]);

        // Verify purchase was created
        $this->assertDatabaseHas('uber_shop_purchases', [
            'account_id' => $gameAccount->ragnarok_account_id,
            'shop_item_id' => $item->id,
            'uber_cost' => 25,
            'uber_balance_after' => 75,
            'status' => 'pending',
        ]);
    }

    #[Test]
    public function user_with_insufficient_balance_cannot_purchase(): void
    {
        $user = $this->createUserWithGameAccount(10);
        $gameAccount = $user->gameAccounts()->first();
        $item = $this->createCategoryWithItem(['uber_cost' => 50]);

        Livewire::actingAs($user)
            ->test(DonateShop::class)
            ->call('selectItem', $item->id)
            ->call('purchase');

        // Verify balance was not deducted
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'uber_balance' => 10,
        ]);

        // Verify no purchase was created
        $this->assertDatabaseMissing('uber_shop_purchases', [
            'account_id' => $gameAccount->ragnarok_account_id,
        ]);
    }

    #[Test]
    public function purchase_decrements_item_stock_when_limited(): void
    {
        $user = $this->createUserWithGameAccount(100);
        $item = $this->createCategoryWithItem([
            'uber_cost' => 10,
            'stock' => 5,
        ]);

        Livewire::actingAs($user)
            ->test(DonateShop::class)
            ->call('selectItem', $item->id)
            ->call('purchase');

        // Verify stock was decremented
        $this->assertDatabaseHas('uber_shop_items', [
            'id' => $item->id,
            'stock' => 4, // 5 - 1
        ]);
    }

    #[Test]
    public function cannot_purchase_out_of_stock_item(): void
    {
        $user = $this->createUserWithGameAccount(100);
        $gameAccount = $user->gameAccounts()->first();
        $item = $this->createCategoryWithItem([
            'uber_cost' => 10,
            'stock' => 0,
        ]);

        Livewire::actingAs($user)
            ->test(DonateShop::class)
            ->call('selectItem', $item->id)
            ->call('purchase');

        // Verify no purchase was created
        $this->assertDatabaseMissing('uber_shop_purchases', [
            'account_id' => $gameAccount->ragnarok_account_id,
        ]);
    }

    #[Test]
    public function cannot_purchase_disabled_item(): void
    {
        $user = $this->createUserWithGameAccount(100);
        $gameAccount = $user->gameAccounts()->first();
        $category = UberShopCategory::factory()->create(['enabled' => true]);
        $item = UberShopItem::factory()->create([
            'category_id' => $category->id,
            'uber_cost' => 10,
            'enabled' => false,
        ]);

        Livewire::actingAs($user)
            ->test(DonateShop::class)
            ->set('selectedItemId', $item->id)
            ->call('purchase');

        // Verify no purchase was created
        $this->assertDatabaseMissing('uber_shop_purchases', [
            'account_id' => $gameAccount->ragnarok_account_id,
        ]);
    }

    #[Test]
    public function guest_cannot_purchase(): void
    {
        $item = $this->createCategoryWithItem(['uber_cost' => 10]);

        Livewire::test(DonateShop::class)
            ->call('selectItem', $item->id)
            ->call('purchase');

        // Verify no purchase was created
        $this->assertDatabaseCount('uber_shop_purchases', 0);
    }

    #[Test]
    public function purchase_resets_selected_item_on_success(): void
    {
        $user = $this->createUserWithGameAccount(100);
        $item = $this->createCategoryWithItem(['uber_cost' => 10]);

        Livewire::actingAs($user)
            ->test(DonateShop::class)
            ->call('selectItem', $item->id)
            ->assertSet('selectedItemId', $item->id)
            ->call('purchase')
            ->assertSet('selectedItemId', null)
            ->assertSet('showPurchaseConfirm', false);
    }

    #[Test]
    public function multiple_purchases_are_tracked_correctly(): void
    {
        $user = $this->createUserWithGameAccount(100);
        $gameAccount = $user->gameAccounts()->first();
        $item = $this->createCategoryWithItem(['uber_cost' => 20]);

        // First purchase
        Livewire::actingAs($user)
            ->test(DonateShop::class)
            ->call('selectItem', $item->id)
            ->call('purchase');

        // Second purchase
        Livewire::actingAs($user)
            ->test(DonateShop::class)
            ->call('selectItem', $item->id)
            ->call('purchase');

        // Verify final balance
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'uber_balance' => 60, // 100 - 20 - 20
        ]);

        // Verify both purchases were created
        $this->assertEquals(2, UberShopPurchase::where('account_id', $gameAccount->ragnarok_account_id)->count());
    }

    #[Test]
    public function user_can_select_different_game_account(): void
    {
        $user = User::factory()->create([
            'uber_balance' => 100,
        ]);
        $account1 = GameAccount::factory()->create([
            'user_id' => $user->id,
        ]);
        $account2 = GameAccount::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->createCategoryWithItem();

        Livewire::actingAs($user)
            ->test(DonateShop::class)
            ->assertSet('selectedGameAccountId', $account1->id)
            ->set('selectedGameAccountId', $account2->id)
            ->assertSet('selectedGameAccountId', $account2->id);
    }

    #[Test]
    public function it_shows_all_items_regardless_of_server(): void
    {
        $user = User::factory()->create(['uber_balance' => 100]);
        GameAccount::factory()->create(['user_id' => $user->id, 'server' => 'xilero']);

        $category = UberShopCategory::factory()->create(['enabled' => true]);

        $xileroDbItem = DatabaseItem::factory()->create(['name' => 'XileRO Only Item']);
        $xileretroDbItem = DatabaseItem::factory()->create(['name' => 'XileRetro Only Item']);

        UberShopItem::factory()->forXileRO()->create([
            'category_id' => $category->id,
            'database_item_id' => $xileroDbItem->id,
            'enabled' => true,
        ]);

        UberShopItem::factory()->forXileRetro()->create([
            'category_id' => $category->id,
            'database_item_id' => $xileretroDbItem->id,
            'enabled' => true,
        ]);

        // All items should be visible regardless of selected account's server
        Livewire::actingAs($user)
            ->test(DonateShop::class)
            ->assertSee('XileRO Only Item')
            ->assertSee('XileRetro Only Item');
    }

    #[Test]
    public function it_shows_server_mismatch_message_in_modal(): void
    {
        $user = User::factory()->create(['uber_balance' => 100]);
        GameAccount::factory()->xileretro()->create(['user_id' => $user->id]);

        $category = UberShopCategory::factory()->create(['enabled' => true]);
        $dbItem = DatabaseItem::factory()->create(['name' => 'XileRO Only Item']);

        $xileroItem = UberShopItem::factory()->forXileRO()->create([
            'category_id' => $category->id,
            'database_item_id' => $dbItem->id,
            'uber_cost' => 10,
            'enabled' => true,
        ]);

        Livewire::actingAs($user)
            ->test(DonateShop::class)
            ->call('selectItem', $xileroItem->id)
            ->assertSee('This item is only available for')
            ->assertSee('Not Available for This Server');
    }

    #[Test]
    public function it_shows_items_available_for_both_servers(): void
    {
        $user = User::factory()->create(['uber_balance' => 100]);
        GameAccount::factory()->create(['user_id' => $user->id, 'server' => 'xilero']);

        $category = UberShopCategory::factory()->create(['enabled' => true]);
        $dbItem = DatabaseItem::factory()->create(['name' => 'Both Servers Item']);

        UberShopItem::factory()->forBothServers()->create([
            'category_id' => $category->id,
            'database_item_id' => $dbItem->id,
            'enabled' => true,
        ]);

        Livewire::actingAs($user)
            ->test(DonateShop::class)
            ->assertSee('Both Servers Item');
    }

    #[Test]
    public function cannot_purchase_item_not_available_for_server(): void
    {
        $user = User::factory()->create(['uber_balance' => 100]);
        $gameAccount = GameAccount::factory()->xileretro()->create(['user_id' => $user->id]);

        $category = UberShopCategory::factory()->create(['enabled' => true]);
        $xileroOnlyItem = UberShopItem::factory()->forXileRO()->create([
            'category_id' => $category->id,
            'uber_cost' => 10,
            'enabled' => true,
        ]);

        Livewire::actingAs($user)
            ->test(DonateShop::class)
            ->set('selectedItemId', $xileroOnlyItem->id)
            ->call('purchase')
            ->assertHasNoErrors();

        // Verify no purchase was created
        $this->assertDatabaseMissing('uber_shop_purchases', [
            'account_id' => $gameAccount->ragnarok_account_id,
        ]);

        // Verify balance was not deducted
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'uber_balance' => 100,
        ]);
    }

    #[Test]
    public function can_purchase_item_available_for_both_servers(): void
    {
        $user = User::factory()->create(['uber_balance' => 100]);
        $gameAccount = GameAccount::factory()->xileretro()->create(['user_id' => $user->id]);

        $category = UberShopCategory::factory()->create(['enabled' => true]);
        $bothServersItem = UberShopItem::factory()->forBothServers()->create([
            'category_id' => $category->id,
            'uber_cost' => 10,
            'enabled' => true,
        ]);

        Livewire::actingAs($user)
            ->test(DonateShop::class)
            ->call('selectItem', $bothServersItem->id)
            ->call('purchase');

        // Verify purchase was created
        $this->assertDatabaseHas('uber_shop_purchases', [
            'account_id' => $gameAccount->ragnarok_account_id,
            'shop_item_id' => $bothServersItem->id,
        ]);
    }

    #[Test]
    public function switching_game_account_resets_purchase_confirmation(): void
    {
        $user = User::factory()->create(['uber_balance' => 100]);
        $xileroAccount = GameAccount::factory()->create(['user_id' => $user->id, 'server' => 'xilero']);
        $xileretroAccount = GameAccount::factory()->xileretro()->create(['user_id' => $user->id]);

        $item = $this->createCategoryWithItem();

        Livewire::actingAs($user)
            ->test(DonateShop::class)
            ->call('selectItem', $item->id)
            ->call('confirmPurchase')
            ->assertSet('showPurchaseConfirm', true)
            ->set('selectedGameAccountId', $xileretroAccount->id)
            ->assertSet('showPurchaseConfirm', false)
            ->assertSet('selectedItemId', $item->id); // Item stays selected
    }
}
