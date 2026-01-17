<?php

namespace Tests\Feature;

use App\XileRO\CastleName;
use App\XileRO\XileRO_GuildCastle;
use Tests\TestCase;

class GuildCastleTest extends TestCase
{
    public function test_get_name_by_id(): void
    {
        $this->assertEquals('Neuschwanstein', XileRO_GuildCastle::getCastleNameById(0));
        $this->assertEquals('Kriemhild', XileRO_GuildCastle::getCastleNameById(15));
        $this->assertNull(XileRO_GuildCastle::getCastleNameById(100));
    }

    public function test_get_id_by_name(): void
    {
        $this->assertEquals(0, XileRO_GuildCastle::getCastleIdByName('Neuschwanstein'));
        $this->assertEquals(15, XileRO_GuildCastle::getCastleIdByName('Kriemhild'));
        $this->assertNull(XileRO_GuildCastle::getCastleIdByName('NonExistentCastle'));
    }

    public function test_get_name_attribute(): void
    {
        $guildCastle = new XileRO_GuildCastle(['castle_id' => 0]);
        $this->assertEquals('Neuschwanstein', $guildCastle->name);

        $guildCastle = new XileRO_GuildCastle(['castle_id' => 15]);
        $this->assertEquals('Kriemhild', $guildCastle->name);

        $guildCastle = new XileRO_GuildCastle(['castle_id' => 100]);
        $this->assertEquals('Unknown Castle', $guildCastle->name);
    }

    public function test_castle_edition(): void
    {
        $this->assertEquals(1, CastleName::Neuschwanstein->getEdition());
        $this->assertEquals(2, CastleName::Himinn->getEdition());
        $this->assertEquals(0, CastleName::GuildVsGuild->getEdition());
    }

    public function test_castle_name(): void
    {
        $this->assertEquals('Neuschwanstein', CastleName::Neuschwanstein->getName());
        $this->assertEquals('Himinn', CastleName::Himinn->getName());
        $this->assertEquals('Unknown Castle', CastleName::tryFrom(100)?->getName() ?? 'Unknown Castle');
    }

    public function test_enum_value(): void
    {
        $this->assertEquals(0, CastleName::Neuschwanstein->value);
        $this->assertEquals(25, CastleName::Himinn->value);
        $this->assertNull(CastleName::tryFrom(100));
    }
}
