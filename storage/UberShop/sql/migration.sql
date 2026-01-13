-- ================================================================================
-- UberShop Migration Data
-- Migrates 119 items from hardcoded donation_shop.txt to database
-- All item metadata included for web display
-- ================================================================================

-- ================================================================================
-- INSERT Categories (6 total)
-- ================================================================================
INSERT INTO `uber_shop_categories` (`id`, `name`, `display_name`, `tagline`, `uber_range`, `display_order`, `enabled`) VALUES
(1, 'budget',    '^00BFC8Budget Shop^000000',    'Affordable essentials - Misc items and shields', '1-3 Ubers', 1, 1),
(2, 'equipment', '^0000ffEquipment Shop^000000', 'Mid-tier gear - Token sets and Ultimate equipment sets', '4-27 Ubers', 2, 1),
(3, 'weapons',   '^ff9900Weapons Shop^000000',   'Combat gear - Basic weapons (1 uber) and elite +10 weapons (3 ubers)', '1-3 Ubers', 3, 1),
(4, 'cards',     '^008800Card Shop^000000',      'Power enhancement - Cards to boost your character stats and abilities', '1-5 Ubers', 4, 1),
(5, 'costume',   '^FF4500Costume Shop^000000',   'Style and fashion - XileRO relics, designer pieces and battleground costumes', '1-85 Ubers', 5, 1),
(6, 'elite',     '^8B0000Elite Shop^000000',     'Premium exclusives - Flexors and ultimate +10 headgear for top supporters', '7-120 Ubers', 6, 1);

-- ================================================================================
-- INSERT Items - Budget Shop (11 items)
-- ================================================================================
INSERT INTO `uber_shop_items` (`category_id`, `item_id`, `uber_cost`, `refine_level`, `quantity`, `display_order`, `enabled`, `item_name`, `aegis_name`, `item_type`, `item_subtype`, `weight`, `slots`, `description`, `equip_locations`, `icon_path`, `collection_path`) VALUES
(1, 17246, 2, 0, 1, 1, 1, 'HD Elunium Box(30)', 'HD_Elunium_Box_30', 'Usable', NULL, 10, 0, 'A box containinging 30 HD Elunium. Used as a material to strengthen armor currently from Refine Levels 7 through 9.', NULL, 'items/17246.png', NULL),
(1, 17247, 2, 0, 1, 2, 1, 'HD Oridecon Box(30)', 'HD_Oridecon_Box_30', 'Usable', NULL, 10, 0, 'A box containinging 30 HD Oridecon. Used as a material to strengthen weapon currently from Refine Levels 7 through 9.', NULL, 'items/17247.png', NULL),
(1, 20418, 1, 0, 1, 3, 1, 'Clone Pet Egg Box', 'Clone_Pet_Egg_Box', 'Usable', NULL, 200, 0, 'Grant yourself a Random Clone Pet~ WoE Exclusive', NULL, 'items/20418.png', NULL),
(1, 20001, 1, 0, 1, 4, 1, 'Corrupt Emperium', 'Corrupt_Emperium', 'Etc', NULL, 1000, 0, 'An Emperium with broken atoms that produces unexpected results. Take to Sealmaster Orion for further use.', NULL, 'items/20001.png', NULL),
(1, 20450, 3, 0, 1, 5, 1, 'Name Change Scroll', 'Name_Change_Scroll', 'Etc', NULL, 1000, 0, 'Ability to change your character name and identity in a flash.', NULL, 'items/20450.png', NULL),
(1, 2102, 2, 10, 1, 6, 1, 'Guard', 'Guard_', 'Armor', 'Shield', 300, 1, 'A square shield, small but effective. Defense: 3', 'Left_Hand', 'items/2102.png', 'collection/2102.png'),
(1, 2104, 2, 10, 1, 7, 1, 'Buckler', 'Buckler_', 'Armor', 'Shield', 600, 1, 'A round shield, small but effective. Defense: 4', 'Left_Hand', 'items/2104.png', 'collection/2104.png'),
(1, 2106, 2, 10, 1, 8, 1, 'Shield', 'Shield_', 'Armor', 'Shield', 1300, 1, 'A well crafted shield, impenetrable to nearly all attacks. Defense: 6', 'Left_Hand', 'items/2106.png', 'collection/2106.png'),
(1, 2108, 2, 10, 1, 9, 1, 'Mirror Shield', 'Mirror_Shield_', 'Armor', 'Shield', 1000, 1, 'A shield made entirely of mirrors. Reflects harmful magic. MDEF + 5. Defense: 4', 'Left_Hand', 'items/2108.png', 'collection/2108.png'),
(1, 2504, 2, 10, 1, 10, 1, 'Muffler', 'Muffler_', 'Armor', 'Garment', 400, 1, 'A scarf, usually worn around the neck and shoulders for warmth. Defense: 2', 'Garment', 'items/2504.png', 'collection/2504.png'),
(1, 2506, 2, 10, 1, 11, 1, 'Manteau', 'Manteau_', 'Armor', 'Garment', 600, 1, 'A long, loose, capelike garment providing protection from nature''s worst. Defense: 4', 'Garment', 'items/2506.png', 'collection/2506.png');

