<?php

namespace Tests\Unit\Livewire;

use App\Livewire\DonationUberSender;
use App\Ragnarok\DonationUber;
use App\Ragnarok\Login;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DonationUberSenderTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_shows_the_donation_uber_sender_component()
    {
        Livewire::test(DonationUberSender::class)
            ->assertSee('Username')
            ->assertSee('Uber Amount');
    }

    /** @test */
    public function username_is_required()
    {
        Livewire::test(DonationUberSender::class)
            ->set('username', '')
            ->call('send')
            ->assertHasErrors(['username' => 'required']);
    }

    /** @test */
    public function username_has_valid_length()
    {
        Livewire::test(DonationUberSender::class)
            ->set('username', 'a')
            ->call('send')
            ->assertHasErrors(['username' => 'min']);

        Livewire::test(DonationUberSender::class)
            ->set('username', str_repeat('a', 24))
            ->call('send')
            ->assertHasErrors(['username' => 'max']);
    }

    /** @test */
    public function uber_amount_is_required_and_must_be_integer()
    {
        Livewire::test(DonationUberSender::class)
            ->set('uber_amount', '')
            ->call('send')
            ->assertHasErrors(['uber_amount' => 'required']);

        Livewire::test(DonationUberSender::class)
            ->set('uber_amount', '1.5')
            ->call('send')
            ->assertHasErrors(['uber_amount' => 'integer']);
    }

    /** @test */
    public function send_method_is_defined()
    {
        $component = Livewire::test(DonationUberSender::class);

        $this->assertTrue(method_exists($component->instance(), 'send'));
    }

    /** @test */
    public function it_updates_pending_uber_amount_for_a_valid_user()
    {
        $this->withoutExceptionHandling();

        // Arrange
        $login = Login::factory()->create(['userid' => 'johndoe']);

        DonationUber::factory()->create([
            'account_id' => $login->account_id,
            'username' => 'johndoe',
            'pending_ubers' => 20
        ]);

        // Act
        Livewire::test(DonationUberSender::class)
            ->set('username', 'johndoe')
            ->set('uber_amount', 50)
            ->call('send');

        // Assert
        $this->assertDatabaseHas('donation_ubers', [
            'account_id' => $login->account_id,
            'pending_ubers' => 70,  // initial 20 + added 50
            'username' => 'johndoe'
        ]);
    }

    /** @test */
    public function it_validates_required_fields_before_sending()
    {
        // Act & Assert
        Livewire::test(DonationUberSender::class)
            ->set('username', '')
            ->set('uber_amount', '')
            ->call('send')
            ->assertHasErrors(['username' => 'required', 'uber_amount' => 'required']);
    }
}
