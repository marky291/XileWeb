<?php

namespace App\Actions;

use App\XileRetro\XileRetro_Char;
use App\XileRO\XileRO_Char;
use Lorisleiva\Actions\Concerns\AsAction;

class ResetCharacterPosition
{
    use AsAction;

    public function handle(XileRO_Char|XileRetro_Char $char): void
    {
        $char->update([
            'last_map' => config('xilero.character.reset.position.map'),
            'last_x' => config('xilero.character.reset.position.x'),
            'last_y' => config('xilero.character.reset.position.y'),
        ]);
    }
}
