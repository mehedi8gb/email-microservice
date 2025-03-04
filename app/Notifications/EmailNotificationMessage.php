<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmailNotificationMessage extends Notification
{
    use Queueable;

    protected $recipient;
    protected $subject;

    public function __construct($recipient, $subject)
    {
        $this->recipient = $recipient;
        $this->subject = $subject;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Email Sent Notification')
            ->line("An email was sent to {$this->recipient}.")
            ->line("Subject: {$this->subject}");
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
