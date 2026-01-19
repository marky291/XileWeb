<?php

namespace App\Notifications;

use App\Models\GameAccount;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GameAccountPasswordResetNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public GameAccount $gameAccount
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Game Account Password Changed')
            ->greeting('Hello!')
            ->line("The password for your game account **{$this->gameAccount->userid}** on **{$this->gameAccount->serverName()}** has been changed.")
            ->line('If you made this change, no further action is needed.')
            ->line('**If you did not make this change**, please contact support immediately via our Discord server.')
            ->action('Go to Dashboard', route('dashboard'))
            ->salutation('Stay safe! - The XileRO Team');
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
