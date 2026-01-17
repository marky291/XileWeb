<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Welcome to XileRO! Your Adventure Begins')
            ->greeting('Hey there, Adventurer!')
            ->line('Welcome to XileRO! We\'re thrilled to have you join our community. Your master account has been created successfully, and you\'re all set to start your journey.')
            ->line('With your master account, you can create up to **6 game accounts** to explore different characters and playstyles.')
            ->action('Go to Dashboard', route('dashboard'))
            ->line('**Here are some helpful resources to get you started:**')
            ->line('- [XileRO Player Guide](https://info.xilero.net) - XileRO Guides')
            ->line('- [XileRetro Wiki](https://wiki.xilero.net) - XileRetro Guides')
            ->line('- [Join our Discord](https://discord.gg/pvXGhChQyh) - Chat with the community')
            ->line('- [Support Tickets](https://discord.gg/pvXGhChQyh) - Get help from our staff')
            ->line('- [Download the Game Client](https://xilero.net) - Get the latest client to start playing')
            ->line('If you ever need help, our friendly community and staff are always here for you. Don\'t hesitate to reach out!')
            ->salutation('See you in-game! - The XileRO Team');
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
