<?php

namespace Tests\Unit\Models;

use App\Models\User;
use App\Models\LoginUser;
use App\Ragnarok\Login;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_user_can_have_multiple_logins()
    {
        $user = User::factory()->create()->refresh();

        $userLogins1 = Login::factory()->create();
        $userLogins2 = Login::factory()->create();

        $user->logins()->attach($userLogins1);
        $user->logins()->attach($userLogins2);

        $this->assertCount(2, $user->logins);
    }
}
