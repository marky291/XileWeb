<?php

namespace Tests\Unit\Ragnarok;

use App\Ragnarok\ServerZeny;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServerZenyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test default values for ServerZeny model.
     *
     * @return void
     */
    public function testDefaultValues()
    {
        // Create a new model instance without setting any attributes
        $serverZeny = new ServerZeny();

        // Assert that the default values are set correctly
        $this->assertEquals(0, $serverZeny->silver_count);
        $this->assertEquals(0, $serverZeny->gold_count);
        $this->assertEquals(0, $serverZeny->mithril_count);
        $this->assertEquals(0, $serverZeny->platinum_count);
        $this->assertEquals(0, $serverZeny->player_zeny);
        $this->assertEquals(0, $serverZeny->char_online);
        $this->assertEquals(0, $serverZeny->silver_zeny);
        $this->assertEquals(0, $serverZeny->gold_zeny);
        $this->assertEquals(0, $serverZeny->mithril_zeny);
        $this->assertEquals(0, $serverZeny->platinum_zeny);
        $this->assertEquals(0, $serverZeny->total_zeny);
        $this->assertEquals(0, $serverZeny->total_uber_cost);
        $this->assertEquals(0, $serverZeny->mithril_cost);
        $this->assertEquals(0, $serverZeny->platinum_cost);
        $this->assertEquals(0, $serverZeny->gold_cost);
        $this->assertEquals(0, $serverZeny->silver_cost);
        $this->assertEquals(0, $serverZeny->zeny_cost);
    }

    /**
     * Test attribute casting for ServerZeny model.
     *
     * @return void
     */
    public function testAttributeCasting()
    {
        ServerZeny::unguard();

        $serverZeny = new ServerZeny([
            'silver_count' => '100',
            'gold_count' => '200',
            'mithril_count' => '300',
            'platinum_count' => '400',
            'player_zeny' => '500',
            'char_online' => '1',
            'silver_zeny' => '600',
            'gold_zeny' => '700',
            'mithril_zeny' => '800',
            'platinum_zeny' => '900',
            'total_zeny' => '1000',
            'total_uber_cost' => '1100',
            'mithril_cost' => '1200',
            'platinum_cost' => '1300',
            'gold_cost' => '1400',
            'silver_cost' => '1500',
            'zeny_cost' => '1600',
        ]);

        $this->assertSame(100, $serverZeny->silver_count);
        $this->assertSame(200, $serverZeny->gold_count);
        $this->assertSame(300, $serverZeny->mithril_count);
        $this->assertSame(400, $serverZeny->platinum_count);
        $this->assertSame(500, $serverZeny->player_zeny);
        $this->assertSame(1, $serverZeny->char_online);
        $this->assertSame(600, $serverZeny->silver_zeny);
        $this->assertSame(700, $serverZeny->gold_zeny);
        $this->assertSame(800, $serverZeny->mithril_zeny);
        $this->assertSame(900, $serverZeny->platinum_zeny);
        $this->assertSame(1000, $serverZeny->total_zeny);
        $this->assertSame(1100, $serverZeny->total_uber_cost);
        $this->assertSame(1200, $serverZeny->mithril_cost);
        $this->assertSame(1300, $serverZeny->platinum_cost);
        $this->assertSame(1400, $serverZeny->gold_cost);
        $this->assertSame(1500, $serverZeny->silver_cost);
        $this->assertSame(1600, $serverZeny->zeny_cost);
    }

    /**
     * Test database interaction.
     *
     * @return void
     */
    public function testDatabaseInteraction()
    {
        ServerZeny::unguard();

        // Assuming you have a way to seed or mock your view data
        // For this example, I'll just create a mock record
        ServerZeny::create([]);

        $serverZeny = ServerZeny::first();

        $this->assertNotNull($serverZeny);
        $this->assertIsInt($serverZeny->silver_count ?? 0);
        $this->assertIsInt($serverZeny->gold_count ?? 0);
        $this->assertIsInt($serverZeny->mithril_count ?? 0);
        $this->assertIsInt($serverZeny->platinum_count ?? 0);
        $this->assertIsInt($serverZeny->player_zeny ?? 0);
        $this->assertIsInt($serverZeny->char_online ?? 0);
        $this->assertIsInt($serverZeny->silver_zeny ?? 0);
        $this->assertIsInt($serverZeny->gold_zeny ?? 0);
        $this->assertIsInt($serverZeny->mithril_zeny ?? 0);
        $this->assertIsInt($serverZeny->platinum_zeny ?? 0);
        $this->assertIsInt($serverZeny->total_zeny ?? 0);
        $this->assertIsInt($serverZeny->total_uber_cost ?? 0);
        $this->assertIsInt($serverZeny->mithril_cost ?? 0);
        $this->assertIsInt($serverZeny->platinum_cost ?? 0);
        $this->assertIsInt($serverZeny->gold_cost ?? 0);
        $this->assertIsInt($serverZeny->silver_cost ?? 0);
        $this->assertIsInt($serverZeny->zeny_cost ?? 0);
    }
}
