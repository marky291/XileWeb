<?php

namespace Tests\Unit\Livewire;

use App\Livewire\ItemDatabase;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ItemDatabaseTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_renders_the_item_database_page(): void
    {
        Livewire::test(ItemDatabase::class)
            ->assertStatus(200)
            ->assertSee('Item Database');
    }

    #[Test]
    public function it_displays_items(): void
    {
        Item::factory()->create([
            'name' => 'Red Potion',
            'item_id' => 501,
            'type' => 'Healing',
            'is_xileretro' => false,
        ]);

        Livewire::test(ItemDatabase::class)
            ->assertSee('Red Potion')
            ->assertSee('501');
    }

    #[Test]
    public function it_filters_items_by_type(): void
    {
        Item::factory()->create([
            'name' => 'Red Potion',
            'type' => 'Healing',
            'is_xileretro' => false,
        ]);
        Item::factory()->create([
            'name' => 'Knife',
            'type' => 'Weapon',
            'is_xileretro' => false,
        ]);

        Livewire::test(ItemDatabase::class)
            ->assertSee('Red Potion')
            ->assertSee('Knife')
            ->call('selectType', 'Healing')
            ->assertSee('Red Potion')
            ->assertDontSee('Knife');
    }

    #[Test]
    public function it_searches_items_by_name(): void
    {
        Item::factory()->create([
            'name' => 'Red Potion',
            'type' => 'Healing',
            'is_xileretro' => false,
        ]);
        Item::factory()->create([
            'name' => 'Blue Potion',
            'type' => 'Healing',
            'is_xileretro' => false,
        ]);

        Livewire::test(ItemDatabase::class)
            ->assertSee('Red Potion')
            ->assertSee('Blue Potion')
            ->set('search', 'Red')
            ->assertSee('Red Potion')
            ->assertDontSee('Blue Potion');
    }

    #[Test]
    public function it_searches_items_by_item_id(): void
    {
        Item::factory()->create([
            'name' => 'Red Potion',
            'item_id' => 501,
            'is_xileretro' => false,
        ]);
        Item::factory()->create([
            'name' => 'Blue Potion',
            'item_id' => 502,
            'is_xileretro' => false,
        ]);

        Livewire::test(ItemDatabase::class)
            ->set('search', '501')
            ->assertSee('Red Potion')
            ->assertDontSee('Blue Potion');
    }

    #[Test]
    public function it_can_select_item_to_view_details(): void
    {
        $item = Item::factory()->create([
            'name' => 'Red Potion',
            'is_xileretro' => false,
        ]);

        Livewire::test(ItemDatabase::class)
            ->call('selectItem', $item->id)
            ->assertSet('selectedItemId', $item->id);
    }

    #[Test]
    public function it_shows_item_details_in_modal(): void
    {
        $item = Item::factory()->create([
            'name' => 'Red Potion',
            'aegis_name' => 'Red_Potion',
            'description' => 'A healing potion that restores HP.',
            'weight' => 70,
            'buy' => 50,
            'sell' => 25,
            'is_xileretro' => false,
        ]);

        Livewire::test(ItemDatabase::class)
            ->call('selectItem', $item->id)
            ->assertSee('Red Potion')
            ->assertSee('Red_Potion')
            ->assertSee('A healing potion that restores HP.');
    }

    #[Test]
    public function it_clears_search_when_requested(): void
    {
        Item::factory()->create([
            'name' => 'Red Potion',
            'is_xileretro' => false,
        ]);
        Item::factory()->create([
            'name' => 'Blue Potion',
            'is_xileretro' => false,
        ]);

        Livewire::test(ItemDatabase::class)
            ->set('search', 'Red')
            ->assertDontSee('Blue Potion')
            ->call('clearSearch')
            ->assertSet('search', '')
            ->assertSee('Blue Potion');
    }

    #[Test]
    public function it_only_shows_xilero_items(): void
    {
        Item::factory()->create([
            'name' => 'XileRO Item',
            'is_xileretro' => false,
        ]);
        Item::factory()->create([
            'name' => 'XileRetro Item',
            'is_xileretro' => true,
        ]);

        Livewire::test(ItemDatabase::class)
            ->assertSee('XileRO Item')
            ->assertDontSee('XileRetro Item');
    }

    #[Test]
    public function it_resets_pagination_when_search_changes(): void
    {
        Item::factory()->count(30)->create([
            'type' => 'Weapon',
            'is_xileretro' => false,
        ]);

        $component = Livewire::test(ItemDatabase::class)
            ->call('gotoPage', 2)
            ->set('search', 'test');

        $this->assertEquals(1, $component->get('paginators.page'));
    }

    #[Test]
    public function it_resets_pagination_when_type_changes(): void
    {
        Item::factory()->count(30)->create([
            'type' => 'Weapon',
            'is_xileretro' => false,
        ]);

        $component = Livewire::test(ItemDatabase::class)
            ->call('gotoPage', 2)
            ->call('selectType', 'Healing');

        $this->assertEquals(1, $component->get('paginators.page'));
    }

    #[Test]
    public function it_increments_view_count_when_item_is_selected(): void
    {
        $item = Item::factory()->create([
            'name' => 'Red Potion',
            'is_xileretro' => false,
            'views' => 0,
        ]);

        Livewire::test(ItemDatabase::class)
            ->call('selectItem', $item->id);

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'views' => 1,
        ]);
    }

    #[Test]
    public function it_increments_view_count_multiple_times(): void
    {
        $item = Item::factory()->create([
            'name' => 'Red Potion',
            'is_xileretro' => false,
            'views' => 5,
        ]);

        Livewire::test(ItemDatabase::class)
            ->call('selectItem', $item->id)
            ->call('selectItem', null)
            ->call('selectItem', $item->id);

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'views' => 7,
        ]);
    }

    #[Test]
    public function it_sorts_by_most_popular_by_default(): void
    {
        $lessPopular = Item::factory()->create([
            'name' => 'AAA Item',
            'is_xileretro' => false,
            'views' => 5,
        ]);
        $morePopular = Item::factory()->create([
            'name' => 'ZZZ Item',
            'is_xileretro' => false,
            'views' => 100,
        ]);

        $component = Livewire::test(ItemDatabase::class);

        // The more popular item should appear first even though it's alphabetically last
        $items = $component->viewData('items');
        $this->assertEquals($morePopular->id, $items->first()->id);
    }

    #[Test]
    public function it_can_sort_by_name_ascending(): void
    {
        Item::factory()->create([
            'name' => 'Zephyr Sword',
            'is_xileretro' => false,
            'views' => 100,
        ]);
        $alphaFirst = Item::factory()->create([
            'name' => 'Apple',
            'is_xileretro' => false,
            'views' => 1,
        ]);

        $component = Livewire::test(ItemDatabase::class)
            ->set('sort', 'name');

        $items = $component->viewData('items');
        $this->assertEquals($alphaFirst->id, $items->first()->id);
    }

    #[Test]
    public function it_can_sort_by_name_descending(): void
    {
        $alphaLast = Item::factory()->create([
            'name' => 'Zephyr Sword',
            'is_xileretro' => false,
            'views' => 1,
        ]);
        Item::factory()->create([
            'name' => 'Apple',
            'is_xileretro' => false,
            'views' => 100,
        ]);

        $component = Livewire::test(ItemDatabase::class)
            ->set('sort', 'name_desc');

        $items = $component->viewData('items');
        $this->assertEquals($alphaLast->id, $items->first()->id);
    }

    #[Test]
    public function it_can_sort_by_item_id(): void
    {
        Item::factory()->create([
            'name' => 'Second Item',
            'item_id' => 999,
            'is_xileretro' => false,
        ]);
        $lowestId = Item::factory()->create([
            'name' => 'First Item',
            'item_id' => 1,
            'is_xileretro' => false,
        ]);

        $component = Livewire::test(ItemDatabase::class)
            ->set('sort', 'id');

        $items = $component->viewData('items');
        $this->assertEquals($lowestId->id, $items->first()->id);
    }

    #[Test]
    public function it_resets_pagination_when_sort_changes(): void
    {
        Item::factory()->count(30)->create([
            'is_xileretro' => false,
        ]);

        $component = Livewire::test(ItemDatabase::class)
            ->call('gotoPage', 2)
            ->set('sort', 'name');

        $this->assertEquals(1, $component->get('paginators.page'));
    }

    #[Test]
    public function it_can_switch_to_xileretro_server(): void
    {
        Item::factory()->create([
            'name' => 'XileRO Item',
            'is_xileretro' => false,
        ]);
        Item::factory()->create([
            'name' => 'XileRetro Item',
            'is_xileretro' => true,
        ]);

        Livewire::test(ItemDatabase::class)
            ->assertSee('XileRO Item')
            ->assertDontSee('XileRetro Item')
            ->set('server', 'xileretro')
            ->assertSee('XileRetro Item')
            ->assertDontSee('XileRO Item');
    }

    #[Test]
    public function switching_server_resets_type_and_pagination(): void
    {
        Item::factory()->count(30)->create([
            'type' => 'Weapon',
            'is_xileretro' => false,
        ]);

        $component = Livewire::test(ItemDatabase::class)
            ->call('selectType', 'Weapon')
            ->call('gotoPage', 2)
            ->set('server', 'xileretro');

        $this->assertNull($component->get('type'));
        $this->assertEquals(1, $component->get('paginators.page'));
    }
}
