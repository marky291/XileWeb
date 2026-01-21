<?php

namespace Tests\Feature\Console;

use App\Actions\MakeHashedLoginPassword;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class EncryptGamePasswordTest extends TestCase
{
    #[Test]
    public function it_encrypts_password_with_argument(): void
    {
        $this->artisan('game:encrypt-password', ['password' => 'testpassword'])
            ->expectsOutputToContain('Encrypted password for xilero')
            ->assertExitCode(0);
    }

    #[Test]
    public function it_uses_xilero_server_by_default(): void
    {
        $expectedHash = MakeHashedLoginPassword::run('mypassword', 'xilero');

        $this->artisan('game:encrypt-password', ['password' => 'mypassword'])
            ->expectsOutputToContain($expectedHash)
            ->assertExitCode(0);
    }

    #[Test]
    public function it_accepts_xileretro_server_option(): void
    {
        $expectedHash = MakeHashedLoginPassword::run('mypassword', 'xileretro');

        $this->artisan('game:encrypt-password', [
            'password' => 'mypassword',
            '--server' => 'xileretro',
        ])
            ->expectsOutputToContain('Encrypted password for xileretro')
            ->expectsOutputToContain($expectedHash)
            ->assertExitCode(0);
    }

    #[Test]
    public function it_rejects_invalid_server(): void
    {
        $this->artisan('game:encrypt-password', [
            'password' => 'testpassword',
            '--server' => 'invalidserver',
        ])
            ->expectsOutputToContain('Invalid server')
            ->assertExitCode(1);
    }

    #[Test]
    public function it_prompts_for_password_when_not_provided(): void
    {
        $expectedHash = MakeHashedLoginPassword::run('secretpass', 'xilero');

        $this->artisan('game:encrypt-password')
            ->expectsQuestion('Enter password to encrypt', 'secretpass')
            ->expectsOutputToContain($expectedHash)
            ->assertExitCode(0);
    }

    #[Test]
    public function it_rejects_empty_password(): void
    {
        $this->artisan('game:encrypt-password')
            ->expectsQuestion('Enter password to encrypt', '')
            ->expectsOutputToContain('Password cannot be empty')
            ->assertExitCode(1);
    }

    #[Test]
    public function it_handles_special_characters_in_password(): void
    {
        $password = 'p@$$w0rd!@#$%';
        $expectedHash = MakeHashedLoginPassword::run($password, 'xilero');

        $this->artisan('game:encrypt-password', ['password' => $password])
            ->expectsOutputToContain($expectedHash)
            ->assertExitCode(0);
    }
}
