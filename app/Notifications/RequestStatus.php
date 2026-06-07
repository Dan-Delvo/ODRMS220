<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

use Illuminate\Notifications\Notifiable;

class RequestStatus extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public string $name, public string $email)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        Log::info('Sending request status email', ['email' => $this->email]);

        // Mail::raw('This is a test email from the notification.', function ($message) {
        //     $message->to($this->email)
        //             ->subject('Test Email from Notification')
        //             ;
        // });

        return (new MailMessage)
            ->subject('Document Request Status Update')
            ->greeting('Hello ' . $this->name)
            ->line('The status of your document request has been updated.')
            ->line('Please contact the registrar\'s office if you need assistance.')
            ->salutation('Online Document Request Management System');

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