-- ================================================================================
-- INSERT Items - Equipment Shop (22 items)
-- ================================================================================
INSERT INTO `uber_shop_items` (`category_id`, `item_id`, `uber_cost`, `refine_level`, `quantity`, `display_order`, `enabled`, `item_name`, `aegis_name`, `item_type`, `item_subtype`, `weight`, `slots`, `description`, `equip_locations`, `icon_path`, `collection_path`) VALUES
(2, 20101, 5, 10, 1, 1, 1, 'Traveler Hat', 'Traveler_Hat', 'Armor', 'Headgear', 10, 3, '[Elite A-Tier Gear] A hat worn by the wandering traveler. All Stats +10, INT + 5, MDEF +5.', 'Head_Top', 'items/20101.png', 'collection/20101.png'),
(2, 20102, 10, 10, 1, 2, 1, 'Renove Armor', 'Renove_Armor', 'Armor', 'Body', 2500, 1, '[Elite A-Tier Gear] Armor of unknown origin. Said to have endless power! Set bonus with Traveler Hat.', 'Armor', 'items/20102.png', 'collection/20102.png'),
(2, 20103, 5, 10, 1, 3, 1, 'Dragon Helm', 'Dragon_Helm', 'Armor', 'Headgear', 10, 3, '[Elite A-Tier Gear] A helm made from the scales of true dragons. All Stats +10, STR + 5, MDEF +5.', 'Head_Top', 'items/20103.png', 'collection/20103.png'),
(2, 20104, 10, 10, 1, 4, 1, 'Lilim Armor', 'Lilim_Armor', 'Armor', 'Body', 2500, 1, '[Elite A-Tier Gear] Armor of unknown origin. Said to have endless power! Set bonus with Dragon Helm.', 'Armor', 'items/20104.png', 'collection/20104.png'),
(2, 20105, 5, 10, 1, 5, 1, 'Dowry Hat', 'Dowry', 'Armor', 'Headgear', 10, 3, '[Elite A-Tier Gear] A hat worn by the greatest of Ninja. All Stats +10, DEX + 5, MDEF +5.', 'Head_Top', 'items/20105.png', 'collection/20105.png'),
(2, 20106, 10, 10, 1, 6, 1, 'Tannin Armor', 'Tannin_Armor', 'Armor', 'Body', 2500, 1, '[Elite A-Tier Gear] Armor of unknown origin. Said to have endless power! Set bonus with Dowry Hat.', 'Armor', 'items/20106.png', 'collection/20106.png'),
(2, 20107, 4, 10, 1, 7, 1, 'Deviling Wings', 'Deviling_Wings', 'Armor', 'Headgear', 100, 1, '[Elite A-Tier Gear] Wings of the great Deviling itself. All Stats +5. Battlegrounds Exclusive.', 'Head_Low', 'items/20107.png', 'collection/20107.png'),
(2, 3170, 6, 0, 1, 8, 1, 'Scarlet Angel Helm', 'scarlet_angel_helm', 'Armor', 'Headgear', 100, 3, '[Godly S-Tier Gear] Helm of the fallen Scarlet Angel. DEX +4, STR +4, VIT +8, MDEF +9.', 'Head_Top', 'items/3170.png', 'collection/3170.png'),
(2, 3172, 6, 0, 1, 9, 1, 'Scarlet Angel Ears', 'scarlet_angel_ears', 'Armor', 'Headgear', 100, 1, '[Godly S-Tier Gear] Ears of the fallen Scarlet Angel. Add a 10% resistance against all Status Ailments.', 'Head_Mid', 'items/3172.png', 'collection/3172.png'),
(2, 3171, 9, 0, 1, 10, 1, 'Scarlet Angel Wings', 'scarlet_angel_wings', 'Armor', 'Headgear', 50, 1, '[Godly S-Tier Gear] Wings of the fallen Scarlet Angel. +5 to All Stats.', 'Head_Low', 'items/3171.png', 'collection/3171.png'),
(2, 3173, 6, 0, 1, 11, 1, 'Emperor Helm', 'emperor_helm', 'Armor', 'Headgear', 100, 3, '[Godly S-Tier Gear] Helm of the Emperor, ruler of all things. INT +3, AGI +3, VIT +5, MDEF +3.', 'Head_Top', 'items/3173.png', 'collection/3173.png'),
(2, 3175, 6, 0, 1, 12, 1, 'Emperor Shoulders', 'emperor_shoulders', 'Armor', 'Headgear', 100, 1, '[Godly S-Tier Gear] Shoulderpads of the Emperor, ruler of all things. Max HP +5%.', 'Head_Mid', 'items/3175.png', 'collection/3175.png'),
(2, 3174, 9, 0, 1, 13, 1, 'Emperor Wings', 'emperor_wings', 'Armor', 'Headgear', 50, 1, '[Godly S-Tier Gear] Wings of the Emperor, ruler of all things. +5 to All Stats.', 'Head_Low', 'items/3174.png', 'collection/3174.png'),
(2, 3176, 7, 0, 1, 14, 1, 'Little Devil Horns', 'little_devil_horns', 'Armor', 'Headgear', 100, 3, '[Godly S-Tier Gear] Horns that mark one as a devil. STR +5, VIT +1, MDEF +3.', 'Head_Top', 'items/3176.png', 'collection/3176.png'),
(2, 3178, 7, 0, 1, 15, 1, 'Little Devil Tail', 'little_devil_tail', 'Armor', 'Headgear', 100, 1, '[Godly S-Tier Gear] A tail that marks one as a devil. +5 to All Stats, MDEF +3, ATK +25, HIT +20.', 'Head_Mid', 'items/3178.png', 'collection/3178.png'),
(2, 3177, 11, 0, 1, 16, 1, 'Little Devil Wings', 'little_devil_wings', 'Armor', 'Headgear', 50, 1, '[Godly S-Tier Gear] Wings that mark one as a devil. MaxHP +5%, Movement +10%.', 'Head_Low', 'items/3177.png', 'collection/3177.png'),
(2, 3163, 3, 0, 1, 17, 1, 'Boots of Guidance', 'Boots_of_Guidance', 'Armor', 'Footgear', 3500, 1, '[Godly S-Tier Gear] Godly boots, that when equipped with special items, gives one wondrous powers. +5 to All Stats.', 'Shoes', 'items/3163.png', 'collection/3163.png'),
(2, 3164, 3, 0, 1, 18, 1, 'Hero Plate Armor', 'Hero_Plate_Armor', 'Armor', 'Body', 5500, 1, '[Godly S-Tier Gear] Very Heavy Armor worn only by true heroes. STR +5, VIT +5, INT +5.', 'Armor', 'items/3164.png', 'collection/3164.png'),
(2, 3165, 3, 0, 1, 19, 1, 'Rally Plate Armor', 'Rally_Plate_Armor', 'Armor', 'Body', 4500, 1, '[Godly S-Tier Gear] Heavy armor, used in the most intense times of battle. STR +5, VIT +5, LUK +5.', 'Armor', 'items/3165.png', 'collection/3165.png'),
(2, 3166, 3, 0, 1, 20, 1, 'Mithril Armor', 'Mithril_Armor', 'Armor', 'Body', 3300, 1, '[Godly S-Tier Gear] Mail armor formed from incredibly strong and light metal. STR +5, VIT +5, INT +5, LUK +5.', 'Armor', 'items/3166.png', 'collection/3166.png'),
(2, 3167, 3, 0, 1, 21, 1, 'Grand Gaiters', 'Grand_Gaiters', 'Armor', 'Body', 500, 1, '[Godly S-Tier Gear] A pair of form fitting tights, made for comfort and stealth. STR +5, VIT +5, DEX +5, LUK +5.', 'Armor', 'items/3167.png', 'collection/3167.png'),
(2, 3168, 3, 0, 1, 22, 1, 'Clothes of Brilliance', 'Clothes_of_Brilliance', 'Armor', 'Body', 2500, 1, '[Godly S-Tier Gear] Clothes given to adepts with exceptional talent. STR +5, VIT +5, INT +5, LUK +5, DEX +5.', 'Armor', 'items/3168.png', 'collection/3168.png');

