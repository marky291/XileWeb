<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DiscordLinkedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $discordUsername
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Discord Account Linked')
            ->greeting('Hello!')
            ->line("A Discord account **{$this->discordUsername}** has been linked to your XileRO master account.")
            ->line('You can now use "Login with Discord" to access your account.')
            ->line('**If you did not do this**, please contact support immediately via our Discord server and change your password.')
            ->action('Go to Dashboard', route('dashboard'))
            ->salutation('Stay safe! - The XileRO Team');
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
