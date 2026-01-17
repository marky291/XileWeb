<?php

namespace Tests\Unit\XileRO;

use App\XileRO\XileRO_Login;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_create_a_login_record(): void
    {
        $login = XileRO_Login::factory()->create();

        $this->assertDatabaseHas('login', ['userid' => $login->userid]);
    }

    public function test_it_can_update_a_login_record(): void
    {
        $login = XileRO_Login::factory()->create();
        $newEmail = 'newemail@example.com';

        $login->update(['email' => $newEmail]);

        $this->assertDatabaseHas('login', ['email' => $newEmail]);
    }

    public function test_it_can_delete_a_login_record(): void
    {
        $login = XileRO_Login::factory()->create();

        $login->delete();

        $this->assertDatabaseMissing('login', ['userid' => $login->userid]);
    }
}
