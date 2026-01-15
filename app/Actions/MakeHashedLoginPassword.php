<?php

namespace App\Actions;

use Lorisleiva\Actions\Concerns\AsAction;

class MakeHashedLoginPassword
{
    use AsAction;

    public function handle(string $unhashedPassword, string $server = 'xilero'): string
    {
        $secret = $server === 'xileretro'
            ? config('database.secret_xileretro')
            : config('database.secret');

        return hash('sha256', $unhashedPassword.$secret);
    }
}
