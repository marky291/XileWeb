<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\UberShopCategoryResource;
use App\Filament\Resources\UberShopCategoryResource\Pages\CreateUberShopCategory;
use App\Filament\Resources\UberShopCategoryResource\Pages\EditUberShopCategory;
use App\Filament\Resources\UberShopCategoryResource\Pages\ListUberShopCategories;
use App\Filament\Resources\UberShopItemResource;
use App\Filament\Resources\UberShopItemResource\Pages\CreateUberShopItem;
use App\Filament\Resources\UberShopItemResource\Pages\EditUberShopItem;
use App\Filament\Resources\UberShopItemResource\Pages\ListUberShopItems;
use App\Models\Item;
use App\Models\UberShopCategory;
use App\Models\UberShopItem;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UberShopResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    #[Test]
    public function category_resource_has_correct_navigation_group(): void
    {
        $this->assertEquals('Website', UberShopCategoryResource::getNavigationGroup());
        $this->assertEquals('Uber Shop', UberShopCategoryResource::getNavigationLabel());
    }

    #[Test]
    public function item_resource_is_hidden_from_navigation(): void
    {
        $this->assertFalse(UberShopItemResource::shouldRegisterNavigation());
    }

    #[Test]
    public function admin_can_view_categories_list(): void
    {
        $admin = User::factory()->admin()->create();
        $categories = UberShopCategory::factory()->count(3)->create();

        Livewire::actingAs($admin)
            ->test(ListUberShopCategories::class)
            ->assertSuccessful()
            ->assertCanSeeTableRecords($categories);
    }

    #[Test]
    public function admin_can_create_category(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(CreateUberShopCategory::class)
            ->fillForm([
                'name' => 'test-category',
                'display_name' => 'Test Category',
                'tagline' => 'A test tagline',
                'uber_range' => '50-100',
                'display_order' => 1,
                'enabled' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('uber_shop_categories', [
            'name' => 'test-category',
            'display_name' => 'Test Category',
            'tagline' => 'A test tagline',
            'enabled' => true,
        ]);
    }

    #[Test]
    public function admin_can_edit_category(): void
    {
        $admin = User::factory()->admin()->create();
        $category = UberShopCategory::factory()->create([
            'name' => 'original-name',
            'display_name' => 'Original Name',
        ]);

        Livewire::actingAs($admin)
            ->test(EditUberShopCategory::class, ['record' => $category->id])
            ->assertFormSet([
                'name' => 'original-name',
                'display_name' => 'Original Name',
            ])
            ->fillForm([
                'name' => 'updated-name',
                'display_name' => 'Updated Name',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('uber_shop_categories', [
            'id' => $category->id,
            'name' => 'updated-name',
            'display_name' => 'Updated Name',
        ]);
    }

    #[Test]
    public function admin_can_view_items_list(): void
    {
        $admin = User::factory()->admin()->create();
        $item = Item::factory()->create();
        $shopItems = UberShopItem::factory()->count(3)->create(['item_id' => $item->id]);

        Livewire::actingAs($admin)
            ->test(ListUberShopItems::class)
            ->assertSuccessful()
            ->assertCanSeeTableRecords($shopItems);
    }

    #[Test]
    public function admin_can_create_shop_item(): void
    {
        $admin = User::factory()->admin()->create();
        $category = UberShopCategory::factory()->create();
        $item = Item::factory()->create();

        Livewire::actingAs($admin)
            ->test(CreateUberShopItem::class)
            ->fillForm([
                'category_id' => $category->id,
                'item_id' => $item->id,
                'uber_cost' => 100,
                'quantity' => 1,
                'refine_level' => 0,
                'display_order' => 1,
                'enabled' => true,
                'is_xilero' => true,
                'is_xileretro' => false,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('uber_shop_items', [
            'category_id' => $category->id,
            'item_id' => $item->id,
            'uber_cost' => 100,
            'is_xilero' => true,
            'is_xileretro' => false,
        ]);
    }

    #[Test]
    public function admin_can_edit_shop_item(): void
    {
        $admin = User::factory()->admin()->create();
        $item = Item::factory()->create();
        $shopItem = UberShopItem::factory()->create([
            'item_id' => $item->id,
            'uber_cost' => 50,
            'quantity' => 1,
        ]);

        Livewire::actingAs($admin)
            ->test(EditUberShopItem::class, ['record' => $shopItem->id])
            ->assertFormSet([
                'uber_cost' => 50,
                'quantity' => 1,
            ])
            ->fillForm([
                'uber_cost' => 200,
                'quantity' => 5,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('uber_shop_items', [
            'id' => $shopItem->id,
            'uber_cost' => 200,
            'quantity' => 5,
        ]);
    }

    #[Test]
    public function admin_can_filter_items_by_category(): void
    {
        $admin = User::factory()->admin()->create();
        $item = Item::factory()->create();
        $category1 = UberShopCategory::factory()->create(['name' => 'Category 1']);
        $category2 = UberShopCategory::factory()->create(['name' => 'Category 2']);

        $itemInCategory1 = UberShopItem::factory()->create([
            'item_id' => $item->id,
            'category_id' => $category1->id,
        ]);
        $itemInCategory2 = UberShopItem::factory()->create([
            'item_id' => $item->id,
            'category_id' => $category2->id,
        ]);

        Livewire::actingAs($admin)
            ->test(ListUberShopItems::class)
            ->filterTable('category', $category1->id)
            ->assertCanSeeTableRecords([$itemInCategory1])
            ->assertCanNotSeeTableRecords([$itemInCategory2]);
    }

    #[Test]
    public function admin_can_filter_items_by_enabled_status(): void
    {
        $admin = User::factory()->admin()->create();
        $item = Item::factory()->create();

        $enabledItem = UberShopItem::factory()->create([
            'item_id' => $item->id,
            'enabled' => true,
        ]);
        $disabledItem = UberShopItem::factory()->create([
            'item_id' => $item->id,
            'enabled' => false,
        ]);

        Livewire::actingAs($admin)
            ->test(ListUberShopItems::class)
            ->filterTable('enabled', true)
            ->assertCanSeeTableRecords([$enabledItem])
            ->assertCanNotSeeTableRecords([$disabledItem]);
    }

    #[Test]
    public function admin_can_toggle_item_status(): void
    {
        $admin = User::factory()->admin()->create();
        $item = Item::factory()->create();
        $shopItem = UberShopItem::factory()->create([
            'item_id' => $item->id,
            'enabled' => true,
        ]);

        Livewire::actingAs($admin)
            ->test(ListUberShopItems::class)
            ->callTableAction('toggle', $shopItem);

        $this->assertDatabaseHas('uber_shop_items', [
            'id' => $shopItem->id,
            'enabled' => false,
        ]);
    }

    #[Test]
    public function admin_can_search_categories(): void
    {
        $admin = User::factory()->admin()->create();
        $searchableCategory = UberShopCategory::factory()->create(['name' => 'searchable-category']);
        $otherCategory = UberShopCategory::factory()->create(['name' => 'other-category']);

        Livewire::actingAs($admin)
            ->test(ListUberShopCategories::class)
            ->searchTable('searchable')
            ->assertCanSeeTableRecords([$searchableCategory])
            ->assertCanNotSeeTableRecords([$otherCategory]);
    }
}
