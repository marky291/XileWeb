<?php

namespace App\Console\Commands;

use App\Actions\MakeHashedLoginPassword;
use Illuminate\Console\Command;

class EncryptGamePassword extends Command
{
    protected $signature = 'game:encrypt-password {password?} {--server=xilero : The server to use (xilero or xileretro)}';

    protected $description = 'Encrypt a password using the game server hashing algorithm';

    public function handle(): int
    {
        $password = $this->argument('password');
        $server = $this->option('server');

        if (! in_array($server, ['xilero', 'xileretro'])) {
            $this->error('Invalid server. Use "xilero" or "xileretro".');

            return self::FAILURE;
        }

        if (! $password) {
            $password = $this->secret('Enter password to encrypt');
        }

        if (empty($password)) {
            $this->error('Password cannot be empty.');

            return self::FAILURE;
        }

        $hashed = MakeHashedLoginPassword::run($password, $server);

        $this->newLine();
        $this->info("Encrypted password for {$server}:");
        $this->line($hashed);
        $this->newLine();

        return self::SUCCESS;
    }
}
