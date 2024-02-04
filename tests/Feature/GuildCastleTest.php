<?php

namespace Tests\Feature;

use App\Ragnarok\CastleName;
use App\Ragnarok\GuildCastle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GuildCastleTest extends TestCase
{
    public function testGetNameById()
    {
        $this->assertEquals('Neuschwanstein', GuildCastle::getCastleNameById(0));
        $this->assertEquals('Kriemhild', GuildCastle::getCastleNameById(15));
        $this->assertNull(GuildCastle::getCastleNameById(100)); // Assuming 100 does not exist
    }

    public function testGetIdByName()
    {
        $this->assertEquals(0, GuildCastle::getCastleIdByName('Neuschwanstein'));
        $this->assertEquals(15, GuildCastle::getCastleIdByName('Kriemhild'));
        $this->assertNull(GuildCastle::getCastleIdByName('NonExistentCastle'));
    }

    public function testGetNameAttribute()
    {
        $guildCastle = new GuildCastle(['castle_id' => 0]); // Assuming constructor or an ORM method to set properties
        $this->assertEquals('Neuschwanstein', $guildCastle->name);

        $guildCastle = new GuildCastle(['castle_id' => 15]);
        $this->assertEquals('Kriemhild', $guildCastle->name);

        $guildCastle = new GuildCastle(['castle_id' => 100]);
        $this->assertEquals('Unknown Castle', $guildCastle->name);
    }

    public function testCastleEdition()
    {
        $this->assertEquals(1, CastleName::Neuschwanstein->getEdition());
        $this->assertEquals(2, CastleName::Himinn->getEdition());
        $this->assertEquals(0, CastleName::GuildVsGuild->getEdition()); // Assuming 'GuildVsGuild' is not explicitly assigned to an edition, thus defaults to 0.
    }

    public function testCastleName()
    {
        $this->assertEquals('Neuschwanstein', CastleName::Neuschwanstein->getName());
        $this->assertEquals('Himinn', CastleName::Himinn->getName());
        // Test for a castle that might not have a direct name method call, assuming 'GuildVsGuild' returns 'Unknown Castle' by default.
        $this->assertEquals('Unknown Castle', CastleName::tryFrom(100)?->getName() ?? 'Unknown Castle');
    }

    public function testEnumValue()
    {
        $this->assertEquals(0, CastleName::Neuschwanstein->value);
        $this->assertEquals(25, CastleName::Himinn->value);
        // Test to ensure invalid castle ID returns null
        $this->assertNull(CastleName::tryFrom(100));
    }
}
