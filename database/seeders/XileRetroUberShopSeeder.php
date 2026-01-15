<?php

namespace Database\Seeders;

use App\Models\DatabaseItem;
use App\Models\UberShopCategory;
use App\Models\UberShopItem;
use Illuminate\Database\Seeder;

class XileRetroUberShopSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Seeds XileRetro-only shop categories and items from the donation shop script.
     */
    public function run(): void
    {
        $categories = $this->getCategories();

        // Pre-load all database items for efficiency
        $allItemIds = collect($categories)
            ->flatMap(fn ($cat) => collect($cat['items'])->pluck('item_id'))
            ->unique()
            ->values()
            ->all();

        $databaseItems = DatabaseItem::whereIn('item_id', $allItemIds)
            ->get()
            ->keyBy('item_id');

        foreach ($categories as $order => $categoryData) {
            $category = UberShopCategory::updateOrCreate(
                ['name' => $categoryData['name']],
                [
                    'display_name' => $categoryData['display_name'],
                    'tagline' => $categoryData['tagline'],
                    'uber_range' => $categoryData['uber_range'] ?? null,
                    'display_order' => $order + 1,
                    'enabled' => true,
                    'is_xilero' => false,
                    'is_xileretro' => true,
                ]
            );

            foreach ($categoryData['items'] as $itemOrder => $item) {
                $dbItem = $databaseItems->get($item['item_id']);

                UberShopItem::updateOrCreate(
                    [
                        'item_id' => $item['item_id'],
                        'refine_level' => $item['refine'] ?? 0,
                        'is_xileretro' => true,
                    ],
                    [
                        'category_id' => $category->id,
                        'item_name' => $dbItem?->name ?? 'Unknown Item #'.$item['item_id'],
                        'aegis_name' => $dbItem?->aegis_name,
                        'description' => $dbItem?->description,
                        'item_type' => $dbItem?->item_type,
                        'item_subtype' => $dbItem?->item_subtype,
                        'icon_path' => $dbItem?->icon_path,
                        'collection_path' => $dbItem?->collection_path,
                        'slots' => $dbItem?->slots ?? 0,
                        'weight' => $dbItem?->weight ?? 0,
                        'equip_locations' => $dbItem?->equip_locations ? implode(',', $dbItem->equip_locations) : null,
                        'uber_cost' => $item['cost'],
                        'quantity' => 1,
                        'refine_level' => $item['refine'] ?? 0,
                        'display_order' => $itemOrder + 1,
                        'enabled' => true,
                        'is_xilero' => false,
                        'is_xileretro' => true,
                    ]
                );
            }
        }
    }

    /**
     * @return array<int, array{name: string, display_name: string, tagline: string, uber_range?: string, items: array<int, array{item_id: int, cost: int, refine?: int, name?: string}>}>
     */
    private function getCategories(): array
    {
        return [
            [
                'name' => 'retro-relic-costumes',
                'display_name' => '^00BFC8Relic Costumes^000000',
                'tagline' => 'Embrace XileRO Relic Most Favored Equips as Costumes',
                'uber_range' => '3-14',
                'items' => $this->buildItems(
                    [19808, 19809, 19810, 19811, 19812, 19813, 20466, 20472, 20469, 20467, 20473, 20470, 20468, 20474, 20471, 31329, 31327, 31328],
                    [3, 3, 3, 3, 3, 3, 10, 8, 12, 10, 8, 12, 10, 8, 12, 9, 8, 14]
                ),
            ],
            [
                'name' => 'retro-misc',
                'display_name' => '^00BFC8Misc Items^000000',
                'tagline' => 'Exotic items for exotic people',
                'uber_range' => '1-8',
                'items' => $this->buildItems(
                    [20452, 21006, 20363, 17246, 17247, 20418, 20001, 22552, 20450, 20110, 20111, 20112, 20113],
                    [1, 4, 4, 2, 2, 1, 1, 1, 3, 1, 2, 4, 8]
                ),
            ],
            [
                'name' => 'retro-essence',
                'display_name' => '^ff0000Essence^000000',
                'tagline' => 'Essence with mysterious power',
                'uber_range' => '1-5',
                'items' => $this->buildItems(
                    [20065, 20066, 20067, 20068, 20069, 20070, 20459, 20460, 20461, 20462, 20463, 20464],
                    [1, 1, 1, 1, 1, 1, 5, 5, 5, 5, 5, 5]
                ),
            ],
            [
                'name' => 'retro-flexors',
                'display_name' => '^006400F^00008Bl^8B4513e^2F4F4Fx^800080o^696969r^8B0000s^000000',
                'tagline' => 'Exclusive Elegance Awaits',
                'uber_range' => '120',
                'items' => $this->buildItems(
                    [20453, 20454, 20465],
                    [120, 120, 120]
                ),
            ],
            [
                'name' => 'retro-premium-tickets',
                'display_name' => '^00BFC8Premium Tickets^000000',
                'tagline' => 'Get access to our extra features and functionalities in the Premium Room and Buffs(Agi+Bless) on Kitty Girls with this ticket!',
                'uber_range' => '1',
                'items' => $this->buildItems(
                    [20192],
                    [1]
                ),
            ],
            [
                'name' => 'retro-cards',
                'display_name' => '^008800Card Wizard^000000',
                'tagline' => 'Cards to boost your character(s) power!!',
                'uber_range' => '1-5',
                'items' => $this->buildItems(
                    [4172, 4196, 4147, 4047, 4198, 4207, 4208, 4209, 4210, 4211, 4121, 4128, 4441, 4263, 4403, 4318],
                    [1, 1, 2, 2, 2, 2, 2, 2, 2, 2, 3, 3, 5, 5, 5, 5]
                ),
            ],
            [
                'name' => 'retro-defence',
                'display_name' => '^ff0000Shield & Garment Guardian^000000',
                'tagline' => 'Shields of blocking and supersoft Garments',
                'uber_range' => '2',
                'items' => $this->buildItems(
                    [2102, 2104, 2106, 2108, 2504, 2506],
                    [2, 2, 2, 2, 2, 2],
                    [10, 10, 10, 10, 10, 10]
                ),
            ],
            [
                'name' => 'retro-weapons',
                'display_name' => '^0000ffWeapon Fusion^000000',
                'tagline' => 'Weapons of destruction',
                'uber_range' => '1-3',
                'items' => array_merge(
                    // +0 weapons at 1 uber
                    $this->buildItems(
                        [3150, 3151, 3152, 3153, 3154, 3155, 3156, 3157, 3158, 3159, 3160, 3161, 19118, 19119, 19120, 19121, 19122, 19123, 19124],
                        array_fill(0, 19, 1),
                        array_fill(0, 19, 0)
                    ),
                    // +10 weapons at 3 uber
                    $this->buildItems(
                        [3150, 3151, 3152, 3153, 3154, 3155, 3156, 3157, 3158, 3159, 3160, 3161, 19118, 19119, 19120, 19121, 19122, 19123, 19124],
                        array_fill(0, 19, 3),
                        array_fill(0, 19, 10)
                    )
                ),
            ],
            [
                'name' => 'retro-token-set',
                'display_name' => '^149D01Token Set Forger^000000',
                'tagline' => 'Token Sets from our WoE Castle Drop Quests, not as powerful as our Battlegrounds Sets!',
                'uber_range' => '4-10',
                'items' => $this->buildItems(
                    [20101, 20102, 20103, 20104, 20105, 20106, 20107],
                    [5, 10, 5, 10, 5, 10, 4],
                    [10, 10, 10, 10, 10, 10, 10]
                ),
            ],
            [
                'name' => 'retro-godly-headgear',
                'display_name' => '^008000Ultimate Set Forger^000000',
                'tagline' => 'Ultimate Sets, best gears in the game!',
                'uber_range' => '6-12',
                'items' => array_merge(
                    // +0 headgear
                    $this->buildItems(
                        [3170, 3172, 3171, 3173, 3175, 3174, 3176, 3178, 3177],
                        [6, 6, 9, 6, 6, 9, 7, 7, 11],
                        [0, 0, 0, 0, 0, 0, 0, 0, 0]
                    ),
                    // +10 headgear
                    $this->buildItems(
                        [3170, 3171, 3173, 3174, 3176, 3177],
                        [7, 10, 7, 10, 8, 12],
                        [10, 10, 10, 10, 10, 10]
                    )
                ),
            ],
            [
                'name' => 'retro-godly-headgear-v2',
                'display_name' => '^008000Ultimate Set Forger v2^000000',
                'tagline' => 'Ultimate Sets, best gears in the game!',
                'uber_range' => '15-31',
                'items' => array_merge(
                    // +0 headgear v2
                    $this->buildItems(
                        [31200, 31198, 31199, 31197, 31195, 31196],
                        [17, 15, 24, 18, 16, 27],
                        [0, 0, 0, 0, 0, 0]
                    ),
                    // +10 headgear v2
                    $this->buildItems(
                        [31200, 31199, 31197, 31196],
                        [20, 26, 21, 31],
                        [10, 10, 10, 10]
                    )
                ),
            ],
            [
                'name' => 'retro-godly-clothing',
                'display_name' => '^006400Ultimate Equipment Forger^000000',
                'tagline' => 'Ultimate Boots & Armors for SA/EMP/LDS Sets from our Battlegrounds, best gear in the game!',
                'uber_range' => '3-5',
                'items' => array_merge(
                    // +0 boots/armor
                    $this->buildItems(
                        [3163, 3164, 3165, 3166, 3167, 3168],
                        [3, 3, 3, 3, 3, 3],
                        [0, 0, 0, 0, 0, 0]
                    ),
                    // +10 boots/armor
                    $this->buildItems(
                        [3163, 3164, 3165, 3166, 3167, 3168],
                        [5, 5, 5, 5, 5, 5],
                        [10, 10, 10, 10, 10, 10]
                    )
                ),
            ],
            [
                'name' => 'retro-accessories',
                'display_name' => '^FF005DAccessory Dealer^000000',
                'tagline' => 'Rare Accessories',
                'uber_range' => '4',
                'items' => $this->buildItems(
                    [28000, 28001, 28002, 28003, 28004, 28005, 28006, 28007, 28008, 28009, 28010, 28011, 28012, 28013, 28014, 28015, 28016, 28017],
                    array_fill(0, 18, 4)
                ),
            ],
            [
                'name' => 'retro-costumes',
                'display_name' => '^FF005DCostume Designer^000000',
                'tagline' => 'Amazing Costumes available for our biggest supporters~',
                'uber_range' => '1-85',
                'items' => $this->buildItems(
                    [20398, 19563, 19594, 19595, 31265],
                    [1, 60, 65, 85, 20]
                ),
            ],
            [
                'name' => 'retro-battleground-costumes',
                'display_name' => '^FF4500Battleground Costumes^000000',
                'tagline' => 'Battleground Costumes for you<3 !',
                'uber_range' => '5-45',
                'items' => $this->buildItems(
                    [19846, 19847, 19848, 19849, 20396, 19628, 19629, 19630, 19631, 19632, 31254],
                    [14, 14, 14, 14, 45, 5, 5, 5, 5, 5, 10]
                ),
            ],
        ];
    }

    /**
     * @param  array<int, int>  $itemIds
     * @param  array<int, int>  $costs
     * @param  array<int, int>|null  $refines
     * @return array<int, array{item_id: int, cost: int, refine?: int}>
     */
    private function buildItems(array $itemIds, array $costs, ?array $refines = null): array
    {
        $items = [];
        foreach ($itemIds as $index => $itemId) {
            $item = [
                'item_id' => $itemId,
                'cost' => $costs[$index] ?? 0,
            ];
            if ($refines !== null && isset($refines[$index])) {
                $item['refine'] = $refines[$index];
            }
            $items[] = $item;
        }

        return $items;
    }
}
