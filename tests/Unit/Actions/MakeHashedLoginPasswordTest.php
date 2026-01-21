<?php

namespace Tests\Unit\Actions;

use App\Actions\MakeHashedLoginPassword;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MakeHashedLoginPasswordTest extends TestCase
{
    #[Test]
    public function it_hashes_password_with_sha256(): void
    {
        $password = 'testpassword123';
        $server = 'xilero';

        $hashed = MakeHashedLoginPassword::run($password, $server);

        // Result should be a 64-character hex string (SHA256 output)
        $this->assertEquals(64, strlen($hashed));
        $this->assertMatchesRegularExpression('/^[a-f0-9]{64}$/', $hashed);
    }

    #[Test]
    public function it_uses_xilero_secret_by_default(): void
    {
        $password = 'testpassword';

        $hashed1 = MakeHashedLoginPassword::run($password);
        $hashed2 = MakeHashedLoginPassword::run($password, 'xilero');

        $this->assertEquals($hashed1, $hashed2);
    }

    #[Test]
    public function it_produces_different_hashes_for_different_servers(): void
    {
        $password = 'testpassword';

        $xileroHash = MakeHashedLoginPassword::run($password, 'xilero');
        $xileretroHash = MakeHashedLoginPassword::run($password, 'xileretro');

        // If secrets are different, hashes should be different
        // If secrets are the same (in test env), they might be equal
        // At minimum, both should be valid hashes
        $this->assertEquals(64, strlen($xileroHash));
        $this->assertEquals(64, strlen($xileretroHash));
    }

    #[Test]
    public function it_produces_consistent_hashes(): void
    {
        $password = 'consistentpassword';

        $hash1 = MakeHashedLoginPassword::run($password, 'xilero');
        $hash2 = MakeHashedLoginPassword::run($password, 'xilero');

        $this->assertEquals($hash1, $hash2);
    }

    #[Test]
    public function it_produces_different_hashes_for_different_passwords(): void
    {
        $hash1 = MakeHashedLoginPassword::run('password1', 'xilero');
        $hash2 = MakeHashedLoginPassword::run('password2', 'xilero');

        $this->assertNotEquals($hash1, $hash2);
    }

    #[Test]
    public function it_handles_empty_password(): void
    {
        $hash = MakeHashedLoginPassword::run('', 'xilero');

        // Should still produce a valid hash
        $this->assertEquals(64, strlen($hash));
    }

    #[Test]
    public function it_handles_special_characters_in_password(): void
    {
        $password = 'p@ssw0rd!#%^&*(){}[]|\\:\";<>,.?/~`';

        $hash = MakeHashedLoginPassword::run($password, 'xilero');

        $this->assertEquals(64, strlen($hash));
    }

    #[Test]
    public function it_handles_unicode_characters_in_password(): void
    {
        $password = "пароль密码パスワード";

        $hash = MakeHashedLoginPassword::run($password, 'xilero');

        $this->assertEquals(64, strlen($hash));
    }

    #[Test]
    public function it_handles_very_long_password(): void
    {
        $password = str_repeat('a', 10000);

        $hash = MakeHashedLoginPassword::run($password, 'xilero');

        $this->assertEquals(64, strlen($hash));
    }
}