-- ================================================================================
-- INSERT Items - Weapons Shop (38 items: 19 unrefined + 19 +10)
-- ================================================================================
INSERT INTO `uber_shop_items` (`category_id`, `item_id`, `uber_cost`, `refine_level`, `quantity`, `display_order`, `enabled`, `item_name`, `aegis_name`, `item_type`, `item_subtype`, `weight`, `slots`, `description`, `equip_locations`, `icon_path`, `collection_path`) VALUES
-- Unrefined weapons (1 uber each)
(3, 3150, 1, 0, 1, 1, 1, 'Staff of Ages', 'Staff_of_Ages', 'Weapon', 'Staff', 50, 4, '[Elite A-Tier Gear] Staff with powers said strong enough to stop time itself. Int +15, Dex +10, MatkRate +20%.', 'Right_Hand', 'items/3150.png', 'collection/3150.png'),
(3, 3151, 1, 0, 1, 2, 1, 'Soul Seeker', 'Soul_Seeker', 'Weapon', 'Bow', 50, 4, '[Elite A-Tier Gear] Bow with speed and accuracy that are unrivaled. Dex +15, Vit +10, Luk +5.', 'Both_Hand', 'items/3151.png', 'collection/3151.png'),
(3, 3152, 1, 0, 1, 3, 1, 'Holy Flail', 'Holy_Flail', 'Weapon', 'Mace', 50, 4, '[Elite A-Tier Gear] Mace used by grand Priest and Sages. Int +15, Luk +10, Vit +10, Healing +105%, MATK +15%.', 'Right_Hand', 'items/3152.png', 'collection/3152.png'),
(3, 3153, 1, 0, 1, 4, 1, 'Oblivion Blade', 'Oblivion_Blade', 'Weapon', 'Dagger', 50, 4, '[Elite A-Tier Gear] Large blade which grants anyone who wears it massive power. +7 to All Stats.', 'Right_Hand', 'items/3153.png', 'collection/3153.png'),
(3, 3154, 1, 0, 1, 5, 1, 'Frenzic', 'Frenzic', 'Weapon', 'Knuckle', 5, 4, '[Elite A-Tier Gear] Insanely sharp claws used for hunting only the most massive of beast. AspdRate +15%, Luk +10, Vit +10, Int +5.', 'Right_Hand', 'items/3154.png', 'collection/3154.png'),
(3, 3155, 1, 0, 1, 6, 1, 'Katar of Chaos', 'Katar_of_Chaos', 'Weapon', 'Katar', 50, 4, '[Elite A-Tier Gear] Katar which brings only chaos with its blades. Agi +15, Vit +10, Str +5.', 'Both_Hand', 'items/3155.png', 'collection/3155.png'),
(3, 3156, 1, 0, 1, 7, 1, 'Reaper', 'Reaper', 'Weapon', '2hAxe', 5, 4, '[Elite A-Tier Gear] Long and fast axe used to defend and protect ones assets. AspdRate +40%, Str +30, Vit +10.', 'Right_Hand', 'items/3156.png', 'collection/3156.png'),
(3, 3157, 1, 0, 1, 8, 1, 'Wind Weaver', 'Wind_Weaver', 'Weapon', 'Musical', 50, 4, '[Elite A-Tier Gear] Guitar which projects arrows that pierce the wind. Int +5, Vit +10, Dex +15.', 'Right_Hand', 'items/3157.png', 'collection/3157.png'),
(3, 3158, 1, 0, 1, 9, 1, 'Dark Defier', 'Dark_Defier', 'Weapon', '2hSpear', 4500, 4, '[Elite A-Tier Gear] Spear of deep, twisted defiance. Str +15, Vit +10, Luk +5.', 'Right_Hand', 'items/3158.png', 'collection/3158.png'),
(3, 3159, 1, 0, 1, 10, 1, 'Phoenix Wing', 'Phoenix_Wing', 'Weapon', '1hSword', 5, 4, '[Elite A-Tier Gear] A Sword that grants the perfect balance of power. +5 to All Stats.', 'Right_Hand', 'items/3159.png', 'collection/3159.png'),
(3, 3160, 1, 0, 1, 11, 1, 'Retribution', 'Retribution', 'Weapon', '2hSword', 50, 4, '[Elite A-Tier Gear] Sword with dark powers used for the light of good. Critical +40, Aspd +10%, Str +20.', 'Both_Hand', 'items/3160.png', 'collection/3160.png'),
(3, 3161, 1, 0, 1, 12, 1, 'Raging Mic', 'Raging_Mic', 'Weapon', 'Whip', 50, 4, '[Elite A-Tier Gear] A mic enhanced to send out massive beatdowns. Luk +10, Dex +10, Vit +10.', 'Right_Hand', 'items/3161.png', 'collection/3161.png'),
(3, 19118, 1, 0, 1, 13, 1, 'Mossberg 500', 'Mossberg_500', 'Weapon', 'Shotgun', 100, 4, '[Elite A-Tier Gear] Mossberg with barrel reinforced with Carnium. Splash Damage. DEX +10, VIT +10, LUK +10, AGI +5.', 'Both_Hand', 'items/19118.png', 'collection/19118.png'),
(3, 19119, 1, 0, 1, 14, 1, 'Vulcan Minigun', 'Vulcan_Minigun', 'Weapon', 'Gatling', 250, 4, '[Elite A-Tier Gear] Minigun fires in rapid succession with electronic rotating barrel. Crit +10 on Brute. DEX +20, VIT +10.', 'Both_Hand', 'items/19119.png', 'collection/19119.png'),
(3, 19120, 1, 0, 1, 15, 1, 'Hawk RPG', 'Hawk_RPG', 'Weapon', 'Grenade', 120, 4, '[Elite A-Tier Gear] High performance grenade launcher with Stone of Sage inlays. DEX +10, VIT +10, LUK +10, AGI +5.', 'Both_Hand', 'items/19120.png', 'collection/19120.png'),
(3, 19121, 1, 0, 1, 16, 1, 'Lee-Enfield', 'Lee', 'Weapon', 'Rifle', 70, 4, '[Elite A-Tier Gear] High performance rifle with long range and great power. DEX +15, VIT +5, LUK +15, AGI +10, HIT +20, CRIT +10.', 'Both_Hand', 'items/19121.png', 'collection/19121.png'),
(3, 19122, 1, 0, 1, 17, 1, 'Ancient Celestial Chronicles', 'Ancient_Celestial_Chronicles', 'Weapon', 'Book', 70, 4, '[Elite A-Tier Gear] Ancient book of sacred texts. Pierces Dragon Defense. All Stats +10, STR +10, AGI +15.', 'Right_Hand', 'items/19122.png', 'collection/19122.png'),
(3, 19123, 1, 0, 1, 18, 1, 'Colt 36', 'Colt', 'Weapon', 'Revolver', 50, 4, '[Elite A-Tier Gear] Notorious rare revolver only produced when specially ordered. DEX +15, LUK +20, VIT +5, HIT +20.', 'Both_Hand', 'items/19123.png', 'collection/19123.png'),
(3, 19124, 1, 0, 1, 19, 1, 'Devil Huuma Shuriken', 'Devil_Huuma_Shuriken', 'Weapon', 'Huuma', 250, 4, '[Elite A-Tier Gear] Heavy giant shuriken with radiant iridescent metal. STR +20, AGI +10, VIT +15, INT +10, DEX +5, LUK +5, ATK +100.', 'Both_Hand', 'items/19124.png', 'collection/19124.png'),
-- +10 Refined weapons (3 ubers each)
(3, 3150, 3, 10, 1, 20, 1, 'Staff of Ages', 'Staff_of_Ages', 'Weapon', 'Staff', 50, 4, '[Elite A-Tier Gear] Staff with powers said strong enough to stop time itself. Int +15, Dex +10, MatkRate +20%.', 'Right_Hand', 'items/3150.png', 'collection/3150.png'),
(3, 3151, 3, 10, 1, 21, 1, 'Soul Seeker', 'Soul_Seeker', 'Weapon', 'Bow', 50, 4, '[Elite A-Tier Gear] Bow with speed and accuracy that are unrivaled. Dex +15, Vit +10, Luk +5.', 'Both_Hand', 'items/3151.png', 'collection/3151.png'),
(3, 3152, 3, 10, 1, 22, 1, 'Holy Flail', 'Holy_Flail', 'Weapon', 'Mace', 50, 4, '[Elite A-Tier Gear] Mace used by grand Priest and Sages. Int +15, Luk +10, Vit +10, Healing +105%, MATK +15%.', 'Right_Hand', 'items/3152.png', 'collection/3152.png'),
(3, 3153, 3, 10, 1, 23, 1, 'Oblivion Blade', 'Oblivion_Blade', 'Weapon', 'Dagger', 50, 4, '[Elite A-Tier Gear] Large blade which grants anyone who wears it massive power. +7 to All Stats.', 'Right_Hand', 'items/3153.png', 'collection/3153.png'),
(3, 3154, 3, 10, 1, 24, 1, 'Frenzic', 'Frenzic', 'Weapon', 'Knuckle', 5, 4, '[Elite A-Tier Gear] Insanely sharp claws used for hunting only the most massive of beast. AspdRate +15%, Luk +10, Vit +10, Int +5.', 'Right_Hand', 'items/3154.png', 'collection/3154.png'),
(3, 3155, 3, 10, 1, 25, 1, 'Katar of Chaos', 'Katar_of_Chaos', 'Weapon', 'Katar', 50, 4, '[Elite A-Tier Gear] Katar which brings only chaos with its blades. Agi +15, Vit +10, Str +5.', 'Both_Hand', 'items/3155.png', 'collection/3155.png'),
(3, 3156, 3, 10, 1, 26, 1, 'Reaper', 'Reaper', 'Weapon', '2hAxe', 5, 4, '[Elite A-Tier Gear] Long and fast axe used to defend and protect ones assets. AspdRate +40%, Str +30, Vit +10.', 'Right_Hand', 'items/3156.png', 'collection/3156.png'),
(3, 3157, 3, 10, 1, 27, 1, 'Wind Weaver', 'Wind_Weaver', 'Weapon', 'Musical', 50, 4, '[Elite A-Tier Gear] Guitar which projects arrows that pierce the wind. Int +5, Vit +10, Dex +15.', 'Right_Hand', 'items/3157.png', 'collection/3157.png'),
(3, 3158, 3, 10, 1, 28, 1, 'Dark Defier', 'Dark_Defier', 'Weapon', '2hSpear', 4500, 4, '[Elite A-Tier Gear] Spear of deep, twisted defiance. Str +15, Vit +10, Luk +5.', 'Right_Hand', 'items/3158.png', 'collection/3158.png'),
(3, 3159, 3, 10, 1, 29, 1, 'Phoenix Wing', 'Phoenix_Wing', 'Weapon', '1hSword', 5, 4, '[Elite A-Tier Gear] A Sword that grants the perfect balance of power. +5 to All Stats.', 'Right_Hand', 'items/3159.png', 'collection/3159.png'),
(3, 3160, 3, 10, 1, 30, 1, 'Retribution', 'Retribution', 'Weapon', '2hSword', 50, 4, '[Elite A-Tier Gear] Sword with dark powers used for the light of good. Critical +40, Aspd +10%, Str +20.', 'Both_Hand', 'items/3160.png', 'collection/3160.png'),
(3, 3161, 3, 10, 1, 31, 1, 'Raging Mic', 'Raging_Mic', 'Weapon', 'Whip', 50, 4, '[Elite A-Tier Gear] A mic enhanced to send out massive beatdowns. Luk +10, Dex +10, Vit +10.', 'Right_Hand', 'items/3161.png', 'collection/3161.png'),
(3, 19118, 3, 10, 1, 32, 1, 'Mossberg 500', 'Mossberg_500', 'Weapon', 'Shotgun', 100, 4, '[Elite A-Tier Gear] Mossberg with barrel reinforced with Carnium. Splash Damage. DEX +10, VIT +10, LUK +10, AGI +5.', 'Both_Hand', 'items/19118.png', 'collection/19118.png'),
(3, 19119, 3, 10, 1, 33, 1, 'Vulcan Minigun', 'Vulcan_Minigun', 'Weapon', 'Gatling', 250, 4, '[Elite A-Tier Gear] Minigun fires in rapid succession with electronic rotating barrel. Crit +10 on Brute. DEX +20, VIT +10.', 'Both_Hand', 'items/19119.png', 'collection/19119.png'),
(3, 19120, 3, 10, 1, 34, 1, 'Hawk RPG', 'Hawk_RPG', 'Weapon', 'Grenade', 120, 4, '[Elite A-Tier Gear] High performance grenade launcher with Stone of Sage inlays. DEX +10, VIT +10, LUK +10, AGI +5.', 'Both_Hand', 'items/19120.png', 'collection/19120.png'),
(3, 19121, 3, 10, 1, 35, 1, 'Lee-Enfield', 'Lee', 'Weapon', 'Rifle', 70, 4, '[Elite A-Tier Gear] High performance rifle with long range and great power. DEX +15, VIT +5, LUK +15, AGI +10, HIT +20, CRIT +10.', 'Both_Hand', 'items/19121.png', 'collection/19121.png'),
(3, 19122, 3, 10, 1, 36, 1, 'Ancient Celestial Chronicles', 'Ancient_Celestial_Chronicles', 'Weapon', 'Book', 70, 4, '[Elite A-Tier Gear] Ancient book of sacred texts. Pierces Dragon Defense. All Stats +10, STR +10, AGI +15.', 'Right_Hand', 'items/19122.png', 'collection/19122.png'),
(3, 19123, 3, 10, 1, 37, 1, 'Colt 36', 'Colt', 'Weapon', 'Revolver', 50, 4, '[Elite A-Tier Gear] Notorious rare revolver only produced when specially ordered. DEX +15, LUK +20, VIT +5, HIT +20.', 'Both_Hand', 'items/19123.png', 'collection/19123.png'),
(3, 19124, 3, 10, 1, 38, 1, 'Devil Huuma Shuriken', 'Devil_Huuma_Shuriken', 'Weapon', 'Huuma', 250, 4, '[Elite A-Tier Gear] Heavy giant shuriken with radiant iridescent metal. STR +20, AGI +10, VIT +15, INT +10, DEX +5, LUK +5, ATK +100.', 'Both_Hand', 'items/19124.png', 'collection/19124.png');

