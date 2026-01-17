<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\UberShopCategory;
use App\Models\UberShopItem;
use Illuminate\Database\Seeder;

class DonateStoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Seeds the donation store with 6 categories and items for both XileRO and XileRetro.
     */
    public function run(): void
    {
        // Clear existing shop data
        UberShopItem::query()->delete();

        $this->seedCategories();
        $this->seedXileROItems();
        $this->seedXileRetroItems();
    }

    private function seedCategories(): void
    {
        $categories = [
            [
                'id' => 1,
                'name' => 'budget',
                'display_name' => '^00BFC8Budget Shop^000000',
                'tagline' => 'Affordable essentials - Misc items and shields',
                'uber_range' => '1-3 Ubers',
                'display_order' => 1,
                'enabled' => true,
            ],
            [
                'id' => 2,
                'name' => 'equipment',
                'display_name' => '^0000ffEquipment Shop^000000',
                'tagline' => 'Mid-tier gear - Token sets and Ultimate equipment sets',
                'uber_range' => '4-27 Ubers',
                'display_order' => 2,
                'enabled' => true,
            ],
            [
                'id' => 3,
                'name' => 'weapons',
                'display_name' => '^ff9900Weapons Shop^000000',
                'tagline' => 'Combat gear - Basic weapons (1 uber) and elite +10 weapons (3 ubers)',
                'uber_range' => '1-3 Ubers',
                'display_order' => 3,
                'enabled' => true,
            ],
            [
                'id' => 4,
                'name' => 'cards',
                'display_name' => '^008800Card Shop^000000',
                'tagline' => 'Power enhancement - Cards to boost your character stats and abilities',
                'uber_range' => '1-5 Ubers',
                'display_order' => 4,
                'enabled' => true,
            ],
            [
                'id' => 5,
                'name' => 'costume',
                'display_name' => '^FF4500Costume Shop^000000',
                'tagline' => 'Style and fashion - XileRO relics, designer pieces and battleground costumes',
                'uber_range' => '1-85 Ubers',
                'display_order' => 5,
                'enabled' => true,
            ],
            [
                'id' => 6,
                'name' => 'elite',
                'display_name' => '^8B0000Elite Shop^000000',
                'tagline' => 'Premium exclusives - Flexors and ultimate +10 headgear for top supporters',
                'uber_range' => '7-120 Ubers',
                'display_order' => 6,
                'enabled' => true,
            ],
        ];

        foreach ($categories as $category) {
            UberShopCategory::updateOrCreate(
                ['id' => $category['id']],
                $category
            );
        }
    }

    private function seedXileROItems(): void
    {
        $items = [
            // Budget Shop (11 items)
            ['category_id' => 1, 'item_id' => 17246, 'uber_cost' => 2, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 1],
            ['category_id' => 1, 'item_id' => 17247, 'uber_cost' => 2, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 2],
            ['category_id' => 1, 'item_id' => 20418, 'uber_cost' => 1, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 3],
            ['category_id' => 1, 'item_id' => 20001, 'uber_cost' => 1, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 4],
            ['category_id' => 1, 'item_id' => 20450, 'uber_cost' => 3, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 5],
            ['category_id' => 1, 'item_id' => 2102, 'uber_cost' => 2, 'refine_level' => 10, 'quantity' => 1, 'display_order' => 6],
            ['category_id' => 1, 'item_id' => 2104, 'uber_cost' => 2, 'refine_level' => 10, 'quantity' => 1, 'display_order' => 7],
            ['category_id' => 1, 'item_id' => 2106, 'uber_cost' => 2, 'refine_level' => 10, 'quantity' => 1, 'display_order' => 8],
            ['category_id' => 1, 'item_id' => 2108, 'uber_cost' => 2, 'refine_level' => 10, 'quantity' => 1, 'display_order' => 9],
            ['category_id' => 1, 'item_id' => 2504, 'uber_cost' => 2, 'refine_level' => 10, 'quantity' => 1, 'display_order' => 10],
            ['category_id' => 1, 'item_id' => 2506, 'uber_cost' => 2, 'refine_level' => 10, 'quantity' => 1, 'display_order' => 11],

            // Equipment Shop (22 items)
            ['category_id' => 2, 'item_id' => 20101, 'uber_cost' => 5, 'refine_level' => 10, 'quantity' => 1, 'display_order' => 1],
            ['category_id' => 2, 'item_id' => 20102, 'uber_cost' => 10, 'refine_level' => 10, 'quantity' => 1, 'display_order' => 2],
            ['category_id' => 2, 'item_id' => 20103, 'uber_cost' => 5, 'refine_level' => 10, 'quantity' => 1, 'display_order' => 3],
            ['category_id' => 2, 'item_id' => 20104, 'uber_cost' => 10, 'refine_level' => 10, 'quantity' => 1, 'display_order' => 4],
            ['category_id' => 2, 'item_id' => 20105, 'uber_cost' => 5, 'refine_level' => 10, 'quantity' => 1, 'display_order' => 5],
            ['category_id' => 2, 'item_id' => 20106, 'uber_cost' => 10, 'refine_level' => 10, 'quantity' => 1, 'display_order' => 6],
            ['category_id' => 2, 'item_id' => 20107, 'uber_cost' => 4, 'refine_level' => 10, 'quantity' => 1, 'display_order' => 7],
            ['category_id' => 2, 'item_id' => 3170, 'uber_cost' => 6, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 8],
            ['category_id' => 2, 'item_id' => 3172, 'uber_cost' => 6, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 9],
            ['category_id' => 2, 'item_id' => 3171, 'uber_cost' => 9, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 10],
            ['category_id' => 2, 'item_id' => 3173, 'uber_cost' => 6, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 11],
            ['category_id' => 2, 'item_id' => 3175, 'uber_cost' => 6, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 12],
            ['category_id' => 2, 'item_id' => 3174, 'uber_cost' => 9, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 13],
            ['category_id' => 2, 'item_id' => 3176, 'uber_cost' => 7, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 14],
            ['category_id' => 2, 'item_id' => 3178, 'uber_cost' => 7, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 15],
            ['category_id' => 2, 'item_id' => 3177, 'uber_cost' => 11, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 16],
            ['category_id' => 2, 'item_id' => 3163, 'uber_cost' => 3, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 17],
            ['category_id' => 2, 'item_id' => 3164, 'uber_cost' => 3, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 18],
            ['category_id' => 2, 'item_id' => 3165, 'uber_cost' => 3, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 19],
            ['category_id' => 2, 'item_id' => 3166, 'uber_cost' => 3, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 20],
            ['category_id' => 2, 'item_id' => 3167, 'uber_cost' => 3, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 21],
            ['category_id' => 2, 'item_id' => 3168, 'uber_cost' => 3, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 22],

            // Weapons Shop - Unrefined (19 items, 1 uber each)
            ['category_id' => 3, 'item_id' => 3150, 'uber_cost' => 1, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 1],
            ['category_id' => 3, 'item_id' => 3151, 'uber_cost' => 1, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 2],
            ['category_id' => 3, 'item_id' => 3152, 'uber_cost' => 1, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 3],
            ['category_id' => 3, 'item_id' => 3153, 'uber_cost' => 1, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 4],
            ['category_id' => 3, 'item_id' => 3154, 'uber_cost' => 1, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 5],
            ['category_id' => 3, 'item_id' => 3155, 'uber_cost' => 1, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 6],
            ['category_id' => 3, 'item_id' => 3156, 'uber_cost' => 1, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 7],
            ['category_id' => 3, 'item_id' => 3157, 'uber_cost' => 1, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 8],
            ['category_id' => 3, 'item_id' => 3158, 'uber_cost' => 1, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 9],
            ['category_id' => 3, 'item_id' => 3159, 'uber_cost' => 1, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 10],
            ['category_id' => 3, 'item_id' => 3160, 'uber_cost' => 1, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 11],
            ['category_id' => 3, 'item_id' => 3161, 'uber_cost' => 1, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 12],
            ['category_id' => 3, 'item_id' => 19118, 'uber_cost' => 1, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 13],
            ['category_id' => 3, 'item_id' => 19119, 'uber_cost' => 1, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 14],
            ['category_id' => 3, 'item_id' => 19120, 'uber_cost' => 1, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 15],
            ['category_id' => 3, 'item_id' => 19121, 'uber_cost' => 1, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 16],
            ['category_id' => 3, 'item_id' => 19122, 'uber_cost' => 1, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 17],
            ['category_id' => 3, 'item_id' => 19123, 'uber_cost' => 1, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 18],
            ['category_id' => 3, 'item_id' => 19124, 'uber_cost' => 1, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 19],

            // Weapons Shop - +10 Refined (19 items, 3 ubers each)
            ['category_id' => 3, 'item_id' => 3150, 'uber_cost' => 3, 'refine_level' => 10, 'quantity' => 1, 'display_order' => 20],
            ['category_id' => 3, 'item_id' => 3151, 'uber_cost' => 3, 'refine_level' => 10, 'quantity' => 1, 'display_order' => 21],
            ['category_id' => 3, 'item_id' => 3152, 'uber_cost' => 3, 'refine_level' => 10, 'quantity' => 1, 'display_order' => 22],
            ['category_id' => 3, 'item_id' => 3153, 'uber_cost' => 3, 'refine_level' => 10, 'quantity' => 1, 'display_order' => 23],
            ['category_id' => 3, 'item_id' => 3154, 'uber_cost' => 3, 'refine_level' => 10, 'quantity' => 1, 'display_order' => 24],
            ['category_id' => 3, 'item_id' => 3155, 'uber_cost' => 3, 'refine_level' => 10, 'quantity' => 1, 'display_order' => 25],
            ['category_id' => 3, 'item_id' => 3156, 'uber_cost' => 3, 'refine_level' => 10, 'quantity' => 1, 'display_order' => 26],
            ['category_id' => 3, 'item_id' => 3157, 'uber_cost' => 3, 'refine_level' => 10, 'quantity' => 1, 'display_order' => 27],
            ['category_id' => 3, 'item_id' => 3158, 'uber_cost' => 3, 'refine_level' => 10, 'quantity' => 1, 'display_order' => 28],
            ['category_id' => 3, 'item_id' => 3159, 'uber_cost' => 3, 'refine_level' => 10, 'quantity' => 1, 'display_order' => 29],
            ['category_id' => 3, 'item_id' => 3160, 'uber_cost' => 3, 'refine_level' => 10, 'quantity' => 1, 'display_order' => 30],
            ['category_id' => 3, 'item_id' => 3161, 'uber_cost' => 3, 'refine_level' => 10, 'quantity' => 1, 'display_order' => 31],
            ['category_id' => 3, 'item_id' => 19118, 'uber_cost' => 3, 'refine_level' => 10, 'quantity' => 1, 'display_order' => 32],
            ['category_id' => 3, 'item_id' => 19119, 'uber_cost' => 3, 'refine_level' => 10, 'quantity' => 1, 'display_order' => 33],
            ['category_id' => 3, 'item_id' => 19120, 'uber_cost' => 3, 'refine_level' => 10, 'quantity' => 1, 'display_order' => 34],
            ['category_id' => 3, 'item_id' => 19121, 'uber_cost' => 3, 'refine_level' => 10, 'quantity' => 1, 'display_order' => 35],
            ['category_id' => 3, 'item_id' => 19122, 'uber_cost' => 3, 'refine_level' => 10, 'quantity' => 1, 'display_order' => 36],
            ['category_id' => 3, 'item_id' => 19123, 'uber_cost' => 3, 'refine_level' => 10, 'quantity' => 1, 'display_order' => 37],
            ['category_id' => 3, 'item_id' => 19124, 'uber_cost' => 3, 'refine_level' => 10, 'quantity' => 1, 'display_order' => 38],

            // Card Shop (16 items)
            ['category_id' => 4, 'item_id' => 4172, 'uber_cost' => 1, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 1],
            ['category_id' => 4, 'item_id' => 4196, 'uber_cost' => 1, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 2],
            ['category_id' => 4, 'item_id' => 4147, 'uber_cost' => 2, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 3],
            ['category_id' => 4, 'item_id' => 4047, 'uber_cost' => 2, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 4],
            ['category_id' => 4, 'item_id' => 4198, 'uber_cost' => 2, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 5],
            ['category_id' => 4, 'item_id' => 4207, 'uber_cost' => 2, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 6],
            ['category_id' => 4, 'item_id' => 4208, 'uber_cost' => 2, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 7],
            ['category_id' => 4, 'item_id' => 4209, 'uber_cost' => 2, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 8],
            ['category_id' => 4, 'item_id' => 4210, 'uber_cost' => 2, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 9],
            ['category_id' => 4, 'item_id' => 4211, 'uber_cost' => 2, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 10],
            ['category_id' => 4, 'item_id' => 4121, 'uber_cost' => 3, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 11],
            ['category_id' => 4, 'item_id' => 4128, 'uber_cost' => 3, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 12],
            ['category_id' => 4, 'item_id' => 4441, 'uber_cost' => 5, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 13],
            ['category_id' => 4, 'item_id' => 4263, 'uber_cost' => 5, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 14],
            ['category_id' => 4, 'item_id' => 4403, 'uber_cost' => 5, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 15],
            ['category_id' => 4, 'item_id' => 4318, 'uber_cost' => 5, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 16],

            // Costume Shop (24 items)
            ['category_id' => 5, 'item_id' => 20398, 'uber_cost' => 1, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 1],
            ['category_id' => 5, 'item_id' => 19808, 'uber_cost' => 3, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 2],
            ['category_id' => 5, 'item_id' => 19809, 'uber_cost' => 3, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 3],
            ['category_id' => 5, 'item_id' => 19810, 'uber_cost' => 3, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 4],
            ['category_id' => 5, 'item_id' => 19811, 'uber_cost' => 3, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 5],
            ['category_id' => 5, 'item_id' => 19812, 'uber_cost' => 3, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 6],
            ['category_id' => 5, 'item_id' => 19813, 'uber_cost' => 3, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 7],
            ['category_id' => 5, 'item_id' => 20466, 'uber_cost' => 10, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 8],
            ['category_id' => 5, 'item_id' => 20472, 'uber_cost' => 8, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 9],
            ['category_id' => 5, 'item_id' => 20469, 'uber_cost' => 12, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 10],
            ['category_id' => 5, 'item_id' => 20467, 'uber_cost' => 10, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 11],
            ['category_id' => 5, 'item_id' => 20473, 'uber_cost' => 8, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 12],
            ['category_id' => 5, 'item_id' => 20470, 'uber_cost' => 12, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 13],
            ['category_id' => 5, 'item_id' => 20468, 'uber_cost' => 10, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 14],
            ['category_id' => 5, 'item_id' => 20474, 'uber_cost' => 8, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 15],
            ['category_id' => 5, 'item_id' => 20471, 'uber_cost' => 12, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 16],
            ['category_id' => 5, 'item_id' => 31329, 'uber_cost' => 9, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 17],
            ['category_id' => 5, 'item_id' => 31327, 'uber_cost' => 8, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 18],
            ['category_id' => 5, 'item_id' => 31328, 'uber_cost' => 14, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 19],
            ['category_id' => 5, 'item_id' => 19563, 'uber_cost' => 60, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 20],
            ['category_id' => 5, 'item_id' => 19594, 'uber_cost' => 65, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 21],
            ['category_id' => 5, 'item_id' => 19595, 'uber_cost' => 85, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 22],
            ['category_id' => 5, 'item_id' => 31265, 'uber_cost' => 20, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 23],
            ['category_id' => 5, 'item_id' => 31254, 'uber_cost' => 10, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 24],

            // Elite Shop (8 items)
            ['category_id' => 6, 'item_id' => 20453, 'uber_cost' => 120, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 1],
            ['category_id' => 6, 'item_id' => 20454, 'uber_cost' => 120, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 2],
            ['category_id' => 6, 'item_id' => 3170, 'uber_cost' => 7, 'refine_level' => 10, 'quantity' => 1, 'display_order' => 3],
            ['category_id' => 6, 'item_id' => 3171, 'uber_cost' => 10, 'refine_level' => 10, 'quantity' => 1, 'display_order' => 4],
            ['category_id' => 6, 'item_id' => 3173, 'uber_cost' => 7, 'refine_level' => 10, 'quantity' => 1, 'display_order' => 5],
            ['category_id' => 6, 'item_id' => 3174, 'uber_cost' => 10, 'refine_level' => 10, 'quantity' => 1, 'display_order' => 6],
            ['category_id' => 6, 'item_id' => 3176, 'uber_cost' => 8, 'refine_level' => 10, 'quantity' => 1, 'display_order' => 7],
            ['category_id' => 6, 'item_id' => 3177, 'uber_cost' => 12, 'refine_level' => 10, 'quantity' => 1, 'display_order' => 8],
        ];

        // Look up Item IDs and create UberShopItems
        foreach ($items as $itemData) {
            $item = Item::where('item_id', $itemData['item_id'])
                ->where('is_xileretro', false)
                ->first();

            if (! $item) {
                $this->command->warn("Item not found: {$itemData['item_id']} (XileRO)");

                continue;
            }

            UberShopItem::create([
                'category_id' => $itemData['category_id'],
                'item_id' => $item->id,
                'uber_cost' => $itemData['uber_cost'],
                'refine_level' => $itemData['refine_level'],
                'quantity' => $itemData['quantity'],
                'display_order' => $itemData['display_order'],
                'enabled' => true,
                'is_xilero' => true,
                'is_xileretro' => false,
            ]);
        }

        $this->command->info('Seeded '.count($items).' XileRO donation shop items.');
    }

    private function seedXileRetroItems(): void
    {
        $items = [
            // Budget Shop - Misc Items
            ['category_id' => 1, 'item_id' => 20452, 'uber_cost' => 1, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 1],
            ['category_id' => 1, 'item_id' => 21006, 'uber_cost' => 4, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 2],
            ['category_id' => 1, 'item_id' => 20363, 'uber_cost' => 4, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 3],
            ['category_id' => 1, 'item_id' => 17246, 'uber_cost' => 2, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 4],
            ['category_id' => 1, 'item_id' => 17247, 'uber_cost' => 2, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 5],
            ['category_id' => 1, 'item_id' => 20418, 'uber_cost' => 1, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 6],
            ['category_id' => 1, 'item_id' => 20001, 'uber_cost' => 1, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 7],
            ['category_id' => 1, 'item_id' => 22552, 'uber_cost' => 1, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 8],
            ['category_id' => 1, 'item_id' => 20450, 'uber_cost' => 3, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 9],
            ['category_id' => 1, 'item_id' => 20110, 'uber_cost' => 1, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 10],
            ['category_id' => 1, 'item_id' => 20111, 'uber_cost' => 2, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 11],
            ['category_id' => 1, 'item_id' => 20112, 'uber_cost' => 4, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 12],
            ['category_id' => 1, 'item_id' => 20113, 'uber_cost' => 8, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 13],

            // Budget Shop - Essence
            ['category_id' => 1, 'item_id' => 20065, 'uber_cost' => 1, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 14],
            ['category_id' => 1, 'item_id' => 20066, 'uber_cost' => 1, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 15],
            ['category_id' => 1, 'item_id' => 20067, 'uber_cost' => 1, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 16],
            ['category_id' => 1, 'item_id' => 20068, 'uber_cost' => 1, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 17],
            ['category_id' => 1, 'item_id' => 20069, 'uber_cost' => 1, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 18],
            ['category_id' => 1, 'item_id' => 20070, 'uber_cost' => 1, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 19],
            ['category_id' => 1, 'item_id' => 20459, 'uber_cost' => 5, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 20],
            ['category_id' => 1, 'item_id' => 20460, 'uber_cost' => 5, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 21],
            ['category_id' => 1, 'item_id' => 20461, 'uber_cost' => 5, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 22],
            ['category_id' => 1, 'item_id' => 20462, 'uber_cost' => 5, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 23],
            ['category_id' => 1, 'item_id' => 20463, 'uber_cost' => 5, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 24],
            ['category_id' => 1, 'item_id' => 20464, 'uber_cost' => 5, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 25],

            // Budget Shop - Premium Tickets
            ['category_id' => 1, 'item_id' => 20192, 'uber_cost' => 1, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 26],

            // Budget Shop - Shield & Garment (+10)
            ['category_id' => 1, 'item_id' => 2102, 'uber_cost' => 2, 'refine_level' => 10, 'quantity' => 1, 'display_order' => 27],
            ['category_id' => 1, 'item_id' => 2104, 'uber_cost' => 2, 'refine_level' => 10, 'quantity' => 1, 'display_order' => 28],
            ['category_id' => 1, 'item_id' => 2106, 'uber_cost' => 2, 'refine_level' => 10, 'quantity' => 1, 'display_order' => 29],
            ['category_id' => 1, 'item_id' => 2108, 'uber_cost' => 2, 'refine_level' => 10, 'quantity' => 1, 'display_order' => 30],
            ['category_id' => 1, 'item_id' => 2504, 'uber_cost' => 2, 'refine_level' => 10, 'quantity' => 1, 'display_order' => 31],
            ['category_id' => 1, 'item_id' => 2506, 'uber_cost' => 2, 'refine_level' => 10, 'quantity' => 1, 'display_order' => 32],

            // Equipment Shop - Token Sets (+10)
            ['category_id' => 2, 'item_id' => 20101, 'uber_cost' => 5, 'refine_level' => 10, 'quantity' => 1, 'display_order' => 1],
            ['category_id' => 2, 'item_id' => 20102, 'uber_cost' => 10, 'refine_level' => 10, 'quantity' => 1, 'display_order' => 2],
            ['category_id' => 2, 'item_id' => 20103, 'uber_cost' => 5, 'refine_level' => 10, 'quantity' => 1, 'display_order' => 3],
            ['category_id' => 2, 'item_id' => 20104, 'uber_cost' => 10, 'refine_level' => 10, 'quantity' => 1, 'display_order' => 4],
            ['category_id' => 2, 'item_id' => 20105, 'uber_cost' => 5, 'refine_level' => 10, 'quantity' => 1, 'display_order' => 5],
            ['category_id' => 2, 'item_id' => 20106, 'uber_cost' => 10, 'refine_level' => 10, 'quantity' => 1, 'display_order' => 6],
            ['category_id' => 2, 'item_id' => 20107, 'uber_cost' => 4, 'refine_level' => 10, 'quantity' => 1, 'display_order' => 7],

            // Equipment Shop - Godly Headgear SA/EMP/LD (unrefined)
            ['category_id' => 2, 'item_id' => 3170, 'uber_cost' => 6, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 8],
            ['category_id' => 2, 'item_id' => 3172, 'uber_cost' => 6, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 9],
            ['category_id' => 2, 'item_id' => 3171, 'uber_cost' => 9, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 10],
            ['category_id' => 2, 'item_id' => 3173, 'uber_cost' => 6, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 11],
            ['category_id' => 2, 'item_id' => 3175, 'uber_cost' => 6, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 12],
            ['category_id' => 2, 'item_id' => 3174, 'uber_cost' => 9, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 13],
            ['category_id' => 2, 'item_id' => 3176, 'uber_cost' => 7, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 14],
            ['category_id' => 2, 'item_id' => 3178, 'uber_cost' => 7, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 15],
            ['category_id' => 2, 'item_id' => 3177, 'uber_cost' => 11, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 16],

            // Equipment Shop - Godly Headgear SA/EMP/LD (+10)
            ['category_id' => 2, 'item_id' => 3170, 'uber_cost' => 7, 'refine_level' => 10, 'quantity' => 1, 'display_order' => 17],
            ['category_id' => 2, 'item_id' => 3171, 'uber_cost' => 10, 'refine_level' => 10, 'quantity' => 1, 'display_order' => 18],
            ['category_id' => 2, 'item_id' => 3173, 'uber_cost' => 7, 'refine_level' => 10, 'quantity' => 1, 'display_order' => 19],
            ['category_id' => 2, 'item_id' => 3174, 'uber_cost' => 10, 'refine_level' => 10, 'quantity' => 1, 'display_order' => 20],
            ['category_id' => 2, 'item_id' => 3176, 'uber_cost' => 8, 'refine_level' => 10, 'quantity' => 1, 'display_order' => 21],
            ['category_id' => 2, 'item_id' => 3177, 'uber_cost' => 12, 'refine_level' => 10, 'quantity' => 1, 'display_order' => 22],

            // Equipment Shop - Godly Headgear v2 Infi/DD (unrefined)
            ['category_id' => 2, 'item_id' => 31200, 'uber_cost' => 17, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 23],
            ['category_id' => 2, 'item_id' => 31198, 'uber_cost' => 15, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 24],
            ['category_id' => 2, 'item_id' => 31199, 'uber_cost' => 24, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 25],
            ['category_id' => 2, 'item_id' => 31197, 'uber_cost' => 18, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 26],
            ['category_id' => 2, 'item_id' => 31195, 'uber_cost' => 16, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 27],
            ['category_id' => 2, 'item_id' => 31196, 'uber_cost' => 27, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 28],

            // Equipment Shop - Godly Headgear v2 Infi/DD (+10)
            ['category_id' => 2, 'item_id' => 31200, 'uber_cost' => 20, 'refine_level' => 10, 'quantity' => 1, 'display_order' => 29],
            ['category_id' => 2, 'item_id' => 31199, 'uber_cost' => 26, 'refine_level' => 10, 'quantity' => 1, 'display_order' => 30],
            ['category_id' => 2, 'item_id' => 31197, 'uber_cost' => 21, 'refine_level' => 10, 'quantity' => 1, 'display_order' => 31],
            ['category_id' => 2, 'item_id' => 31196, 'uber_cost' => 31, 'refine_level' => 10, 'quantity' => 1, 'display_order' => 32],

            // Equipment Shop - Godly Clothing (unrefined)
            ['category_id' => 2, 'item_id' => 3163, 'uber_cost' => 3, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 33],
            ['category_id' => 2, 'item_id' => 3164, 'uber_cost' => 3, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 34],
            ['category_id' => 2, 'item_id' => 3165, 'uber_cost' => 3, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 35],
            ['category_id' => 2, 'item_id' => 3166, 'uber_cost' => 3, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 36],
            ['category_id' => 2, 'item_id' => 3167, 'uber_cost' => 3, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 37],
            ['category_id' => 2, 'item_id' => 3168, 'uber_cost' => 3, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 38],

            // Equipment Shop - Godly Clothing (+10)
            ['category_id' => 2, 'item_id' => 3163, 'uber_cost' => 5, 'refine_level' => 10, 'quantity' => 1, 'display_order' => 39],
            ['category_id' => 2, 'item_id' => 3164, 'uber_cost' => 5, 'refine_level' => 10, 'quantity' => 1, 'display_order' => 40],
            ['category_id' => 2, 'item_id' => 3165, 'uber_cost' => 5, 'refine_level' => 10, 'quantity' => 1, 'display_order' => 41],
            ['category_id' => 2, 'item_id' => 3166, 'uber_cost' => 5, 'refine_level' => 10, 'quantity' => 1, 'display_order' => 42],
            ['category_id' => 2, 'item_id' => 3167, 'uber_cost' => 5, 'refine_level' => 10, 'quantity' => 1, 'display_order' => 43],
            ['category_id' => 2, 'item_id' => 3168, 'uber_cost' => 5, 'refine_level' => 10, 'quantity' => 1, 'display_order' => 44],

            // Equipment Shop - Accessories
            ['category_id' => 2, 'item_id' => 28000, 'uber_cost' => 4, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 45],
            ['category_id' => 2, 'item_id' => 28001, 'uber_cost' => 4, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 46],
            ['category_id' => 2, 'item_id' => 28002, 'uber_cost' => 4, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 47],
            ['category_id' => 2, 'item_id' => 28003, 'uber_cost' => 4, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 48],
            ['category_id' => 2, 'item_id' => 28004, 'uber_cost' => 4, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 49],
            ['category_id' => 2, 'item_id' => 28005, 'uber_cost' => 4, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 50],
            ['category_id' => 2, 'item_id' => 28006, 'uber_cost' => 4, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 51],
            ['category_id' => 2, 'item_id' => 28007, 'uber_cost' => 4, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 52],
            ['category_id' => 2, 'item_id' => 28008, 'uber_cost' => 4, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 53],
            ['category_id' => 2, 'item_id' => 28009, 'uber_cost' => 4, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 54],
            ['category_id' => 2, 'item_id' => 28010, 'uber_cost' => 4, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 55],
            ['category_id' => 2, 'item_id' => 28011, 'uber_cost' => 4, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 56],
            ['category_id' => 2, 'item_id' => 28012, 'uber_cost' => 4, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 57],
            ['category_id' => 2, 'item_id' => 28013, 'uber_cost' => 4, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 58],
            ['category_id' => 2, 'item_id' => 28014, 'uber_cost' => 4, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 59],
            ['category_id' => 2, 'item_id' => 28015, 'uber_cost' => 4, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 60],
            ['category_id' => 2, 'item_id' => 28016, 'uber_cost' => 4, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 61],
            ['category_id' => 2, 'item_id' => 28017, 'uber_cost' => 4, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 62],

            // Weapons Shop - Unrefined (1 uber each)
            ['category_id' => 3, 'item_id' => 3150, 'uber_cost' => 1, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 1],
            ['category_id' => 3, 'item_id' => 3151, 'uber_cost' => 1, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 2],
            ['category_id' => 3, 'item_id' => 3152, 'uber_cost' => 1, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 3],
            ['category_id' => 3, 'item_id' => 3153, 'uber_cost' => 1, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 4],
            ['category_id' => 3, 'item_id' => 3154, 'uber_cost' => 1, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 5],
            ['category_id' => 3, 'item_id' => 3155, 'uber_cost' => 1, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 6],
            ['category_id' => 3, 'item_id' => 3156, 'uber_cost' => 1, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 7],
            ['category_id' => 3, 'item_id' => 3157, 'uber_cost' => 1, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 8],
            ['category_id' => 3, 'item_id' => 3158, 'uber_cost' => 1, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 9],
            ['category_id' => 3, 'item_id' => 3159, 'uber_cost' => 1, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 10],
            ['category_id' => 3, 'item_id' => 3160, 'uber_cost' => 1, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 11],
            ['category_id' => 3, 'item_id' => 3161, 'uber_cost' => 1, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 12],
            ['category_id' => 3, 'item_id' => 19118, 'uber_cost' => 1, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 13],
            ['category_id' => 3, 'item_id' => 19119, 'uber_cost' => 1, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 14],
            ['category_id' => 3, 'item_id' => 19120, 'uber_cost' => 1, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 15],
            ['category_id' => 3, 'item_id' => 19121, 'uber_cost' => 1, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 16],
            ['category_id' => 3, 'item_id' => 19122, 'uber_cost' => 1, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 17],
            ['category_id' => 3, 'item_id' => 19123, 'uber_cost' => 1, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 18],
            ['category_id' => 3, 'item_id' => 19124, 'uber_cost' => 1, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 19],

            // Weapons Shop - +10 Refined (3 ubers each)
            ['category_id' => 3, 'item_id' => 3150, 'uber_cost' => 3, 'refine_level' => 10, 'quantity' => 1, 'display_order' => 20],
            ['category_id' => 3, 'item_id' => 3151, 'uber_cost' => 3, 'refine_level' => 10, 'quantity' => 1, 'display_order' => 21],
            ['category_id' => 3, 'item_id' => 3152, 'uber_cost' => 3, 'refine_level' => 10, 'quantity' => 1, 'display_order' => 22],
            ['category_id' => 3, 'item_id' => 3153, 'uber_cost' => 3, 'refine_level' => 10, 'quantity' => 1, 'display_order' => 23],
            ['category_id' => 3, 'item_id' => 3154, 'uber_cost' => 3, 'refine_level' => 10, 'quantity' => 1, 'display_order' => 24],
            ['category_id' => 3, 'item_id' => 3155, 'uber_cost' => 3, 'refine_level' => 10, 'quantity' => 1, 'display_order' => 25],
            ['category_id' => 3, 'item_id' => 3156, 'uber_cost' => 3, 'refine_level' => 10, 'quantity' => 1, 'display_order' => 26],
            ['category_id' => 3, 'item_id' => 3157, 'uber_cost' => 3, 'refine_level' => 10, 'quantity' => 1, 'display_order' => 27],
            ['category_id' => 3, 'item_id' => 3158, 'uber_cost' => 3, 'refine_level' => 10, 'quantity' => 1, 'display_order' => 28],
            ['category_id' => 3, 'item_id' => 3159, 'uber_cost' => 3, 'refine_level' => 10, 'quantity' => 1, 'display_order' => 29],
            ['category_id' => 3, 'item_id' => 3160, 'uber_cost' => 3, 'refine_level' => 10, 'quantity' => 1, 'display_order' => 30],
            ['category_id' => 3, 'item_id' => 3161, 'uber_cost' => 3, 'refine_level' => 10, 'quantity' => 1, 'display_order' => 31],
            ['category_id' => 3, 'item_id' => 19118, 'uber_cost' => 3, 'refine_level' => 10, 'quantity' => 1, 'display_order' => 32],
            ['category_id' => 3, 'item_id' => 19119, 'uber_cost' => 3, 'refine_level' => 10, 'quantity' => 1, 'display_order' => 33],
            ['category_id' => 3, 'item_id' => 19120, 'uber_cost' => 3, 'refine_level' => 10, 'quantity' => 1, 'display_order' => 34],
            ['category_id' => 3, 'item_id' => 19121, 'uber_cost' => 3, 'refine_level' => 10, 'quantity' => 1, 'display_order' => 35],
            ['category_id' => 3, 'item_id' => 19122, 'uber_cost' => 3, 'refine_level' => 10, 'quantity' => 1, 'display_order' => 36],
            ['category_id' => 3, 'item_id' => 19123, 'uber_cost' => 3, 'refine_level' => 10, 'quantity' => 1, 'display_order' => 37],
            ['category_id' => 3, 'item_id' => 19124, 'uber_cost' => 3, 'refine_level' => 10, 'quantity' => 1, 'display_order' => 38],

            // Card Shop
            ['category_id' => 4, 'item_id' => 4172, 'uber_cost' => 1, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 1],
            ['category_id' => 4, 'item_id' => 4196, 'uber_cost' => 1, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 2],
            ['category_id' => 4, 'item_id' => 4147, 'uber_cost' => 2, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 3],
            ['category_id' => 4, 'item_id' => 4047, 'uber_cost' => 2, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 4],
            ['category_id' => 4, 'item_id' => 4198, 'uber_cost' => 2, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 5],
            ['category_id' => 4, 'item_id' => 4207, 'uber_cost' => 2, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 6],
            ['category_id' => 4, 'item_id' => 4208, 'uber_cost' => 2, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 7],
            ['category_id' => 4, 'item_id' => 4209, 'uber_cost' => 2, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 8],
            ['category_id' => 4, 'item_id' => 4210, 'uber_cost' => 2, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 9],
            ['category_id' => 4, 'item_id' => 4211, 'uber_cost' => 2, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 10],
            ['category_id' => 4, 'item_id' => 4121, 'uber_cost' => 3, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 11],
            ['category_id' => 4, 'item_id' => 4128, 'uber_cost' => 3, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 12],
            ['category_id' => 4, 'item_id' => 4441, 'uber_cost' => 5, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 13],
            ['category_id' => 4, 'item_id' => 4263, 'uber_cost' => 5, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 14],
            ['category_id' => 4, 'item_id' => 4403, 'uber_cost' => 5, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 15],
            ['category_id' => 4, 'item_id' => 4318, 'uber_cost' => 5, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 16],

            // Costume Shop - Relic Costumes
            ['category_id' => 5, 'item_id' => 19808, 'uber_cost' => 3, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 1],
            ['category_id' => 5, 'item_id' => 19809, 'uber_cost' => 3, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 2],
            ['category_id' => 5, 'item_id' => 19810, 'uber_cost' => 3, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 3],
            ['category_id' => 5, 'item_id' => 19811, 'uber_cost' => 3, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 4],
            ['category_id' => 5, 'item_id' => 19812, 'uber_cost' => 3, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 5],
            ['category_id' => 5, 'item_id' => 19813, 'uber_cost' => 3, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 6],
            ['category_id' => 5, 'item_id' => 20466, 'uber_cost' => 10, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 7],
            ['category_id' => 5, 'item_id' => 20472, 'uber_cost' => 8, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 8],
            ['category_id' => 5, 'item_id' => 20469, 'uber_cost' => 12, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 9],
            ['category_id' => 5, 'item_id' => 20467, 'uber_cost' => 10, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 10],
            ['category_id' => 5, 'item_id' => 20473, 'uber_cost' => 8, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 11],
            ['category_id' => 5, 'item_id' => 20470, 'uber_cost' => 12, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 12],
            ['category_id' => 5, 'item_id' => 20468, 'uber_cost' => 10, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 13],
            ['category_id' => 5, 'item_id' => 20474, 'uber_cost' => 8, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 14],
            ['category_id' => 5, 'item_id' => 20471, 'uber_cost' => 12, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 15],
            ['category_id' => 5, 'item_id' => 31329, 'uber_cost' => 9, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 16],
            ['category_id' => 5, 'item_id' => 31327, 'uber_cost' => 8, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 17],
            ['category_id' => 5, 'item_id' => 31328, 'uber_cost' => 14, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 18],

            // Costume Shop - Costumes
            ['category_id' => 5, 'item_id' => 20398, 'uber_cost' => 1, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 19],
            ['category_id' => 5, 'item_id' => 19563, 'uber_cost' => 60, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 20],
            ['category_id' => 5, 'item_id' => 19594, 'uber_cost' => 65, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 21],
            ['category_id' => 5, 'item_id' => 19595, 'uber_cost' => 85, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 22],
            ['category_id' => 5, 'item_id' => 31265, 'uber_cost' => 20, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 23],

            // Costume Shop - Battleground Costumes
            ['category_id' => 5, 'item_id' => 19846, 'uber_cost' => 14, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 24],
            ['category_id' => 5, 'item_id' => 19847, 'uber_cost' => 14, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 25],
            ['category_id' => 5, 'item_id' => 19848, 'uber_cost' => 14, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 26],
            ['category_id' => 5, 'item_id' => 19849, 'uber_cost' => 14, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 27],
            ['category_id' => 5, 'item_id' => 20396, 'uber_cost' => 45, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 28],
            ['category_id' => 5, 'item_id' => 19628, 'uber_cost' => 5, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 29],
            ['category_id' => 5, 'item_id' => 19629, 'uber_cost' => 5, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 30],
            ['category_id' => 5, 'item_id' => 19630, 'uber_cost' => 5, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 31],
            ['category_id' => 5, 'item_id' => 19631, 'uber_cost' => 5, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 32],
            ['category_id' => 5, 'item_id' => 19632, 'uber_cost' => 5, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 33],
            ['category_id' => 5, 'item_id' => 31254, 'uber_cost' => 10, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 34],

            // Elite Shop - Flexors
            ['category_id' => 6, 'item_id' => 20453, 'uber_cost' => 120, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 1],
            ['category_id' => 6, 'item_id' => 20454, 'uber_cost' => 120, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 2],
            ['category_id' => 6, 'item_id' => 20465, 'uber_cost' => 120, 'refine_level' => 0, 'quantity' => 1, 'display_order' => 3],
        ];

        // Look up Item IDs and create UberShopItems
        foreach ($items as $itemData) {
            $item = Item::where('item_id', $itemData['item_id'])
                ->where('is_xileretro', true)
                ->first();

            if (! $item) {
                $this->command->warn("Item not found: {$itemData['item_id']} (XileRetro)");

                continue;
            }

            UberShopItem::create([
                'category_id' => $itemData['category_id'],
                'item_id' => $item->id,
                'uber_cost' => $itemData['uber_cost'],
                'refine_level' => $itemData['refine_level'],
                'quantity' => $itemData['quantity'],
                'display_order' => $itemData['display_order'],
                'enabled' => true,
                'is_xilero' => false,
                'is_xileretro' => true,
            ]);
        }

        $this->command->info('Seeded '.count($items).' XileRetro donation shop items.');
    }
}
