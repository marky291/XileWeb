<?php

namespace Tests\Unit\Models;

use App\Models\UberShopItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UberShopItemTest extends TestCase
{
    use RefreshDatabase;

    private function createItem(array $attributes = []): UberShopItem
    {
        return UberShopItem::factory()->create($attributes);
    }

    #[Test]
    public function exclusive_server_returns_xilero_when_only_available_on_xilero(): void
    {
        $item = $this->createItem([
            'is_xilero' => true,
            'is_xileretro' => false,
        ]);

        $this->assertEquals('XileRO', $item->exclusive_server);
    }

    #[Test]
    public function exclusive_server_returns_xileretro_when_only_available_on_xileretro(): void
    {
        $item = $this->createItem([
            'is_xilero' => false,
            'is_xileretro' => true,
        ]);

        $this->assertEquals('XileRetro', $item->exclusive_server);
    }

    #[Test]
    public function exclusive_server_returns_null_when_available_on_both_servers(): void
    {
        $item = $this->createItem([
            'is_xilero' => true,
            'is_xileretro' => true,
        ]);

        $this->assertNull($item->exclusive_server);
    }

    #[Test]
    public function exclusive_server_returns_null_when_not_available_on_any_server(): void
    {
        $item = $this->createItem([
            'is_xilero' => false,
            'is_xileretro' => false,
        ]);

        $this->assertNull($item->exclusive_server);
    }

    #[Test]
    public function is_available_for_server_returns_true_for_xilero_item(): void
    {
        $item = $this->createItem([
            'is_xilero' => true,
            'is_xileretro' => false,
        ]);

        $this->assertTrue($item->isAvailableForServer('xilero'));
        $this->assertFalse($item->isAvailableForServer('xileretro'));
    }

    #[Test]
    public function is_available_for_server_returns_true_for_xileretro_item(): void
    {
        $item = $this->createItem([
            'is_xilero' => false,
            'is_xileretro' => true,
        ]);

        $this->assertFalse($item->isAvailableForServer('xilero'));
        $this->assertTrue($item->isAvailableForServer('xileretro'));
    }

    #[Test]
    public function is_available_for_server_returns_true_for_both_servers_item(): void
    {
        $item = $this->createItem([
            'is_xilero' => true,
            'is_xileretro' => true,
        ]);

        $this->assertTrue($item->isAvailableForServer('xilero'));
        $this->assertTrue($item->isAvailableForServer('xileretro'));
    }

    #[Test]
    public function is_available_for_server_returns_false_for_invalid_server(): void
    {
        $item = $this->createItem([
            'is_xilero' => true,
            'is_xileretro' => true,
        ]);

        $this->assertFalse($item->isAvailableForServer('invalid'));
    }
}
