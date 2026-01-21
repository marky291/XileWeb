<?php

namespace Tests\Unit\Actions;

use App\Actions\ResetCharacterPosition;
use App\XileRO\XileRO_Char;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ResetCharacterPositionTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_resets_character_position_to_config_values(): void
    {
        config([
            'xilero.character.reset.position.map' => 'prontera',
            'xilero.character.reset.position.x' => 156,
            'xilero.character.reset.position.y' => 191,
        ]);

        $character = XileRO_Char::factory()->create([
            'last_map' => 'prt_fild08',
            'last_x' => 100,
            'last_y' => 200,
        ]);

        ResetCharacterPosition::run($character);

        $character->refresh();

        $this->assertEquals('prontera', $character->last_map);
        $this->assertEquals(156, $character->last_x);
        $this->assertEquals(191, $character->last_y);
    }

    #[Test]
    public function it_updates_only_position_fields(): void
    {
        config([
            'xilero.character.reset.position.map' => 'prontera',
            'xilero.character.reset.position.x' => 100,
            'xilero.character.reset.position.y' => 100,
        ]);

        $character = XileRO_Char::factory()->create([
            'name' => 'TestCharacter',
            'base_level' => 99,
            'job_level' => 50,
            'last_map' => 'somewhere',
            'last_x' => 50,
            'last_y' => 50,
        ]);

        ResetCharacterPosition::run($character);

        $character->refresh();

        // Position should be updated
        $this->assertEquals('prontera', $character->last_map);
        $this->assertEquals(100, $character->last_x);
        $this->assertEquals(100, $character->last_y);

        // Other fields should remain unchanged
        $this->assertEquals('TestCharacter', $character->name);
        $this->assertEquals(99, $character->base_level);
        $this->assertEquals(50, $character->job_level);
    }

    #[Test]
    public function it_persists_changes_to_database(): void
    {
        config([
            'xilero.character.reset.position.map' => 'geffen',
            'xilero.character.reset.position.x' => 120,
            'xilero.character.reset.position.y' => 68,
        ]);

        $character = XileRO_Char::factory()->create([
            'last_map' => 'alberta',
            'last_x' => 30,
            'last_y' => 40,
        ]);

        $charId = $character->char_id;

        ResetCharacterPosition::run($character);

        // Fetch fresh from database
        $freshCharacter = XileRO_Char::find($charId);

        $this->assertEquals('geffen', $freshCharacter->last_map);
        $this->assertEquals(120, $freshCharacter->last_x);
        $this->assertEquals(68, $freshCharacter->last_y);
    }
}