-- ================================================================================
-- INSERT Items - Card Shop (16 items)
-- ================================================================================
INSERT INTO `uber_shop_items` (`category_id`, `item_id`, `uber_cost`, `refine_level`, `quantity`, `display_order`, `enabled`, `item_name`, `aegis_name`, `item_type`, `item_subtype`, `weight`, `slots`, `description`, `equip_locations`, `icon_path`, `collection_path`) VALUES
(4, 4172, 1, 0, 1, 1, 1, 'Monkey Fist Card', 'Monkey_Fist_Card', 'Card', NULL, 1, 0, '[Uber B-Tier Gear] A gift from the gods. +10 to all stats!!', 'Right_Hand', 'items/4172.png', NULL),
(4, 4196, 1, 0, 1, 2, 1, 'Bloody Knight Card', 'Bloody_Knight_Card', 'Card', NULL, 1, 0, 'Bloody Knight Champ Card. All Stats +10!!', 'Head_Top', 'items/4196.png', NULL),
(4, 4147, 2, 0, 1, 3, 1, 'Baphomet Card', 'Baphomet_Card', 'Card', NULL, 10, 0, 'Do Area of Effect damage around 9 cells of the owner. Accuracy -10.', 'Right_Hand', 'items/4147.png', NULL),
(4, 4047, 2, 0, 1, 4, 1, 'Ghostring Card', 'Ghostring_Card', 'Card', NULL, 10, 0, 'Enchant Armor with Ghost Property. Reduce 25% of HP Recovery.', 'Armor', 'items/4047.png', NULL),
(4, 4198, 2, 0, 1, 5, 1, 'Maya Purple Card', 'Maya_Puple_Card', 'Card', NULL, 10, 0, 'Enable its user to detect hidden enemies.', 'Head_Top', 'items/4198.png', NULL),
(4, 4207, 2, 0, 1, 6, 1, 'Greed Card', 'Greed_Card', 'Card', NULL, 10, 0, 'Greed Card. VIT +35.', 'Accessory', 'items/4207.png', NULL),
(4, 4208, 2, 0, 1, 7, 1, 'Envy Card', 'Envy_Card', 'Card', NULL, 10, 0, 'Envy Card. AGI +35.', 'Accessory', 'items/4208.png', NULL),
(4, 4209, 2, 0, 1, 8, 1, 'Faith Card', 'Faith_Card', 'Card', NULL, 10, 0, 'Faith Card. INT +35.', 'Accessory', 'items/4209.png', NULL),
(4, 4210, 2, 0, 1, 9, 1, 'Lust Card', 'Lust_Card', 'Card', NULL, 10, 0, 'Lust Card. LUK +35.', 'Accessory', 'items/4210.png', NULL),
(4, 4211, 2, 0, 1, 10, 1, 'Exile Card', 'Exile_Card', 'Card', NULL, 10, 0, 'Exile Card. DEX +35.', 'Accessory', 'items/4211.png', NULL),
(4, 4121, 3, 0, 1, 11, 1, 'Phreeoni Card', 'Phreeoni_Card', 'Card', NULL, 10, 0, 'Accuracy + 100.', 'Right_Hand', 'items/4121.png', NULL),
(4, 4128, 3, 0, 1, 12, 1, 'Golden Thiefbug Card', 'Golden_Bug_Card', 'Card', NULL, 10, 0, 'Blocks Greatly against Magic Spells. Increase SP consumption double when using skills.', 'Left_Hand', 'items/4128.png', NULL),
(4, 4441, 5, 0, 1, 13, 1, 'Fallen Bishop Card', 'Fallen_Bishop_Card', 'Card', NULL, 10, 0, 'Matk +3%, MaxSP -20%, Fixed Cast 100ms. +10% magic damage to Demihuman and Angel.', 'Shoes', 'items/4441.png', NULL),
(4, 4263, 5, 0, 1, 14, 1, 'Incantation Samurai Card', 'Incant_Samurai_Card', 'Card', NULL, 10, 0, 'Ignore normal enemy Defense. Disable HP recovery and drain 666 HP every 10 seconds.', 'Right_Hand', 'items/4263.png', NULL),
(4, 4403, 5, 0, 1, 15, 1, 'Kiel D-01 Card', 'Kiel_Card', 'Card', NULL, 10, 0, 'Reduces after cast delay of all skills by 30%. Does not reduce cooldown delays.', 'Head_Top', 'items/4403.png', NULL),
(4, 4318, 5, 0, 1, 16, 1, 'Stormy Knight Card', 'Knight_Windstorm_Card', 'Card', NULL, 10, 0, '2% chance of auto casting Level 1 Storm Gust. 20% chance of Freezing enemy.', 'Right_Hand', 'items/4318.png', NULL);

