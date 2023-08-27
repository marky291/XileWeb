<?php

namespace Tests\Unit\Ragnarok;

use App\Ragnarok\DonationUber;
use App\Ragnarok\Login;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_login_record()
    {
        $login = Login::factory()->create();

        $this->assertDatabaseHas('login', ['userid' => $login->userid]);
    }

    /** @test */
    public function it_can_update_a_login_record()
    {
        $login = Login::factory()->create();
        $newEmail = 'newemail@example.com';

        $login->update(['email' => $newEmail]);

        $this->assertDatabaseHas('login', ['email' => $newEmail]);
    }

    /** @test */
    public function it_can_delete_a_login_record()
    {
        $login = Login::factory()->create();

        $login->delete();

        $this->assertDatabaseMissing('login', ['userid' => $login->userid]);
    }

    /** @test */
    public function it_can_have_donation_ubers()
    {
        $login = Login::factory()->create();

        DonationUber::factory()->create([
            'account_id' => $login->account_id,
            'pending_ubers' => 50
        ]);

        $this->assertDatabaseHas('donation_ubers', [
            'account_id' => $login->account_id,
            'pending_ubers' => 50
        ]);
    }
}
