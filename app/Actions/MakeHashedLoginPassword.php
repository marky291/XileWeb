<?php

namespace App\Actions;

use Lorisleiva\Actions\Concerns\AsAction;

class MakeHashedLoginPassword
{
    use AsAction;

    public function handle(string $unhashedPassword)
    {
        return hash('sha256', $unhashedPassword.config('database.secret'));
    }
}
