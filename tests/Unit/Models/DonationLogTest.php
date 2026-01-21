<?php

namespace Tests\Unit\Models;

use App\Models\DonationLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DonationLogTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $log = DonationLog::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $log->user);
        $this->assertEquals($user->id, $log->user->id);
    }

    #[Test]
    public function it_belongs_to_admin(): void
    {
        $admin = User::factory()->admin()->create();
        $log = DonationLog::factory()->create(['admin_id' => $admin->id]);

        $this->assertInstanceOf(User::class, $log->admin);
        $this->assertEquals($admin->id, $log->admin->id);
    }

    #[Test]
    public function it_belongs_to_reverted_by_admin(): void
    {
        $admin = User::factory()->admin()->create();
        $log = DonationLog::factory()->create([
            'reverted_by' => $admin->id,
            'reverted_at' => now(),
        ]);

        $this->assertInstanceOf(User::class, $log->revertedByAdmin);
        $this->assertEquals($admin->id, $log->revertedByAdmin->id);
    }

    #[Test]
    public function it_detects_reverted_status(): void
    {
        $notReverted = DonationLog::factory()->create([
            'reverted_at' => null,
        ]);

        $reverted = DonationLog::factory()->create([
            'reverted_at' => now(),
        ]);

        $this->assertFalse($notReverted->isReverted());
        $this->assertTrue($reverted->isReverted());
    }

    #[Test]
    public function it_returns_payment_method_name_from_config(): void
    {
        config(['donation.payment_methods.paypal.name' => 'PayPal']);

        $log = DonationLog::factory()->create(['payment_method' => 'paypal']);

        $this->assertEquals('PayPal', $log->paymentMethodName());
    }

    #[Test]
    public function it_returns_raw_payment_method_when_config_missing(): void
    {
        $log = DonationLog::factory()->create(['payment_method' => 'unknown_method']);

        $this->assertEquals('unknown_method', $log->paymentMethodName());
    }

    #[Test]
    public function it_casts_amount_to_decimal(): void
    {
        $log = DonationLog::factory()->create(['amount' => 25.99]);

        $this->assertEquals('25.99', $log->amount);
    }

    #[Test]
    public function it_casts_uber_fields_to_integer(): void
    {
        $log = DonationLog::factory()->create([
            'base_ubers' => 100,
            'bonus_ubers' => 20,
            'total_ubers' => 120,
            'ubers_recovered' => 50,
        ]);

        $this->assertIsInt($log->base_ubers);
        $this->assertIsInt($log->bonus_ubers);
        $this->assertIsInt($log->total_ubers);
        $this->assertIsInt($log->ubers_recovered);
    }

    #[Test]
    public function it_casts_reverted_at_to_datetime(): void
    {
        $log = DonationLog::factory()->create([
            'reverted_at' => '2024-01-15 10:30:00',
        ]);

        $this->assertInstanceOf(\Carbon\Carbon::class, $log->reverted_at);
    }

    #[Test]
    public function it_has_fillable_attributes(): void
    {
        $log = new DonationLog();

        $this->assertContains('user_id', $log->getFillable());
        $this->assertContains('admin_id', $log->getFillable());
        $this->assertContains('amount', $log->getFillable());
        $this->assertContains('payment_method', $log->getFillable());
        $this->assertContains('base_ubers', $log->getFillable());
        $this->assertContains('bonus_ubers', $log->getFillable());
        $this->assertContains('total_ubers', $log->getFillable());
        $this->assertContains('notes', $log->getFillable());
        $this->assertContains('reverted_at', $log->getFillable());
        $this->assertContains('reverted_by', $log->getFillable());
        $this->assertContains('ubers_recovered', $log->getFillable());
    }
}
