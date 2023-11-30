<?php

namespace App\Actions;

use App\Ragnarok\Char;
use Lorisleiva\Actions\Concerns\AsAction;

class ResetCharacterPosition
{
    use AsAction;

    public function handle(Char $char)
    {
        $char->update([
            'last_map' => config('xilero.character.reset.position.map'),
            'last_x' => config('xilero.character.reset.position.x'),
            'last_y' => config('xilero.character.reset.position.y'),
        ]);
    }
}
