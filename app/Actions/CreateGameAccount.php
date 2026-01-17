<?php

namespace App\Actions;

use App\Models\GameAccount;
use App\Models\User;
use App\XileRetro\XileRetro_Login;
use App\XileRO\XileRO_Login;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateGameAccount
{
    use AsAction;

    public function handle(User $user, array $data): GameAccount
    {
        return DB::transaction(function () use ($user, $data) {
            $server = $data['server'] ?? 'xilero';
            $hashedPassword = MakeHashedLoginPassword::run($data['password'], $server);

            // Select the appropriate Login model based on server
            $loginClass = $server === 'xileretro' ? XileRetro_Login::class : XileRO_Login::class;

            // Create in game database first to get account_id
            $login = $loginClass::create([
                'userid' => $data['userid'],
                'email' => $data['email'],
                'user_pass' => $hashedPassword,
                'sex' => $data['sex'] ?? 'M',
                'group_id' => 0,
                'state' => 0,
            ]);

            // Create in website database with reference
            return GameAccount::create([
                'user_id' => $user->id,
                'server' => $server,
                'ragnarok_account_id' => $login->account_id,
                'userid' => $data['userid'],
                'email' => $data['email'],
                'user_pass' => $hashedPassword,
                'sex' => $data['sex'] ?? 'M',
                'group_id' => 0,
                'state' => 0,
            ]);
        });
    }
}
