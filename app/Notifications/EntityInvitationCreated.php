<?php

namespace App\Notifications;

use App\Models\EntityInvitation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EntityInvitationCreated extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public EntityInvitation $invitation)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = route('invitations.accept', ['token' => $this->invitation->token]);

        return (new MailMessage)
                    ->subject('Invitation to join ' . $this->invitation->entity->name)
                    ->greeting('Hello!')
                    ->line('You have been invited to join the team **' . $this->invitation->entity->name . '**.')
                    ->action('Accept Invitation', $url)
                    ->line('If you did not expect this invitation, you can ignore this email.');
    }
}