-- ================================================================================
-- INSERT Items - Costume Shop (24 items)
-- ================================================================================
INSERT INTO `uber_shop_items` (`category_id`, `item_id`, `uber_cost`, `refine_level`, `quantity`, `display_order`, `enabled`, `item_name`, `aegis_name`, `item_type`, `item_subtype`, `weight`, `slots`, `description`, `equip_locations`, `icon_path`, `collection_path`) VALUES
(5, 20398, 1, 0, 1, 1, 1, 'Gem of Costume', 'Gem_of_Costume', 'Etc', NULL, 1, 0, 'Used with Peddlin'' Peter in Jawaii to exchange for Uber Exclusive Costumes.', NULL, 'items/20398.png', NULL),
(5, 19808, 3, 0, 1, 2, 1, '[C] Lord Kaho''s Horn (Relic)', 'C_Lord_Kaho_Horn_Clone', 'Armor', 'Costume', 10, 0, 'Embrace the legendary aesthetic of XileRO''s most treasured relics in your costume ensemble.', 'Costume_Head_Top', 'items/19808.png', 'collection/19808.png'),
(5, 19809, 3, 0, 1, 3, 1, '[C] Black Helm (Relic)', 'C_Black_Helm_Clone', 'Armor', 'Costume', 10, 0, 'Embrace the legendary aesthetic of XileRO''s most treasured relics in your costume ensemble.', 'Costume_Head_Top', 'items/19809.png', 'collection/19809.png'),
(5, 19810, 3, 0, 1, 4, 1, '[C] Equality Wings (Relic)', 'C_Equality_Wings_Clone', 'Armor', 'Costume', 10, 0, 'Embrace the legendary aesthetic of XileRO''s most treasured relics in your costume ensemble.', 'Costume_Head_Top', 'items/19810.png', 'collection/19810.png'),
(5, 19811, 3, 0, 1, 5, 1, '[C] Alpha Wings (Relic)', 'C_Alpha_Wings_Clone', 'Armor', 'Costume', 10, 0, 'Embrace the legendary aesthetic of XileRO''s most treasured relics in your costume ensemble.', 'Costume_Head_Low', 'items/19811.png', 'collection/19811.png'),
(5, 19812, 3, 0, 1, 6, 1, '[C] Omega Wings (Relic)', 'C_Omega_Wings_Clone', 'Armor', 'Costume', 10, 0, 'Embrace the legendary aesthetic of XileRO''s most treasured relics in your costume ensemble.', 'Costume_Head_Low', 'items/19812.png', 'collection/19812.png'),
(5, 19813, 3, 0, 1, 7, 1, '[C] ArchAngel Wings (Relic)', 'C_ArchAngel_Wings_Clone', 'Armor', 'Costume', 10, 0, 'Embrace the legendary aesthetic of XileRO''s most treasured relics in your costume ensemble.', 'Costume_Head_Low', 'items/19813.png', 'collection/19813.png'),
(5, 20466, 10, 0, 1, 8, 1, '[C] Onyx Dragon Helm (Relic)', 'c_onyx_dragon_helm', 'Armor', 'Costume', 10, 0, 'Embrace the legendary aesthetic of XileRO''s most treasured relics in your costume ensemble.', 'Costume_Head_Top', 'items/20466.png', 'collection/20466.png'),
(5, 20472, 8, 0, 1, 9, 1, '[C] Onyx Blindfold (Relic)', 'c_onyx_blindfold', 'Armor', 'Costume', 10, 0, 'Embrace the legendary aesthetic of XileRO''s most treasured relics in your costume ensemble.', 'Costume_Head_Mid', 'items/20472.png', 'collection/20472.png'),
(5, 20469, 12, 0, 1, 10, 1, '[C] Onyx Dragon Wings (Relic)', 'c_onyx_dragon_wings', 'Armor', 'Costume', 10, 0, 'Embrace the legendary aesthetic of XileRO''s most treasured relics in your costume ensemble.', 'Costume_Head_Low', 'items/20469.png', 'collection/20469.png'),
(5, 20467, 10, 0, 1, 11, 1, '[C] Sapphire Traveler Hat (Relic)', 'c_saphire_traveler', 'Armor', 'Costume', 10, 0, 'Embrace the legendary aesthetic of XileRO''s most treasured relics in your costume ensemble.', 'Costume_Head_Top', 'items/20467.png', 'collection/20467.png'),
(5, 20473, 8, 0, 1, 12, 1, '[C] Sapphire Blindfold (Relic)', 'c_sapphire_blindfold', 'Armor', 'Costume', 10, 0, 'Embrace the legendary aesthetic of XileRO''s most treasured relics in your costume ensemble.', 'Costume_Head_Mid', 'items/20473.png', 'collection/20473.png'),
(5, 20470, 12, 0, 1, 13, 1, '[C] Sapphire Phychic Wings (Relic)', 'c_sapphire_phychic_wings', 'Armor', 'Costume', 10, 0, 'Embrace the legendary aesthetic of XileRO''s most treasured relics in your costume ensemble.', 'Costume_Head_Low', 'items/20470.png', 'collection/20470.png'),
(5, 20468, 10, 0, 1, 14, 1, '[C] Emerald Dowry Hat (Relic)', 'c_emerald_dowry_hat', 'Armor', 'Costume', 10, 0, 'Embrace the legendary aesthetic of XileRO''s most treasured relics in your costume ensemble.', 'Costume_Head_Top', 'items/20468.png', 'collection/20468.png'),
(5, 20474, 8, 0, 1, 15, 1, '[C] Emerald Blindfold (Relic)', 'c_emerald_blindfold', 'Armor', 'Costume', 10, 0, 'Embrace the legendary aesthetic of XileRO''s most treasured relics in your costume ensemble.', 'Costume_Head_Mid', 'items/20474.png', 'collection/20474.png'),
(5, 20471, 12, 0, 1, 16, 1, '[C] Emerald Enchanted Wings (Relic)', 'c_emerald_enchanted_wings', 'Armor', 'Costume', 10, 0, 'Embrace the legendary aesthetic of XileRO''s most treasured relics in your costume ensemble.', 'Costume_Head_Low', 'items/20471.png', 'collection/20471.png'),
(5, 31329, 9, 0, 1, 17, 1, '[C] Dark Devil Horns (Relic)', 'dark_devil_horns_clone', 'Armor', 'Costume', 10, 0, 'Embrace the legendary aesthetic of XileRO''s most treasured relics in your costume ensemble.', 'Costume_Head_Top', 'items/31329.png', 'collection/31329.png'),
(5, 31327, 8, 0, 1, 18, 1, '[C] Dark Devil Tail (Relic)', 'Dark_Devil_tail_clone', 'Armor', 'Costume', 10, 0, 'Embrace the legendary aesthetic of XileRO''s most treasured relics in your costume ensemble.', 'Costume_Head_Mid', 'items/31327.png', 'collection/31327.png'),
(5, 31328, 14, 0, 1, 19, 1, '[C] Dark Devil Wings (Relic)', 'dark_devil_wings_clone', 'Armor', 'Costume', 10, 0, 'Embrace the legendary aesthetic of XileRO''s most treasured relics in your costume ensemble.', 'Costume_Head_Low', 'items/31328.png', 'collection/31328.png'),
(5, 19563, 60, 0, 1, 20, 1, '[C] Samehada Sword', 'C_Samehada_Sword', 'Armor', 'Costume', 10, 0, 'XileRO Costume. Donation Exclusive.', 'Costume_Head_Low', 'items/19563.png', 'collection/19563.png'),
(5, 19594, 65, 0, 1, 21, 1, '[C] Invocation Cape (Dark Side)', 'C_Invocation_Cape_Dark', 'Armor', 'Costume', 10, 0, 'XileRO Costume. Quest Exclusive.', 'Costume_Head_Low', 'items/19594.png', 'collection/19594.png'),
(5, 19595, 85, 0, 1, 22, 1, '[C] Invocation Cape (Light Side)', 'C_Invocation_Cape_Light', 'Armor', 'Costume', 10, 0, 'XileRO Costume. Quest Exclusive.', 'Costume_Head_Low', 'items/19595.png', 'collection/19595.png'),
(5, 31265, 20, 0, 1, 23, 1, '[C] Nichirin''s Sword', 'DM_GA02', 'Armor', 'Costume', 10, 0, 'XileRO Costume.', 'Costume_Garment', 'items/31265.png', 'collection/31265.png'),
(5, 31254, 10, 0, 1, 24, 1, '[C] Valkyrie Helm', 'VALKYRIEHELM', 'Armor', 'Costume', 10, 0, 'XileRO Costume.', 'Costume_Head_Top', 'items/31254.png', 'collection/31254.png');

