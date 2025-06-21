<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;

class CustomRegistrationMail extends Notification
{
    /**
     * Get the delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Build the custom email verification message.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );

        return (new MailMessage)
            ->subject('✨ Confirm Your Email for PEOConnect')
            ->view('emails.verify', [
                'verificationUrl' => $verificationUrl,
                'notifiable' => $notifiable,
            ]);
    }

    /**
     * Optional: Notification array representation
     */
    public function toArray($notifiable)
    {
        return [];
    }
}
