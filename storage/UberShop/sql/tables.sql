-- ================================================================================
-- UberShop Database Schema
-- XileRO Server - Database-driven donation shop system
-- ================================================================================

-- Drop existing tables if they exist (for clean migration)
DROP TABLE IF EXISTS `uber_shop_purchases`;
DROP TABLE IF EXISTS `uber_shop_items`;
DROP TABLE IF EXISTS `uber_shop_categories`;

-- ================================================================================
-- Categories Table
-- Stores shop category definitions with display settings
-- ================================================================================
CREATE TABLE `uber_shop_categories` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(50) NOT NULL COMMENT 'Internal category name (e.g., budget, equipment)',
    `display_name` VARCHAR(100) NOT NULL COMMENT 'Display name with color codes (e.g., ^00BFC8Budget Shop^000000)',
    `tagline` VARCHAR(255) NOT NULL COMMENT 'Category description shown to player',
    `uber_range` VARCHAR(20) NOT NULL COMMENT 'Display text like "1-3 Ubers"',
    `display_order` INT NOT NULL DEFAULT 0 COMMENT 'Sort order in menu (lower = first)',
    `enabled` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1 = active, 0 = hidden',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `idx_name` (`name`),
    KEY `idx_display_order` (`display_order`),
    KEY `idx_enabled` (`enabled`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='UberShop category definitions';

-- ================================================================================
-- Items Table
-- Stores shop items with full metadata for web display
-- ================================================================================
CREATE TABLE `uber_shop_items` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `category_id` INT UNSIGNED NOT NULL COMMENT 'FK to uber_shop_categories.id',

    -- Core shop data
    `item_id` INT UNSIGNED NOT NULL COMMENT 'rAthena item ID',
    `uber_cost` INT UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Cost in Ubers',
    `refine_level` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Refine level (0-20)',
    `quantity` INT UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Amount given per purchase',
    `stock` INT DEFAULT NULL COMMENT 'NULL = unlimited, otherwise remaining stock',
    `enabled` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1 = active, 0 = hidden',
    `display_order` INT NOT NULL DEFAULT 0 COMMENT 'Sort order within category',

    -- Item metadata (from client/server for web display)
    `item_name` VARCHAR(100) NOT NULL COMMENT 'Display name from database',
    `aegis_name` VARCHAR(50) DEFAULT NULL COMMENT 'Internal AegisName',
    `item_type` VARCHAR(50) DEFAULT NULL COMMENT 'Type: Weapon, Armor, Card, Usable, Etc',
    `item_subtype` VARCHAR(50) DEFAULT NULL COMMENT 'Subtype: 1hSword, Headgear, etc.',
    `weight` INT UNSIGNED DEFAULT 0 COMMENT 'Item weight',
    `slots` TINYINT UNSIGNED DEFAULT 0 COMMENT 'Card slots',
    `description` TEXT COMMENT 'Client description (cleaned)',
    `equip_locations` VARCHAR(100) DEFAULT NULL COMMENT 'Where item can be equipped',
    `icon_path` VARCHAR(100) DEFAULT NULL COMMENT 'Relative path: items/{item_id}.png',
    `collection_path` VARCHAR(100) DEFAULT NULL COMMENT 'Relative path: collection/{item_id}.png',

    -- Availability windows for time-limited sales
    `available_from` DATETIME DEFAULT NULL COMMENT 'NULL = always available',
    `available_until` DATETIME DEFAULT NULL COMMENT 'NULL = never expires',

    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (`id`),
    KEY `idx_category_id` (`category_id`),
    KEY `idx_item_id` (`item_id`),
    KEY `idx_enabled` (`enabled`),
    KEY `idx_display_order` (`display_order`),
    KEY `idx_availability` (`available_from`, `available_until`),
    CONSTRAINT `fk_ubershop_category` FOREIGN KEY (`category_id`)
        REFERENCES `uber_shop_categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='UberShop item listings with full metadata';

-- ================================================================================
-- Purchase History Table
-- Website creates pending purchases, game server claims them on login
-- ================================================================================
CREATE TABLE `uber_shop_purchases` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,

    -- Account info (for auditing)
    `account_id` INT UNSIGNED NOT NULL,
    `account_name` VARCHAR(23) NOT NULL COMMENT 'Username at time of purchase (audit trail)',

    -- Item details (denormalized for history)
    `shop_item_id` INT UNSIGNED DEFAULT NULL COMMENT 'FK to uber_shop_items.id (nullable)',
    `item_id` INT UNSIGNED NOT NULL,
    `item_name` VARCHAR(100) NOT NULL,
    `refine_level` TINYINT UNSIGNED NOT NULL DEFAULT 0,
    `quantity` INT UNSIGNED NOT NULL DEFAULT 1,

    -- Transaction details
    `uber_cost` INT UNSIGNED NOT NULL,
    `uber_balance_after` INT UNSIGNED NOT NULL,

    -- Status tracking (website -> pending, login -> claimed)
    `status` ENUM('pending', 'claimed') NOT NULL DEFAULT 'pending',
    `purchased_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    -- Claim tracking (set when redeemed in-game)
    `claimed_at` TIMESTAMP NULL DEFAULT NULL COMMENT 'When redeemed in-game',
    `claimed_by_char_id` INT UNSIGNED DEFAULT NULL COMMENT 'Which character received item',
    `claimed_by_char_name` VARCHAR(30) DEFAULT NULL COMMENT 'Character name (denormalized)',

    PRIMARY KEY (`id`),
    KEY `idx_account_status` (`account_id`, `status`) COMMENT 'Fast pending lookup on login',
    KEY `idx_purchased_at` (`purchased_at`),
    KEY `idx_account_name` (`account_name`) COMMENT 'Search by username for auditing'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='UberShop purchases - website creates pending, game claims on login';

-- ================================================================================
-- Verification Query
-- ================================================================================
SELECT 'Tables created successfully' AS status;