-- ================================================================================
-- INSERT Items - Elite Shop (8 items)
-- ================================================================================
INSERT INTO `uber_shop_items` (`category_id`, `item_id`, `uber_cost`, `refine_level`, `quantity`, `display_order`, `enabled`, `item_name`, `aegis_name`, `item_type`, `item_subtype`, `weight`, `slots`, `description`, `equip_locations`, `icon_path`, `collection_path`) VALUES
(6, 20453, 120, 0, 1, 1, 1, '[C] Celestial Wings', 'C_Celestial_Wings_Blue', 'Armor', 'Costume', 10, 0, 'Heavenly Elegance. Grace of the skies. A breathtaking pair of wings imbued with the hue of the clearest sky.', 'Costume_Head_Low', 'items/20453.png', 'collection/20453.png'),
(6, 20454, 120, 0, 1, 2, 1, '[C] Celestial Wings (Red)', 'C_Celestial_Wings_Red', 'Armor', 'Costume', 10, 0, 'Fiery Majesty. Embrace the blaze. Magnificent wings that shimmer with the intensity of a raging inferno.', 'Costume_Head_Low', 'items/20454.png', 'collection/20454.png'),
(6, 3170, 7, 10, 1, 3, 1, 'Scarlet Angel Helm', 'scarlet_angel_helm', 'Armor', 'Headgear', 100, 3, '[Godly S-Tier Gear] Helm of the fallen Scarlet Angel. DEX +4, STR +4, VIT +8, MDEF +9.', 'Head_Top', 'items/3170.png', 'collection/3170.png'),
(6, 3171, 10, 10, 1, 4, 1, 'Scarlet Angel Wings', 'scarlet_angel_wings', 'Armor', 'Headgear', 50, 1, '[Godly S-Tier Gear] Wings of the fallen Scarlet Angel. +5 to All Stats.', 'Head_Low', 'items/3171.png', 'collection/3171.png'),
(6, 3173, 7, 10, 1, 5, 1, 'Emperor Helm', 'emperor_helm', 'Armor', 'Headgear', 100, 3, '[Godly S-Tier Gear] Helm of the Emperor, ruler of all things. INT +3, AGI +3, VIT +5, MDEF +3.', 'Head_Top', 'items/3173.png', 'collection/3173.png'),
(6, 3174, 10, 10, 1, 6, 1, 'Emperor Wings', 'emperor_wings', 'Armor', 'Headgear', 50, 1, '[Godly S-Tier Gear] Wings of the Emperor, ruler of all things. +5 to All Stats.', 'Head_Low', 'items/3174.png', 'collection/3174.png'),
(6, 3176, 8, 10, 1, 7, 1, 'Little Devil Horns', 'little_devil_horns', 'Armor', 'Headgear', 100, 3, '[Godly S-Tier Gear] Horns that mark one as a devil. STR +5, VIT +1, MDEF +3.', 'Head_Top', 'items/3176.png', 'collection/3176.png'),
(6, 3177, 12, 10, 1, 8, 1, 'Little Devil Wings', 'little_devil_wings', 'Armor', 'Headgear', 50, 1, '[Godly S-Tier Gear] Wings that mark one as a devil. MaxHP +5%, Movement +10%.', 'Head_Low', 'items/3177.png', 'collection/3177.png');

-- ================================================================================
-- Verify Migration
-- ================================================================================
SELECT 'Categories' AS table_name, COUNT(*) AS count FROM uber_shop_categories
UNION ALL
SELECT 'Items', COUNT(*) FROM uber_shop_items;
-- Expected: Categories = 6, Items = 119
