<?php

namespace App\Listeners;

use App\Notifications\WelcomeNotification;
use Illuminate\Auth\Events\Verified;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendWelcomeEmailOnVerified implements ShouldQueue
{
    public function handle(Verified $event): void
    {
        $event->user->notify(new WelcomeNotification);
    }
}
