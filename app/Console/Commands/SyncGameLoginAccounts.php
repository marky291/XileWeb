<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\LoginUser;
use App\Models\UserLogin;
use App\Ragnarok\Login;
use Illuminate\Console\Command;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Mockery\Exception;
use Ramsey\Uuid\Guid\Guid;

class SyncGameLoginAccounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-game-login-accounts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!app()->runningUnitTests()) {
            $this->info('Getting all logins');
        }

        $startId = 2000000;

        Login::where('account_id', '>=', $startId)->chunk(1000, function (Collection $logins) {
            foreach ($logins as $login) {

                $created = false;

                do {
                    if ($login)
                    {
                        if (User::firstWhere('email', '=', $login->email))
                        {
                            $login->email = Str::replace('-', '', Str::uuid()->toString()) . "@xilero.net";
                        }

                        $user = User::query()->firstOrCreate(['name' => $login->userid], [
                            'email' => $login->email,
                            'password' => Hash::make("Xile "),
                            'group_id' => $login->group_id,
                        ]);

                        // sync the group_id
                        $user->group_id = $login->group_id;
                        $user->save();

                        try {
                            $user->logins()->attach($login);

                            if (!app()->runningUnitTests()) {
                                $this->info("{$login->account_id} :: Created {$user->name} {$user->email} {$user->group_id}");
                            }
                        } catch (UniqueConstraintViolationException $e) {
                            if (!app()->runningUnitTests()) {
                                $this->error("{$login->account_id} :: Already attached {$login->userid}");
                            }
                        }
                    }
                    $created = true;
                } while ($created == false);
            }
        });
    }
}
