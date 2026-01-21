<?php

namespace Tests\Unit\Models;

use App\Models\UberShopItem;
use App\Models\UberShopPurchase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UberShopPurchaseTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_has_status_constants(): void
    {
        $this->assertEquals('pending', UberShopPurchase::STATUS_PENDING);
        $this->assertEquals('claimed', UberShopPurchase::STATUS_CLAIMED);
        $this->assertEquals('cancelled', UberShopPurchase::STATUS_CANCELLED);
    }

    #[Test]
    public function it_belongs_to_shop_item(): void
    {
        $item = UberShopItem::factory()->create();
        $purchase = UberShopPurchase::factory()->create(['shop_item_id' => $item->id]);

        $this->assertInstanceOf(UberShopItem::class, $purchase->shopItem);
        $this->assertEquals($item->id, $purchase->shopItem->id);
    }

    #[Test]
    public function it_detects_pending_status(): void
    {
        $pending = UberShopPurchase::factory()->create([
            'status' => UberShopPurchase::STATUS_PENDING,
        ]);

        $claimed = UberShopPurchase::factory()->create([
            'status' => UberShopPurchase::STATUS_CLAIMED,
        ]);

        $this->assertTrue($pending->is_pending);
        $this->assertFalse($claimed->is_pending);
    }

    #[Test]
    public function it_detects_claimed_status(): void
    {
        $pending = UberShopPurchase::factory()->create([
            'status' => UberShopPurchase::STATUS_PENDING,
        ]);

        $claimed = UberShopPurchase::factory()->create([
            'status' => UberShopPurchase::STATUS_CLAIMED,
        ]);

        $this->assertFalse($pending->is_claimed);
        $this->assertTrue($claimed->is_claimed);
    }

    #[Test]
    public function it_casts_purchased_at_to_datetime(): void
    {
        $purchase = UberShopPurchase::factory()->create([
            'purchased_at' => '2024-01-15 10:30:00',
        ]);

        $this->assertInstanceOf(\Carbon\Carbon::class, $purchase->purchased_at);
    }

    #[Test]
    public function it_casts_claimed_at_to_datetime(): void
    {
        $purchase = UberShopPurchase::factory()->create([
            'claimed_at' => '2024-01-16 15:45:00',
        ]);

        $this->assertInstanceOf(\Carbon\Carbon::class, $purchase->claimed_at);
    }

    #[Test]
    public function it_casts_is_xileretro_to_boolean(): void
    {
        $xilero = UberShopPurchase::factory()->create(['is_xileretro' => false]);
        $xileretro = UberShopPurchase::factory()->create(['is_xileretro' => true]);

        $this->assertFalse($xilero->is_xileretro);
        $this->assertTrue($xileretro->is_xileretro);
    }

    #[Test]
    public function it_has_fillable_attributes(): void
    {
        $purchase = new UberShopPurchase();
        $fillable = $purchase->getFillable();

        $this->assertContains('account_id', $fillable);
        $this->assertContains('account_name', $fillable);
        $this->assertContains('shop_item_id', $fillable);
        $this->assertContains('item_id', $fillable);
        $this->assertContains('item_name', $fillable);
        $this->assertContains('refine_level', $fillable);
        $this->assertContains('quantity', $fillable);
        $this->assertContains('uber_cost', $fillable);
        $this->assertContains('uber_balance_after', $fillable);
        $this->assertContains('status', $fillable);
        $this->assertContains('purchased_at', $fillable);
        $this->assertContains('claimed_at', $fillable);
        $this->assertContains('claimed_by_char_id', $fillable);
        $this->assertContains('claimed_by_char_name', $fillable);
        $this->assertContains('is_xileretro', $fillable);
    }

    #[Test]
    public function it_allows_null_shop_item(): void
    {
        $purchase = UberShopPurchase::factory()->create(['shop_item_id' => null]);

        $this->assertNull($purchase->shopItem);
    }

    #[Test]
    public function it_allows_null_claimed_at(): void
    {
        $purchase = UberShopPurchase::factory()->create([
            'status' => UberShopPurchase::STATUS_PENDING,
            'claimed_at' => null,
        ]);

        $this->assertNull($purchase->claimed_at);
    }
}
