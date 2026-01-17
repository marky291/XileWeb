<?php

namespace App\Auth;

use App\Actions\MakeHashedLoginPassword;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;

class RagnarokUserProvider extends EloquentUserProvider
{
    /**
     * Validate a user against the given credentials using SHA256.
     */
    public function validateCredentials(Authenticatable $user, array $credentials): bool
    {
        $plain = $credentials['password'];
        $hashed = MakeHashedLoginPassword::run($plain);

        return hash_equals($user->getAuthPassword(), $hashed);
    }
}
