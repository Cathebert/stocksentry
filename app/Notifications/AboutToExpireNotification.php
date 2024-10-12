<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AboutToExpireNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    protected $path;
    protected $lab_name;
    public function __construct($path,$lab_name)
    {
        $this->path=$path;
        $this->lab_name=$lab_name;
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
        return (new MailMessage)
                    ->line('We are reminding you that the following items from '.$this->lab_name.' have 30 days before they expire.')
                    ->line('Please consider using them quickly to avoid loss due to expiry')
                    ->attach($this->path)
                    ->line('Thank you for using our application!');
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